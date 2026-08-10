<?php
require_once __DIR__ . '/functions.php';

$judul_halaman = $judul_halaman ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul_halaman) ?> — <?= e(APP_NAME) ?></title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        coi: { 50:'#f0fdfa', 100:'#ccfbf1', 200:'#99f6e4', 300:'#5eead4', 400:'#2dd4bf',
               500:'#14b8a6', 600:'#0d9488', 700:'#0f766e', 800:'#115e59', 900:'#134e4a' },
        cream: '#f7faf9',
      },
      fontFamily: {
        display: ['Cinzel','serif'],
        sans:    ['"Plus Jakarta Sans"','system-ui','sans-serif'],
      },
    },
  },
};
</script>

<style>
  body { background:#f7faf9; }
</style>
</head>
<body class="min-h-full font-sans text-slate-800 antialiased">

<?php if (function_exists('admin_logged_in') && admin_logged_in()):
    $current = basename($_SERVER['SCRIPT_NAME']);

    // [file => [label, path ikon SVG]]
    $nav = [
        'index.php'    => ['Dashboard',     'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        'approval.php' => ['Approval',      'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
        'peserta.php'  => ['Peserta',       'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
        'checkin.php'  => ['Scan Hadir',     'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ];

    // Jumlah pendaftar yang menunggu, untuk lencana pada menu
    $jml_pending = 0;
    try {
        $jml_pending = (int) db()->query("SELECT COUNT(*) FROM peserta WHERE status_acc = 'pending'")->fetchColumn();
    } catch (Throwable $ex) {
        // Abaikan: navigasi tidak boleh gagal hanya karena hitungan lencana
    }
?>
<nav class="sticky top-0 z-40 bg-slate-900 shadow-lg">
  <div class="h-1 w-full bg-gradient-to-r from-coi-700 via-coi-500 to-coi-700"></div>

  <div class="mx-auto max-w-7xl px-4">
    <div class="flex h-16 items-center justify-between">

      <a href="<?= e(BASE_URL) ?>/admin/index.php" class="flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-coi-500 to-coi-600 font-display text-sm font-black text-white">
          C
        </span>
        <span class="font-display text-lg font-bold text-white"><?= e(APP_NAME) ?></span>
      </a>

      <!-- Menu desktop -->
      <ul class="hidden items-center gap-1 lg:flex">
        <?php // Nama variabel diberi prefiks nav* agar tidak menimpa $ikon/$label milik halaman pemanggil
        foreach ($nav as $file => [$navLabel, $navIkon]):
          $aktif = $current === $file; ?>
          <li>
            <a href="<?= e(BASE_URL) ?>/admin/<?= $file ?>"
               class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold transition
                      <?= $aktif ? 'bg-coi-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="<?= e($navIkon) ?>"/>
              </svg>
              <?= e($navLabel) ?>
              <?php if ($file === 'approval.php' && $jml_pending > 0): ?>
                <span class="rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-black text-white"><?= $jml_pending ?></span>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="flex items-center gap-3">
        <span class="hidden text-sm text-slate-400 sm:inline"><?= e(admin_nama()) ?></span>
        <a href="<?= e(BASE_URL) ?>/admin/logout.php"
           class="rounded-lg bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-red-600 hover:text-white">
          Keluar
        </a>
        <button type="button" id="tombol-menu" aria-label="Menu" aria-expanded="false"
                class="rounded-lg p-2 text-slate-300 hover:bg-slate-800 lg:hidden">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Menu mobile -->
  <ul id="menu-mobile" class="hidden border-t border-slate-800 px-4 pb-4 pt-2 lg:hidden">
    <?php foreach ($nav as $file => [$navLabel, $navIkon]):
      $aktif = $current === $file; ?>
      <li>
        <a href="<?= e(BASE_URL) ?>/admin/<?= $file ?>"
           class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold transition
                  <?= $aktif ? 'bg-coi-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?>">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="<?= e($navIkon) ?>"/>
          </svg>
          <?= e($navLabel) ?>
          <?php if ($file === 'approval.php' && $jml_pending > 0): ?>
            <span class="rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-black text-white"><?= $jml_pending ?></span>
          <?php endif; ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>

<script>
(function () {
  var tombol = document.getElementById('tombol-menu');
  var menu   = document.getElementById('menu-mobile');
  if (!tombol || !menu) { return; }

  tombol.addEventListener('click', function () {
    var terbuka = menu.classList.toggle('hidden') === false;
    tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
  });
})();
</script>
<?php endif; ?>
