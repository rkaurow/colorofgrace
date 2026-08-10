# Barcode Generation

Barcode yang dikirim ke peserta berbentuk **barcode 1D Code 128**, dibaca memakai mesin barcode (barcode scanner gun).

## Format Kode

- Pola: `EVT-` + nomor urut 6 digit, contoh `EVT-000001`.
- Dihasilkan setelah INSERT berhasil, berdasarkan `id` peserta agar dijamin unik.
- Kode disimpan di kolom `kode` dengan constraint UNIQUE.
- Seluruh karakter (`A-Z`, `0-9`, `-`) didukung penuh oleh Code 128.

## Simbologi

- Tipe: **Code 128** (subset B).
- Library: `picqer/php-barcode-generator` atau setara yang mendukung Code 128 dan output PNG.
- Isi barcode **hanya kode peserta**, tanpa URL atau data pribadi.

## Dua Media Barcode

Kode yang sama dipakai pada dua media berbeda:

| Media | Dipakai Saat | Sumber |
|---|---|---|
| Layar HP | Tahap 1, daftar ulang | Gambar barcode di email |
| Gelang | Tahap 2, masuk ruangan | Dicetak di meja registrasi |

Detail cetak gelang ada di `22-wristband-printing.md`.

## Spesifikasi Gambar untuk Email

Ukuran menentukan keberhasilan pemindaian, jadi wajib diikuti:

| Parameter | Nilai |
|---|---|
| Lebar modul (narrow bar) | Minimal 2 px pada resolusi cetak 300 DPI |
| Tinggi bar | Minimal 60 px |
| Quiet zone kiri & kanan | Minimal 10x lebar modul, area putih polos |
| Warna | Hitam murni di atas putih murni, tanpa gradasi |
| Format file | PNG, tanpa transparansi |

- Teks kode dicetak di bawah barcode sebagai cadangan bila pemindaian gagal.
- Barcode tidak boleh diperkecil, dipotong, atau diberi latar berwarna oleh template email.

## Keterbacaan dari Layar Ponsel

Pada tahap 1 peserta menunjukkan barcode dari layar HP, sehingga:

- Barcode dibuat cukup besar agar tetap tajam saat email dibuka di layar kecil.
- Email menyarankan peserta menaikkan kecerahan layar saat check-in.
- Panitia **wajib** memakai scanner tipe **image/2D** di meja daftar ulang. Scanner laser umumnya gagal membaca dari layar yang memancarkan cahaya.
- Untuk tahap 2 yang memindai gelang, scanner laser maupun image sama-sama bekerja.

## Penyimpanan

- Disimpan di `/assets/barcode/EVT-000001.png` untuk digunakan ulang saat admin mengirim ulang email.
- Bila file tidak ditemukan, gambar dibuat ulang otomatis dari kode peserta.
- Folder penyimpanan tidak boleh menampilkan daftar isi direktori.
- Barcode yang sama dipakai konsisten, baik saat pendaftaran maupun saat kirim ulang oleh admin.