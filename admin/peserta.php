<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// ---------------- Filter & paginasi ----------------
$cari   = get('cari');
$filter = get('filter');
$hal    = max(1, (int) get('hal', '1'));
$perHal = 25;

$where  = [];
$params = [];

if ($cari !== '') {
    $where[] = '(nama LIKE ? OR email LIKE ? OR kode LIKE ? OR gereja LIKE ?)';
    $like    = '%' . $cari . '%';
    array_push($params, $like, $like, $like, $like);
}

switch ($filter) {
    case 'pending':      $where[] = "status_acc = 'pending'";        break;
    case 'diterima':     $where[] = "status_acc = 'diterima'";       break;
    case 'ditolak':      $where[] = "status_acc = 'ditolak'";        break;
    case 'hadir':        $where[] = "status = 'hadir'";              break;
    case 'belum_hadir':  $where[] = "status = 'belum_hadir'";        break;
}

$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

// Total baris untuk paginasi
$stmtTotal = db()->prepare('SELECT COUNT(*) FROM peserta' . $sqlWhere);
$stmtTotal->execute($params);
$totalBaris = (int) $stmtTotal->fetchColumn();

$totalHal = max(1, (int) ceil($totalBaris / $perHal));
$hal      = min($hal, $totalHal);
$offset   = ($hal - 1) * $perHal;

// LIMIT/OFFSET disisipkan sebagai integer hasil kalkulasi, bukan input mentah
$stmt = db()->prepare(
    'SELECT * FROM peserta' . $sqlWhere .
    ' ORDER BY id DESC LIMIT ' . $perHal . ' OFFSET ' . $offset
);
$stmt->execute($params);
$daftar = $stmt->fetchAll();

$stat  = statistik_peserta();
$angka = static fn(int $n): string => number_format($n, 0, ',', '.');

/** Bangun URL dengan mempertahankan parameter lain. */
function url_hal(array $ubah = []): string
{
    $params = array_merge([
        'cari'   => get('cari'),
        'filter' => get('filter'),
        'hal'    => get('hal', '1'),
    ], $ubah);

    return '?' . http_build_query(array_filter($params, static fn($v) => $v !== '' && $v !== null));
}

$judul_halaman = 'Peserta';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="mx-auto max-w-[100rem] px-4 py-8 sm:px-6 lg:px-8">

  <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="font-display text-3xl font-black text-slate-900">Daftar Peserta</h1>
      <p class="mt-1 text-sm text-slate-500">
        Menampilkan <?= $angka(count($daftar)) ?> dari <?= $angka($totalBaris) ?> data
      </p>
    </div>
    <a href="<?= e(BASE_URL) ?>/admin/export.php<?= $cari !== '' || $filter !== '' ? e(url_hal(['hal' => null])) : '' ?>"
       class="inline-flex items-center gap-2 rounded-xl border-2 border-emerald-500 px-4 py-2.5 text-sm font-bold text-emerald-600 transition hover:bg-emerald-50">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
      </svg>
      Export CSV
    </a>
  </div>

  <?= flash_render() ?>

  <!-- Pencarian & filter -->
  <form method="get" class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="grid gap-4 md:grid-cols-12">
      <div class="md:col-span-6">
        <label for="cari" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Cari</label>
        <input type="search" id="cari" name="cari" value="<?= e($cari) ?>"
               placeholder="Nama, email, kode, atau asal jemaat…"
               class="w-full rounded-xl border-2 border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-coi-500 focus:ring-2 focus:ring-coi-100">
      </div>
      <div class="md:col-span-4">
        <label for="filter" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Filter Status</label>
        <select id="filter" name="filter"
                class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-coi-500 focus:ring-2 focus:ring-coi-100">
          <?php
          $opsiFilter = [
              ''            => 'Semua Peserta (' . $stat['total'] . ')',
              'pending'     => '— Menunggu Approval (' . $stat['pending'] . ')',
              'diterima'    => '— Diterima (' . $stat['diterima'] . ')',
              'ditolak'     => '— Ditolak (' . $stat['ditolak'] . ')',
              'hadir'       => 'Sudah Hadir (' . $stat['hadir'] . ')',
              'belum_hadir' => 'Belum Hadir (' . $stat['belum_hadir'] . ')',
          ];
          foreach ($opsiFilter as $val => $label): ?>
          <option value="<?= e($val) ?>" <?= $filter === $val ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex items-end gap-2 md:col-span-2">
        <button type="submit"
                class="flex-1 rounded-xl bg-coi-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-coi-700">
          Terapkan
        </button>
        <?php if ($cari !== '' || $filter !== ''): ?>
        <a href="<?= e(BASE_URL) ?>/admin/peserta.php"
           class="rounded-xl border-2 border-slate-200 px-3 py-2.5 text-sm font-bold text-slate-500 transition hover:bg-slate-50">
          Reset
        </a>
        <?php endif; ?>
      </div>
    </div>
  </form>

  <!-- Tabel -->
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
          <tr>
            <th scope="col" class="px-4 py-3 font-bold">No</th>
            <th scope="col" class="px-4 py-3 font-bold">Peserta</th>
            <th scope="col" class="px-4 py-3 font-bold">Asal Jemaat</th>
            <th scope="col" class="px-4 py-3 font-bold">Kontak</th>
            <th scope="col" class="px-4 py-3 font-bold">Tahu Dari</th>
            <th scope="col" class="px-4 py-3 font-bold">Approval</th>
            <th scope="col" class="px-4 py-3 font-bold">Kehadiran</th>
            <th scope="col" class="px-4 py-3 font-bold">Tgl Daftar</th>
            <th scope="col" class="px-4 py-3 text-right font-bold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!$daftar): ?>
          <tr>
            <td colspan="11" class="px-4 py-16 text-center text-sm text-slate-400">
              <?= ($cari !== '' || $filter !== '') ? 'Tidak ada data yang cocok.' : 'Belum ada peserta terdaftar.' ?>
            </td>9
          </tr>
          <?php else: ?>
            <?php foreach ($daftar as $i => $p): ?>
            <tr class="transition hover:bg-slate-50/70">
              <td class="px-4 py-3 text-xs text-slate-400"><?= $offset + $i + 1 ?></td>

              <td class="px-4 py-3">
                <p class="font-semibold text-slate-800"><?= e($p['nama']) ?></p>
                <p class="font-mono text-xs text-slate-400"><?= e($p['kode']) ?></p>
              </td>

              <td class="px-4 py-3 text-slate-600"><?= e($p['gereja']) ?></td>

              <td class="px-4 py-3">
                <p class="flex items-center gap-1.5 text-xs text-slate-600">
                  <span class="truncate max-w-[13rem]"><?= e($p['email']) ?></span>
                  <?php if ($p['email_sent_at']): ?>
                    <span title="Email terkirim <?= e(format_tanggal($p['email_sent_at'])) ?>" class="text-emerald-500">
                      <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                      </svg>
                      <span class="sr-only">Email terkirim</span>
                    </span>
                  <?php endif; ?>
                </p>
                <a href="https://wa.me/<?= e($p['whatsapp']) ?>" target="_blank" rel="noopener"
                   class="text-xs text-coi-600 hover:underline"><?= e($p['whatsapp']) ?></a>
              </td>

              <td class="px-4 py-3 text-xs text-slate-400"><?= $p['info_dari'] !== null && $p['info_dari'] !== '' ? e($p['info_dari']) : '—' ?></td>

              <td class="px-4 py-3">
                <?php
                $accGaya = [
                    'pending'  => ['bg-amber-100 text-amber-700', 'Menunggu'],
                    'diterima' => ['bg-emerald-100 text-emerald-700', 'Diterima'],
                    'ditolak'  => ['bg-slate-200 text-slate-600', 'Ditolak'],
                ];
                [$accCls, $accTxt] = $accGaya[$p['status_acc']] ?? $accGaya['pending'];
                ?>
                <span class="inline-block rounded-full px-2.5 py-1 text-xs font-bold <?= $accCls ?>"><?= e($accTxt) ?></span>
                <?php if ($p['acc_at']): ?>
                  <p class="mt-1 text-[0.68rem] leading-tight text-slate-400"><?= e(format_tanggal($p['acc_at'])) ?></p>
                <?php endif; ?>
              </td>

              <td class="px-4 py-3">
                <?php if ($p['status'] === 'hadir'): ?>
                  <span class="inline-block rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Hadir</span>
                  <p class="mt-1 text-[0.68rem] leading-tight text-slate-400"><?= e(format_tanggal($p['checkin_at'])) ?></p>
                <?php else: ?>
                  <span class="inline-block rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">Belum</span>
                <?php endif; ?>
              </td>

              <td class="px-4 py-3 text-xs text-slate-400"><?= e(format_tanggal($p['created_at'], false)) ?></td>

              <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1.5">
                  <!-- Kirim ulang barcode: hanya untuk peserta yang sudah diterima -->
                  <?php if ($p['status_acc'] === 'diterima'): ?>
                  <form method="post" action="<?= e(BASE_URL) ?>/admin/send-barcode.php"
                        onsubmit="return confirm('Kirim ulang barcode ke <?= e(addslashes($p['email'])) ?>?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="peserta_id" value="<?= (int) $p['id'] ?>">
                    <button type="submit" title="Kirim ulang barcode"
                            class="rounded-lg border-2 border-coi-200 p-2 text-coi-600 transition hover:bg-coi-50">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                      </svg>
                      <span class="sr-only">Kirim ulang barcode</span>
                    </button>
                  </form>
                  <?php else: ?>
                  <a href="<?= e(BASE_URL) ?>/admin/approval.php?status=<?= e($p['status_acc']) ?>"
                     title="Peserta belum disetujui — buka halaman approval"
                     class="rounded-lg border-2 border-amber-200 p-2 text-amber-600 transition hover:bg-amber-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="sr-only">Buka halaman approval</span>
                  </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalHal > 1): ?>
    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
      <span class="text-xs text-slate-400">Halaman <?= $hal ?> dari <?= $totalHal ?></span>
      <nav aria-label="Paginasi">
        <ul class="flex items-center gap-1">
          <li>
            <?php if ($hal <= 1): ?>
              <span class="cursor-not-allowed rounded-lg border border-slate-100 px-3 py-1.5 text-sm text-slate-300">&laquo;</span>
            <?php else: ?>
              <a href="<?= e(url_hal(['hal' => $hal - 1])) ?>" rel="prev" aria-label="Halaman sebelumnya"
                 class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 transition hover:bg-slate-50">&laquo;</a>
            <?php endif; ?>
          </li>
          <?php
          $mulai = max(1, $hal - 2);
          $akhir = min($totalHal, $mulai + 4);
          $mulai = max(1, $akhir - 4);
          for ($i = $mulai; $i <= $akhir; $i++):
          ?>
          <li>
            <?php if ($i === $hal): ?>
              <span aria-current="page" class="rounded-lg bg-coi-600 px-3 py-1.5 text-sm font-bold text-white"><?= $i ?></span>
            <?php else: ?>
              <a href="<?= e(url_hal(['hal' => $i])) ?>"
                 class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 transition hover:bg-slate-50"><?= $i ?></a>
            <?php endif; ?>
          </li>
          <?php endfor; ?>
          <li>
            <?php if ($hal >= $totalHal): ?>
              <span class="cursor-not-allowed rounded-lg border border-slate-100 px-3 py-1.5 text-sm text-slate-300">&raquo;</span>
            <?php else: ?>
              <a href="<?= e(url_hal(['hal' => $hal + 1])) ?>" rel="next" aria-label="Halaman berikutnya"
                 class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 transition hover:bg-slate-50">&raquo;</a>
            <?php endif; ?>
          </li>
        </ul>
      </nav>
    </div>
    <?php endif; ?>
  </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
