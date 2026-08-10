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
    send-barcode.php    Proses kirim ulang barcode
    logout.php          Destroy session
/config
    database.php        Koneksi MySQL
    smtp.php            Kredensial SMTP
/includes
    functions.php       Helper umum
    auth.php            Middleware session admin
    mailer.php          Service pengiriman email
    barcode.php         Service pembuatan barcode Code 128
    header.php          Layout atas
    footer.php          Layout bawah
/assets
    css/
        print-gelang.css  Gaya khusus cetak gelang
    js/
    barcode/            Hasil generate barcode peserta
/vendor                 PHPMailer + barcode generator
/database.sql           Skema tabel
```

Logika bisnis diletakkan di `/includes`, halaman hanya menangani alur dan tampilan.