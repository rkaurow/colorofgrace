<?php
/**
 * Generator QR Code peserta.
 *
 * Memakai chillerlan/php-qrcode agar output QR dapat dipindai kamera
 * atau scanner 2D. Membutuhkan ext-gd untuk output PNG.
 */

require_once __DIR__ . '/functions.php';

/** Muat library QR Code dari Composer. */
function muat_qrcode(): bool
{
    if (class_exists(\chillerlan\QRCode\QRCode::class)) {
        return true;
    }

    $autoload = BASE_PATH . '/vendor/autoload.php';
    if (is_file($autoload)) {
        require_once $autoload;
        return class_exists(\chillerlan\QRCode\QRCode::class);
    }

    return false;
}

/**
 * Buat file PNG QR Code peserta bila belum ada, lalu kembalikan path-nya.
 * Mengembalikan null bila library, GD, atau penulisan file gagal.
 */
function buat_qr_peserta(string $kode, bool $paksa = false): ?string
{
    $kode = normalisasi_kode($kode);
    $file = QR_PATH . '/' . $kode . '.png';

    if (!$paksa && is_file($file)) {
        return $file;
    }

    if (!muat_qrcode()) {
        error_log('Library QR Code tidak tersedia. Jalankan composer install.');
        return null;
    }

    try {
        $options = new \chillerlan\QRCode\QROptions([
            'outputType'    => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'      => \chillerlan\QRCode\QRCode::ECC_M,
            'scale'         => 8,
            'addQuietzone'  => true,
            'quietzoneSize' => 4,
        ]);

        if (!is_dir(QR_PATH)) {
            @mkdir(QR_PATH, 0755, true);
        }

        @(new \chillerlan\QRCode\QRCode($options))->render($kode, $file);

        return is_file($file) && filesize($file) > 0 ? $file : null;
    } catch (\Throwable $ex) {
        @unlink($file);
        error_log('Gagal membuat QR Code ' . $kode . ': ' . $ex->getMessage());
        return null;
    }
}

/** URL publik QR Code peserta. */
function url_qr_peserta(string $kode): string
{
    return BASE_URL . '/assets/qr/' . normalisasi_kode($kode) . '.png';
}
