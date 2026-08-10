# Admin Dashboard

Area admin dilindungi session login. Semua halaman di `/admin` wajib melewati middleware auth.

## Alur Admin

1. **Login admin** — `/admin/login.php`, username + password.
2. **Dashboard** — `/admin/index.php`, ringkasan statistik.
3. **List peserta** — `/admin/peserta.php`, menampilkan semua variabel peserta, dengan tombol **Kirim Barcode ke Email** dan **Cetak Gelang** di kolom paling ujung setiap baris.
4. **Daftar ulang** — `/admin/checkin.php`, scan barcode dari layar HP (tahap 1).
5. **Masuk ruangan** — `/admin/masuk.php`, scan barcode dari gelang (tahap 2).
6. **Logout** — hancurkan session.

## Kartu Statistik

| Kartu | Sumber Data |
|---|---|
| Total Pendaftar | `COUNT(*)` dari `peserta` |
| Sudah Daftar Ulang | `COUNT(*) WHERE status = 'hadir'` |
| Belum Daftar Ulang | `COUNT(*) WHERE status = 'belum_hadir'` |
| Gelang Tercetak | `COUNT(*) WHERE gelang_dicetak = 1` |
| Sudah Masuk Ruangan | `COUNT(*) WHERE status_masuk = 'masuk'` |

Kartu disusun mengikuti urutan corong: daftar → daftar ulang → gelang → masuk ruangan, agar panitia dapat melihat di tahap mana peserta tertahan.

## Navigasi

Navbar admin berisi: **Dashboard**, **Peserta**, **Daftar Ulang**, **Masuk Ruangan**, **Logout**.

## Halaman Admin

| Halaman | Path | Fungsi |
|---|---|---|
| Login | `/admin/login.php` | Autentikasi admin |
| Dashboard | `/admin/index.php` | Kartu statistik |
| Peserta | `/admin/peserta.php` | List peserta, kirim barcode, cetak gelang |
| Daftar Ulang | `/admin/checkin.php` | Scan tahap 1 dari layar HP |
| Masuk Ruangan | `/admin/masuk.php` | Scan tahap 2 dari gelang |
| Cetak Gelang | `/admin/cetak-gelang.php` | Halaman siap cetak |
| Logout | `/admin/logout.php` | Destroy session |