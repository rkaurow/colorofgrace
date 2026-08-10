# UI Components

Menggunakan Bootstrap 5. Seluruh halaman responsif dan mengutamakan tampilan mobile, karena peserta mendaftar dan panitia melakukan scan dari ponsel.

## Halaman Publik

| Halaman | Komponen |
|---|---|
| Landing | Hero informasi acara, tombol **Daftar Sekarang** |
| Registrasi | Card berisi form 5 field, label jelas, pesan error per field |
| Sukses | Alert hijau berisi pemberitahuan barcode dikirim ke email |

## Halaman Admin

| Halaman | Komponen |
|---|---|
| Login | Card form terpusat, alert error |
| Dashboard | Lima kartu statistik mengikuti urutan corong kehadiran |
| Peserta | Tabel responsif, input pencarian, filter status, badge status, tombol kirim barcode & cetak gelang, modal konfirmasi, pagination |
| Daftar Ulang | Field scan ter-fokus otomatis, panel hasil besar, tombol Cetak Gelang, tombol kamera cadangan, counter |
| Masuk Ruangan | Field scan ter-fokus otomatis, panel hasil besar, counter |
| Cetak Gelang | Layout khusus cetak, tanpa elemen antarmuka |

## Konvensi Visual

- Navbar admin konsisten di seluruh halaman terproteksi.
- Badge status: hijau untuk selesai (`hadir`, `masuk`, gelang tercetak), abu-abu untuk yang belum.
- Hasil scan memakai warna semantik: hijau (sukses), kuning (sudah discan sebelumnya), merah (tidak valid atau belum daftar ulang).
- Nama peserta pada hasil scan ditampilkan paling besar agar mudah dibaca dari jarak pandang panitia.
- Halaman daftar ulang dan masuk ruangan diberi **warna aksen berbeda** agar panitia tidak tertukar membuka halaman.
- Aksi yang mengirim email atau mencetak selalu didahului modal konfirmasi.
- Seluruh teks antarmuka menggunakan bahasa Indonesia.