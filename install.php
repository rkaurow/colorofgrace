<?php
/**
 * Installer — membuat akun admin pertama.
 *
 * Hash password dibuat oleh PHP sendiri sehingga dijamin cocok
 * dengan password_verify() saat login.
 *
 * HAPUS FILE INI setelah instalasi selesai.
 */

require_once __DIR__ . '/includes/functions.php';

$pesan  = '';
$sukses = false;
$fatal  = '';

// Cek tabel sudah ada
try {
    $adaTabel = (bool) db()->query("SHOW TABLES LIKE 'admin'")->fetch();
    if (!$adaTabel) {
        $fatal = 'Tabel belum ada. Impor file <code>database.sql</code> terlebih dahulu melalui phpMyAdmin.';
    } else {
        $jumlahAdmin = (int) db()->query('SELECT COUNT(*) FROM admin')->fetchColumn();
    }
} catch (Throwable $ex) {
    $fatal = 'Tidak dapat terhubung ke database. Periksa <code>config/database.php</code>.<br><small>'
        . e($ex->getMessage()) . '</small>';
}

if (!$fatal && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require();

    $username = post('username');
    $password = $_POST['password'] ?? '';
    $ulangi   = $_POST['password2'] ?? '';
    $nama     = post('nama') ?: 'Administrator';

    if ($username === '' || $password === '') {
        $pesan = 'Username dan password wajib diisi.';
    } elseif (strlen($username) < 4) {
        $pesan = 'Username minimal 4 karakter.';
    } elseif (strlen($password) < 8) {
        $pesan = 'Password minimal 8 karakter.';
    } elseif ($password !== $ulangi) {
        $pesan = 'Konfirmasi password tidak cocok.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            db()->prepare(
                'INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE password = VALUES(password), nama = VALUES(nama)'
            )->execute([$username, $hash, $nama]);

            $sukses = true;
        } catch (Throwable $ex) {
            $pesan = 'Gagal menyimpan admin: ' . $ex->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Instalasi — <?= e(APP_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>body{background:#f4f6f9;} .card{border:none;border-radius:14px;box-shadow:0 4px 20px rgba(0,0,0,.08);}</style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

      <div class="card p-4 p-md-5">
        <div class="text-center mb-4">
          <i class="bi bi-gear-fill text-primary" style="font-size:2.5rem;"></i>
          <h4 class="fw-bold mt-2 mb-1">Instalasi <?= e(APP_NAME) ?></h4>
          <p class="text-muted small mb-0">Buat akun administrator pertama</p>
        </div>

        <?php if ($fatal): ?>
          <div class="alert alert-danger"><?= $fatal ?></div>

        <?php elseif ($sukses): ?>
          <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Instalasi selesai.</strong>
          </div>
          <div class="alert alert-danger small">
            <i class="bi bi-shield-exclamation me-1"></i>
            <strong>Penting:</strong> hapus file <code>install.php</code> sekarang juga
            agar tidak bisa disalahgunakan orang lain.
          </div>
          <a href="<?= e(BASE_URL) ?>/admin/login.php" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Panel Admin
          </a>

        <?php else: ?>

          <?php if (!empty($jumlahAdmin)): ?>
          <div class="alert alert-warning small">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Sudah ada <?= (int) $jumlahAdmin ?> akun admin. Mengisi username yang sama
            akan <strong>menimpa password</strong> akun tersebut.
          </div>
          <?php endif; ?>

          <?php if ($pesan): ?>
          <div class="alert alert-danger small"><?= e($pesan) ?></div>
          <?php endif; ?>

          <form method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label for="nama" class="form-label small fw-semibold">Nama Lengkap</label>
              <input type="text" class="form-control" id="nama" name="nama"
                     value="<?= e(post('nama')) ?>" placeholder="Administrator">
            </div>

            <div class="mb-3">
              <label for="username" class="form-label small fw-semibold">Username</label>
              <input type="text" class="form-control" id="username" name="username"
                     value="<?= e(post('username')) ?>" required minlength="4" autocomplete="username">
            </div>

            <div class="mb-3">
              <label for="password" class="form-label small fw-semibold">Password</label>
              <input type="password" class="form-control" id="password" name="password"
                     required minlength="8" autocomplete="new-password">
              <div class="form-text">Minimal 8 karakter.</div>
            </div>

            <div class="mb-4">
              <label for="password2" class="form-label small fw-semibold">Ulangi Password</label>
              <input type="password" class="form-control" id="password2" name="password2"
                     required minlength="8" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-semibold">
              <i class="bi bi-check-circle me-2"></i>Buat Akun Admin
            </button>
          </form>

        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
</body>
</html>
