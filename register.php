<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/mailer.php';

$errors = [];
$old    = ['nama' => '', 'gereja' => '', 'info_dari' => '', 'info_lainnya' => '', 'email' => '', 'whatsapp' => ''];

const OPSI_INFO_DARI = ['Instagram', 'TikTok', 'Teman', 'Jemaat', 'Poster / Flyer', 'Lainnya'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require();

    $nama         = post('nama');
    $gereja       = post('gereja');
    $info_dari    = post('info_dari');
    $info_lainnya = post('info_lainnya');
    $email        = strtolower(post('email'));
    $whatsapp     = post('whatsapp');

    $old = compact('nama', 'gereja', 'info_dari', 'info_lainnya', 'email', 'whatsapp');

    // --- Validasi nama ---
    $panjangNama = mb_strlen($nama);
    if ($nama === '') {
        $errors['nama'] = 'Nama lengkap wajib diisi.';
    } elseif ($panjangNama < 3 || $panjangNama > 100) {
        $errors['nama'] = 'Nama lengkap harus 3 sampai 100 karakter.';
    }

    // --- Validasi asal jemaat ---
    $panjangGereja = mb_strlen($gereja);
    if ($gereja === '') {
        $errors['gereja'] = 'Asal jemaat wajib diisi.';
    } elseif ($panjangGereja < 3 || $panjangGereja > 150) {
        $errors['gereja'] = 'Asal jemaat harus 3 sampai 150 karakter.';
    }

    // --- Validasi sumber informasi (OPSIONAL) ---
    if ($info_dari !== '') {
        if (!in_array($info_dari, OPSI_INFO_DARI, true)) {
            $errors['info_dari'] = 'Pilihan tidak valid.';
        } elseif ($info_dari === 'Lainnya' && mb_strlen($info_lainnya) > 100) {
            $errors['info_lainnya'] = 'Maksimal 100 karakter.';
        }
    }

    // --- Validasi email ---
    if ($email === '') {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    } elseif (mb_strlen($email) > 150) {
        $errors['email'] = 'Email terlalu panjang.';
    } else {
        $cek = db()->prepare('SELECT id FROM peserta WHERE email = ? LIMIT 1');
        $cek->execute([$email]);
        if ($cek->fetch()) {
            $errors['email'] = 'Email ini sudah terdaftar. Gunakan email lain.';
        }
    }

    // --- Validasi WhatsApp ---
    $waBersih = preg_replace('/\D+/', '', $whatsapp) ?? '';
    if ($whatsapp === '') {
        $errors['whatsapp'] = 'Nomor WhatsApp wajib diisi.';
    } elseif (strlen($waBersih) < 10 || strlen($waBersih) > 15) {
        $errors['whatsapp'] = 'Nomor WhatsApp harus 10 sampai 15 digit.';
    }

    // --- Simpan ---
    if (!$errors) {
        // Sumber informasi opsional: kosong disimpan sebagai NULL
        $sumber = $info_dari === ''         ? null
                : ($info_dari === 'Lainnya' ? ($info_lainnya !== '' ? $info_lainnya : 'Lainnya')
                : $info_dari);

        $waFinal = normalisasi_whatsapp($whatsapp);

        try {
            $pdo = db();
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO peserta (kode, nama, gereja, info_dari, email, whatsapp, status_acc)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            // Kode sementara unik, diperbarui setelah ID diketahui
            $stmt->execute(['TMP-' . bin2hex(random_bytes(6)), $nama, $gereja, $sumber, $email, $waFinal, 'pending']);

            $id   = (int) $pdo->lastInsertId();
            $kode = buat_kode_peserta($id);

            $pdo->prepare('UPDATE peserta SET kode = ? WHERE id = ?')->execute([$kode, $id]);
            $pdo->commit();

            // Barcode BELUM dibuat/dikirim — menunggu persetujuan panitia.
            // Peserta hanya menerima email konfirmasi bahwa data sudah masuk.
            $peserta = cari_peserta_by_id($id);
            if ($peserta) {
                kirim_email_pending($peserta);
            }

            $_SESSION['registrasi_sukses'] = ['kode' => $kode, 'nama' => $nama, 'email' => $email];
            redirect(BASE_URL . '/success.php');

        } catch (PDOException $ex) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Registrasi gagal: ' . $ex->getMessage());

            // 23000 = pelanggaran constraint unik (email balapan antar request)
            $errors['umum'] = $ex->getCode() === '23000'
                ? 'Email ini sudah terdaftar. Gunakan email lain.'
                : 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        }
    }
}

/** Kelas input, berubah bila field bermasalah. */
function kls_input(array $errors, string $field): string
{
    $dasar = 'field-input w-full rounded-xl border-2 bg-slate-50 px-4 py-3.5 text-slate-800 placeholder-slate-400 '
           . 'transition outline-none';
    return isset($errors[$field])
        ? $dasar . ' border-red-400 focus:border-red-500'
        : $dasar . ' border-slate-200';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pendaftaran — <?= e(EVENT_NAME) ?></title>

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
        cream:'#FFF4D6', 'cream-light':'#FFF9ED', gold:'#D9A441', navy:'#07152D',
      },
      fontFamily: {
        display: ['Cinzel','serif'],
        sans: ['"Plus Jakarta Sans"','system-ui','sans-serif'],
      },
    },
  },
};
</script>

<style>
  body { background:#FFF9ED; }
  .hero-bg { background: radial-gradient(ellipse at 80% 20%,rgba(249,115,22,.28),transparent 45%), linear-gradient(145deg,#07152D,#1a0a0a); }
  .field-input:focus { border-color:#F97316; box-shadow:0 0 0 3px rgba(249,115,22,.18); outline:none; }
  .radio-option { min-height:3.25rem; display:flex; align-items:center; justify-content:center; }
  @media (max-width: 640px) {
    .hero-title { font-size:2.25rem; line-height:1.05; }
    .form-card { margin-left:-.25rem; margin-right:-.25rem; }
  }
</style>
</head>

<body class="overflow-x-hidden font-sans text-slate-800 antialiased">

<!-- Header -->
<header class="hero-bg relative overflow-hidden pb-20 pt-6 sm:pb-24 sm:pt-10">
  <div class="relative mx-auto max-w-2xl px-4 text-center sm:px-5">
    <a href="<?= e(BASE_URL) ?>/index.php"
       class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-2 text-xs font-bold text-white/80 transition hover:border-white/30 hover:text-white sm:mb-6 sm:text-sm">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
      </svg>
      Kembali
    </a>

    <img src="<?= e(BASE_URL) ?>/assets/img/logo-otw.png" alt="One Truth Way" class="mx-auto mb-4 h-10 w-auto object-contain sm:h-12">
    <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-white/70 sm:text-xs sm:tracking-[0.35em]">Festival of Unity &amp; Faith</p>
    <h1 class="hero-title mt-2 font-display text-4xl font-black text-white drop-shadow sm:text-5xl">COLOR OF GRACE</h1>
    <p class="mx-auto mt-3 max-w-xs text-xs leading-relaxed text-white/80 sm:max-w-none sm:text-sm"><?= e(EVENT_DATE_TEXT) ?> &middot; <?= e(EVENT_LOCATION) ?></p>
  </div>
</header>

<main class="mx-auto max-w-2xl px-4 pb-14 pt-6 sm:px-5 sm:pb-20 sm:pt-8">
  <div class="form-card overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200" style="box-shadow:0 20px 60px rgba(0,0,0,.12)">
    <div class="p-5 sm:p-10">

      <div class="mb-6 flex items-start justify-between gap-4">
        <div>
          <p class="mb-1 text-[10px] font-bold uppercase tracking-[0.22em] text-orange-primary sm:text-xs">Formulir Peserta</p>
          <h2 class="font-display text-2xl font-black text-slate-900 sm:text-3xl">Daftar Sekarang</h2>
        </div>
        <div class="hidden h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl sm:flex" style="background:rgba(249,115,22,.12)">
          <svg class="h-5 w-5" fill="none" stroke="#F97316" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18zM9 12l2 2 4-4"/></svg>
        </div>
      </div>
      <p class="-mt-3 text-sm leading-relaxed text-slate-500">
        Isi data diri kamu di bawah ini. Kolom bertanda <span class="font-bold text-red-500">*</span> wajib diisi.
      </p>

      <?php if (isset($errors['umum'])): ?>
        <div class="mt-6 rounded-xl border-2 border-red-200 bg-red-50 p-4">
          <p class="text-sm font-semibold text-red-800"><?= e($errors['umum']) ?></p>
        </div>
      <?php endif; ?>

      <div class="mt-6 flex gap-3 rounded-2xl border p-4" style="border-color:rgba(217,164,65,.35);background:rgba(217,164,65,.08)">
        <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="#D9A441" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"/></svg>
        <p class="text-sm leading-relaxed text-slate-700">
          Pendaftaran akan <strong>ditinjau panitia</strong> terlebih dahulu. Barcode dikirim ke email setelah disetujui.
        </p>
      </div>

      <form method="post" action="" novalidate class="mt-7 space-y-5 sm:mt-8 sm:space-y-6" id="form-daftar">
        <?= csrf_field() ?>

        <!-- Nama -->
        <div>
          <label for="nama" class="mb-2 block text-sm font-bold text-slate-700">
            Nama Lengkap <span class="text-red-500">*</span>
          </label>
          <input type="text" id="nama" name="nama" required maxlength="100"
                 value="<?= e($old['nama']) ?>" placeholder="Masukkan nama lengkap"
                 class="<?= kls_input($errors, 'nama') ?>">
          <?php if (isset($errors['nama'])): ?>
            <p class="mt-2 text-sm font-medium text-red-600"><?= e($errors['nama']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="mb-2 block text-sm font-bold text-slate-700">
            Email <span class="text-red-500">*</span>
          </label>
          <input type="email" id="email" name="email" required maxlength="150"
                 value="<?= e($old['email']) ?>" placeholder="Masukkan alamat email"
                 class="<?= kls_input($errors, 'email') ?>">
          <p class="mt-2 text-xs text-slate-400">Konfirmasi dan barcode tiket dikirim ke alamat ini setelah pendaftaranmu disetujui panitia.</p>
          <?php if (isset($errors['email'])): ?>
            <p class="mt-2 text-sm font-medium text-red-600"><?= e($errors['email']) ?></p>
          <?php endif; ?>
        </div>

        <!-- WhatsApp -->
        <div>
          <label for="whatsapp" class="mb-2 block text-sm font-bold text-slate-700">
            Nomor WhatsApp <span class="text-red-500">*</span>
          </label>
          <input type="tel" id="whatsapp" name="whatsapp" required inputmode="numeric" maxlength="20"
                 value="<?= e($old['whatsapp']) ?>" placeholder="08xxxxxxxxxx"
                 class="<?= kls_input($errors, 'whatsapp') ?>">
          <?php if (isset($errors['whatsapp'])): ?>
            <p class="mt-2 text-sm font-medium text-red-600"><?= e($errors['whatsapp']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Asal jemaat -->
        <div>
          <label for="gereja" class="mb-2 block text-sm font-bold text-slate-700">
            Asal Jemaat <span class="text-red-500">*</span>
          </label>
          <input type="text" id="gereja" name="gereja" required maxlength="150"
                 value="<?= e($old['gereja']) ?>" placeholder="Masukkan asal jemaat"
                 class="<?= kls_input($errors, 'gereja') ?>">
          <?php if (isset($errors['gereja'])): ?>
            <p class="mt-2 text-sm font-medium text-red-600"><?= e($errors['gereja']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Sumber informasi (opsional) -->
        <div>
          <label class="mb-2 block text-sm font-bold text-slate-700">
            Mengetahui acara ini dari mana?
            <span class="ml-1 rounded-md bg-orange-50 px-2 py-0.5 text-xs font-semibold" style="color:#F97316">opsional</span>
          </label>

          <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
            <?php foreach (OPSI_INFO_DARI as $opsi):
              $aktif = $old['info_dari'] === $opsi; ?>
              <label class="cursor-pointer">
                <input type="radio" name="info_dari" value="<?= e($opsi) ?>" class="peer sr-only opsi-info"
                       <?= $aktif ? 'checked' : '' ?>>
                <span class="radio-option block rounded-xl border-2 border-orange-100 px-2 py-2 text-center text-xs font-semibold leading-tight text-slate-500 transition sm:px-3 sm:py-3 sm:text-sm
                             peer-checked:border-orange-primary peer-checked:bg-orange-50 peer-checked:text-orange-dark
                             hover:border-orange-300">
                  <?= e($opsi) ?>
                </span>
              </label>
            <?php endforeach; ?>
          </div>

          <?php if (isset($errors['info_dari'])): ?>
            <p class="mt-2 text-sm font-medium text-red-600"><?= e($errors['info_dari']) ?></p>
          <?php endif; ?>

          <div id="wrap-lainnya" class="mt-3 <?= $old['info_dari'] === 'Lainnya' ? '' : 'hidden' ?>">
            <input type="text" id="info_lainnya" name="info_lainnya" maxlength="100"
                   value="<?= e($old['info_lainnya']) ?>" placeholder="Sebutkan sumbernya"
                   class="<?= kls_input($errors, 'info_lainnya') ?>">
            <?php if (isset($errors['info_lainnya'])): ?>
              <p class="mt-2 text-sm font-medium text-red-600"><?= e($errors['info_lainnya']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Kirim -->
        <button type="submit" id="tombol-kirim"
                class="w-full rounded-2xl py-3.5 text-base font-extrabold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 sm:py-4 sm:text-lg disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0"
                style="background:linear-gradient(135deg,#F97316,#E84716);box-shadow:0 8px 32px rgba(249,115,22,.35)">
          DAFTAR SEKARANG →
        </button>

        <p class="text-center text-xs leading-relaxed text-slate-400">
          Dengan mendaftar, kamu setuju datanya digunakan panitia untuk keperluan acara ini.
        </p>
      </form>

    </div>
  </div>
</main>

<script>
(function () {
  // Tampilkan kolom "Lainnya" hanya bila opsi tersebut dipilih
  var wrap = document.getElementById('wrap-lainnya');
  var isian = document.getElementById('info_lainnya');

  document.querySelectorAll('.opsi-info').forEach(function (radio) {
    radio.addEventListener('change', function () {
      var perlu = radio.value === 'Lainnya' && radio.checked;
      wrap.classList.toggle('hidden', !perlu);
      if (perlu) { isian.focus(); }
    });
  });

  // Cegah pengiriman ganda
  var form = document.getElementById('form-daftar');
  var tombol = document.getElementById('tombol-kirim');
  var sudahKirim = false;

  form.addEventListener('submit', function (ev) {
    if (sudahKirim) { ev.preventDefault(); return; }
    sudahKirim = true;
    tombol.disabled = true;
    tombol.textContent = 'Mengirim…';
  });
})();
</script>

</body>
</html>
