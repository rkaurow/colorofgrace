# Email Service

Menggunakan PHPMailer dengan SMTP (Gmail App Password atau Brevo). Konfigurasi disimpan di `/config/smtp.php`.

## Pemicu Pengiriman

1. **Otomatis** — setelah pendaftaran berhasil disimpan.
2. **Manual** — saat admin menekan tombol **Kirim Barcode** di list peserta.

Keduanya memanggil fungsi yang sama, misalnya `kirimBarcodePeserta($peserta)`.

## Isi Email

- Subjek: `Barcode Pendaftaran - <Nama Acara>`
- Sapaan dengan nama peserta
- Detail pendaftaran: nama, asal gereja, email, WhatsApp, kode peserta
- **Gambar barcode Code 128** ditanam sebagai inline attachment (CID), bukan link eksternal
- Barcode juga dilampirkan sebagai file PNG agar mudah disimpan di galeri
- **Kode peserta ditulis sebagai teks** di bawah gambar, sebagai cadangan bila pemindaian gagal
- Instruksi: barcode wajib ditunjukkan kepada panitia saat check-in
- Saran agar peserta menaikkan kecerahan layar atau mencetak barcode
- Template HTML responsif dengan fallback teks biasa

## Aturan Tampilan Barcode di Email

Barcode 1D lebih sensitif terhadap distorsi dibanding QR, sehingga:

- Gambar tidak boleh diperkecil oleh CSS. Gunakan lebar asli dengan `max-width` yang cukup besar.
- Latar belakang di sekitar barcode harus putih polos, menjaga quiet zone tetap bersih.
- Hindari border, bayangan, atau warna latar pada container barcode.

## Penanganan Kegagalan

- Kegagalan SMTP **tidak boleh** membatalkan pendaftaran yang sudah tersimpan.
- Error dicatat ke log, peserta melihat pesan ramah untuk menghubungi panitia.
- Fungsi mengembalikan boolean sukses/gagal agar pemanggil dapat menampilkan alert yang sesuai.
- Bila sukses, `email_sent_at` diperbarui ke waktu sekarang.
- Kredensial SMTP tidak pernah ditampilkan di pesan error yang dilihat pengguna.