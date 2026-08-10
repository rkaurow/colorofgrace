<?php
/**
 * Autentikasi & proteksi halaman admin
 */

require_once __DIR__ . '/functions.php';

/** Apakah admin sudah login. */
function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

/** Nama admin yang sedang login. */
function admin_nama(): string
{
    return $_SESSION['admin_nama'] ?? 'Admin';
}

/**
 * Proteksi halaman admin.
 * Panggil di baris paling atas setiap halaman /admin.
 */
function require_admin(): void
{
    if (!admin_logged_in()) {
        flash_set('error', 'Silakan login terlebih dahulu.');
        redirect(BASE_URL . '/admin/login.php');
    }
}

/**
 * Proteksi endpoint AJAX. Mengembalikan JSON, bukan redirect.
 */
function require_admin_json(): void
{
    if (!admin_logged_in()) {
        json_response([
            'status'  => 'unauthorized',
            'message' => 'Sesi berakhir. Silakan login ulang.',
        ], 401);
    }
}

/**
 * Proses login. Mengembalikan true bila berhasil.
 */
function admin_login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT * FROM admin WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password'])) {
        return false;
    }

    // Cegah session fixation
    session_regenerate_id(true);

    $_SESSION['admin_id']       = (int) $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_nama']     = $admin['nama'];

    db()->prepare('UPDATE admin SET last_login = NOW() WHERE id = ?')
        ->execute([$admin['id']]);

    return true;
}

/** Logout dan hancurkan session. */
function admin_logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }

    session_destroy();
}

// =========================================================
// Pembatasan percobaan login
// =========================================================

/** Apakah login sedang dikunci karena terlalu banyak percobaan gagal. */
function login_terkunci(): bool
{
    $percobaan = $_SESSION['login_gagal'] ?? 0;
    $terakhir  = $_SESSION['login_gagal_at'] ?? 0;

    if ($percobaan >= 5 && (time() - $terakhir) < 300) {
        return true;
    }

    // Reset otomatis setelah 5 menit
    if ($percobaan >= 5) {
        unset($_SESSION['login_gagal'], $_SESSION['login_gagal_at']);
    }

    return false;
}

/** Sisa waktu kunci dalam detik. */
function login_sisa_kunci(): int
{
    $terakhir = $_SESSION['login_gagal_at'] ?? 0;
    return max(0, 300 - (time() - $terakhir));
}

function login_catat_gagal(): void
{
    $_SESSION['login_gagal']    = ($_SESSION['login_gagal'] ?? 0) + 1;
    $_SESSION['login_gagal_at'] = time();
}

function login_reset_gagal(): void
{
    unset($_SESSION['login_gagal'], $_SESSION['login_gagal_at']);
}
