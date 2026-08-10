<?php
/**
 * Koneksi database (PDO)
 *
 * Ubah nilai di bawah sesuai environment.
 * InfinityFree biasanya memakai host 'sqlXXX.infinityfree.com'.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'coiministry');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Mengembalikan instance PDO tunggal.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec("SET time_zone = '+08:00'"); // WITA
    } catch (PDOException $e) {
        error_log('DB connection failed: ' . $e->getMessage());
        http_response_code(500);
        exit('Koneksi database gagal. Silakan hubungi administrator.');
    }

    return $pdo;
}
