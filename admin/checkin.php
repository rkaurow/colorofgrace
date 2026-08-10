<?php
/**
 * Scan kehadiran satu tahap.
 * Barcode dipindai dari layar HP peserta (email) saat kedatangan.
 */

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$stat = statistik_peserta();

$tahap      = 'hadir';
$judul      = 'Scan Kehadiran';
$subjudul   = 'Pindai barcode dari email peserta saat datang — 22 Agustus 2026';
$warna      = '#059669'; // emerald
// Path SVG (viewBox 24x24) — ikon "peserta terverifikasi"
$ikon       = 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z';
$stat_label = 'Sudah Hadir';
$stat_nilai = $stat['hadir'];
// Hanya peserta yang disetujui yang berhak hadir, jadi itulah pembaginya
$stat_total = $stat['diterima'];

$judul_halaman = $judul;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/scan-ui.php';
require_once __DIR__ . '/../includes/footer.php';
