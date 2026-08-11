# Folder Structure

```
/index.php              Landing page + tombol daftar
/register.php           Form pendaftaran + proses submit
/success.php            Notifikasi pendaftaran berhasil
/admin
    login.php           Login admin
    index.php           Dashboard statistik
    peserta.php         List peserta + aksi
    checkin.php         Scan tahap 1 (layar HP)
    masuk.php           Scan tahap 2 (gelang)
    scan-process.php    Endpoint validasi scan (JSON)
    cetak-gelang.php    Halaman siap cetak gelang
    send-barcode.php    Proses kirim ulang QR Code
    logout.php          Destroy session
/config
    database.php        Koneksi MySQL
    smtp.php            Kredensial SMTP
/includes
    functions.php       Helper umum
    auth.php            Middleware session admin
    mailer.php          Service pengiriman email
    qrcode.php          Service pembuatan QR Code
    header.php          Layout atas
    footer.php          Layout bawah
/assets
    css/
        print-gelang.css  Gaya khusus cetak gelang
    js/
    qr/                 Hasil generate QR Code peserta
/vendor                 PHPMailer + QR Code generator
/database.sql           Skema tabel
```

Logika bisnis diletakkan di `/includes`, halaman hanya menangani alur dan tampilan.