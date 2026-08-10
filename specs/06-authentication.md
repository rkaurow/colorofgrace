# Authentication

Hanya ada satu peran: **admin**. Peserta tidak memiliki akun.

## Login

- Halaman `/admin/login.php` dengan field username dan password.
- Password diverifikasi memakai `password_verify()` terhadap hash di tabel `admin`.
- Setelah login sukses, panggil `session_regenerate_id(true)`.
- Simpan `admin_id` dan `admin_username` di session.
- Kredensial salah menampilkan pesan umum tanpa membocorkan field mana yang keliru.
- Terapkan pembatasan percobaan login untuk mencegah brute force.

## Middleware

- File `/includes/auth.php` memeriksa keberadaan session admin.
- Disertakan di bagian paling atas setiap halaman `/admin` kecuali `login.php`.
- Bila tidak terautentikasi, redirect ke halaman login dan hentikan eksekusi dengan `exit`.
- Endpoint pemroses (scan dan kirim barcode) juga wajib melewati pemeriksaan ini.

## Logout

- `/admin/logout.php` mengosongkan session, menghapus cookie session, lalu `session_destroy()`.
- Setelah logout, pengguna diarahkan kembali ke halaman login.