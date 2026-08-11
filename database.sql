-- =====================================================
-- COI Ministry - Event Registration System
-- Skema Database
-- =====================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';

-- -----------------------------------------------------
-- Tabel: admin
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(100) NOT NULL DEFAULT 'Administrator',
  last_login DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Tabel: peserta
-- Dua tahap kehadiran:
--   Tahap 1 (daftar ulang) : status      + checkin_at
--   Tahap 2 (masuk ruangan): status_masuk + masuk_at
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS peserta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  gereja VARCHAR(150) NOT NULL,
  info_dari VARCHAR(100) NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  whatsapp VARCHAR(20) NOT NULL,

  -- Persetujuan admin (kuota terbatas)
  -- QR Code hanya dikirim setelah status_acc = 'diterima'
  status_acc ENUM('pending','diterima','ditolak') NOT NULL DEFAULT 'pending',
  acc_at DATETIME NULL,
  acc_oleh VARCHAR(50) NULL,
  catatan_acc VARCHAR(255) NULL,

  -- Tahap 1: daftar ulang (scan QR Code dari layar HP)
  status ENUM('belum_hadir','hadir') NOT NULL DEFAULT 'belum_hadir',
  checkin_at DATETIME NULL,

  -- Cetak gelang
  gelang_dicetak TINYINT(1) NOT NULL DEFAULT 0,
  gelang_dicetak_at DATETIME NULL,

  -- Tahap 2: masuk ruangan (scan QR Code dari gelang)
  status_masuk ENUM('belum_masuk','masuk') NOT NULL DEFAULT 'belum_masuk',
  masuk_at DATETIME NULL,

  -- Email
  email_sent_at DATETIME NULL,

  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  INDEX idx_status_acc (status_acc),
  INDEX idx_status (status),
  INDEX idx_status_masuk (status_masuk),
  INDEX idx_gelang (gelang_dicetak),
  INDEX idx_email (email),
  INDEX idx_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Admin default
--
-- Tabel ini sengaja dibiarkan KOSONG.
-- Hash password harus dibuat oleh PHP agar dijamin valid.
--
-- Setelah mengimpor file ini, buka di browser:
--   http://localhost/coiministry/install.php
--
-- Installer akan membuat admin pertama dengan password
-- yang kamu tentukan sendiri.
-- -----------------------------------------------------
