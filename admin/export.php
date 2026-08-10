<?php
/**
 * Export data peserta ke CSV.
 * Menghormati parameter cari & filter yang sedang aktif.
 */

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$cari   = get('cari');
$filter = get('filter');

$where  = [];
$params = [];

if ($cari !== '') {
    $where[] = '(nama LIKE ? OR email LIKE ? OR kode LIKE ? OR gereja LIKE ?)';
    $like    = '%' . $cari . '%';
    array_push($params, $like, $like, $like, $like);
}

switch ($filter) {
    case 'pending':     $where[] = "status_acc = 'pending'";        break;
    case 'diterima':    $where[] = "status_acc = 'diterima'";       break;
    case 'ditolak':     $where[] = "status_acc = 'ditolak'";        break;
    case 'hadir':       $where[] = "status = 'hadir'";              break;
    case 'belum_hadir': $where[] = "status = 'belum_hadir'";        break;
}

$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$stmt = db()->prepare('SELECT * FROM peserta' . $sqlWhere . ' ORDER BY id ASC');
$stmt->execute($params);

$namaFile = 'peserta-' . date('Ymd-His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $namaFile . '"');
header('Pragma: no-cache');

$out = fopen('php://output', 'w');

// BOM UTF-8 agar Excel membaca karakter Indonesia dengan benar
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, [
    'No', 'Kode', 'Nama', 'Asal Gereja', 'Tahu Dari', 'Email', 'WhatsApp',
    'Status Approval', 'Status Kehadiran', 'Waktu Kehadiran',
    'Email Terkirim', 'Tanggal Daftar',
]);

$no = 1;
while ($p = $stmt->fetch()) {
    fputcsv($out, [
        $no++,
        $p['kode'],
        $p['nama'],
        $p['gereja'],
        $p['info_dari'],
        $p['email'],
        $p['whatsapp'],
        $p['status_acc'] === 'diterima' ? 'Diterima' : ($p['status_acc'] === 'ditolak' ? 'Ditolak' : 'Menunggu'),
        $p['status'] === 'hadir' ? 'Hadir' : 'Belum Hadir',
        $p['checkin_at'] ?? '',
        $p['email_sent_at'] ?? '',
        $p['created_at'],
    ]);
}

fclose($out);
exit;
