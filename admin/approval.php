<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

require_once __DIR__ . '/../includes/barcode.php';
require_once __DIR__ . '/../includes/mailer.php';

/**
 * Setujui seorang peserta.
 *
 * Setiap pendaftar tetap masuk database; panitia menentukan
 * penerimaan satu per satu tanpa batas kuota otomatis.
 *
 * @return array{ok:bool, pesan:string}
 */
function setujui_peserta(int $id, string $olehAdmin): array
{
    $pdo = db();

    try {
        $pdo->beginTransaction();

        // Hanya peserta berstatus pending yang bisa disetujui
        $stmt = $pdo->prepare(
            "UPDATE peserta
                SET status_acc = 'diterima', acc_at = NOW(), acc_oleh = ?, catatan_acc = NULL
              WHERE id = ? AND status_acc = 'pending'"
        );
        $stmt->execute([$olehAdmin, $id]);

        if ($stmt->rowCount() === 0) {
            $pdo->rollBack();
            return ['ok' => false, 'pesan' => 'Peserta tidak ditemukan atau sudah diproses.'];
        }

        $pdo->commit();

    } catch (Throwable $ex) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Gagal menyetujui peserta #' . $id . ': ' . $ex->getMessage());
        return ['ok' => false, 'pesan' => 'Terjadi kesalahan saat menyimpan data.'];
    }

    // Barcode & email dikerjakan di luar transaksi.
    // Kegagalan di sini tidak membatalkan persetujuan — admin bisa kirim ulang.
    $peserta = cari_peserta_by_id($id);
    if (!$peserta) {
        return ['ok' => true, 'pesan' => 'Peserta disetujui.'];
    }

    if (buat_barcode_peserta($peserta['kode']) === null) {
        return ['ok' => true, 'pesan' => 'Peserta disetujui, tetapi barcode gagal dibuat (periksa ekstensi GD).'];
    }

    if (kirim_barcode_peserta($peserta)) {
        tandai_email_terkirim($id);
        return ['ok' => true, 'pesan' => 'Peserta disetujui dan barcode terkirim.'];
    }

    return ['ok' => true, 'pesan' => 'Peserta disetujui, tetapi email belum terkirim. Gunakan tombol kirim ulang.'];
}

/**
 * Tolak seorang peserta.
 *
 * @return array{ok:bool, pesan:string}
 */
function tolak_peserta(int $id, string $olehAdmin, string $catatan = ''): array
{
    $catatan = mb_substr($catatan, 0, 255);

    $stmt = db()->prepare(
        "UPDATE peserta
            SET status_acc = 'ditolak', acc_at = NOW(), acc_oleh = ?, catatan_acc = ?
          WHERE id = ? AND status_acc = 'pending'"
    );
    $stmt->execute([$olehAdmin, ($catatan !== '' ? $catatan : null), $id]);

    if ($stmt->rowCount() === 0) {
        return ['ok' => false, 'pesan' => 'Peserta tidak ditemukan atau sudah diproses.'];
    }

    $peserta = cari_peserta_by_id($id);
    if ($peserta) {
        kirim_email_ditolak($peserta, $catatan);
    }

    return ['ok' => true, 'pesan' => 'Pendaftaran ditolak.'];
}

// ---------------- Aksi ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require();

    $aksi    = post('aksi');
    $catatan = post('catatan');
    $admin   = admin_nama();

    // Tombol "Setujui" per baris mengirim satu ID lewat nama tombolnya sendiri,
    // sehingga tetap berfungsi walau JavaScript mati.
    if (post('satu_id') !== '') {
        $aksi = 'setujui';
        $ids  = [(int) post('satu_id')];
    } else {
        // ID bisa datang sebagai satu nilai atau banyak (aksi massal)
        $ids = array_values(array_filter(array_map('intval', (array) ($_POST['id'] ?? []))));
        if (!$ids && post('id') !== '') {
            $ids = [(int) post('id')];
        }
    }

    if (!in_array($aksi, ['setujui', 'tolak'], true)) {
        flash_set('error', 'Aksi tidak dikenali.');
        redirect(BASE_URL . '/admin/approval.php');
    }

    if (!$ids) {
        flash_set('warning', 'Tidak ada peserta yang dipilih.');
        redirect(BASE_URL . '/admin/approval.php');
    }

    $berhasil = 0;
    $gagal    = 0;
    $pesanTerakhir = '';

    foreach ($ids as $id) {
        $hasil = $aksi === 'setujui'
            ? setujui_peserta($id, $admin)
            : tolak_peserta($id, $admin, $catatan);

        $hasil['ok'] ? $berhasil++ : $gagal++;
        $pesanTerakhir = $hasil['pesan'];
    }

    if (count($ids) === 1) {
        flash_set($berhasil ? 'success' : 'error', $pesanTerakhir);
    } elseif ($gagal === 0) {
        flash_set('success', $berhasil . ' peserta berhasil diproses.');
    } else {
        flash_set('warning', $berhasil . ' berhasil, ' . $gagal . ' gagal diproses.');
    }

    $kembaliKe = post('status');
    if (!in_array($kembaliKe, ['pending', 'diterima', 'ditolak'], true)) {
        $kembaliKe = 'pending';
    }
    redirect(BASE_URL . '/admin/approval.php?status=' . urlencode($kembaliKe));
}

// ---------------- Data ----------------
$status = get('status', 'pending');
if (!in_array($status, ['pending', 'diterima', 'ditolak'], true)) {
    $status = 'pending';
}

$cari = get('cari');

$where  = ['status_acc = ?'];
$params = [$status];

if ($cari !== '') {
    $where[] = '(nama LIKE ? OR email LIKE ? OR gereja LIKE ? OR kode LIKE ?)';
    $like    = '%' . $cari . '%';
    array_push($params, $like, $like, $like, $like);
}

$sqlWhere = ' WHERE ' . implode(' AND ', $where);

$stmt = db()->prepare(
    'SELECT * FROM peserta' . $sqlWhere . ' ORDER BY created_at ASC, id ASC LIMIT 500'
);
$stmt->execute($params);
$daftar = $stmt->fetchAll();

$stat = statistik_peserta();

/** Bangun URL tab dengan mempertahankan kata kunci pencarian. */
function url_tab(string $status, string $cari): string
{
    $q = ['status' => $status];
    if ($cari !== '') {
        $q['cari'] = $cari;
    }
    return BASE_URL . '/admin/approval.php?' . http_build_query($q);
}

$judul_halaman = 'Approval Pendaftaran';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="mx-auto max-w-7xl px-4 py-8">

  <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
      <h1 class="font-display text-3xl font-black text-slate-900">Approval Pendaftaran</h1>
      <p class="mt-1 text-sm text-slate-500">
        Setujui pendaftaran satu per satu. Barcode dikirim otomatis ke email setelah disetujui.
      </p>
    </div>

    <div class="rounded-2xl border-2 border-coi-200 bg-coi-50 px-6 py-4 text-center">
      <p class="text-xs font-semibold uppercase tracking-wider text-coi-600">Peserta Diterima</p>
      <p class="font-display text-2xl font-black text-coi-700"><?= $stat['diterima'] ?></p>
      <p class="mt-0.5 text-xs text-slate-500">Tanpa batas kuota</p>
    </div>
  </div>

  <?= flash_render() ?>

  <div class="mb-6 flex flex-wrap items-center gap-2">
    <?php
    $tabs = [
      'pending'  => ['Menunggu', $stat['pending'],  'bg-amber-500'],
      'diterima' => ['Diterima', $stat['diterima'], 'bg-emerald-500'],
      'ditolak'  => ['Ditolak',  $stat['ditolak'],  'bg-slate-400'],
    ];
    foreach ($tabs as $key => [$label, $jml, $warna]):
      $aktif = $status === $key; ?>
      <a href="<?= e(url_tab($key, $cari)) ?>"
         class="flex items-center gap-2 rounded-xl border-2 px-4 py-2.5 text-sm font-bold transition
                <?= $aktif ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300' ?>">
        <?= e($label) ?>
        <span class="rounded-full <?= $aktif ? $warna : 'bg-slate-200 text-slate-600' ?> px-2 py-0.5 text-xs font-black <?= $aktif ? 'text-white' : '' ?>">
          <?= (int) $jml ?>
        </span>
      </a>
    <?php endforeach; ?>

    <form method="get" action="" class="ml-auto flex gap-2">
      <input type="hidden" name="status" value="<?= e($status) ?>">
      <input type="search" name="cari" value="<?= e($cari) ?>" placeholder="Cari nama / email…"
             class="w-56 rounded-xl border-2 border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-coi-500 focus:ring-4 focus:ring-coi-100">
      <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-700">
        Cari
      </button>
    </form>
  </div>

  <?php if (!$daftar): ?>

    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white p-16 text-center">
      <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="mt-4 font-semibold text-slate-500">
        <?= $cari !== '' ? 'Tidak ada hasil untuk pencarian ini.' : 'Belum ada data pada status ini.' ?>
      </p>
    </div>

  <?php else: ?>

    <form method="post" action="" id="form-massal">
      <?= csrf_field() ?>
      <input type="hidden" name="status" value="<?= e($status) ?>">

      <?php if ($status === 'pending'): ?>
        <!-- Aksi massal -->
        <div id="bilah-massal" class="mb-4 hidden items-center gap-3 rounded-xl border-2 border-slate-900 bg-slate-900 px-5 py-3">
          <p class="text-sm font-semibold text-white">
            <span id="jml-terpilih">0</span> peserta dipilih
          </p>
          <div class="ml-auto flex gap-2">
            <button type="submit" name="aksi" value="setujui"
                    class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-600">
              Setujui Terpilih
            </button>
            <button type="submit" name="aksi" value="tolak"
                    class="rounded-lg bg-red-500 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-600">
              Tolak Terpilih
            </button>
          </div>
        </div>
      <?php endif; ?>

      <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                <?php if ($status === 'pending'): ?>
                  <th class="w-12 px-4 py-3">
                    <input type="checkbox" id="pilih-semua" aria-label="Pilih semua"
                           class="h-4 w-4 rounded border-slate-300 text-coi-600 focus:ring-coi-500">
                  </th>
                <?php endif; ?>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Peserta</th>
                <th class="px-4 py-3">Asal Jemaat</th>
                <th class="px-4 py-3">Kontak</th>
                <th class="px-4 py-3">Tahu Dari</th>
                <th class="px-4 py-3">Daftar</th>
                <th class="px-4 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($daftar as $i => $p): ?>
                <tr class="transition hover:bg-coi-50/40">
                  <?php if ($status === 'pending'): ?>
                    <td class="px-4 py-3">
                      <input type="checkbox" name="id[]" value="<?= (int) $p['id'] ?>"
                             aria-label="Pilih <?= e($p['nama']) ?>"
                             class="pilih-baris h-4 w-4 rounded border-slate-300 text-coi-600 focus:ring-coi-500">
                    </td>
                  <?php endif; ?>

                  <td class="px-4 py-3 text-slate-400"><?= $i + 1 ?></td>

                  <td class="px-4 py-3">
                    <p class="font-semibold text-slate-900"><?= e($p['nama']) ?></p>
                    <p class="font-mono text-xs text-slate-400"><?= e($p['kode']) ?></p>
                  </td>

                  <td class="px-4 py-3 text-slate-600"><?= e($p['gereja']) ?></td>

                  <td class="px-4 py-3">
                    <p class="text-slate-600"><?= e($p['email']) ?></p>
                    <p class="text-xs text-slate-400"><?= e($p['whatsapp']) ?></p>
                  </td>

                  <td class="px-4 py-3">
                    <?php if (!empty($p['info_dari'])): ?>
                      <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">
                        <?= e($p['info_dari']) ?>
                      </span>
                    <?php else: ?>
                      <span class="text-xs text-slate-300">—</span>
                    <?php endif; ?>
                  </td>

                  <td class="px-4 py-3 text-xs text-slate-500"><?= e(format_tanggal($p['created_at'])) ?></td>

                  <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                      <?php if ($status === 'pending'): ?>
                        <button type="submit" name="satu_id" value="<?= (int) $p['id'] ?>"
                                class="rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-600">
                          Setujui
                        </button>

                        <button type="button"
                                data-id="<?= (int) $p['id'] ?>" data-nama="<?= e($p['nama']) ?>"
                                class="tombol-tolak rounded-lg bg-white px-3 py-1.5 text-xs font-bold text-red-600 ring-1 ring-red-200 transition hover:bg-red-50">
                          Tolak
                        </button>

                      <?php elseif ($status === 'diterima'): ?>
                        <span class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                          <?= !empty($p['email_sent_at']) ? 'Barcode terkirim' : 'Email tertunda' ?>
                        </span>
                        <a href="<?= e(BASE_URL) ?>/admin/send-barcode.php?id=<?= (int) $p['id'] ?>"
                           class="rounded-lg bg-white px-3 py-1.5 text-xs font-bold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-50">
                          Kirim Ulang
                        </a>

                      <?php else: ?>
                        <span class="text-xs text-slate-400">
                          <?= !empty($p['catatan_acc']) ? e($p['catatan_acc']) : 'Ditolak' ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php if (count($daftar) >= 500): ?>
        <p class="mt-4 text-center text-xs text-slate-400">
          Menampilkan 500 data pertama. Gunakan pencarian untuk mempersempit hasil.
        </p>
      <?php endif; ?>
    </form>

  <?php endif; ?>
</main>

<!-- Dialog penolakan -->
<div id="dialog-tolak" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4">
  <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
    <h2 class="font-display text-xl font-black text-slate-900">Tolak Pendaftaran</h2>
    <p class="mt-2 text-sm text-slate-500">
      Menolak <strong id="nama-tolak" class="text-slate-700"></strong>.
      Peserta akan menerima email pemberitahuan.
    </p>

    <form method="post" action="" class="mt-5">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="tolak">
      <input type="hidden" name="status" value="<?= e($status) ?>">
      <input type="hidden" name="id" id="id-tolak" value="">

      <label for="catatan" class="mb-2 block text-sm font-bold text-slate-700">
        Catatan <span class="font-normal text-slate-400">(opsional, ikut dikirim ke peserta)</span>
      </label>
      <textarea name="catatan" id="catatan" rows="3" maxlength="255"
                placeholder="Contoh: kuota sudah terpenuhi"
                class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-coi-500 focus:ring-4 focus:ring-coi-100"></textarea>

      <div class="mt-5 flex gap-3">
        <button type="button" id="batal-tolak"
                class="flex-1 rounded-xl bg-slate-100 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-200">
          Batal
        </button>
        <button type="submit"
                class="flex-1 rounded-xl bg-red-500 py-3 text-sm font-bold text-white transition hover:bg-red-600">
          Tolak Pendaftaran
        </button>
      </div>
    </form>
  </div>
</div>

<script>
(function () {
  // ---- Pilih banyak baris ----
  var semua   = document.getElementById('pilih-semua');
  var baris   = Array.prototype.slice.call(document.querySelectorAll('.pilih-baris'));
  var bilah   = document.getElementById('bilah-massal');
  var jumlah  = document.getElementById('jml-terpilih');

  function perbarui() {
    if (!bilah) { return; }
    var n = baris.filter(function (c) { return c.checked; }).length;
    jumlah.textContent = n;
    bilah.classList.toggle('hidden', n === 0);
    bilah.classList.toggle('flex', n > 0);
  }

  if (semua) {
    semua.addEventListener('change', function () {
      baris.forEach(function (c) { c.checked = semua.checked; });
      perbarui();
    });
  }
  baris.forEach(function (c) { c.addEventListener('change', perbarui); });

  // ---- Dialog penolakan ----
  var dialog = document.getElementById('dialog-tolak');
  var idInput = document.getElementById('id-tolak');
  var namaEl  = document.getElementById('nama-tolak');

  function tutup() {
    dialog.classList.add('hidden');
    dialog.classList.remove('flex');
  }

  document.querySelectorAll('.tombol-tolak').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
      idInput.value  = tombol.dataset.id;
      namaEl.textContent = tombol.dataset.nama;
      dialog.classList.remove('hidden');
      dialog.classList.add('flex');
    });
  });

  document.getElementById('batal-tolak').addEventListener('click', tutup);
  dialog.addEventListener('click', function (ev) { if (ev.target === dialog) { tutup(); } });
  document.addEventListener('keydown', function (ev) {
    if (ev.key === 'Escape' && !dialog.classList.contains('hidden')) { tutup(); }
  });
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
