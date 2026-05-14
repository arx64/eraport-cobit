-- Database: eraport_cobit
-- Sistem Analisis Risiko TI e-Raport menggunakan COBIT 2019
-- SMKN 1 Teluk Mengkudu

CREATE DATABASE IF NOT EXISTS eraport_cobit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eraport_cobit;

-- Tabel Users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Respondents (Responden)
CREATE TABLE respondents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    unit VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    tanggal_input DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Processes (Domain COBIT)
CREATE TABLE processes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_domain VARCHAR(10) NOT NULL,
    nama_domain VARCHAR(100) NOT NULL,
    deskripsi TEXT NOT NULL,
    tujuan TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Assessment Questions (Pertanyaan Penilaian)
CREATE TABLE assessment_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    process_id INT NOT NULL,
    kode_pertanyaan VARCHAR(20) NOT NULL,
    pertanyaan TEXT NOT NULL,
    komponen VARCHAR(50) NOT NULL,
    urutan INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (process_id) REFERENCES processes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel Assessment Answers (Jawaban Penilaian)
CREATE TABLE assessment_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    respondent_id INT NOT NULL,
    question_id INT NOT NULL,
    nilai INT NOT NULL CHECK (nilai >= 0 AND nilai <= 5),
    keterangan TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (respondent_id) REFERENCES respondents(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES assessment_questions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_answer (respondent_id, question_id)
) ENGINE=InnoDB;

-- Tabel Results (Hasil Perhitungan)
CREATE TABLE results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    respondent_id INT NOT NULL,
    process_id INT NOT NULL,
    total_nilai INT NOT NULL,
    rata_rata DECIMAL(4,2) NOT NULL,
    capability_level DECIMAL(4,2) NOT NULL,
    current_level VARCHAR(50) NOT NULL,
    target_level INT DEFAULT 4,
    gap DECIMAL(4,2) NOT NULL,
    status VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (respondent_id) REFERENCES respondents(id) ON DELETE CASCADE,
    FOREIGN KEY (process_id) REFERENCES processes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_result (respondent_id, process_id)
) ENGINE=InnoDB;

-- Tabel Design Factors
CREATE TABLE design_factors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_df VARCHAR(10) NOT NULL,
    nama_df VARCHAR(200) NOT NULL,
    status ENUM('Relevan','Tidak Relevan') NOT NULL,
    keterangan TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==========================================
-- SEEDER DATA
-- ==========================================

-- User Admin (password: admin123)
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@smkn1telukmengkudu.sch.id', 'admin');
-- Password default: 'password'

-- Data Responden (Dummy)
INSERT INTO respondents (nama, jabatan, unit, no_hp, email, tanggal_input) VALUES
('Ahmad Rizky', 'Kepala Sekolah', 'Tata Usaha', '081234567890', 'kepsek@smkn1telukmengkudu.sch.id', '2024-01-15'),
('Siti Nurhaliza', 'Wakasek Kurikulum', 'Kurikulum', '081234567891', 'wakasek@smkn1telukmengkudu.sch.id', '2024-01-16'),
('Budi Santoso', 'Operator e-Raport', 'TI', '081234567892', 'operator@smkn1telukmengkudu.sch.id', '2024-01-17'),
('Dewi Lestari', 'Guru Matematika', 'Matematika', '081234567893', 'dewi@smkn1telukmengkudu.sch.id', '2024-01-18'),
('Eko Prasetyo', 'Guru TI', 'Teknik Informatika', '081234567894', 'eko@smkn1telukmengkudu.sch.id', '2024-01-19'),
('Fauzan Maulana', 'Staff TU', 'Tata Usaha', '081234567895', 'fauzan@smkn1telukmengkudu.sch.id', '2024-01-20');

-- Data Domain COBIT
INSERT INTO processes (kode_domain, nama_domain, deskripsi, tujuan) VALUES
('DSS01', 'Manage Operations', 'Domain DSS01 berfokus pada pengelolaan operasional IT untuk memastikan layanan TI berjalan dengan baik dan mendukung proses bisnis sekolah. Domain ini mencakup aktivitas harian dalam mengelola sistem e-Raport termasuk pemantauan ketersediaan sistem, penanganan insiden, dan pemenuhan permintaan layanan.', '1. Melaksanakan pengelolaan operasional sistem e-Raport secara efektif dan efisien.\n2. Memastikan ketersediaan sistem e-Raport sesuai dengan kebutuhan pengguna.\n3. Mengelola permintaan layanan dan insiden dengan cepat dan tepat.\n4. Melakukan pemantauan dan pelaporan operasional secara berkala.\n5. Menjaga kontinuitas layanan sistem e-Raport.'),
('DSS05', 'Manage Security Services', 'Domain DSS05 berfokus pada pengelolaan keamanan layanan TI untuk melindungi sistem e-Raport dari ancaman dan risiko keamanan. Domain ini mencakup perlindungan data, manajemen akses, deteksi insiden keamanan, dan kepatuhan terhadap kebijakan keamanan.', '1. Melaksanakan pengelolaan keamanan sistem e-Raport secara menyeluruh.\n2. Melindungi data dan informasi e-Raport dari ancaman dan akses tidak sah.\n3. Mengelola akses dan identitas pengguna sistem e-Raport.\n4. Melakukan pemantauan dan deteksi insiden keamanan secara proaktif.\n5. Memastikan kepatuhan terhadap kebijakan dan standar keamanan.');

-- Data Pertanyaan DSS01 (Manage Operations) - 6 pertanyaan
INSERT INTO assessment_questions (process_id, kode_pertanyaan, pertanyaan, komponen, urutan) VALUES
(1, 'DSS01-A.1', 'Apakah terdapat prosedur tertulis untuk pengelolaan operasional sistem e-Raport yang mencakup monitoring ketersediaan sistem dan penanganan insiden?', 'Process', 1),
(1, 'DSS01-A.2', 'Apakah sistem e-Raport memiliki rencana pemeliharaan rutin dan jadwal backup data yang terdokumentasi dan dijalankan secara konsisten?', 'Process', 2),
(1, 'DSS01-B.1', 'Apakah permintaan layanan terkait sistem e-Raport dicatat, dikategorikan, dan ditindaklanjuti sesuai dengan tingkat prioritasnya?', 'Process', 3),
(1, 'DSS01-B.2', 'Apakah terdapat sistem ticketing atau pencatatan insiden untuk permasalahan sistem e-Raport?', 'Process', 4),
(1, 'DSS01-C.1', 'Apakah kinerja sistem e-Raport dipantau secara berkala dan dilaporkan kepada pihak terkait?', 'Process', 5),
(1, 'DSS01-C.2', 'Apakah kapasitas sistem e-Raport dievaluasi secara periodik untuk mengantisipasi peningkatan beban pengguna?', 'Process', 6);

-- Data Pertanyaan DSS05 (Manage Security Services) - 6 pertanyaan
INSERT INTO assessment_questions (process_id, kode_pertanyaan, pertanyaan, komponen, urutan) VALUES
(2, 'DSS05-A.1', 'Apakah terdapat kebijakan keamanan informasi untuk sistem e-Raport yang mencakup klasifikasi data dan kontrol akses?', 'Process', 1),
(2, 'DSS05-A.2', 'Apakah implementasi kontrol keamanan sistem e-Raport mencakup autentikasi pengguna, enkripsi data, dan perlindungan malware?', 'Process', 2),
(2, 'DSS05-B.1', 'Apakah hak akses pengguna sistem e-Raport dikelola berdasarkan prinsip least privilege dan dievaluasi secara berkala?', 'Process', 3),
(2, 'DSS05-B.2', 'Apakah aktivitas pengguna dalam sistem e-Raport dilog dan diaudit untuk mendeteksi akses tidak sah?', 'Process', 4),
(2, 'DSS05-C.1', 'Apakah terdapat prosedur respons insiden keamanan untuk sistem e-Raport yang mencakup deteksi, analisis, dan pemulihan?', 'Process', 5),
(2, 'DSS05-C.2', 'Apakah dilakukan audit keamanan berkala terhadap sistem e-Raport untuk mengidentifikasi kerentanan dan risiko keamanan?', 'Process', 6);

-- Data Design Factors
INSERT INTO design_factors (kode_df, nama_df, status, keterangan) VALUES
('DF2', 'Enterprise Goals', 'Relevan', 'Faktor desain yang menentukan tujuan strategis sekolah terkait pengelolaan sistem e-Raport, termasuk peningkatan efisiensi administrasi dan kualitas layanan pendidikan.'),
('DF3', 'Risk Profile', 'Relevan', 'Profil risiko yang mengidentifikasi ancaman terhadap sistem e-Raport seperti kehilangan data, gangguan sistem, dan akses tidak sah yang perlu dikelola.'),
('DF4', 'IT-related Issues', 'Relevan', 'Isu-isu terkait teknologi informasi yang dihadapi dalam pengelolaan sistem e-Raport termasuk keterbatasan infrastruktur dan sumber daya manusia TI.'),
('DF6', 'Compliance Requirements', 'Relevan', 'Persyaratan kepatuhan terhadap regulasi dan kebijakan pemerintah terkait pengelolaan data pendidikan dan sistem informasi sekolah.'),
('DF7', 'Role of IT', 'Relevan', 'Peran teknologi informasi dalam mendukung proses bisnis sekolah, khususnya dalam pengelolaan nilai dan rapor siswa secara digital.');

-- Data Hasil Penilaian (Dummy - sudah ada jawaban)
INSERT INTO assessment_answers (respondent_id, question_id, nilai, keterangan) VALUES
-- Responden 1 - DSS01
(1, 1, 3, 'Prosedur ada namun belum lengkap'),
(1, 2, 4, 'Backup dilakukan rutin setiap minggu'),
(1, 3, 3, 'Pencatatan ada tapi belum terintegrasi'),
(1, 4, 2, 'Belum ada sistem ticketing formal'),
(1, 5, 3, 'Pemantauan dilakukan secara manual'),
(1, 6, 2, 'Evaluasi kapasitas jarang dilakukan'),
-- Responden 1 - DSS05
(1, 7, 3, 'Kebijakan ada namun belum diimplementasikan penuh'),
(1, 8, 4, 'Autentikasi dan enkripsi sudah diterapkan'),
(1, 9, 3, 'Hak akses dikelola tapi belum berkala dievaluasi'),
(1, 10, 2, 'Logging ada tapi belum diaudit secara rutin'),
(1, 11, 2, 'Prosedur insiden belum terdokumentasi lengkap'),
(1, 12, 3, 'Audit dilakukan setiap semester'),
-- Responden 2 - DSS01
(2, 1, 4, 'Prosedur lengkap dan terdokumentasi'),
(2, 2, 4, 'Backup otomatis harian'),
(2, 3, 4, 'Sistem ticketing sudah digunakan'),
(2, 4, 3, 'Pencatatan insiden sudah terintegrasi'),
(2, 5, 4, 'Pemantauan real-time dengan dashboard'),
(2, 6, 3, 'Evaluasi dilakukan tiap semester'),
-- Responden 2 - DSS05
(2, 7, 4, 'Kebijakan lengkap dan sudah disosialisasikan'),
(2, 8, 4, 'Semua kontrol keamanan diterapkan'),
(2, 9, 4, 'Evaluasi akses dilakukan tiap semester'),
(2, 10, 3, 'Audit log dilakukan secara berkala'),
(2, 11, 3, 'Prosedur insiden sudah ada'),
(2, 12, 4, 'Audit keamanan dilakukan berkala'),
-- Responden 3 - DSS01
(3, 1, 3, 'Prosedur ada namun perlu pemutakhiran'),
(3, 2, 3, 'Backup dilakukan tapi tidak terjadwal'),
(3, 3, 4, 'Pencatatan layanan sudah baik'),
(3, 4, 3, 'Sederhana tapi berfungsi'),
(3, 5, 3, 'Pemantauan manual'),
(3, 6, 2, 'Perlu evaluasi lebih rutin'),
-- Responden 3 - DSS05
(3, 7, 3, 'Kebijakan dasar sudah ada'),
(3, 8, 3, 'Kontrol dasar sudah diterapkan'),
(3, 9, 3, 'Manajemen akses sederhana'),
(3, 10, 2, 'Belum ada audit log rutin'),
(3, 11, 2, 'Respons insiden ad-hoc'),
(3, 12, 2, 'Audit jarang dilakukan');

-- Insert calculated results
INSERT INTO results (respondent_id, process_id, total_nilai, rata_rata, capability_level, current_level, target_level, gap, status) VALUES
(1, 1, 17, 2.83, 0.47, 'Repeatable', 4, 1.17, 'Cukup'),
(1, 2, 17, 2.83, 0.47, 'Repeatable', 4, 1.17, 'Cukup'),
(2, 1, 22, 3.67, 0.61, 'Defined', 4, 0.33, 'Baik'),
(2, 2, 22, 3.67, 0.61, 'Defined', 4, 0.33, 'Baik'),
(3, 1, 18, 3.00, 0.50, 'Repeatable', 4, 1.00, 'Cukup'),
(3, 2, 15, 2.50, 0.42, 'Repeatable', 4, 1.58, 'Kurang');
