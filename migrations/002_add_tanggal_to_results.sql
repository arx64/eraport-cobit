-- ============================================================
-- Migration: 002_add_tanggal_to_results.sql
-- Tujuan   : Hasil analisis juga ikut berbasis tanggal,
--            sehingga dashboard/analisis/laporan bisa tampil
--            berdasarkan hari yang dipilih.
--
-- CATATAN  : IDEMPOTENT. Aman dijalankan berulang.
-- ============================================================

-- 0. Tambah kolom tanggal_penilaian jika belum ada
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'results'
      AND COLUMN_NAME = 'tanggal_penilaian');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE results ADD COLUMN tanggal_penilaian DATE NULL AFTER process_id',
    'SELECT "kolom tanggal_penilaian sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1. Backfill dari DATE(created_at) untuk data lama
UPDATE results
    SET tanggal_penilaian = DATE(created_at)
    WHERE tanggal_penilaian IS NULL;

-- 2. Tambah index penampung FK (supaya unique_result bisa di-drop)
SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'results'
      AND INDEX_NAME = 'idx_res_respondent');

SET @sql = IF(@idx = 0,
    'ALTER TABLE results ADD INDEX idx_res_respondent (respondent_id)',
    'SELECT "idx_res_respondent sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'results'
      AND INDEX_NAME = 'idx_res_process');

SET @sql = IF(@idx = 0,
    'ALTER TABLE results ADD INDEX idx_res_process (process_id)',
    'SELECT "idx_res_process sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Hapus unique_result lama
SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'results'
      AND INDEX_NAME = 'unique_result');

SET @sql = IF(@idx > 0,
    'ALTER TABLE results DROP INDEX unique_result',
    'SELECT "unique_result sudah terhapus" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4. Tambah unique_result_daily
SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'results'
      AND INDEX_NAME = 'unique_result_daily');

SET @sql = IF(@idx = 0,
    'ALTER TABLE results ADD UNIQUE KEY unique_result_daily (respondent_id, process_id, tanggal_penilaian)',
    'SELECT "unique_result_daily sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5. Tambah index tanggal
SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'results'
      AND INDEX_NAME = 'idx_res_tanggal');

SET @sql = IF(@idx = 0,
    'ALTER TABLE results ADD INDEX idx_res_tanggal (tanggal_penilaian)',
    'SELECT "idx_res_tanggal sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 6. Set NOT NULL
SET @nullable = (SELECT IS_NULLABLE FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'results'
      AND COLUMN_NAME = 'tanggal_penilaian');

SET @sql = IF(@nullable = 'YES',
    'ALTER TABLE results MODIFY COLUMN tanggal_penilaian DATE NOT NULL',
    'SELECT "tanggal_penilaian sudah NOT NULL" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7. Verifikasi
SELECT 'Migration selesai. Struktur index saat ini:' AS status;
SHOW INDEX FROM results;
