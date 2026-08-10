<?php
require_once __DIR__ . '/../includes/auth.php';

// Sudah login? langsung ke dashboard
if (admin_logged_in()) {
    redirect(BASE_URL . '/admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require();

    if (login_terkunci()) {
        $menit = (int) ceil(login_sisa_kunci() / 60);
        $error = "Terlalu banyak percobaan gagal. Coba lagi dalam {$menit} menit.";
    } else {
        $username = post('username');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Username dan password wajib diisi.';
        } elseif (admin_login($username, $password)) {
            login_reset_gagal();
            redirect(BASE_URL . '/admin/index.php');
        } else {
            login_catat_gagal();
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin — <?= e(APP_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
        display: ['Cinzel', 'serif'],
      },
      colors: {
        coi: {
          50:'#f0fdfa',100:'#ccfbf1',200:'#99f6e4',300:'#5eead4',400:'#2dd4bf',
          500:'#14b8a6',600:'#0d9488',700:'#0f766e',800:'#115e59',900:'#134e4a',
        },
        cream: '#f7faf9',
      },
    },
  },
};
</script>
</head>
<body class="min-h-screen bg-slate-900 font-sans antialiased">

<div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 px-4 py-12">
  <div class="w-full max-w-md">

    <div class="rounded-3xl border border-white/10 bg-white p-8 shadow-2xl sm:p-10">

      <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-coi-500 to-coi-700 text-white shadow-lg">
          <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
          </svg>
        </div>
        <h1 class="font-display text-2xl font-black text-slate-900">Panel Admin</h1>
        <p class="mt-1 text-sm text-slate-500"><?= e(EVENT_NAME) ?> — <?= e(APP_NAME) ?></p>
      </div>

      <?= flash_render() ?>

      <?php if ($error): ?>
      <div role="alert" class="mb-6 flex items-start gap-3 rounded-xl border-2 border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>
        <span class="font-medium"><?= e($error) ?></span>
      </div>
      <?php endif; ?>

      <form method="post" action="" class="space-y-5">
        <?= csrf_field() ?>

        <div>
          <label for="username" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Username</label>
          <div class="relative">
            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
              </svg>
            </span>
            <input type="text" id="username" name="username" value="<?= e(post('username')) ?>"
                   required autofocus autocomplete="username"
                   class="w-full rounded-xl border-2 border-slate-200 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-coi-500 focus:ring-2 focus:ring-coi-100">
          </div>
        </div>

        <div>
          <label for="password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Password</label>
          <div class="relative">
            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
              </svg>
            </span>
            <input type="password" id="password" name="password"
                   required autocomplete="current-password"
                   class="w-full rounded-xl border-2 border-slate-200 py-3 pl-12 pr-12 text-sm outline-none transition focus:border-coi-500 focus:ring-2 focus:ring-coi-100">
            <button type="button" id="togglePass" tabindex="-1" aria-label="Tampilkan password" aria-pressed="false"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
              <svg id="ikonMata" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>
            </button>
          </div>
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-gradient-to-r from-coi-600 to-coi-700 py-3.5 text-sm font-bold text-white shadow-lg shadow-coi-600/25 transition hover:from-coi-700 hover:to-coi-800 focus:outline-none focus:ring-2 focus:ring-coi-400 focus:ring-offset-2">
          Masuk
        </button>
      </form>

      <div class="mt-8 border-t border-slate-100 pt-6 text-center">
        <a href="<?= e(BASE_URL) ?>/index.php"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-400 transition hover:text-coi-600">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
          </svg>
          Kembali ke situs
        </a>
      </div>

    </div>

    <p class="mt-6 text-center text-xs text-white/40">
      Halaman ini khusus panitia. Akses tanpa izin dicatat.
    </p>

  </div>
</div>

<script>
(() => {
  const tombol = document.getElementById('togglePass');
  const input  = document.getElementById('password');
  const ikon   = document.getElementById('ikonMata');

  const MATA = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
  const MATA_SILANG = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243" />';

  tombol.addEventListener('click', () => {
    const tampil = input.type === 'password';
    input.type = tampil ? 'text' : 'password';
    ikon.innerHTML = tampil ? MATA_SILANG : MATA;
    tombol.setAttribute('aria-pressed', tampil ? 'true' : 'false');
    tombol.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
  });
})();
</script>
</body>
</html>
