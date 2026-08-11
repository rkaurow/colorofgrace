<?php
require_once __DIR__ . '/includes/functions.php';

// Halaman ini hanya bisa diakses tepat setelah pendaftaran berhasil
if (empty($_SESSION['registrasi_sukses'])) {
    redirect(BASE_URL . '/index.php');
}

$data = $_SESSION['registrasi_sukses'];
unset($_SESSION['registrasi_sukses']);

$nama  = (string) ($data['nama'] ?? '');
$email = (string) ($data['email'] ?? '');
$kode  = (string) ($data['kode'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pendaftaran Terkirim — <?= e(EVENT_NAME) ?></title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        orange: { primary:'#F97316', light:'#F59E0B', dark:'#D94B12' },
        red: { primary:'#C91F25', dark:'#8E151B' },
        cream: '#FFF4D6',
        'cream-light': '#FFF9ED',
        gold: '#D9A441',
        navy: '#07152D',
      },
      fontFamily: {
        display: ['Cinzel','serif'],
        sans:    ['"Plus Jakarta Sans"','system-ui','sans-serif'],
      },
      animation: { 'pop': 'pop .5s cubic-bezier(.34,1.56,.64,1) both' },
      keyframes: { pop: { '0%':{opacity:0,transform:'scale(.7)'}, '100%':{opacity:1,transform:'scale(1)'} } },
    },
  },
};
</script>

<style>
  body { background:#FFF9ED; }
  .success-bg { background:radial-gradient(ellipse at 70% 20%,rgba(249,115,22,.28),transparent 45%),linear-gradient(145deg,#07152D,#1a0a0a); }
  @media (prefers-reduced-motion: reduce) { * { animation:none !important; } }
</style>
</head>

<body class="font-sans text-slate-800 antialiased">

<main class="success-bg mx-auto flex min-h-screen max-w-2xl items-center px-5 py-12">
  <div class="w-full overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-slate-200">

    <div class="p-8 text-center sm:p-12">

      <!-- Ikon -->
      <div class="animate-pop mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full shadow-lg" style="background:linear-gradient(135deg,#F97316,#C91F25)">
        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
      </div>

      <h1 class="font-display text-3xl font-black text-slate-900">Registrasi Berhasil!</h1>
      <p class="mt-3 leading-relaxed text-slate-500">
        Terima kasih<?= $nama !== '' ? ', <strong class="text-slate-700">' . e($nama) . '</strong>' : '' ?>.
        Data kamu sudah kami terima.
      </p>

      <!-- Status menunggu -->
      <div class="mt-8 rounded-2xl border-2 border-amber-200 bg-amber-50 p-6 text-left">
        <div class="flex gap-4">
          <svg class="mt-0.5 h-6 w-6 shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <div>
            <p class="font-bold text-amber-900">Menunggu Konfirmasi Panitia</p>
            <p class="mt-2 text-sm leading-relaxed text-amber-800">
              Panitia akan meninjau setiap pendaftaran terlebih dahulu.
              Setelah disetujui, QR Code tiket akan dikirim ke emailmu.
            </p>
          </div>
        </div>
      </div>

      <!-- Nomor registrasi -->
      <?php if ($kode !== ''): ?>
        <div class="mt-6 rounded-2xl bg-slate-50 p-6">
          <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Registrasi</p>
          <p class="mt-2 font-display text-2xl font-black tracking-[0.15em]" style="color:#F97316"><?= e($kode) ?></p>
          <p class="mt-2 text-xs text-slate-400">Simpan nomor ini untuk keperluan konfirmasi.</p>
        </div>
      <?php endif; ?>

      <!-- Langkah berikutnya -->
      <div class="mt-8 text-left">
        <p class="mb-4 text-sm font-bold text-slate-700">Apa yang terjadi selanjutnya?</p>
        <ol class="space-y-4">
          <?php
          $langkah = [
            ['Panitia meninjau pendaftaran', 'Panitia memeriksa data dan memberikan persetujuan.'],
            ['Kamu menerima email hasilnya',
              $email !== ''
                ? 'Pemberitahuan dikirim ke <strong>' . e($email) . '</strong>.'
                : 'Pemberitahuan dikirim ke email yang kamu daftarkan.'],
            ['Bila disetujui, QR Code dikirim', 'Simpan QR Code di ponsel untuk ditunjukkan saat datang.'],
          ];
          foreach ($langkah as $i => [$judul, $isi]): ?>
            <li class="flex gap-4">
              <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-black text-white" style="background:linear-gradient(135deg,#F97316,#D94B12)">
                <?= $i + 1 ?>
              </span>
              <div>
                <p class="text-sm font-semibold text-slate-800"><?= e($judul) ?></p>
                <p class="mt-0.5 text-sm text-slate-500"><?= $isi ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>

      <!-- Catatan email -->
      <div class="mt-8 rounded-xl bg-slate-50 p-4">
        <p class="text-xs leading-relaxed text-slate-500">
          Tidak menerima email? Periksa folder <strong>Spam</strong> atau <strong>Promosi</strong>.
        </p>
      </div>

      <a href="<?= e(BASE_URL) ?>/index.php"
         class="mt-8 inline-block rounded-xl px-8 py-3.5 font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl"
         style="background:linear-gradient(135deg,#F97316,#E84716);box-shadow:0 8px 32px rgba(249,115,22,.35)">
        Kembali ke Beranda
      </a>

    </div>
  </div>
</main>

</body>
</html>
