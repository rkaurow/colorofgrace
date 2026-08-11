# Database

Dua tabel: `admin` dan `peserta`.

## Tabel `peserta`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT AUTO_INCREMENT | Primary key |
| `kode` | VARCHAR(20) UNIQUE | Kode peserta di dalam QR Code, format `EVT-000001` |
| `nama` | VARCHAR(100) | Nama lengkap |
| `gereja` | VARCHAR(150) | Asal gereja |
| `info_dari` | VARCHAR(100) | Tahu acara dari mana |
| `email` | VARCHAR(150) UNIQUE | Email peserta |
| `whatsapp` | VARCHAR(20) | Nomor WhatsApp |
| `status` | ENUM('belum_hadir','hadir') | Tahap 1. Default `belum_hadir` |
| `checkin_at` | DATETIME NULL | Waktu daftar ulang (tahap 1) |
| `gelang_dicetak` | TINYINT(1) | Default 0, jadi 1 setelah gelang dicetak |
| `gelang_dicetak_at` | DATETIME NULL | Waktu gelang dicetak |
| `status_masuk` | ENUM('belum_masuk','masuk') | Tahap 2. Default `belum_masuk` |
| `masuk_at` | DATETIME NULL | Waktu masuk ruangan (tahap 2) |
| `email_sent_at` | DATETIME NULL | Waktu QR Code terakhir dikirim |
| `created_at` | TIMESTAMP | Default `CURRENT_TIMESTAMP` |

### Dua Tahap Kehadiran

Peserta discan dua kali dengan kode yang sama:

| Tahap | Media | Kolom Status | Kolom Waktu |
|---|---|---|---|
| 1. Daftar ulang | QR Code di layar HP / email | `status` | `checkin_at` |
| 2. Masuk ruangan | QR Code di gelang | `status_masuk` | `masuk_at` |

Gelang dicetak di antara kedua tahap, setelah daftar ulang berhasil.

## Tabel `admin`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT AUTO_INCREMENT | Primary key |
| `username` | VARCHAR(50) UNIQUE | Username login |
| `password` | VARCHAR(255) | Hash dari `password_hash()` |
| `created_at` | TIMESTAMP | Default `CURRENT_TIMESTAMP` |

## DDL

```sql
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE peserta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  gereja VARCHAR(150) NOT NULL,
  info_dari VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  whatsapp VARCHAR(20) NOT NULL,
  status ENUM('belum_hadir','hadir') NOT NULL DEFAULT 'belum_hadir',
  checkin_at DATETIME NULL,
  gelang_dicetak TINYINT(1) NOT NULL DEFAULT 0,
  gelang_dicetak_at DATETIME NULL,
  status_masuk ENUM('belum_masuk','masuk') NOT NULL DEFAULT 'belum_masuk',
  masuk_at DATETIME NULL,
  email_sent_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_status_masuk (status_masuk),
  INDEX idx_email (email),
  INDEX idx_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Admin awal dibuat lewat script seeder terpisah agar hash password tidak ditulis manual di `database.sql`.