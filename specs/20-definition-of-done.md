# Definition of Done

Pekerjaan dianggap selesai bila seluruh poin berikut terpenuhi.

## Fungsional

- [ ] Peserta dapat mendaftar dari link publik dan mengisi kelima field sesuai urutan flow
- [ ] Pendaftaran berhasil menampilkan pemberitahuan bahwa barcode dikirim via email
- [ ] Email berisi barcode diterima peserta
- [ ] Admin dapat login dan mengakses area terproteksi
- [ ] List peserta menampilkan semua variabel dengan tombol aksi di kolom ujung
- [ ] Tahap 1: scan barcode dari layar HP menandai peserta hadir
- [ ] Gelang dapat dicetak dan barcode hasil cetak terbukti dapat dipindai
- [ ] Tahap 2: scan barcode dari gelang menandai peserta masuk ruangan
- [ ] Peserta yang belum daftar ulang ditolak pada tahap 2

## Kualitas

- [ ] Prepared statements di seluruh query
- [ ] CSRF token di seluruh form POST
- [ ] Output di-escape sebelum ditampilkan
- [ ] Password admin disimpan sebagai hash
- [ ] Pesan error ramah pengguna, detail teknis hanya di log

## Operasional

- [ ] Berjalan pada hosting InfinityFree
- [ ] Antarmuka responsif di ponsel dan desktop
- [ ] `database.sql` dapat diimpor tanpa error
- [ ] Dokumentasi instalasi dan konfigurasi tersedia
- [ ] Seluruh item pada `19-testing.md` sudah dicentang