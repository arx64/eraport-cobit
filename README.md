# Sistem Analisis Risiko TI e-Raport - COBIT 2019

## SMKN 1 Teluk Mengkudu

Sistem analisis risiko teknologi informasi pada sistem e-Raport menggunakan framework COBIT 2019 dengan fokus pada domain DSS01 (Manage Operations) dan DSS05 (Manage Security Services).

## Teknologi

- **Backend**: PHP 8.x Native
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5.3, Chart.js 4.4
- **Icons**: Bootstrap Icons 1.11

## Fitur

### 1. Autentikasi
- Login dengan username dan password
- Session-based authentication
- Password hashing (bcrypt)
- CSRF protection
- Logout

### 2. Dashboard
- Statistic cards (Total Responden, Pertanyaan, Penilaian)
- Grafik Capability Level (Bar Chart)
- Grafik Gap Analysis (Bar Chart)
- Radar Chart perbandingan domain
- Ringkasan hasil analisis

### 3. Framework COBIT
- Informasi domain DSS01 - Manage Operations
- Informasi domain DSS05 - Manage Security Services
- Penjelasan, tujuan, dan indikator penilaian
- Skala capability level 0-5

### 4. Design Factor
- Tabel design factor COBIT 2019
- Status relevansi (Relevan/Tidak Relevan)
- Detail keterangan masing-masing DF

### 5. Data Penilaian
- CRUD data responden
- Form penilaian DSS01 (6 pertanyaan)
- Form penilaian DSS05 (6 pertanyaan)
- Progress bar penilaian
- Validasi wajib diisi

### 6. Hasil Analisis
- Perhitungan capability level otomatis
- Gap analysis (Current vs Target Level 4)
- Grafik bar, radar, dan gap
- Rekomendasi otomatis berdasarkan gap
- Status kondisi sistem

### 7. Laporan
- Preview laporan lengkap
- Cetak PDF (dengan Dompdf)
- Export CSV

## Instalasi

### 1. Clone atau copy project
```bash
copy folder project ke htdocs atau www
```

### 2. Buat database
```bash
mysql -u root -p
CREATE DATABASE eraport_cobit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Import database
```bash
mysql -u root -p eraport_cobit < database.sql
```

### 4. Konfigurasi database
Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');  // sesuaikan dengan password MySQL Anda
define('DB_NAME', 'eraport_cobit');
define('BASE_URL', 'http://localhost/eraport-cobit');
```

### 5. Akses aplikasi
Buka browser dan akses:
```
http://localhost/eraport-cobit
```

Login default:
- Username: `admin`
- Password: `password`

## Struktur Folder

```
eraport-cobit/
|-- config/
|   |-- database.php          # Konfigurasi database
|-- controllers/
|   |-- AuthController.php    # Login/logout
|   |-- DashboardController.php
|   |-- FrameworkController.php
|   |-- DesignFactorController.php
|   |-- PenilaianController.php
|   |-- AnalisisController.php
|   |-- LaporanController.php
|-- models/
|   |-- User.php
|   |-- Respondent.php
|   |-- Process.php
|   |-- Question.php
|   |-- Answer.php
|   |-- Result.php
|   |-- DesignFactor.php
|-- views/
|   |-- layouts/
|   |   |-- header.php
|   |   |-- sidebar.php
|   |   |-- topbar.php
|   |   |-- footer.php
|   |-- auth/
|   |   |-- login.php
|   |   |-- 404.php
|   |-- dashboard/
|   |   |-- index.php
|   |-- framework/
|   |   |-- index.php
|   |   |-- domain.php
|   |-- design-factor/
|   |   |-- index.php
|   |-- penilaian/
|   |   |-- responden.php
|   |   |-- dss01.php
|   |   |-- dss05.php
|   |-- analisis/
|   |   |-- index.php
|   |-- laporan/
|   |   |-- index.php
|   |   |-- pdf.php
|-- assets/
|   |-- css/
|   |   |-- style.css
|   |   |-- login.css
|   |-- js/
|   |   |-- main.js
|   |-- images/
|-- helpers/
|   |-- functions.php
|-- uploads/
|-- index.php                  # Router utama
|-- database.sql               # Schema dan data awal
|-- .htaccess                  # Rewrite rules
|-- README.md
```

## Keamanan

- Password hashing dengan bcrypt
- Session-based authentication
- CSRF token validation
- SQL injection prevention (prepared statements PDO)
- XSS prevention (htmlspecialchars)
- Input validation dan sanitization

## Kapasitas Level

| Level | Label | Deskripsi |
|-------|-------|-----------|
| 0 | Non-existent | Praktik tidak ada |
| 1 | Initial | Praktik diinisiasi |
| 2 | Repeatable | Praktik rutin, belum terstandardisasi |
| 3 | Defined | Praktik terdokumentasi dan konsisten |
| 4 | Managed and Measurable | Dipantau dan diukur |
| 5 | Optimized | Dioptimalkan dengan continuous improvement |

## Rumus Perhitungan

```
Capability Level = Total Nilai / (Jumlah Responden x Jumlah Pertanyaan)
Gap = Target Level (4) - Current Level
```

## Lisensi

&copy; <?= date('Y') ?> SMKN 1 Teluk Mengkudu
