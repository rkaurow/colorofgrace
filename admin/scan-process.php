<?php
/**
 * Endpoint validasi scan QR Code (JSON).
 *
 * POST: kode, tahap (hadir|checkin), csrf_token
 *
 * Status yang dikembalikan:
 *   success             — berhasil dicatat hadir
 *   duplicate           — sudah pernah scan
 *   belum_disetujui     — status_acc bukan 'diterima' (pending / ditolak)
 *   invalid             — QR Code tidak ditemukan
 */

require_once __DIR__ . '/../includes/auth.php';
require_admin_json();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['status' => 'invalid', 'message' => 'Metode tidak diizinkan.'], 405);
}

if (!csrf_verify()) {
    json_response([
        'status'  => 'invalid',
        'message' => 'Sesi tidak valid. Silakan muat ulang halaman.',
    ], 419);
}

$kode  = normalisasi_kode(post('kode'));
$tahap = post('tahap');

// Alur sekarang satu tahap: kehadiran.
if ($tahap !== 'hadir') {
    json_response(['status' => 'invalid', 'message' => 'Tahap scan tidak dikenali.'], 400);
}

if ($kode === '') {
    json_response([
        'status'  => 'invalid',
        'tahap'   => $tahap,
        'message' => 'QR Code kosong.',
    ]);
}

$peserta = cari_peserta_by_kode($kode);

if (!$peserta) {
    json_response([
        'status'  => 'invalid',
        'tahap'   => $tahap,
        'kode'    => $kode,
        'message' => 'QR Code tidak valid. Peserta tidak ditemukan.',
    ]);
}

$dasar = [
    'tahap'      => $tahap,
    'peserta_id' => (int) $peserta['id'],
    'kode'       => $peserta['kode'],
    'nama'       => $peserta['nama'],
    'gereja'     => $peserta['gereja'],
];

// =========================================================
// GERBANG PERSETUJUAN — hanya peserta berstatus 'diterima'
// yang boleh dicatat kehadirannya.
// =========================================================
if (($peserta['status_acc'] ?? 'pending') !== 'diterima') {
    $pesan = $peserta['status_acc'] === 'ditolak'
        ? 'Pendaftaran ditolak. Peserta tidak terdaftar.'
        : 'Belum disetujui panitia. Hubungi panitia untuk konfirmasi.';

    json_response($dasar + [
        'status'  => 'belum_disetujui',
        'message' => $pesan,
    ]);
}

// =========================================================
// TAHAP TUNGGAL — Kehadiran (QR Code dari email peserta)
// =========================================================

if ($peserta['status'] === 'hadir') {
    json_response($dasar + [
        'status'  => 'duplicate',
        'waktu'   => format_tanggal($peserta['checkin_at']),
        'message' => 'Sudah hadir pada ' . format_tanggal($peserta['checkin_at']),
    ]);
}

// Update hanya bila masih belum_hadir — mencegah balapan scan ganda
$stmt = db()->prepare(
    "UPDATE peserta SET status = 'hadir', checkin_at = NOW()
     WHERE id = ? AND status = 'belum_hadir'"
);
$stmt->execute([$peserta['id']]);

if ($stmt->rowCount() === 0) {
    $ulang = cari_peserta_by_id((int) $peserta['id']);
    json_response($dasar + [
        'status'  => 'duplicate',
        'waktu'   => format_tanggal($ulang['checkin_at'] ?? null),
        'message' => 'Sudah hadir pada ' . format_tanggal($ulang['checkin_at'] ?? null),
    ]);
}

json_response($dasar + [
    'status'  => 'success',
    'waktu'   => format_tanggal(date('Y-m-d H:i:s')),
    'message' => 'Kehadiran berhasil dicatat',
]);
