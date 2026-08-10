# Wristband Printing

Gelang dicetak di meja registrasi pada hari-H, setelah peserta berhasil daftar ulang (tahap 1).

## Alur Cetak

```
Scan barcode layar HP -> daftar ulang berhasil
  -> Panel hasil menampilkan tombol "Cetak Gelang"
  -> Klik tombol -> buka halaman cetak
  -> Printer wristband mencetak gelang
  -> UPDATE gelang_dicetak = 1, gelang_dicetak_at = NOW()
  -> Gelang dipakaikan ke peserta
```

Pencetakan juga dapat dipicu dari list peserta lewat tombol **Cetak Gelang** di setiap baris.

## Isi Gelang

Ruang pada gelang sangat terbatas, jadi hanya memuat elemen esensial:

| Elemen | Keterangan |
|---|---|
| Barcode Code 128 | Berisi kode peserta, sama persis dengan yang di email |
| Kode peserta | Teks di bawah barcode, cadangan bila scan gagal |
| Nama peserta | Dipotong bila terlalu panjang |
| Nama acara | Opsional, bila ruang mencukupi |

## Spesifikasi Cetak

- Printer: **printer wristband khusus** (mis. Zebra), thermal transfer.
- Halaman cetak memakai CSS `@media print` dengan ukuran halaman sesuai dimensi gelang.
- Seluruh elemen antarmuka (navbar, tombol, latar) disembunyikan saat mencetak.
- Margin dibuat nol agar barcode tidak bergeser.
- Cetak dipicu otomatis lewat `window.print()` saat halaman cetak dibuka.

### Dimensi Barcode di Gelang

Gelang sempit, sedangkan Code 128 untuk `EVT-000001` relatif lebar. Karena itu:

| Parameter | Nilai |
|---|---|
| Lebar modul | Minimal 0,25 mm (setara 3 px @300 DPI) |
| Tinggi bar | Minimal 8 mm |
| Quiet zone | Minimal 2,5 mm di kiri dan kanan |
| Orientasi | Mengikuti panjang gelang, bukan lebarnya |

- Barcode dicetak pada area gelang yang **datar**, bukan di bagian yang melengkung atau tertekuk.
- Bila kode tidak muat, perpendek format kode (mis. buang awalan `EVT-`) daripada memperkecil lebar modul.

## Aturan Penting

- Gelang hanya boleh dicetak untuk peserta yang **sudah daftar ulang**.
- Kode di gelang **wajib sama** dengan kode di email. Tidak ada kode baru.
- `gelang_dicetak` bersifat penanda, **bukan** pengganti status kehadiran.
- Cetak ulang diperbolehkan bila gelang rusak atau salah cetak. `gelang_dicetak_at` diperbarui ke waktu terakhir.
- Tombol cetak memakai POST + CSRF token.
- Uji cetak minimal satu gelang sebelum acara dimulai, lalu pindai hasilnya dengan mesin barcode yang akan dipakai.
