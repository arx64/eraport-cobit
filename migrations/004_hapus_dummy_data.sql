-- ============================================================
-- Hapus Dummy Data Penilaian
-- Tujuan : Membersihkan data dummy agar bisa testing ulang
-- ============================================================

-- ============================================================
-- OPSI 1: Hapus HANYA data Juni 2026 (dari migration 003)
-- Cocok jika ingin reset data dummy terbaru saja,
-- data asli/riwayat sebelumnya tetap aman.
-- ============================================================
DELETE FROM assessment_answers
    WHERE tanggal_penilaian BETWEEN '2026-06-01' AND '2026-06-30';

DELETE FROM results
    WHERE tanggal_penilaian BETWEEN '2026-06-01' AND '2026-06-30';

-- ============================================================
-- OPSI 2: Hapus SEMUA data penilaian + hasil analisis
--         (KECUALI data dari seed bawaan 2024)
-- Hapus semua data yang tanggalnya >= 2025
-- ============================================================
-- DELETE FROM assessment_answers
--     WHERE tanggal_penilaian >= '2025-01-01';
--
-- DELETE FROM results
--     WHERE tanggal_penilaian >= '2025-01-01';

-- ============================================================
-- OPSI 3: Hapus TOTAL seluruh data penilaian & hasil
--         (HATI-HATI: menghapus SEMUA data assessment)
-- ============================================================
-- TRUNCATE TABLE assessment_answers;
-- TRUNCATE TABLE results;

-- ============================================================
-- Verifikasi sisa data
-- ============================================================
SELECT 'Sisa data assessment_answers:' AS info;
SELECT COUNT(*) AS total FROM assessment_answers;

SELECT 'Sisa data results:' AS info;
SELECT COUNT(*) AS total FROM results;

SELECT 'Daftar tanggal yang masih punya data:' AS info;
SELECT DISTINCT tanggal_penilaian AS tanggal
FROM (
    SELECT tanggal_penilaian FROM assessment_answers
    UNION
    SELECT tanggal_penilaian FROM results
) AS d
WHERE tanggal_penilaian IS NOT NULL
ORDER BY tanggal DESC;
