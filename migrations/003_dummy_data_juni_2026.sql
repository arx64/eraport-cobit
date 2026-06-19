-- ============================================================
-- Dummy Data Penilaian - Multi Tanggal (Juni 2026)
-- Tujuan : Mengisi database dengan data penilaian pada
--          6 tanggal berbeda untuk menguji date picker.
--
-- CATATAN : Jalankan SETELAH migration 001 & 002 sukses.
-- ============================================================

-- (Opsional) Bersihkan data dummy lama 2024 agar tampilan bersih
DELETE FROM assessment_answers
    WHERE tanggal_penilaian BETWEEN '2024-01-01' AND '2024-12-31';
DELETE FROM results
    WHERE tanggal_penilaian BETWEEN '2024-01-01' AND '2024-12-31';

-- ============================================================
-- 1. ASSESSMENT ANSWERS
-- ============================================================
-- Respondent 1 (Ahmad Rizky - Kepala Sekolah) - 2026-06-10
INSERT INTO assessment_answers (respondent_id, question_id, tanggal_penilaian, nilai, keterangan) VALUES
(1, 1,  '2026-06-10', 3, 'Prosedur ada namun perlu pemutakhiran'),
(1, 2,  '2026-06-10', 4, 'Backup dilakukan rutin mingguan'),
(1, 3,  '2026-06-10', 3, 'Pencatatan layanan belum terintegrasi penuh'),
(1, 4,  '2026-06-10', 2, 'Belum ada sistem ticketing formal'),
(1, 5,  '2026-06-10', 3, 'Pemantauan masih manual'),
(1, 6,  '2026-06-10', 2, 'Evaluasi kapasitas jarang'),
(1, 7,  '2026-06-10', 3, 'Kebijakan dasar sudah ada'),
(1, 8,  '2026-06-10', 4, 'Autentikasi dan enkripsi diterapkan'),
(1, 9,  '2026-06-10', 3, 'Hak akses dikelola sederhana'),
(1, 10, '2026-06-10', 2, 'Logging belum diaudit rutin'),
(1, 11, '2026-06-10', 2, 'Prosedur insiden belum lengkap'),
(1, 12, '2026-06-10', 3, 'Audit dilakukan tiap semester');

-- Respondent 2 (Siti Nurhaliza - Wakasek Kurikulum) - 2026-06-12 (DSS01 only)
INSERT INTO assessment_answers (respondent_id, question_id, tanggal_penilaian, nilai, keterangan) VALUES
(2, 1,  '2026-06-12', 4, 'Prosedur lengkap dan terdokumentasi'),
(2, 2,  '2026-06-12', 4, 'Backup otomatis harian'),
(2, 3,  '2026-06-12', 4, 'Sistem ticketing sudah digunakan'),
(2, 4,  '2026-06-12', 3, 'Pencatatan insiden sudah terintegrasi'),
(2, 5,  '2026-06-12', 4, 'Pemantauan real-time via dashboard'),
(2, 6,  '2026-06-12', 3, 'Evaluasi dilakukan tiap semester');

-- Respondent 3 (Budi Santoso - Operator e-Raport) - 2026-06-15 (DSS05 only)
INSERT INTO assessment_answers (respondent_id, question_id, tanggal_penilaian, nilai, keterangan) VALUES
(3, 7,  '2026-06-15', 3, 'Kebijakan dasar sudah ada'),
(3, 8,  '2026-06-15', 3, 'Kontrol dasar diterapkan'),
(3, 9,  '2026-06-15', 3, 'Manajemen akses sederhana'),
(3, 10, '2026-06-15', 2, 'Belum ada audit log rutin'),
(3, 11, '2026-06-15', 2, 'Respons insiden ad-hoc'),
(3, 12, '2026-06-15', 2, 'Audit jarang dilakukan');

-- Respondent 4 (Dewi Lestari - Guru Matematika) - 2026-06-16
INSERT INTO assessment_answers (respondent_id, question_id, tanggal_penilaian, nilai, keterangan) VALUES
(4, 1,  '2026-06-16', 4, 'Prosedur sudah baik'),
(4, 2,  '2026-06-16', 3, 'Backup terjadwal mingguan'),
(4, 3,  '2026-06-16', 4, 'Pencatatan layanan baik'),
(4, 4,  '2026-06-16', 3, 'Ticketing sederhana'),
(4, 5,  '2026-06-16', 3, 'Pemantauan berkala'),
(4, 6,  '2026-06-16', 3, 'Evaluasi tiap semester'),
(4, 7,  '2026-06-16', 3, 'Kebijakan ada'),
(4, 8,  '2026-06-16', 4, 'Kontrol keamanan aktif'),
(4, 9,  '2026-06-16', 3, 'Hak akses standar'),
(4, 10, '2026-06-16', 3, 'Logging aktif'),
(4, 11, '2026-06-16', 2, 'Insiden belum terstandar'),
(4, 12, '2026-06-16', 3, 'Audit semesteran');

-- Respondent 5 (Eko Prasetyo - Guru TI) - 2026-06-17
INSERT INTO assessment_answers (respondent_id, question_id, tanggal_penilaian, nilai, keterangan) VALUES
(5, 1,  '2026-06-17', 4, 'Prosedur sangat baik'),
(5, 2,  '2026-06-17', 4, 'Backup harian otomatis'),
(5, 3,  '2026-06-17', 3, 'Pencatatan cukup baik'),
(5, 4,  '2026-06-17', 4, 'Ticketing aktif'),
(5, 5,  '2026-06-17', 4, 'Monitoring real-time'),
(5, 6,  '2026-06-17', 3, 'Evaluasi periodik'),
(5, 7,  '2026-06-17', 4, 'Kebijakan lengkap'),
(5, 8,  '2026-06-17', 4, 'Semua kontrol diterapkan'),
(5, 9,  '2026-06-17', 4, 'Least privilege'),
(5, 10, '2026-06-17', 3, 'Audit log berkala'),
(5, 11, '2026-06-17', 3, 'Prosedur insiden ada'),
(5, 12, '2026-06-17', 4, 'Audit rutin');

-- Respondent 6 (Fauzan Maulana - Staff TU) - 2026-06-18 (hari ini)
INSERT INTO assessment_answers (respondent_id, question_id, tanggal_penilaian, nilai, keterangan) VALUES
(6, 1,  '2026-06-18', 3, 'Prosedur cukup'),
(6, 2,  '2026-06-18', 3, 'Backup mingguan'),
(6, 3,  '2026-06-18', 3, 'Pencatatan standar'),
(6, 4,  '2026-06-18', 3, 'Ticketing berjalan'),
(6, 5,  '2026-06-18', 3, 'Pemantauan berkala'),
(6, 6,  '2026-06-18', 3, 'Evaluasi semesteran');

-- ============================================================
-- 2. RESULTS (Hasil Perhitungan Capability Level)
-- ============================================================

-- Respondent 1 - DSS01 pada 2026-06-10: total=17, rata=2.83
INSERT INTO results (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(1, 1, '2026-06-10', 17, 2.83, 0.57, 'Defined', 5, 2.17, 'Kurang');

-- Respondent 1 - DSS05 pada 2026-06-10: total=17, rata=2.83
INSERT INTO results (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(1, 2, '2026-06-10', 17, 2.83, 0.57, 'Defined', 5, 2.17, 'Kurang');

-- Respondent 2 - DSS01 pada 2026-06-12: total=22, rata=3.67
INSERT INTO results (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(2, 1, '2026-06-12', 22, 3.67, 0.73, 'Defined', 5, 1.33, 'Cukup');

-- Respondent 3 - DSS05 pada 2026-06-15: total=15, rata=2.50
INSERT INTO results (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(3, 2, '2026-06-15', 15, 2.50, 0.50, 'Repeatable', 5, 2.50, 'Kurang');

-- Respondent 4 - DSS01 pada 2026-06-16: total=20, rata=3.33
INSERT INTO results (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(4, 1, '2026-06-16', 20, 3.33, 0.67, 'Defined', 5, 1.67, 'Cukup');

-- Respondent 4 - DSS05 pada 2026-06-16: total=18, rata=3.00
INSERT INTO results (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(4, 2, '2026-06-16', 18, 3.00, 0.60, 'Defined', 5, 2.00, 'Cukup');

-- Respondent 5 - DSS01 pada 2026-06-17: total=22, rata=3.67
INSERT INTO results (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(5, 1, '2026-06-17', 22, 3.67, 0.73, 'Defined', 5, 1.33, 'Cukup');

-- Respondent 5 - DSS05 pada 2026-06-17: total=22, rata=3.67
INSERT INTO results (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(5, 2, '2026-06-17', 22, 3.67, 0.73, 'Defined', 5, 1.33, 'Cukup');

-- Respondent 6 - DSS01 pada 2026-06-18: total=18, rata=3.00
INSERT INTO results (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(6, 1, '2026-06-18', 18, 3.00, 0.60, 'Defined', 5, 2.00, 'Cukup');

-- ============================================================
-- 3. Verifikasi
-- ============================================================
SELECT 'Ringkasan data per tanggal:' AS info;
SELECT
    tanggal_penilaian AS tanggal,
    COUNT(DISTINCT respondent_id) AS responden,
    COUNT(*) AS jumlah_penilaian
FROM assessment_answers
WHERE tanggal_penilaian >= '2026-06-01'
GROUP BY tanggal_penilaian
ORDER BY tanggal_penilaian;

SELECT 'Ringkasan hasil analisis:' AS info;
SELECT
    tanggal_penilaian AS tanggal,
    respondent_id,
    process_id,
    rata_rata,
    current_level,
    status
FROM results
WHERE tanggal_penilaian >= '2026-06-01'
ORDER BY tanggal_penilaian, respondent_id;
