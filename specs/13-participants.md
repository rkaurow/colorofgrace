# Participants Module

Halaman `/admin/peserta.php` menampilkan **semua variabel peserta** dalam tabel, dengan aksi di kolom paling ujung.

## Kolom Tabel

| # | Kolom | Sumber |
|---|---|---|
| 1 | No | nomor urut |
| 2 | Kode | `kode` |
| 3 | Nama | `nama` |
| 4 | Asal Gereja | `gereja` |
| 5 | Tahu Dari | `info_dari` |
| 6 | Email | `email` |
| 7 | WhatsApp | `whatsapp` |
| 8 | Daftar Ulang | badge `hadir` / `belum_hadir` + waktu `checkin_at` |
| 9 | Gelang | badge tercetak / belum + waktu `gelang_dicetak_at` |
| 10 | Masuk Ruangan | badge `masuk` / `belum_masuk` + waktu `masuk_at` |
| 11 | Tanggal Daftar | `created_at` |
| 12 | **Aksi** | Tombol **Kirim Barcode** dan **Cetak Gelang** |

## Tombol Aksi

Berada di kolom paling ujung (kanan) setiap baris.

### Kirim Barcode

- Mengirim ulang email berisi barcode ke alamat email peserta tersebut.
- Wajib memakai POST + CSRF token, bukan link GET.
- Tampilkan modal konfirmasi sebelum mengirim.
- Tombol menampilkan state loading selama pengiriman untuk mencegah klik ganda.
- Bila email pernah terkirim, tampilkan indikator kecil berisi waktu pengiriman terakhir (`email_sent_at`).

### Cetak Gelang

- Membuka halaman cetak gelang untuk peserta tersebut.
- Hanya aktif bila peserta **sudah daftar ulang** (`status = 'hadir'`).
- Bila gelang sudah pernah dicetak, tombol berubah menjadi **Cetak Ulang** dengan konfirmasi tambahan.
- Detail teknis pencetakan ada di `22-wristband-printing.md`.

## Fitur Pendukung

- **Pencarian** berdasarkan nama, email, kode, atau asal gereja. Pencarian nama penting sebagai jalur cadangan saat peserta lupa membawa barcode.
- **Filter status**: semua / belum daftar ulang / sudah daftar ulang / belum cetak gelang / sudah masuk ruangan.
- **Pagination** default 25 baris per halaman.
- **Export CSV** (opsional) berisi seluruh kolom di atas.
- Tabel responsif (`table-responsive`) agar terbaca di perangkat mobile. Dengan 12 kolom, pertimbangkan menyembunyikan kolom sekunder pada layar kecil.