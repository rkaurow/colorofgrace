# Check-in Flow

Kehadiran dicatat dalam **dua tahap** memakai QR Code yang sama, dipindai dengan scanner 2D.

| Tahap | Halaman | Media QR Code | Hasil |
|---|---|---|---|
| 1. Daftar ulang | `/admin/checkin.php` | Layar HP (dari email) | `status = 'hadir'`, `checkin_at` terisi |
| 2. Masuk ruangan | `/admin/masuk.php` | Gelang | `status_masuk = 'masuk'`, `masuk_at` terisi |

Di antara kedua tahap, panitia mencetak gelang (lihat `22-wristband-printing.md`).

## Alur Lengkap di Lapangan

```
Peserta datang
  -> Tunjukkan QR Code di layar HP
  -> [TAHAP 1] Scan di meja daftar ulang -> status = hadir
  -> Panitia cetak gelang -> gelang_dicetak = 1
  -> Gelang dipakaikan ke peserta
  -> Peserta menuju pintu ruangan
  -> [TAHAP 2] Scan gelang -> status_masuk = masuk
  -> Peserta masuk ruangan
```

## Cara Kerja Scanner QR

Scanner 2D terhubung sebagai **USB keyboard wedge (HID)**. Hasil pemindaian langsung "diketikkan" ke field yang sedang aktif, diakhiri karakter Enter.

## Tahap 1 — Daftar Ulang (`/admin/checkin.php`)

1. Field scan otomatis ter-fokus saat halaman dimuat.
2. Peserta menunjukkan QR Code dari layar HP.
3. Scanner mengetikkan kode dan menekan Enter, form ter-submit sendiri.
4. Sistem memvalidasi kode ke database.
5. **Nama peserta tampil** di layar sebagai konfirmasi.
6. `status = 'hadir'`, `checkin_at = NOW()`.
7. Panel hasil menampilkan tombol **Cetak Gelang**.
8. Field dikosongkan dan ter-fokus kembali.

### Logika Validasi Tahap 1

```
Kode masuk -> trim + normalisasi huruf besar
  -> Cari peserta berdasarkan kode
    -> Tidak ditemukan       : error "QR Code tidak valid" (merah)
     -> status = 'hadir'      : peringatan "Sudah daftar ulang pada <waktu>" (kuning)
                                tombol Cetak Ulang Gelang tetap tersedia
     -> status = 'belum_hadir': UPDATE status = 'hadir', checkin_at = NOW()
                                sukses berisi nama + asal gereja (hijau)
```

## Tahap 2 — Masuk Ruangan (`/admin/masuk.php`)

1. Field scan otomatis ter-fokus.
2. Peserta menunjukkan **gelang** di pergelangan tangan.
3. Scanner membaca QR Code dari gelang.
4. Sistem memvalidasi kode.
5. **Nama peserta tampil** di layar.
6. `status_masuk = 'masuk'`, `masuk_at = NOW()`.

### Logika Validasi Tahap 2

```
Kode masuk -> trim + normalisasi huruf besar
  -> Cari peserta berdasarkan kode
    -> Tidak ditemukan        : error "QR Code tidak valid" (merah)
     -> status = 'belum_hadir' : error "Belum daftar ulang, arahkan ke meja registrasi" (merah)
     -> status_masuk = 'masuk' : peringatan "Sudah masuk pada <waktu>" (kuning)
     -> selain itu             : UPDATE status_masuk = 'masuk', masuk_at = NOW()
                                 sukses berisi nama + asal gereja (hijau)
```

Peserta yang belum melewati tahap 1 **tidak boleh** langsung masuk. Ini mencegah orang yang belum terdaftar menyelinap masuk.

## Perilaku Field Scan

Berlaku sama untuk kedua halaman:

- Field wajib **selalu mendapat fokus**. Pasang handler yang mengembalikan fokus saat pengguna tidak sengaja mengklik area lain.
- Submit dipicu oleh tombol Enter dari scanner, bukan klik tombol.
- Setelah setiap scan, field dikosongkan otomatis.
- Input diproses lewat AJAX agar halaman tidak reload dan fokus tidak hilang.
- Kode di-`trim` untuk membuang karakter Enter/Tab bawaan scanner.
- Pembandingan case-insensitive agar scanner yang mengirim huruf kecil tetap dikenali.
- Beri jeda singkat (debounce) untuk mencegah kode sama terkirim dua kali karena scanner terpicu ganda.

## Tampilan Hasil Scan

Panel hasil menampilkan:

- **Nama peserta** dengan ukuran besar dan kontras tinggi, terbaca sambil berdiri
- Asal gereja
- Kode peserta
- Status hasil scan
- Umpan balik suara berbeda untuk sukses dan gagal, karena panitia sering tidak menatap layar
- Khusus tahap 1: tombol **Cetak Gelang**

## Cadangan

- **Pencarian nama**: bila peserta lupa membawa email atau QR Code rusak, panitia dapat mencari nama lalu memproses check-in manual.
- **Input kode manual**: admin mengetik kode lalu menekan Enter, memakai jalur pemrosesan yang sama.

## Aturan Penting

- Update status dilakukan **server-side**, tidak boleh hanya di JavaScript.
- Endpoint pemrosesan scan wajib memeriksa session admin dan CSRF token.
- Scan duplikat tidak mengubah timestamp yang sudah ada.
- Kedua halaman menampilkan counter kehadiran, diperbarui setiap scan berhasil.