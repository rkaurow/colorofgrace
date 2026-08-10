# Deployment

## Aplikasi

- Konfigurasi `config/database.php` dan `config/smtp.php`.
- Impor `database.sql`.
- Unggah folder `vendor` (PHPMailer + barcode generator) secara manual.
- Pastikan folder `assets/barcode/` dapat ditulis.
- Aktifkan HTTPS.

## Persiapan Perangkat Keras Hari-H

| Perangkat | Kebutuhan |
|---|---|
| Scanner meja daftar ulang | Tipe **image/2D**, wajib, karena memindai dari layar HP |
| Scanner pintu ruangan | Tipe image atau laser, keduanya bekerja untuk gelang |
| Printer gelang | Printer wristband thermal transfer |
| Komputer | Satu unit per titik scan, terhubung internet |

## Uji Coba Sebelum Acara

- [ ] Scan barcode dari layar HP dengan scanner yang akan dipakai
- [ ] Cetak satu gelang uji, lalu pindai hasilnya
- [ ] Pastikan scanner mengirim karakter Enter setelah kode
- [ ] Uji koneksi internet di lokasi kedua titik scan
- [ ] Siapkan prosedur cadangan bila internet terputus