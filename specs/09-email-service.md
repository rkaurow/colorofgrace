# Email Service

Menggunakan PHPMailer dengan SMTP (Gmail App Password atau Brevo). Konfigurasi disimpan di `/config/smtp.php`.

## Pemicu Pengiriman

1. **Otomatis** — setelah pendaftaran berhasil disimpan.
2. **Manual** — saat admin menekan tombol **Kirim Ulang QR** di list peserta atau halaman approval.

Keduanya memanggil fungsi yang sama, yaitu `kirim_qr_peserta($peserta)`.

## Isi Email

- Subjek: `QR Code Pendaftaran - <Nama Acara>`
- Sapaan dengan nama peserta
- Detail pendaftaran: nama, asal gereja, email, WhatsApp, kode peserta
- **Gambar QR Code** ditanam sebagai inline attachment (CID), bukan link eksternal
- QR Code dikirim sebagai file PNG agar mudah disimpan di galeri
- **Kode peserta ditulis sebagai teks** di bawah gambar, sebagai cadangan bila pemindaian gagal
- Instruksi: QR Code wajib ditunjukkan kepada panitia saat check-in
- Saran agar peserta menaikkan kecerahan layar atau mencetak QR Code
- Template HTML responsif dengan fallback teks biasa

## Aturan Tampilan QR Code di Email

QR Code harus tetap tajam dan memiliki quiet zone yang cukup, sehingga:

- Gambar tidak boleh diperkecil berlebihan oleh CSS. Gunakan ukuran asli dengan `max-width` yang cukup besar.
- Latar belakang di sekitar QR Code harus putih polos, menjaga quiet zone tetap bersih.
- Hindari border, bayangan, atau warna latar pada container QR Code.

## Penanganan Kegagalan

- Kegagalan SMTP **tidak boleh** membatalkan pendaftaran yang sudah tersimpan.
- Error dicatat ke log, peserta melihat pesan ramah untuk menghubungi panitia.
- Fungsi mengembalikan boolean sukses/gagal agar pemanggil dapat menampilkan alert yang sesuai.
- Bila sukses, `email_sent_at` diperbarui ke waktu sekarang.
- Kredensial SMTP tidak pernah ditampilkan di pesan error yang dilihat pengguna.