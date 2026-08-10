# Registration Flow

Alur pendaftaran peserta sesuai flow resmi. Semua langkah bersifat publik (tanpa login).

## Langkah Pengguna

1. **Link web** — peserta menerima link publik (dibagikan via WA/IG/flyer).
2. **Masuk web** — landing page `/index.php` menampilkan informasi acara dan tombol **Daftar Sekarang**.
3. **Registrasi** — tombol mengarah ke halaman form `/register.php`.
4. **Isi form** dengan urutan field berikut:
   1. Nama lengkap
   2. Asal gereja
   3. Tahu acara dari mana
   4. Email
   5. Nomor WhatsApp
5. **Submit** — data dikirim via POST dengan CSRF token.
6. **Pemberitahuan berhasil** — halaman `/success.php` menampilkan pesan:
   > Pendaftaran berhasil. Barcode pendaftaran akan dikirim melalui email dan wajib ditunjukkan saat check-in.

## Urutan Proses Server

```
POST /register.php
  -> Verifikasi CSRF token
  -> Trim + sanitasi input
  -> Validasi server-side (lihat 15-validation.md)
  -> Cek duplikasi email
  -> Generate kode unik peserta (EVT-000001)
  -> INSERT ke tabel peserta (status = 'belum_hadir')
  -> Generate barcode Code 128 dari kode unik
  -> Kirim email berisi barcode (lihat 09-email-service.md)
  -> Update email_sent_at bila pengiriman sukses
  -> Redirect ke /success.php
```

## Aturan Penting

- Barcode **tidak ditampilkan** di halaman success. Peserta hanya diberi tahu bahwa barcode dikirim ke email.
- Jika pengiriman email gagal, pendaftaran **tetap tersimpan**. Peserta diberi pesan agar menghubungi panitia, dan admin dapat mengirim ulang barcode dari dashboard.
- Satu email hanya boleh terdaftar satu kali.
- Redirect setelah submit menggunakan pola POST-Redirect-GET untuk mencegah double submit.

## Field Form

| Field | Label | Tipe | Wajib |
|---|---|---|---|
| `nama` | Nama Lengkap | text | Ya |
| `gereja` | Asal Gereja | text | Ya |
| `info_dari` | Tahu Acara Ini Dari Mana | select + opsi "Lainnya" | Ya |
| `email` | Email | email | Ya |
| `whatsapp` | Nomor WhatsApp | tel | Ya |

Opsi `info_dari`: Teman, Media Sosial, Gereja, Poster/Flyer, Lainnya.