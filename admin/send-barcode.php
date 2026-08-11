<?php
/**
 * Kirim ulang email QR Code ke peserta.
 * POST: peserta_id, csrf_token
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mailer.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/peserta.php');
}

csrf_require();

$tujuan = post('kembali') === 'approval'
    ? BASE_URL . '/admin/approval.php?status=diterima'
    : BASE_URL . '/admin/peserta.php';

$id      = (int) post('peserta_id');
$peserta = $id > 0 ? cari_peserta_by_id($id) : null;

if (!$peserta) {
    flash_set('error', 'Peserta tidak ditemukan.');
    redirect($tujuan);
}

if (($peserta['status_acc'] ?? '') !== 'diterima') {
    flash_set('error', 'QR Code hanya dapat dikirim ulang untuk peserta yang sudah diterima.');
    redirect($tujuan);
}

if (buat_qr_peserta($peserta['kode']) === null) {
    flash_set('error', 'QR Code gagal dibuat. Periksa Composer, ekstensi GD, dan izin folder assets/qr.');
} elseif (kirim_qr_peserta($peserta)) {
    tandai_email_terkirim($id);
    flash_set('success', 'QR Code berhasil dikirim ke ' . $peserta['email'] . '.');
} else {
    flash_set('error', MAIL_ENABLED
        ? 'Gagal mengirim email ke ' . $peserta['email'] . '. Periksa pengaturan SMTP.'
        : 'Pengiriman email dinonaktifkan. Aktifkan MAIL_ENABLED di config/smtp.php.');
}

redirect($tujuan);
