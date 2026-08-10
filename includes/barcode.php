<?php
/**
 * Generator Barcode Code 128 (subset B)
 *
 * Ditulis mandiri tanpa library eksternal agar tidak butuh Composer
 * saat deploy ke shared hosting seperti InfinityFree.
 *
 * Membutuhkan ekstensi GD untuk output PNG.
 */

require_once __DIR__ . '/functions.php';

/**
 * Tabel pola Code 128.
 * Setiap pola terdiri dari 6 angka lebar bar/spasi bergantian,
 * dimulai dari bar. Total lebar tiap karakter = 11 modul.
 */
function code128_patterns(): array
{
    return [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312',
        '132212', '221213', '221312', '231212', '112232', '122132', '122231', '113222',
        '123122', '123221', '223211', '221132', '221231', '213212', '223112', '312131',
        '311222', '321122', '321221', '312212', '322112', '322211', '212123', '212321',
        '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121',
        '313121', '211331', '231131', '213113', '213311', '213131', '311123', '311321',
        '331121', '312113', '312311', '332111', '314111', '221411', '431111', '111224',
        '111422', '121124', '121421', '141122', '141221', '112214', '112412', '122114',
        '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112',
        '421211', '212141', '214121', '412121', '111143', '111341', '131141', '114113',
        '114311', '411113', '411311', '113141', '114131', '311141', '411131', '211412',
        '211214', '211232',
        // Indeks 106 = STOP. Polanya 7 elemen (2331112), bukan 6 seperti lainnya,
        // sehingga ditangani terpisah di code128_encode().
    ];
}

/**
 * Hitung urutan lebar bar untuk teks tertentu (Code 128 subset B).
 *
 * @return int[] Deret lebar, indeks genap = bar hitam, ganjil = spasi putih.
 */
function code128_encode(string $teks): array
{
    $patterns = code128_patterns();

    // START B = 104
    $codes   = [104];
    $checksum = 104;

    $len = strlen($teks);
    for ($i = 0; $i < $len; $i++) {
        $ascii = ord($teks[$i]);

        // Subset B mencakup ASCII 32..126
        if ($ascii < 32 || $ascii > 126) {
            $ascii = 32; // ganti karakter tak didukung dengan spasi
        }

        $value    = $ascii - 32;
        $codes[]  = $value;
        $checksum += $value * ($i + 1);
    }

    // Checksum modulo 103
    $codes[] = $checksum % 103;

    // STOP
    $codes[] = 106;

    $widths = [];
    foreach ($codes as $code) {
        if ($code === 106) {
            // Pola STOP: 2331112 (7 elemen)
            foreach (str_split('2331112') as $w) {
                $widths[] = (int) $w;
            }
            continue;
        }
        foreach (str_split($patterns[$code]) as $w) {
            $widths[] = (int) $w;
        }
    }

    return $widths;
}

/**
 * Buat file PNG barcode.
 *
 * @param string $teks         Isi barcode (kode peserta).
 * @param string $path         Lokasi file PNG yang akan ditulis.
 * @param int    $modul        Lebar 1 modul dalam pixel.
 * @param int    $tinggi       Tinggi bar dalam pixel.
 * @param bool   $tampilkanTeks Cetak teks kode di bawah barcode.
 * @return bool
 */
function code128_ke_png(
    string $teks,
    string $path,
    int $modul = 3,
    int $tinggi = 90,
    bool $tampilkanTeks = true
): bool {
    if (!function_exists('imagecreatetruecolor')) {
        error_log('Ekstensi GD tidak tersedia, barcode tidak dapat dibuat.');
        return false;
    }

    $widths = code128_encode($teks);

    $quietZone   = 10 * $modul;               // zona tenang minimal 10x lebar modul
    $tinggiTeks  = $tampilkanTeks ? 26 : 0;
    $paddingAtas = 10;

    $lebarBar = 0;
    foreach ($widths as $w) {
        $lebarBar += $w * $modul;
    }

    $lebarTotal  = $lebarBar + ($quietZone * 2);
    $tinggiTotal = $paddingAtas + $tinggi + $tinggiTeks + 8;

    $img = imagecreatetruecolor($lebarTotal, $tinggiTotal);
    if ($img === false) {
        return false;
    }

    $putih = imagecolorallocate($img, 255, 255, 255);
    $hitam = imagecolorallocate($img, 0, 0, 0);

    imagefilledrectangle($img, 0, 0, $lebarTotal, $tinggiTotal, $putih);

    // Gambar bar
    $x     = $quietZone;
    $isBar = true; // elemen pertama selalu bar
    foreach ($widths as $w) {
        $lebar = $w * $modul;
        if ($isBar) {
            imagefilledrectangle($img, $x, $paddingAtas, $x + $lebar - 1, $paddingAtas + $tinggi, $hitam);
        }
        $x    += $lebar;
        $isBar = !$isBar;
    }

    // Teks kode di bawah barcode
    if ($tampilkanTeks) {
        $font       = 5;
        $lebarTeks  = imagefontwidth($font) * strlen($teks);
        $posX       = (int) (($lebarTotal - $lebarTeks) / 2);
        $posY       = $paddingAtas + $tinggi + 6;
        imagestring($img, $font, $posX, $posY, $teks, $hitam);
    }

    $dir = dirname($path);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $ok = imagepng($img, $path);

    return $ok;
}

/**
 * Buat barcode peserta bila belum ada, lalu kembalikan path filenya.
 * Mengembalikan null bila gagal.
 */
function buat_barcode_peserta(string $kode, bool $paksa = false): ?string
{
    $kode = normalisasi_kode($kode);
    $file = BARCODE_PATH . '/' . $kode . '.png';

    if (!$paksa && is_file($file)) {
        return $file;
    }

    return code128_ke_png($kode, $file) ? $file : null;
}

/** URL publik barcode peserta. */
function url_barcode_peserta(string $kode): string
{
    return BASE_URL . '/assets/barcode/' . normalisasi_kode($kode) . '.png';
}

/**
 * Barcode sebagai data URI base64 — dipakai untuk penyematan langsung di HTML
 * agar tidak bergantung pada request tambahan ke server.
 */
function barcode_data_uri(string $kode, int $modul = 2, int $tinggi = 60): ?string
{
    $tmp = sys_get_temp_dir() . '/bc_' . normalisasi_kode($kode) . '.png';

    if (!code128_ke_png($kode, $tmp, $modul, $tinggi)) {
        return null;
    }

    $data = @file_get_contents($tmp);
    @unlink($tmp);

    return $data === false ? null : 'data:image/png;base64,' . base64_encode($data);
}
