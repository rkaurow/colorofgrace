<?php
/**
 * Pencarian peserta berdasarkan nama (cadangan saat QR Code tidak terbaca).
 * GET: q
 */

require_once __DIR__ . '/../includes/auth.php';
require_admin_json();

$q = get('q');

if (mb_strlen($q) < 2) {
    json_response([]);
}

$stmt = db()->prepare(
    'SELECT kode, nama, gereja, status
     FROM peserta
     WHERE nama LIKE ?
     ORDER BY nama ASC
     LIMIT 10'
);
$stmt->execute(['%' . $q . '%']);

json_response($stmt->fetchAll());
