<?php
/**
 * Helper umum
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================
// Output & Input
// =========================================================

/** Escape output untuk mencegah XSS. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Ambil nilai POST yang sudah di-trim. */
function post(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

/** Ambil nilai GET yang sudah di-trim. */
function get(string $key, string $default = ''): string
{
    return trim((string) ($_GET[$key] ?? $default));
}

// =========================================================
// CSRF
// =========================================================

/** Buat atau ambil token CSRF milik session ini. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Render input hidden berisi token CSRF. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Verifikasi token CSRF dari request. */
function csrf_verify(?string $token = null): bool
{
    $token = $token ?? ($_POST['csrf_token'] ?? '');
    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

/** Hentikan eksekusi bila CSRF tidak valid. */
function csrf_require(): void
{
    if (!csrf_verify()) {
        http_response_code(419);
        exit('Sesi tidak valid atau kedaluwarsa. Silakan muat ulang halaman.');
    }
}

// =========================================================
// Navigasi
// =========================================================

/** Redirect lalu hentikan eksekusi. */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

// =========================================================
// Flash message
// =========================================================

/** Simpan flash message untuk request berikutnya. */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Ambil dan hapus flash message. */
function flash_get(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/** Render flash message sebagai alert Tailwind. */
function flash_render(): string
{
    $flash = flash_get();
    if (!$flash) {
        return '';
    }

    // [kelas kotak, kelas ikon, path SVG]
    $map = [
        'success' => ['border-emerald-200 bg-emerald-50 text-emerald-800', 'text-emerald-500', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        'error'   => ['border-red-200 bg-red-50 text-red-800',             'text-red-500',     'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'warning' => ['border-amber-200 bg-amber-50 text-amber-800',       'text-amber-500',   'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        'info'    => ['border-sky-200 bg-sky-50 text-sky-800',             'text-sky-500',     'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    [$kotak, $warnaIkon, $path] = $map[$flash['type']] ?? $map['info'];

    return '<div role="alert" class="mb-6 flex items-start gap-3 rounded-xl border-2 p-4 ' . $kotak . '">'
        . '<svg class="mt-0.5 h-5 w-5 shrink-0 ' . $warnaIkon . '" fill="none" stroke="currentColor" '
        . 'stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">'
        . '<path stroke-linecap="round" stroke-linejoin="round" d="' . $path . '"/></svg>'
        . '<p class="text-sm font-medium">' . e($flash['message']) . '</p>'
        . '</div>';
}

// =========================================================
// Peserta
// =========================================================

/**
 * Buat kode peserta dari ID.
 * Contoh: 1 -> COI-000001
 */
function buat_kode_peserta(int $id): string
{
    return KODE_PREFIX . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
}

/**
 * Normalisasi kode hasil scan.
 * Membuang spasi/enter bawaan scanner dan menyeragamkan huruf besar.
 */
function normalisasi_kode(string $kode): string
{
    return strtoupper(trim($kode));
}

/**
 * Normalisasi nomor WhatsApp ke format 62xxx.
 */
function normalisasi_whatsapp(string $nomor): string
{
    $nomor = preg_replace('/\D+/', '', $nomor) ?? '';

    if ($nomor === '') {
        return '';
    }
    if (str_starts_with($nomor, '0')) {
        return '62' . substr($nomor, 1);
    }
    if (str_starts_with($nomor, '62')) {
        return $nomor;
    }
    if (str_starts_with($nomor, '8')) {
        return '62' . $nomor;
    }
    return $nomor;
}

/** Cari peserta berdasarkan kode. */
function cari_peserta_by_kode(string $kode): ?array
{
    $stmt = db()->prepare('SELECT * FROM peserta WHERE kode = ? LIMIT 1');
    $stmt->execute([normalisasi_kode($kode)]);
    return $stmt->fetch() ?: null;
}

/** Cari peserta berdasarkan ID. */
function cari_peserta_by_id(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM peserta WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

/**
 * Statistik untuk dashboard.
 *
 * Catatan: angka kehadiran hanya menghitung peserta yang
 * sudah DITERIMA, karena hanya mereka yang punya barcode.
 */
function statistik_peserta(): array
{
    $sql = "SELECT
              COUNT(*)                          AS total,
              SUM(status_acc = 'pending')       AS pending,
              SUM(status_acc = 'diterima')      AS diterima,
              SUM(status_acc = 'ditolak')       AS ditolak,
              SUM(status_acc = 'diterima' AND status = 'hadir')       AS hadir,
              SUM(status_acc = 'diterima' AND status = 'belum_hadir') AS belum_hadir
            FROM peserta";

    $row = db()->query($sql)->fetch();

    return [
        'total'       => (int) ($row['total'] ?? 0),
        'pending'     => (int) ($row['pending'] ?? 0),
        'diterima'    => (int) ($row['diterima'] ?? 0),
        'ditolak'     => (int) ($row['ditolak'] ?? 0),
        'hadir'       => (int) ($row['hadir'] ?? 0),
        'belum_hadir' => (int) ($row['belum_hadir'] ?? 0),
    ];
}

/** Jumlah peserta yang sudah diterima. */
function jumlah_diterima(): int
{
    return (int) db()->query("SELECT COUNT(*) FROM peserta WHERE status_acc = 'diterima'")->fetchColumn();
}

// =========================================================
// Format
// =========================================================

/** Format datetime ke bentuk Indonesia yang mudah dibaca. */
function format_tanggal(?string $datetime, bool $dengan_jam = true): string
{
    if (empty($datetime)) {
        return '-';
    }

    $ts = strtotime($datetime);
    if ($ts === false) {
        return '-';
    }

    $bulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    $hasil = date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);

    if ($dengan_jam) {
        $hasil .= ', ' . date('H:i', $ts);
    }

    return $hasil;
}

/** Format jam saja. */
function format_jam(?string $datetime): string
{
    if (empty($datetime)) {
        return '-';
    }
    $ts = strtotime($datetime);
    return $ts === false ? '-' : date('H:i', $ts);
}

// =========================================================
// Response
// =========================================================

/** Kirim response JSON lalu hentikan eksekusi. */
function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
