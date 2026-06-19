-- ============================================================
-- Migration: 001_add_tanggal_penilaian.sql
-- Tujuan   : Mendukung penilaian berbasis tanggal (per hari)
--             Setiap hari = baris baru, data lampau tetap tersimpan
--
-- CATATAN  : Script ini IDEMPOTENT. Aman dijalankan berulang
--            dan dari state migration yang gagal sebagian.
-- ============================================================

-- 0. Cek state: tambah kolom tanggal_penilaian jika belum ada
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assessment_answers'
      AND COLUMN_NAME = 'tanggal_penilaian');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE assessment_answers ADD COLUMN tanggal_penilaian DATE NULL AFTER question_id',
    'SELECT "kolom tanggal_penilaian sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1. Backfill data lama dari DATE(created_at)
UPDATE assessment_answers
    SET tanggal_penilaian = DATE(created_at)
    WHERE tanggal_penilaian IS NULL;

-- 2. Tambah index penampung FK jika belum ada (supaya unique_answer bisa di-drop)
SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assessment_answers'
      AND INDEX_NAME = 'idx_aa_respondent');

SET @sql = IF(@idx = 0,
    'ALTER TABLE assessment_answers ADD INDEX idx_aa_respondent (respondent_id)',
    'SELECT "idx_aa_respondent sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assessment_answers'
      AND INDEX_NAME = 'idx_aa_question');

SET @sql = IF(@idx = 0,
    'ALTER TABLE assessment_answers ADD INDEX idx_aa_question (question_id)',
    'SELECT "idx_aa_question sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Hapus unique_answer lama jika masih ada
SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assessment_answers'
      AND INDEX_NAME = 'unique_answer');

SET @sql = IF(@idx > 0,
    'ALTER TABLE assessment_answers DROP INDEX unique_answer',
    'SELECT "unique_answer sudah terhapus" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4. Tambah unique_answer_daily jika belum ada
SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assessment_answers'
      AND INDEX_NAME = 'unique_answer_daily');

SET @sql = IF(@idx = 0,
    'ALTER TABLE assessment_answers ADD UNIQUE KEY unique_answer_daily (respondent_id, question_id, tanggal_penilaian)',
    'SELECT "unique_answer_daily sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5. Tambah index tanggal_penilaian jika belum ada
SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assessment_answers'
      AND INDEX_NAME = 'idx_tanggal_penilaian');

SET @sql = IF(@idx = 0,
    'ALTER TABLE assessment_answers ADD INDEX idx_tanggal_penilaian (tanggal_penilaian)',
    'SELECT "idx_tanggal_penilaian sudah ada" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 6. Set tanggal_penilaian jadi NOT NULL
SET @nullable = (SELECT IS_NULLABLE FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'assessment_answers'
      AND COLUMN_NAME = 'tanggal_penilaian');

SET @sql = IF(@nullable = 'YES',
    'ALTER TABLE assessment_answers MODIFY COLUMN tanggal_penilaian DATE NOT NULL',
    'SELECT "tanggal_penilaian sudah NOT NULL" AS info');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7. Verifikasi akhir
SELECT 'Migration selesai. Struktur index saat ini:' AS status;
SHOW INDEX FROM assessment_answers;
