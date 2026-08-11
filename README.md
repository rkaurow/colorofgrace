# COI Ministry — Sistem Registrasi & Check-in Acara

Aplikasi pendaftaran peserta dengan QR Code dan check-in dua tahap untuk acara **Color of Grace — 22 Agustus 2026**.

---

## Alur Sistem

```
Peserta daftar online
        ↓
QR Code dikirim ke email
        ↓
TAHAP 1 — Daftar ulang   (scan QR Code dari LAYAR HP)   → status = hadir
        ↓
Cetak gelang berisi QR Code yang sama
        ↓
TAHAP 2 — Masuk ruangan  (scan QR Code dari GELANG)     → status_masuk = masuk
```

Peserta yang belum menyelesaikan tahap 1 **ditolak** di tahap 2 dan diarahkan kembali ke meja registrasi.

---

## Kebutuhan Server

| Komponen | Versi Minimum | Keterangan |
|---|---|---|
| PHP | 8.0+ | Disarankan 8.2 atau 8.3 |
| Ekstensi `gd` | — | **Wajib** — untuk generate gambar QR Code |
| Ekstensi `pdo_mysql` | — | **Wajib** — koneksi database |
| Ekstensi `mbstring` | — | Wajib |
| Ekstensi `curl` | — | Wajib untuk pengiriman email SMTP |
| MySQL / MariaDB | 5.7+ / 10.3+ | — |
| Web server | Nginx / Apache | Nginx direkomendasikan |

---

## Instalasi di VPS (Ubuntu 22.04 / 24.04)

### 0. Masuk ke server

```bash
ssh root@IP_SERVER
```

### 1. Update sistem & pasang semua paket

```bash
apt update && apt upgrade -y

apt install -y \
  nginx \
  mysql-server \
  php8.3-fpm \
  php8.3-mysql \
  php8.3-gd \
  php8.3-mbstring \
  php8.3-curl \
  php8.3-xml \
  php8.3-zip \
  unzip \
  git \
  composer
```

> Jika `php8.3` tidak tersedia, ganti dengan `php8.2` atau `php8.1` — semua kompatibel.

**Verifikasi ekstensi wajib:**

```bash
php -v
php -m | grep -E "^(gd|pdo_mysql|mbstring|curl)$"
```

Harus muncul keempat baris: `gd`, `pdo_mysql`, `mbstring`, `curl`.  
Tanpa `gd`, QR Code tidak bisa dibuat.

**Aktifkan dan jalankan layanan:**

```bash
systemctl enable nginx mysql php8.3-fpm
systemctl start nginx mysql php8.3-fpm
```

---

### 2. Amankan MySQL

```bash
mysql_secure_installation
```

Jawaban yang disarankan:

| Pertanyaan | Jawaban |
|---|---|
| Validate password plugin | `n` |
| Set root password | `y` → buat password kuat, simpan baik-baik |
| Remove anonymous users | `y` |
| Disallow root login remotely | `y` |
| Remove test database | `y` |
| Reload privileges | `y` |

---

### 3. Buat database & user khusus

Jangan pakai `root` untuk aplikasi. Buat user terbatas:

```bash
mysql -u root -p
```

Di dalam MySQL:

```sql
CREATE DATABASE coiministry CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'coiuser'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_KUAT_DISINI';
GRANT ALL PRIVILEGES ON coiministry.* TO 'coiuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

### 4. Upload file ke server

Dari **komputer lokal** (bukan server), jalankan di terminal:

```bash
cd ~/Developer/coiministry

rsync -avz \
  --exclude '.git' \
  --exclude 'assets/qr/*.png' \
  --exclude 'graphify-out/' \
  ./ root@IP_SERVER:/var/www/coiministry/
```

Atau dengan `scp` jika `rsync` tidak tersedia:

```bash
scp -r ./ root@IP_SERVER:/var/www/coiministry/
```

---

### 5. Atur izin folder

Kembali ke **server**:

```bash
chown -R www-data:www-data /var/www/coiministry
chmod -R 755 /var/www/coiministry
chmod -R 775 /var/www/coiministry/assets/qr
```

Folder `assets/qr` harus bisa ditulis karena QR Code di-generate saat email dikirim.

---

### 6. Impor skema database

```bash
cd /var/www/coiministry
mysql -u coiuser -p coiministry < database.sql
```

Verifikasi tabel berhasil dibuat:

```bash
mysql -u coiuser -p coiministry -e "SHOW TABLES;"
```

Harus muncul tabel `admin` dan `peserta`.

---

### 7. Sesuaikan konfigurasi

#### a. Koneksi database — `config/database.php`

```bash
nano /var/www/coiministry/config/database.php
```

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'coiministry');
define('DB_USER', 'coiuser');
define('DB_PASS', 'PASSWORD_YANG_TADI');
```

#### b. Detail acara — `config/config.php`

```bash
nano /var/www/coiministry/config/config.php
```

Ubah sesuai kebutuhan:

```php
define('APP_NAME',        'COI Ministry');
define('EVENT_NAME',      'Color of Grace');
define('EVENT_SUBTITLE',  'One Truth Way');
define('EVENT_TAGLINE',   'Festival of Unity & Faith');
define('EVENT_DATE',      '2026-08-22');
define('EVENT_DATE_TEXT', '22 Agustus 2026');
define('EVENT_TIME',      'Open Gate 14.00 WITA');
define('EVENT_LOCATION',  'Royal Phoenix Restaurant');
define('EVENT_ADDRESS',   'Lantai 2, Royal Phoenix Restaurant');
define('CONTACT_WA',      '6281234567890');

// Wajib dimatikan di server produksi
define('DEBUG_MODE', false);
```

#### c. Email SMTP — `config/smtp.php`

```bash
nano /var/www/coiministry/config/smtp.php
```

```php
define('MAIL_ENABLED',   true);
define('SMTP_HOST',      'smtp.gmail.com');
define('SMTP_PORT',      587);
define('SMTP_SECURE',    'tls');
define('SMTP_USER',      'emailkamu@gmail.com');
define('SMTP_PASS',      'app-password-16-karakter');  // Gmail App Password
define('MAIL_FROM',      'emailkamu@gmail.com');
define('MAIL_FROM_NAME', 'COI Ministry');
```

> **Gmail:** aktifkan verifikasi 2 langkah → buat **App Password** 16 karakter di https://myaccount.google.com/apppasswords

---

### 8. Pasang dependency email dan QR Code dengan Composer

Karena server menggunakan Ubuntu, pasang Composer dari repository Ubuntu:

```bash
apt install -y composer
composer --version
```

Setelah `composer.json` dan `composer.lock` ikut ter-upload ke server, pasang dependency project:

```bash
cd /var/www/coiministry
composer install --no-dev --optimize-autoloader
```

Verifikasi PHPMailer dan library QR Code berhasil dimuat:

```bash
test -f vendor/autoload.php && echo "Composer autoload OK"
php -r "require 'vendor/autoload.php'; exit(class_exists('PHPMailer\\\\PHPMailer\\\\PHPMailer') ? 0 : 1);"
php -r "require 'vendor/autoload.php'; exit(class_exists('chillerlan\\\\QRCode\\\\QRCode') ? 0 : 1);"
```

Jika `composer.json` belum ada di server, jalankan sekali:

```bash
cd /var/www/coiministry
composer require phpmailer/phpmailer chillerlan/php-qrcode:^4.4
```

> Generator QR Code memakai `chillerlan/php-qrcode` dan membutuhkan ekstensi `gd` serta `mbstring`.

---

### 9. Konfigurasi Nginx

```bash
nano /etc/nginx/sites-available/coiministry
```

Isi dengan (ganti `coiministry.com` dengan domain aslimu):

```nginx
server {
    listen 80;
    server_name coiministry.com www.coiministry.com;
    root /var/www/coiministry;
    index index.php;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    # Blokir akses langsung ke folder config
    location ^~ /config/ {
        deny all;
        return 404;
    }

    # Cegah eksekusi PHP di folder QR Code
    location ~ ^/assets/qr/.*\.php$ {
        deny all;
        return 404;
    }

    location ~ /\.ht {
        deny all;
    }

    client_max_body_size 8M;
}
```

> Sesuaikan `php8.3-fpm.sock` dengan versi PHP-mu. Cek dengan: `ls /run/php/`

Aktifkan konfigurasi:

```bash
ln -s /etc/nginx/sites-available/coiministry /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
```

`nginx -t` harus menampilkan `syntax is ok`.

---

### 10. Arahkan domain

Di panel DNS domainmu, buat A record:

| Type | Name | Value |
|---|---|---|
| A | `@` | IP_SERVER |
| A | `www` | IP_SERVER |

Propagasi DNS bisa memakan waktu hingga 24 jam.

---

### 11. Pasang SSL (HTTPS) — Gratis dengan Let's Encrypt

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d coiministry.com -d www.coiministry.com
```

Ikuti instruksi di layar. Certbot akan otomatis memperbarui konfigurasi Nginx untuk HTTPS.

Verifikasi auto-renewal:

```bash
certbot renew --dry-run
```

---

### 12. Buat akun admin

Buka di browser:

```
https://coiministry.com/install.php
```

Isi username dan password admin. **Hapus `install.php` segera setelah selesai:**

```bash
rm /var/www/coiministry/install.php
```

---

### 13. Verifikasi akhir

```bash
# Cek PHP dan ekstensi
php -m | grep -E "^(gd|pdo_mysql|mbstring|curl)$"

# Cek Nginx berjalan
systemctl status nginx

# Cek PHP-FPM berjalan
systemctl status php8.3-fpm

# Cek MySQL berjalan
systemctl status mysql

# Cek izin folder QR Code
ls -la /var/www/coiministry/assets/qr/

# Cek log error jika ada masalah
tail -f /var/log/nginx/error.log
```

---

## Instalasi Lokal (Pengembangan)

### Kebutuhan

- PHP 8.0+ dengan ekstensi `gd`, `pdo_mysql`, `mbstring`
- MySQL 5.7+ atau MariaDB 10.3+

### Langkah

```bash
# 1. Clone atau letakkan folder di web root
cd /var/www/html   # atau htdocs untuk XAMPP

# 2. Buat database
mysql -u root -p -e "CREATE DATABASE coiministry CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p coiministry < database.sql

# 3. Sesuaikan config/database.php
#    DB_USER = 'root', DB_PASS = '' (default XAMPP/MAMP)

# 4. Buka installer di browser
#    http://localhost/coiministry/install.php

# 5. Hapus install.php setelah membuat akun admin
```

Cek ekstensi GD tersedia:

```bash
php -m | grep gd
```

---

## Struktur Folder

```
index.php               Landing page
register.php            Formulir + proses pendaftaran
success.php             Konfirmasi pendaftaran
install.php             Installer (hapus setelah dipakai!)
database.sql            Skema database

config/
  config.php            Detail acara & konstanta
  database.php          Koneksi MySQL
  smtp.php              Kredensial email

includes/
  functions.php         Helper umum, CSRF, format
  auth.php              Login & proteksi halaman admin
  qrcode.php            Generator QR Code berbasis Composer
  mailer.php            Pengiriman email
  scan-ui.php           Komponen halaman scan
  header.php            Navigasi admin
  footer.php

admin/
  login.php             Login admin
  index.php             Dashboard statistik
  peserta.php           Tabel peserta + aksi
  approval.php          Persetujuan pendaftaran
  checkin.php           TAHAP 1 — scan layar HP
  scan-process.php      Endpoint validasi scan (JSON)
  cari-peserta.php      Pencarian nama
  send-barcode.php      Kirim ulang QR Code
  export.php            Export CSV
  logout.php

assets/
  img/                  Logo dan gambar acara
  qr/                   Hasil generate QR Code (ditulis otomatis)
```

---

## Catatan Perangkat Keras

### Scanner

> **Penting:** scanner laser umumnya **tidak dapat membaca dari layar HP**, karena layar memancarkan cahaya alih-alih memantulkannya.

| Tahap | Media | Scanner yang cocok |
|---|---|---|
| 1 — Daftar ulang | Layar HP | **Wajib** tipe *image/2D* |
| 2 — Masuk ruangan | Gelang | Laser atau image, keduanya bisa |

Scanner harus dalam mode **keyboard wedge (HID)** — mengetik kode lalu menekan Enter.

---

## Panduan Petugas

**Meja registrasi** — buka `admin/checkin.php`
1. Pastikan kolom scan aktif. Bila muncul peringatan, klik kolomnya.
2. Pindai QR Code dari layar HP peserta.
3. Hijau = berhasil, kuning = sudah pernah daftar ulang, merah = tidak valid.

**Pintu masuk** — buka `admin/masuk.php`
1. Pindai QR Code pada gelang peserta.
2. Bila muncul **"Belum daftar ulang"**, arahkan peserta ke meja registrasi.

Bila peserta lupa membawa QR Code, gunakan **pencarian nama** di panel admin.

---

## Keamanan

- Seluruh query memakai *prepared statement*
- Password disimpan dengan `password_hash()` (bcrypt)
- Token CSRF pada semua form POST
- `session_regenerate_id()` setelah login
- Login dikunci 5 menit setelah 5 percobaan gagal
- Semua output di-escape dengan `htmlspecialchars()`
- Folder `config/` dan `assets/qr/` dilindungi konfigurasi web server

**Checklist sebelum go-live:**

- [ ] Hapus `install.php`
- [ ] Set `DEBUG_MODE` ke `false` di `config/config.php`
- [ ] Gunakan password admin yang kuat
- [ ] Pastikan HTTPS aktif (SSL terpasang)
- [ ] Verifikasi email terkirim dengan mengirim test pendaftaran
