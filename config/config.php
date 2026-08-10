<?php
/**
 * Konfigurasi aplikasi COI Ministry
 */

// ---------------------------------------------------------
// Identitas Acara — ubah bagian ini sesuai kebutuhan
// ---------------------------------------------------------
define('APP_NAME',      'COI Ministry');
define('EVENT_NAME',    'Color of Grace');
define('EVENT_SUBTITLE', 'One Truth Way');
define('EVENT_TAGLINE', 'Festival of Unity & Faith');
define('EVENT_DATE',    '2026-08-22');
define('EVENT_DATE_TEXT', '22 Agustus 2026');
define('EVENT_TIME',    'Open Gate 14.00 WITA');
define('EVENT_LOCATION', 'Royal Phoenix Restaurant');
define('EVENT_ADDRESS', 'Lantai 2, Royal Phoenix Restaurant');
define('EVENT_THEME',   'Festival of Unity & Faith');
define('CONTACT_WA',    '6281234567890');

// ---------------------------------------------------------
// Kode peserta
// ---------------------------------------------------------
define('KODE_PREFIX', 'COI-');

// ---------------------------------------------------------
// Path
// ---------------------------------------------------------
define('BASE_PATH', dirname(__DIR__));
define('BARCODE_PATH', BASE_PATH . '/assets/barcode');

// ---------------------------------------------------------
// Base URL — otomatis, biasanya tidak perlu diubah
// ---------------------------------------------------------
if (!defined('BASE_URL')) {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir   = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')));
    $dir   = rtrim($dir, '/');
    // Bila skrip berada di dalam /admin, naik satu level
    if (basename($dir) === 'admin') {
        $dir = dirname($dir);
    }
    define('BASE_URL', $proto . '://' . $host . ($dir === '/' ? '' : $dir));
}

// ---------------------------------------------------------
// Zona waktu — WITA (acara memakai waktu WITA)
// ---------------------------------------------------------
date_default_timezone_set('Asia/Makassar');

// ---------------------------------------------------------
// Mode debug — set false saat produksi
// ---------------------------------------------------------
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
