# Testing Checklist

## Pendaftaran

- [ ] Landing page tampil dan tombol daftar mengarah ke form
- [ ] Form menampilkan urutan field: nama, asal gereja, tahu dari mana, email, WhatsApp
- [ ] Submit dengan data valid menyimpan peserta ke database
- [ ] Kode unik `EVT-xxxxxx` terbentuk dan tidak duplikat
- [ ] Email berisi barcode diterima peserta
- [ ] Halaman sukses memberi tahu barcode dikirim via email dan ditunjukkan saat check-in
- [ ] Field kosong ditolak dengan pesan error
- [ ] Email duplikat ditolak dengan pesan yang jelas
- [ ] Nomor WhatsApp non-numerik ditolak
- [ ] Kegagalan SMTP tidak membatalkan data yang sudah tersimpan
- [ ] Refresh setelah submit tidak membuat data ganda

## Admin

- [ ] Login berhasil dengan kredensial benar
- [ ] Login gagal dengan kredensial salah
- [ ] Halaman admin tidak bisa diakses tanpa login
- [ ] Dashboard menampilkan lima kartu statistik dengan angka benar
- [ ] List peserta menampilkan seluruh variabel peserta
- [ ] Tombol kirim barcode dan cetak gelang ada di kolom paling ujung
- [ ] Tombol kirim barcode berhasil mengirim ulang email
- [ ] Pencarian dan filter status bekerja
- [ ] Logout menghancurkan session

## Tahap 1 — Daftar Ulang

- [ ] Field scan otomatis ter-fokus saat halaman dimuat
- [ ] Fokus kembali ke field setelah diklik di area lain
- [ ] Mesin barcode berhasil membaca barcode dari **layar HP**
- [ ] Scan otomatis ter-submit tanpa perlu klik tombol
- [ ] Scan barcode valid menampilkan nama peserta
- [ ] `status` berubah menjadi `hadir`, `checkin_at` terisi
- [ ] Tombol Cetak Gelang muncul setelah scan berhasil
- [ ] Field dikosongkan dan siap untuk scan berikutnya
- [ ] Scan kedua menampilkan peringatan sudah daftar ulang
- [ ] Scan barcode tidak dikenal menampilkan error
- [ ] Scan beruntun cepat tidak menghasilkan data ganda
- [ ] Kode huruf kecil dari scanner tetap dikenali
- [ ] Pencarian nama bekerja saat peserta lupa membawa barcode

## Cetak Gelang

- [ ] Halaman cetak terbuka dari panel hasil scan
- [ ] Halaman cetak terbuka dari tombol di list peserta
- [ ] Tombol nonaktif bila peserta belum daftar ulang
- [ ] Barcode di gelang berisi kode yang sama dengan di email
- [ ] Elemen antarmuka tersembunyi saat mencetak
- [ ] Hasil cetak muat pada gelang tanpa terpotong
- [ ] **Barcode hasil cetak berhasil dipindai mesin barcode**
- [ ] `gelang_dicetak` dan `gelang_dicetak_at` terisi
- [ ] Cetak ulang bekerja dan memperbarui waktu

## Tahap 2 — Masuk Ruangan

- [ ] Field scan otomatis ter-fokus
- [ ] Mesin barcode berhasil membaca barcode dari **gelang**
- [ ] Scan valid menampilkan nama peserta
- [ ] `status_masuk` berubah menjadi `masuk`, `masuk_at` terisi
- [ ] Peserta yang belum daftar ulang ditolak dengan pesan jelas
- [ ] Scan kedua menampilkan peringatan sudah masuk
- [ ] Scan barcode tidak dikenal menampilkan error
- [ ] Umpan balik suara berbeda antara sukses dan gagal
- [ ] Input kode manual bekerja sebagai cadangan
- [ ] Scan via kamera bekerja sebagai cadangan

## Umum

- [ ] Tampilan responsif di ponsel dan desktop
- [ ] CSRF token aktif di semua form POST
- [ ] Output di-escape untuk mencegah XSS