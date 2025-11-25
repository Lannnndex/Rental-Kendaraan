-- Migration: add payment columns to rental table if missing
ALTER TABLE rental
  ADD COLUMN IF NOT EXISTS jumlah_bayar DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS tanggal_bayar DATETIME DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS metode_bayar ENUM('tunai','kartu','transfer') DEFAULT NULL;

-- Some MySQL versions do not support IF NOT EXISTS for ADD COLUMN; alternative (run only if needed):
-- ALTER TABLE rental ADD COLUMN jumlah_bayar DECIMAL(12,2) DEFAULT NULL;
-- ALTER TABLE rental ADD COLUMN tanggal_bayar DATETIME DEFAULT NULL;
-- ALTER TABLE rental ADD COLUMN metode_bayar ENUM('tunai','kartu','transfer') DEFAULT NULL;
