# QR Code Generation

QR Code yang dikirim ke peserta berbentuk **QR Code 2D**, dibaca memakai scanner image/2D.

## Format Kode

- Pola: `EVT-` + nomor urut 6 digit, contoh `EVT-000001`.
- Dihasilkan setelah INSERT berhasil, berdasarkan `id` peserta agar dijamin unik.
- Kode disimpan di kolom `kode` dengan constraint UNIQUE.
- Isi QR Code hanya kode peserta (`A-Z`, `0-9`, `-`) tanpa URL atau data pribadi.

## Simbologi

- Tipe: **QR Code**, error correction level M.
- Library: `chillerlan/php-qrcode` versi 4.x dan output PNG melalui `ext-gd`.

## Dua Media QR Code

Kode yang sama dipakai pada dua media berbeda:

| Media | Dipakai Saat | Sumber |
|---|---|---|
| Layar HP | Tahap 1, daftar ulang | Gambar QR Code di email |
| Gelang | Tahap 2, masuk ruangan | Dicetak di meja registrasi |

Detail cetak gelang ada di `22-wristband-printing.md`.

## Spesifikasi Gambar untuk Email

Ukuran menentukan keberhasilan pemindaian, jadi wajib diikuti:

| Parameter | Nilai |
|---|---|
| Skala modul | 8 px per modul pada output aplikasi |
| Quiet zone | 4 modul di semua sisi |
| Warna | Hitam murni di atas putih murni, tanpa gradasi |
| Format file | PNG, tanpa transparansi |

- Teks kode ditulis di bawah QR Code sebagai cadangan bila pemindaian gagal.
- QR Code tidak boleh dipotong atau diberi latar berwarna oleh template email.

## Keterbacaan dari Layar Ponsel

Pada tahap 1 peserta menunjukkan QR Code dari layar HP, sehingga:

- QR Code dibuat cukup besar agar tetap tajam saat email dibuka di layar kecil.
- Email menyarankan peserta menaikkan kecerahan layar saat check-in.
- Panitia memakai scanner tipe **image/2D** di meja daftar ulang. Scanner laser tidak dapat membaca QR Code.
- Untuk tahap 2, gunakan scanner 2D dari gelang.

## Penyimpanan

- Disimpan di `/assets/qr/EVT-000001.png` untuk digunakan ulang saat admin mengirim ulang email.
- Bila file tidak ditemukan, gambar dibuat ulang otomatis dari kode peserta.
- Folder penyimpanan tidak boleh menampilkan daftar isi direktori.
- QR Code yang sama dipakai konsisten, baik saat persetujuan maupun saat kirim ulang oleh admin.