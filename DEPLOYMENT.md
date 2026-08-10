# Panduan Deploy ke VPS

Target: Ubuntu 22.04 / 24.04 (Debian 12 sama saja).
Ganti `coiministry.com` dengan domain aslimu di semua perintah.

---

## 0. Cek server

```bash
ssh root@IP_SERVER
cat /etc/os-release | head -2
```

Catat versinya. Kalau bukan Ubuntu/Debian, kabari — perintahnya beda.

---

## 1. Update & pasang paket

```bash
apt update && apt upgrade -y
apt install -y nginx mysql-server php8.3-fpm php8.3-mysql php8.3-gd php8.3-mbstring php8.3-curl unzip
```

Kalau `php8.3` tidak ditemukan, coba `php8.2` atau `php8.1` — semua kompatibel.

**Cek:**
```bash
php -v && php -m | grep -E "^(gd|pdo_mysql)$"
```

Harus muncul `gd` dan `pdo_mysql`. Tanpa `gd`, barcode tidak bisa dibuat.

---

## 2. Amankan MySQL

```bash
mysql_secure_installation
```

Jawaban yang disarankan:
- Validate password plugin: `n`
- Set root password: `y` → **buat password kuat, simpan baik-baik**
- Remove anonymous users: `y`
- Disallow root login remotely: `y`
- Remove test database: `y`
- Reload privileges: `y`

---

## 3. Buat database & user khusus

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

## 4. Upload file

Dari **Mac kamu** (bukan server), di terminal baru:

```bash
cd ~/Developer/coiministry
rsync -avz --exclude '.git' --exclude 'assets/barcode/*.png' \
  ./ root@IP_SERVER:/var/www/coiministry/
```

Kalau `rsync` tidak ada, pakai `scp -r ./ root@IP_SERVER:/var/www/coiministry/`.

---

## 5. Atur izin folder

Kembali ke **server**:

```bash
chown -R www-data:www-data /var/www/coiministry
chmod -R 755 /var/www/coiministry
chmod -R 775 /var/www/coiministry/assets/barcode
```

Folder `assets/barcode` harus bisa ditulis, karena barcode di-generate saat pendaftaran.

---

## 6. Impor database

```bash
cd /var/www/coiministry
mysql -u coiuser -p coiministry < database.sql
mysql -u coiuser -p coiministry -e "SHOW TABLES;"
```

Harus muncul `admin` dan `peserta`.

---

## 7. Sesuaikan konfigurasi

```bash
nano /var/www/coiministry/config/database.php
```

Ubah jadi:
```php
define('DB_USER', 'coiuser');
define('DB_PASS', 'PASSWORD_YANG_TADI');
```

Lalu matikan debug:
```bash
nano /var/www/coiministry/config/config.php
```
```php
define('DEBUG_MODE', false);
```

> `DEBUG_MODE` yang menyala di server publik akan membocorkan struktur database saat terjadi error.

---

## 8. Konfigurasi Nginx

```bash
nano /etc/nginx/sites-available/coiministry
```

Isi dengan:

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
    }

    # Blokir akses ke folder config
    location ^~ /config/ {
        deny all;
        return 404;
    }

    # Cegah eksekusi PHP di folder barcode
    location ~ ^/assets/barcode/.*\.php$ {
        deny all;
        return 404;
    }

    location ~ /\.ht {
        deny all;
    }

    client_max_body_size 8M;
}
```

> Sesuaikan `php8.3-fpm.sock` dengan versi PHP-mu. Cek dengan `ls /run/php/`.

Aktifkan:
```bash
ln -s /etc/nginx/sites-available/coiministry /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
```

`nginx -t` harus bilang `syntax is ok`.

---

## 9. Arahkan domain

Di panel domainmu, buat A record:

| Type | Name | Value |
|---|---|---|
| A | `@` | IP_SERVER |
| A | `www` | IP_SERVER |

Tunggu propagasi (5–30 menit). Cek:
```bash
dig +short coiministry.com
```

Harus keluar IP servermu.

---

## 10. Pasang SSL (HTTPS)

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d coiministry.com -d www.coiministry.com
```

Pilih opsi redirect HTTP → HTTPS saat ditanya.

> Wajib. Tanpa HTTPS, browser menandai form pendaftaran sebagai "Not Secure" dan orang enggan mengisi data.

---

## 11. Buat akun admin

Buka: `https://coiministry.com/install.php`

Isi username & password. Lalu **hapus segera**:

```bash
rm /var/www/coiministry/install.php
```

> Kalau dibiarkan, siapa pun yang menemukan URL itu bisa menimpa password adminmu.

---

## 12. Aktifkan email

```bash
nano /var/www/coiministry/config/smtp.php
```

```php
define('MAIL_ENABLED', true);
define('SMTP_USER', 'emailacara@gmail.com');
define('SMTP_PASS', 'app-password-16-karakter');
```

Butuh **App Password** Gmail (bukan password biasa):
1. Aktifkan verifikasi 2 langkah di akun Google
2. Buka myaccount.google.com/apppasswords
3. Buat password baru, salin 16 karakternya

Pasang PHPMailer agar email tidak masuk spam:
```bash
cd /var/www/coiministry
apt install -y composer
composer require phpmailer/phpmailer
chown -R www-data:www-data vendor
```

---

## 13. Uji sebelum sebar link

| # | Yang dites | Cara |
|---|---|---|
| 1 | Pendaftaran | Daftar pakai email pribadimu di `/register.php` |
| 2 | Email masuk | Cek inbox — barcode harus terlampir |
| 3 | Login admin | `/admin/login.php` |
| 4 | Data tampil | `/admin/peserta.php` |
| 5 | Scan tahap 1 | `/admin/checkin.php` → ketik kode + Enter |
| 6 | Cetak gelang | Klik tombol Cetak Gelang |
| 7 | Scan tahap 2 | `/admin/masuk.php` → kode yang sama + Enter |

Mengetik kode manual = identik dengan hasil scan, karena scanner barcode bekerja seperti keyboard.

---

## 14. Backup otomatis

Data peserta tidak boleh hilang. Pasang backup harian:

```bash
mkdir -p /root/backup
crontab -e
```

Tambahkan:
```
0 2 * * * mysqldump -u coiuser -pPASSWORD coiministry > /root/backup/coi-$(date +\%F).sql
```

Sebelum hari-H, **unduh salinannya ke laptop**:
```bash
scp root@IP_SERVER:/root/backup/*.sql ~/Downloads/
```

---

## Checklist sebelum sebar link pendaftaran

- [ ] HTTPS aktif (gembok hijau di browser)
- [ ] `install.php` sudah dihapus
- [ ] `DEBUG_MODE` = `false`
- [ ] Email uji benar-benar diterima
- [ ] Barcode di email bisa dipindai
- [ ] Backup berjalan

---

## Checklist hari-H

- [ ] Scanner meja registrasi tipe **image/2D** (laser tidak bisa baca layar HP)
- [ ] Sudah cetak 1 gelang uji dan berhasil dipindai
- [ ] Printer gelang siap, stok gelang cukup
- [ ] Petugas tahu: hijau = registrasi, ungu = pintu masuk
- [ ] Ada laptop cadangan yang sudah login admin
