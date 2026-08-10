<?php
/**
 * Kirim ulang email barcode ke peserta.
 * POST: peserta_id, csrf_token
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mailer.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/peserta.php');
}

csrf_require();

$id      = (int) post('peserta_id');
$peserta = $id > 0 ? cari_peserta_by_id($id) : null;

if (!$peserta) {
    flash_set('error', 'Peserta tidak ditemukan.');
    redirect(BASE_URL . '/admin/peserta.php');
}

// Pastikan barcode tersedia sebelum dikirim
buat_barcode_peserta($peserta['kode']);

if (kirim_barcode_peserta($peserta)) {
    tandai_email_terkirim($id);
    flash_set('success', 'Barcode berhasil dikirim ke ' . $peserta['email'] . '.');
} else {
    flash_set('error', MAIL_ENABLED
        ? 'Gagal mengirim email ke ' . $peserta['email'] . '. Periksa pengaturan SMTP.'
        : 'Pengiriman email dinonaktifkan. Aktifkan MAIL_ENABLED di config/smtp.php.');
}

redirect(BASE_URL . '/admin/peserta.php');
