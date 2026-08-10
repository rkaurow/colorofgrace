-- =====================================================
-- Migrasi: tambah fitur approval (kuota 150)
--
-- Jalankan HANYA jika database sudah pernah dibuat
-- dengan versi database.sql sebelumnya.
--
-- Untuk database baru, cukup impor database.sql
-- (sudah termasuk semua perubahan ini).
--
-- Cara pakai:
--   mysql -u coiuser -p coiministry < migrasi-approval.sql
-- =====================================================

ALTER TABLE peserta
  ADD COLUMN status_acc ENUM('pending','diterima','ditolak')
      NOT NULL DEFAULT 'pending' AFTER whatsapp,
  ADD COLUMN acc_at DATETIME NULL AFTER status_acc,
  ADD COLUMN acc_oleh VARCHAR(50) NULL AFTER acc_at,
  ADD COLUMN catatan_acc VARCHAR(255) NULL AFTER acc_oleh,
  ADD INDEX idx_status_acc (status_acc);

-- info_dari kini opsional
ALTER TABLE peserta MODIFY info_dari VARCHAR(100) NULL;

-- Peserta yang sudah terdaftar sebelum fitur ini ada
-- dianggap sudah disetujui, agar barcode mereka tetap sah.
UPDATE peserta
   SET status_acc = 'diterima',
       acc_at     = COALESCE(acc_at, created_at),
       acc_oleh   = 'migrasi'
 WHERE status_acc = 'pending'
   AND email_sent_at IS NOT NULL;
