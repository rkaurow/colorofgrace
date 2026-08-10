<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$stat = statistik_peserta();

// Peserta terbaru
$terbaru = db()->query(
    'SELECT kode, nama, gereja, status_acc, status, created_at
     FROM peserta ORDER BY id DESC LIMIT 8'
)->fetchAll();

// Hitung mundur hari acara
$selisihHari = (int) floor((strtotime(EVENT_DATE) - strtotime(date('Y-m-d'))) / 86400);

// Progres kehadiran diukur terhadap peserta yang diterima, bukan total pendaftar,
// karena hanya peserta diterima yang punya barcode dan bisa hadir.
$basis  = (int) $stat['diterima'];
$persen = static fn(int $bagian): float =>
    $basis > 0 ? round($bagian / $basis * 100, 1) : 0.0;

$angka = static fn(int $n): string => number_format($n, 0, ',', '.');

$judul_halaman = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

  <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="font-display text-3xl font-black text-slate-900">Dashboard</h1>
      <p class="mt-1 text-sm text-slate-500">
        <?= e(EVENT_NAME) ?> &middot; <?= e(EVENT_DATE_TEXT) ?>
      </p>
    </div>
    <?php if ($selisihHari > 1): ?>
      <span class="rounded-full bg-coi-600 px-4 py-2 text-sm font-bold text-white shadow-sm">
        <?= $selisihHari ?> hari lagi
      </span>
    <?php elseif ($selisihHari === 1): ?>
      <span class="rounded-full bg-coi-600 px-4 py-2 text-sm font-bold text-white shadow-sm">Besok!</span>
    <?php elseif ($selisihHari === 0): ?>
      <span class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm">Hari ini!</span>
    <?php else: ?>
      <span class="rounded-full bg-slate-400 px-4 py-2 text-sm font-bold text-white">Acara telah berlalu</span>
    <?php endif; ?>
  </div>

  <?= flash_render() ?>

  <!-- Status approval -->
  <section class="mb-8 grid gap-4 lg:grid-cols-3">

    <div class="rounded-2xl bg-gradient-to-br from-coi-600 to-coi-700 p-6 text-white shadow-lg lg:col-span-1">
      <p class="text-xs font-bold uppercase tracking-widest text-coi-100">Peserta Diterima</p>
      <p class="mt-2 font-display text-4xl font-black"><?= $angka((int) $stat['diterima']) ?></p>
      <p class="mt-3 text-sm text-coi-100">Persetujuan panitia tanpa batas kuota</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3 lg:col-span-2">
      <?php
      $kartuAcc = [
          ['Menunggu', (int) $stat['pending'],  'amber',   'approval.php?status=pending'],
          ['Diterima', (int) $stat['diterima'], 'emerald', 'approval.php?status=diterima'],
          ['Ditolak',  (int) $stat['ditolak'],  'slate',   'approval.php?status=ditolak'],
      ];
      $gaya = [
          'amber'   => ['border-amber-300', 'text-amber-600', 'bg-amber-50'],
          'emerald' => ['border-emerald-300', 'text-emerald-600', 'bg-emerald-50'],
          'slate'   => ['border-slate-300', 'text-slate-500', 'bg-slate-50'],
      ];
      foreach ($kartuAcc as [$label, $nilai, $warna, $tautan]):
          [$border, $teks, $bg] = $gaya[$warna];
      ?>
      <a href="<?= e(BASE_URL) ?>/admin/<?= e($tautan) ?>"
         class="rounded-2xl border-2 <?= $border ?> <?= $bg ?> p-5 transition hover:-translate-y-0.5 hover:shadow-md">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500"><?= e($label) ?></p>
        <p class="mt-2 font-display text-4xl font-black <?= $teks ?>"><?= $angka($nilai) ?></p>
        <?php if ($label === 'Menunggu'): ?>
          <p class="mt-1 text-xs <?= $nilai > 0 ? 'font-semibold text-amber-700' : 'text-slate-400' ?>">
            <?= $nilai > 0 ? 'Perlu ditinjau &rarr;' : 'Tidak ada antrean' ?>
          </p>
        <?php elseif ($label === 'Diterima'): ?>
          <p class="mt-1 text-xs text-slate-400">Sudah dapat barcode</p>
        <?php else: ?>
          <p class="mt-1 text-xs text-slate-400">Tidak dapat barcode</p>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Statistik kehadiran -->
  <section class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Sudah Hadir</p>
      <p class="mt-2 font-display text-4xl font-black text-emerald-600"><?= $angka((int) $stat['hadir']) ?></p>
      <p class="mt-1 text-xs text-slate-400"><?= $persen((int) $stat['hadir']) ?>% dari peserta diterima</p>
    </div>
  </section>

  <div class="grid gap-6 lg:grid-cols-12">

    <!-- Progres + aksi cepat -->
    <section class="lg:col-span-5">
      <div class="h-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="font-display text-lg font-bold text-slate-900">Progres Kehadiran</h2>
        <p class="mt-1 text-xs text-slate-400">
          Dihitung dari <?= $angka($basis) ?> peserta yang diterima.
        </p>

        <div class="mt-5 space-y-4">
          <?php
          $progres = [
              ['Kehadiran', (int) $stat['hadir'], 'bg-emerald-500'],
          ];
          foreach ($progres as [$label, $nilai, $bar]):
              $p = $persen($nilai);
          ?>
          <div>
            <div class="mb-1.5 flex items-center justify-between text-sm">
              <span class="font-semibold text-slate-700"><?= e($label) ?></span>
              <span class="text-slate-400"><?= $angka($nilai) ?> / <?= $angka($basis) ?></span>
            </div>
            <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
              <div class="h-full rounded-full <?= $bar ?> transition-all" style="width:<?= $p ?>%"
                   role="progressbar" aria-valuenow="<?= $p ?>" aria-valuemin="0" aria-valuemax="100"
                   aria-label="<?= e($label) ?>"></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <hr class="my-6 border-slate-200">

        <h2 class="font-display text-lg font-bold text-slate-900">Aksi Cepat</h2>
        <div class="mt-4 grid gap-2.5">
          <?php if ((int) $stat['pending'] > 0): ?>
          <a href="<?= e(BASE_URL) ?>/admin/approval.php"
             class="flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-amber-600">
            Tinjau <?= $angka((int) $stat['pending']) ?> Pendaftaran
          </a>
          <?php endif; ?>
          <a href="<?= e(BASE_URL) ?>/admin/checkin.php"
             class="flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700">
            Scan Kehadiran
          </a>
          <a href="<?= e(BASE_URL) ?>/admin/peserta.php"
             class="flex items-center justify-center gap-2 rounded-xl border-2 border-slate-200 px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
            Kelola Peserta
          </a>
        </div>
      </div>
    </section>

    <!-- Pendaftar terbaru -->
    <section class="lg:col-span-7">
      <div class="h-full rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
          <h2 class="font-display text-lg font-bold text-slate-900">Pendaftar Terbaru</h2>
          <a href="<?= e(BASE_URL) ?>/admin/peserta.php"
             class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
            Lihat Semua
          </a>
        </div>

        <?php if (!$terbaru): ?>
          <div class="px-6 py-16 text-center">
            <p class="text-sm text-slate-400">Belum ada pendaftar.</p>
          </div>
        <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
              <tr>
                <th scope="col" class="px-6 py-3 font-bold">Peserta</th>
                <th scope="col" class="px-4 py-3 font-bold">Asal Jemaat</th>
                <th scope="col" class="px-4 py-3 font-bold">Approval</th>
                <th scope="col" class="px-4 py-3 font-bold">Kehadiran</th>
                <th scope="col" class="px-6 py-3 font-bold">Daftar</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($terbaru as $p): ?>
              <tr class="transition hover:bg-slate-50/70">
                <td class="px-6 py-3">
                  <p class="font-semibold text-slate-800"><?= e($p['nama']) ?></p>
                  <p class="font-mono text-xs text-slate-400"><?= e($p['kode']) ?></p>
                </td>
                <td class="px-4 py-3 text-slate-500"><?= e($p['gereja']) ?></td>
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
                </td>
                <td class="px-4 py-3">
                  <?php if ($p['status'] === 'hadir'): ?>
                    <span class="inline-block rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Hadir</span>
                  <?php else: ?>
                    <span class="inline-block rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">Belum Hadir</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-3 text-xs text-slate-400"><?= e(format_tanggal($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </section>

  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
