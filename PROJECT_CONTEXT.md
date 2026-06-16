# PROJECT_CONTEXT

> Dokumen ini adalah peta lengkap project **e-Raport COBIT 2019** untuk orientasi cepat AI Agent / developer baru. Baca bagian **# START HERE** di `AI_RULES.md` terlebih dahulu.

---

## 1. Project Overview

| Item | Nilai |
|---|---|
| **Nama Project** | Sistem Analisis Risiko TI e-Raport - COBIT 2019 |
| **Instansi** | SMKN 1 Teluk Mengkudu |
| **Tujuan** | Menganalisis tingkat kapabilitas (capability level) tata kelola TI pada sistem e-Raport menggunakan framework **COBIT 2019** |
| **Fokus Domain** | DSS01 (Manage Operations) & DSS05 (Manage Security Services) |
| **Jenis Aplikasi** | Web monolith internal (admin dashboard), single-tenant, single-user-role |
| **Bahasa** | Antarmuka & pesan 100% Bahasa Indonesia |

### Masalah yang Diselesaikan
Pihak sekolah membutuhkan cara terstruktur untuk:
1. Mengukur kapabilitas pengelolaan operasional & keamanan sistem e-Raport.
2. Menghitung gap antara capability level saat ini vs target.
3. Menghasilkan rekomendasi otomatis berdasarkan gap.
4. Menyusun laporan formal (PDF/CSV) untuk keperluan audit/internal review.

### Fitur Utama
1. **Autentikasi admin** (session + CSRF + bcrypt).
2. **Manajemen Responden** (CRUD).
3. **Manajemen Pertanyaan Penilaian** (CRUD + reset semua jawaban).
4. **Penilaian 0-5** per pertanyaan untuk 2 domain × 6 pertanyaan.
5. **Perhitungan capability level otomatis** (`calculateCapabilityLevel` di `helpers/functions.php`).
6. **Dashboard** dengan statistik & chart (bar + radar) berbasis tanggal.
7. **Hasil Analisis** dengan kemampuan filter per domain/responden + rekomendasi otomatis.
8. **Laporan** preview HTML, cetak PDF (Dompdf), export CSV.
9. **Design Factor COBIT 2019** (master 11 DF dengan status Relevan/Tidak Relevan, CRUD).
10. **Framework COBIT** info statis untuk DSS01 & DSS05.

### Target Pengguna
- **Admin** (1 role, hardcoded `admin` di DB) — operator/peneliti yang mengelola responden, pertanyaan, dan menilai.
- **Auditee/Responden** — tidak login; hanya data mereka diinput oleh admin.

---

## 2. Tech Stack

| Lapisan | Teknologi |
|---|---|
| **Bahasa Backend** | PHP 8.x (native, tanpa framework) |
| **Database** | MySQL 5.7+ (charset `utf8mb4`) |
| **Akses DB** | PDO (prepared statements, `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES=false`) |
| **Frontend** | HTML5 + Bootstrap 5.3.2 (CDN) |
| **Charting** | Chart.js 4.4.1 (CDN) |
| **Date Picker** | Flatpickr (CDN) + locale `id` |
| **Icons** | Bootstrap Icons 1.11.1 (CDN) |
| **Font** | Google Fonts – Inter (CDN) |
| **Tabel Interaktif** | jQuery + DataTables (CDN) |
| **PDF Generator** | `dompdf/dompdf` 2.0.3 (via Composer) |
| **Web Server** | Apache (`.htaccess` rewrite, mod_rewrite aktif) |
| **Deployment** | XAMPP / shared-host style — `BASE_URL` di-set manual di `config/database.php` |
| **Session** | PHP native session (file-based, `session_start()` di `helpers/functions.php:7`) |
| **Composer** | Hanya untuk `dompdf/dompdf`; dependency dimuat via `require_once .../vendor/autoload.php` di `LaporanController.php` |

> ⚠️ Perlu Verifikasi: versi tepat PHP/MySQL yang dipakai saat deploy; README menyatakan `PHP 8.x` & `MySQL 5.7+` tapi tidak ada `composer.lock` PHP constraint eksplisit.

---

## 3. Architecture Summary

### Pola
**Custom MVC ringan** (Model = kelas dengan PDO, Controller = action handler, View = file PHP + HTML, Router = `index.php`).

### Layer
```
┌────────────────────────────────────────────┐
│ Browser (HTML + Bootstrap + Chart.js)      │
└──────────────────┬─────────────────────────┘
                   │ GET/POST + session cookie
┌──────────────────▼─────────────────────────┐
│ .htaccess  (rewrite → index.php)           │
├────────────────────────────────────────────┤
│ index.php   (Router: path => [Ctrl, Mtd])  │
├────────────────────────────────────────────┤
│ Controllers/* (validasi, panggil model)    │
├────────────────────────────────────────────┤
│ Models/* (PDO query, return array)         │
│ helpers/functions.php (util + kapabilitas) │
├────────────────────────────────────────────┤
│ MySQL eraport_cobit                        │
└────────────────────────────────────────────┘
```

### Request Flow
1. Request masuk → `.htaccess` rewrite ke `index.php`.
2. `index.php` parse `REQUEST_URI` dengan `BASE_URL` prefix, cocokkan dengan `$routes`.
3. Resolve `[ControllerName, methodName]` → `require_once controllers/<Name>.php` → `new $controllerName()->$methodName()`.
4. Method panggil `requireLogin()` (kecuali `AuthController::login/authenticate`).
5. Controller ambil data via Model, passing ke `view($viewPath, $data)`.
6. `view()` di `helpers/functions.php` extract `$data` lalu `require` layout (header/sidebar/topbar) → view → footer.
7. View render HTML + inline JS (Chart.js, DataTables, Flatpickr via CDN).

### Dependency Flow
```
index.php
  └── helpers/functions.php  (session, db(), view(), helpers, TARGET_LEVEL)
       ├── config/database.php (DB constants + PDO singleton)
       └── views/layouts/* + views/<page>.php
            ├── models/* (dipanggil dari controller, di-require di controller)
            └── assets/* (CSS/JS/img)
```

### Data Flow untuk Penilaian → Analisis
```
Responden + Jawaban (assessment_answers)
        │
        ▼
calculateCapabilityLevel()  (helpers/functions.php:332)
        │  - SUM(nilai), COUNT, rata-rata
        │  - capability_level = rata-rata / 5
        │  - currentLevel = getCapabilityLabel()
        │  - gap = TARGET_LEVEL - rata-rata
        │  - status = getGapStatus(gap)
        ▼
saveResult()                (helpers/functions.php:383)
        │  - UPSERT ke tabel results
        ▼
Dashboard / Analisis / Laporan
        │  - getAggregateByProcess() → bar/radar chart
        │  - getStatistics()          → ringkasan
        │  - generateRecommendations()→ array rekomendasi
        ▼
View + Chart.js / Dompdf / CSV
```

---

## 4. Folder Structure

```
eraport-cobit/
├── index.php                      # Router utama
├── .htaccess                      # Apache rewrite + proteksi file
├── database.sql                   # Schema + seeder (MySQL)
├── composer.json / composer.lock  # Dependency (Dompdf)
├── README.md                      # Dokumentasi user-facing
│
├── config/
│   └── database.php               # Konstanta DB + class Database (PDO singleton) + db()
│
├── helpers/
│   └── functions.php              # session, view(), sanitize, formatDate, capability helpers,
│                                  # calculateCapabilityLevel(), saveResult(), generateRecommendations()
│
├── models/                        # 1 class = 1 tabel (+ helper)
│   ├── User.php                   # users
│   ├── Respondent.php             # respondents
│   ├── Process.php                # processes (domain COBIT)
│   ├── Question.php               # assessment_questions
│   ├── Answer.php                 # assessment_answers
│   ├── Result.php                 # results
│   └── DesignFactor.php           # design_factors + masterList() + ensureDefaults()
│
├── controllers/
│   ├── AuthController.php         # login, authenticate, logout
│   ├── DashboardController.php    # index
│   ├── FrameworkController.php    # index, domain
│   ├── DesignFactorController.php # index, save, update, delete
│   ├── PenilaianController.php    # responden CRUD + dss01/dss05 form + save-penilaian
│   ├── QuestionController.php     # index, save, update, delete, reset-all
│   ├── AnalisisController.php     # index
│   └── LaporanController.php      # index, pdf (Dompdf), export (csv)
│
├── views/
│   ├── layouts/                   # Wajib ada untuk semua halaman pakai layout
│   │   ├── header.php             # <head> + CSS CDN + BASE_URL/stylesheet
│   │   ├── sidebar.php            # Menu navigasi kiri
│   │   ├── topbar.php             # Breadcrumb + user info + showFlash()
│   │   └── footer.php             # Tutup konten + JS CDN + main.js
│   ├── auth/                      # login, 404
│   ├── dashboard/index.php
│   ├── framework/{index,domain}.php
│   ├── design-factor/index.php    # CRUD master DF1-DF11
│   ├── penilaian/                 # responden, dss01, dss05
│   ├── analisis/index.php
│   ├── laporan/{index,pdf}.php    # pdf.php = template Dompdf (tanpa layout)
│   └── question/index.php         # CRUD pertanyaan
│
└── assets/
    ├── css/{style,login}.css      # Tema UI custom
    ├── js/main.js                 # Interaksi UI (sidebar toggle, dll)
    └── img/{logo-smk,background}.jpeg
```

### Tanggung Jawab Folder
- **`config/`** — Sumber kebenaran konfigurasi env. Di-require oleh `helpers/functions.php`. Tidak boleh berisi logika.
- **`helpers/`** — Utilitas lintas modul + business logic yang tidak terikat model tertentu (perhitungan kapabilitas, rekomendasi).
- **`models/`** — Akses DB. Tidak boleh ada echo/HTML. Return `array`/`int`/`bool`.
- **`controllers/`** — Orkestrasi request: validasi input, panggil model, set flash, render view. **Tidak** query SQL langsung.
- **`views/`** — Template HTML dengan inline PHP untuk echo. **Tidak** ada SQL.
- **`assets/`** — Static file. Dipanggil dengan path absolut `<?= BASE_URL ?>/assets/...`.

### Ketergantungan
- `views → controllers` (data lewat `view()`)
- `controllers → models + helpers`
- `models → helpers/functions.php` (untuk `db()` & `sanitize()`)
- `helpers/functions.php → config/database.php` (untuk `db()`)
- `index.php → helpers/functions.php` (untuk routing) + `controllers/*.php` (lazy load)

---

## 5. Critical Files

### `index.php`
- **Purpose**: Single-entry router. Memetakan path URL ke `[Controller, method]`.
- **Dependencies**: `helpers/functions.php` (untuk `BASE_URL`, `isLoggedIn`, `view`, `redirect`).
- **Notes**:
  - Tambah route baru = tambah baris di array `$routes`.
  - Tidak ada middleware/filter terpusat — proteksi via `requireLogin()` di tiap method controller.

### `config/database.php`
- **Purpose**: Konstanta DB (`DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME`, `BASE_URL`) + class `Database` (PDO singleton) + helper `db()`.
- **Dependencies**: Ekstensi `pdo_mysql`.
- **Notes**:
  - `BASE_URL` dipakai di semua `href`/`src` asset/view agar path absolut saat deploy di subfolder.
  - Tidak ada env loader. ⚠️ Hardcoded (lihat Known Risks).

### `helpers/functions.php`
- **Purpose**: Session, helper output (`view`, `sanitize`, `showFlash`, `formatDate`, `redirect`, `currentPage`), business logic kapabilitas (`getCapabilityLabel`, `getCapabilityBadge`, `getGapStatus`, `getGapBadge`, `generateRecommendations`, `calculateCapabilityLevel`, `saveResult`).
- **Dependencies**: `config/database.php`, `$_SESSION`.
- **Notes**:
  - Mendefinisikan konstanta `TARGET_LEVEL` (lihat Known Risks).
  - File ini paling banyak disentuh; perubahan di sini berdampak ke semua view.

### `controllers/AuthController.php`
- **Purpose**: Login form, login handler, logout.
- **Notes**: `view(..., false)` (tanpa layout) untuk login.

### `controllers/LaporanController.php`
- **Purpose**: Generate PDF via Dompdf, export CSV.
- **Notes**: `require_once .../vendor/autoload.php`; ob_start() + view(..., false) untuk tangkap HTML jadi PDF.

### `controllers/DesignFactorController.php`
- **Purpose**: CRUD Design Factor. `index()` panggil `ensureDefaults()` untuk seed master DF1–DF11.
- **Notes**: Method `index/save/update/delete`.

### `models/DesignFactor.php`
- **Purpose**: CRUD + `masterList()` (array 11 DF statis) + `ensureDefaults()` (insert DF1–DF11 yang belum ada, default status = "Tidak Relevan").
- **Notes**: `getAll()` sort natural `DF1..DF11` (strnatcmp).

### `controllers/PenilaianController.php`
- **Purpose**: CRUD responden + form penilaian DSS01/05 + simpan jawaban (panggil `saveResult()`).
- **Notes**: Setiap `save-penilaian` akan trigger perhitungan ulang & simpan ke `results`.

### `.htaccess`
- **Purpose**: Rewrite semua request non-file ke `index.php`. Proteksi file `.sql/.log/.ini`. Limit upload 10M.
- **Notes**: `RewriteBase /eraport-cobit/` — **harus** disesuaikan saat deploy di subfolder berbeda.

---

## 6. Feature Map

### F1. Autentikasi
- **Purpose**: Single-admin login.
- **Main Files**: `controllers/AuthController.php`, `views/auth/login.php`, `models/User.php`, `helpers/functions.php` (`isLoggedIn`, `requireLogin`, `generateCsrfToken`, `validateCsrfToken`).
- **Dependencies**: Session PHP, password_hash/password_verify (bcrypt).
- **Business Flow**: POST `/authenticate` → `User::login()` → set `$_SESSION` → redirect `/dashboard`. Logout hapus session.

### F2. Dashboard
- **Purpose**: Ringkasan metrik + chart capability & gap.
- **Main Files**: `controllers/DashboardController.php`, `views/dashboard/index.php`.
- **Dependencies**: `Result::getAggregateByProcess/getStatistics/getDatesWithData`, `Answer::getSummary`, `Question::getTotalByProcessId`, `Respondent::getTotal`.
- **Business Flow**: Filter `?tanggal=YYYY-MM-DD` → load aggregate → render Chart.js bar + radar.

### F3. Framework COBIT
- **Purpose**: Halaman informasi domain DSS01 & DSS05.
- **Main Files**: `controllers/FrameworkController.php`, `views/framework/index.php`, `views/framework/domain.php`, `models/Process.php`.
- **Dependencies**: `Process::getAll/getByCode`.
- **Business Flow**: List domain dari `processes` → klik → detail (parse `tujuan` per baris).

### F4. Design Factor (CRUD)
- **Purpose**: Kelola 11 master DF + tambah DF kustom.
- **Main Files**: `controllers/DesignFactorController.php`, `models/DesignFactor.php`, `views/design-factor/index.php`.
- **Dependencies**: `DesignFactor::masterList/ensureDefaults/getAll/create/update/delete`.
- **Business Flow**: Buka halaman → `ensureDefaults()` insert DF1–DF11 yang belum ada → tampil `getAll()` urut natural → user tambah/edit/hapus.

### F5. Data Penilaian (Responden + Penilaian DSS01/05)
- **Purpose**: Kelola responden + form nilai 0-5.
- **Main Files**: `controllers/PenilaianController.php`, `views/penilaian/{responden,dss01,dss05}.php`, `models/{Respondent,Question,Answer}.php`.
- **Dependencies**: `Answer::save` + `helpers::saveResult`.
- **Business Flow**: Pilih responden → form rating radio 0-5 + textarea → submit → `save()` per question → `saveResult()` hitung capability level & UPSERT ke `results`.

### F6. CRUD Pertanyaan
- **Purpose**: Kelola 6 pertanyaan per domain (DSS01, DSS05).
- **Main Files**: `controllers/QuestionController.php`, `views/question/index.php`, `models/Question.php`.
- **Dependencies**: `Question::getAll/create/update/delete`, `Answer::deleteAll`, `Result::deleteAll`.
- **Business Flow**: Tambah/edit/hapus pertanyaan (relasi ke `processes`). Tombol "Reset Semua Jawaban" truncate `assessment_answers` + `results`.

### F7. Hasil Analisis
- **Purpose**: Tampilan analisis + rekomendasi otomatis.
- **Main Files**: `controllers/AnalisisController.php`, `views/analisis/index.php`, `helpers::generateRecommendations`.
- **Dependencies**: `Result::getByProcessId/getByRespondentAndProcess/getAll/getAggregateByProcess/getStatistics`, `Process::getAll`, `Respondent::getForDropdown`.
- **Business Flow**: Filter `?process_id&respondent_id&tanggal` → aggregate + generate rekomendasi per domain → render Chart.js + tabel.

### F8. Laporan
- **Purpose**: Preview + PDF + CSV.
- **Main Files**: `controllers/LaporanController.php`, `views/laporan/index.php`, `views/laporan/pdf.php`.
- **Dependencies**: Dompdf, `Result/Respondent/Process` models.
- **Business Flow**: `/laporan` preview HTML → klik "Cetak PDF" → `view('laporan/pdf', ..., false)` di-ob_start, dirender Dompdf → stream. `/laporan/export?type=csv` → fputcsv ke `php://output`.

---

## 7. API Documentation Summary

> Catatan: project ini bukan REST API formal. "Endpoint" di sini adalah URL path yang ditangani router.

### Auth
| Endpoint | Method | Purpose | Auth | Input | Output |
|---|---|---|---|---|---|
| `/login` | GET | Form login | Tidak | – | HTML form |
| `/authenticate` | POST | Proses login | Tidak | `username`, `password`, `csrf_token` | Redirect ke `/dashboard` atau `/login` |
| `/logout` | GET | Hapus session | Ya | – | Redirect `/login` |

### Core
| Endpoint | Method | Purpose | Auth | Input | Output |
|---|---|---|---|---|---|
| `/dashboard` | GET | Dashboard | Ya | `?tanggal=YYYY-MM-DD` | HTML + Chart.js |
| `/framework` | GET | List domain | Ya | – | HTML |
| `/framework/domain` | GET | Detail domain | Ya | `?kode=DSS01` | HTML |
| `/design-factor` | GET | Tabel DF | Ya | – | HTML |
| `/design-factor/save` | POST | Tambah DF | Ya | `kode_df`, `nama_df`, `status`, `keterangan`, `csrf_token` | Redirect |
| `/design-factor/update` | POST | Edit DF | Ya | `id`, field DF, `csrf_token` | Redirect |
| `/design-factor/delete` | GET | Hapus DF | Ya | `?id=N` | Redirect |
| `/penilaian/responden` | GET | List responden | Ya | – | HTML |
| `/penilaian/save-responden` | POST | Tambah responden | Ya | `nama`, `jabatan`, `unit`, `no_hp?`, `email?`, `tanggal_input`, `csrf_token` | Redirect |
| `/penilaian/update-responden` | POST | Edit responden | Ya | `id`, field responden | Redirect |
| `/penilaian/delete-responden` | GET | Hapus responden | Ya | `?id=N` | Redirect |
| `/penilaian/dss01` | GET | Form penilaian DSS01 | Ya | `?respondent_id` | HTML form |
| `/penilaian/dss05` | GET | Form penilaian DSS05 | Ya | `?respondent_id` | HTML form |
| `/penilaian/save-penilaian` | POST | Simpan jawaban | Ya | `respondent_id`, `process_id`, `nilai_<question_id>`, `keterangan_<question_id>` | Redirect |
| `/penilaian/delete-penilaian` | GET | Hapus nilai responden | Ya | `?respondent_id&process_id` | Redirect `/analisis` |
| `/pertanyaan` | GET | List pertanyaan | Ya | – | HTML |
| `/pertanyaan/save` | POST | Tambah pertanyaan | Ya | `process_id`, `kode_pertanyaan`, `pertanyaan`, `komponen`, `urutan`, `csrf_token` | Redirect |
| `/pertanyaan/update` | POST | Edit pertanyaan | Ya | `id`, field pertanyaan | Redirect |
| `/pertanyaan/delete` | GET | Hapus pertanyaan | Ya | `?id=N` | Redirect |
| `/pertanyaan/reset-all` | POST | Truncate jawaban & hasil | Ya | `csrf_token` | Redirect |
| `/analisis` | GET | Hasil analisis | Ya | `?process_id&respondent_id&tanggal` | HTML + Chart.js |
| `/laporan` | GET | Preview laporan | Ya | `?tanggal` | HTML |
| `/laporan/pdf` | GET | Cetak PDF | Ya | `?tanggal` | PDF stream |
| `/laporan/export` | GET | Export CSV | Ya | `?tanggal&type=csv` | CSV stream |

### Lain
| Endpoint | Method | Purpose | Auth | Input | Output |
|---|---|---|---|---|---|
| `/` atau empty | GET | Alias login | Alias | – | HTML form login |
| lainnya | – | – | – | – | 404 (view `auth/404` jika login, redirect ke login sebaliknya) |

---

## 8. Environment Variables

> ⚠️ Project ini **tidak** menggunakan `.env` atau library env loader. Semua konfigurasi ada di `config/database.php` sebagai konstanta PHP.

| Variable | Purpose | Required | Default | Risk |
|---|---|---|---|---|
| `DB_HOST` | Host MySQL | Ya | `localhost` | Tinggi (jika salah: app tidak konek) |
| `DB_USERNAME` | User MySQL | Ya | `root` | Tinggi |
| `DB_PASSWORD` | Password MySQL | Ya | `''` (kosong, default XAMPP) | Tinggi (jika kosong di production:暴露暴露暴露暴露暴露暴露) |
| `DB_NAME` | Nama database | Ya | `eraport_cobit` | Tinggi |
| `BASE_URL` | URL prefix aplikasi | Ya | `http://localhost/eraport-cobit` | Tinggi (path asset/route salah jika dipindah) |
| `TARGET_LEVEL` | Target capability level (konstanta di `helpers/functions.php:11`) | Ya | `5` (saat ini; ⚠️ README & analisis bisnis nyatakan `4`) | Sedang (mengubah analisis gap) |

> ⚠️ Perlu Verifikasi: target runtime (PHP version, ekstensi, memory_limit) di server produksi.

---

## 9. External Services

Project ini **tidak** mengintegrasikan layanan eksternal (payment, email, storage, queue, AI, analytics). Semua asset & data bersifat lokal.

| Kategori | Status | Catatan |
|---|---|---|
| Payment Gateway | ❌ Tidak ada | – |
| Email Service | ❌ Tidak ada | Notifikasi hanya via flash message di session |
| Storage / Cloud | ❌ Tidak ada | File statis di folder `assets/` lokal |
| AI Service | ❌ Tidak ada | – |
| Queue / Worker | ❌ Tidak ada | Semua request sinkron |
| Cache | ❌ Tidak ada | Tidak ada Redis/Memcached; query DB tiap request |
| Analytics | ❌ Tidak ada | – |
| **Dependensi Eksternal (CDN)** | ✅ | Bootstrap, Chart.js, Bootstrap Icons, jQuery (DataTables), Flatpickr, Google Fonts — semua via `cdn.jsdelivr.net` & `fonts.googleapis.com` |

> ⚠️ Perlu Verifikasi: apakah ada rencana migrasi ke offline bundle untuk lingkungan tanpa internet.

---

## 10. Known Risks & Technical Debt

### Bugs Aktif
1. **Undefined `$gap` di `helpers/functions.php:355`**
   - Pada `calculateCapabilityLevel()`, cabang empty-state (`jumlahPertanyaan === 0`) menulis `'gap' => $gap` padahal `$gap` baru didefinisikan di branch bawah. Akan raise `Undefined variable $gap` (PHP 8 warning/notice) saat ada responden tanpa jawaban. **Belum terlihat sebagai fatal karena PHP 8 hanya warning, tapi akan污染污染污染污染污染污染 log.**

2. **CSRF tidak konsisten di semua endpoint POST**
   - `PenilaianController::updateResponden` (tidak validasi CSRF) padahal `saveResponden` validasi. Bandingkan `controllers/PenilaianController.php`.

3. **Inkonsistensi nilai `TARGET_LEVEL`**
   - Kode saat ini `5` (`helpers/functions.php:11`), README nyatakan `4`, business logic & default seeder awalnya `4`. Perbaiki salah satu.

### Hardcoded Values & Konfigurasi
- `BASE_URL` hardcoded di `config/database.php` — harus diedit manual saat deploy.
- Kredensial DB di file yang sama (tidak dipisah ke env).
- Default password admin seed: `password` (lihat komentar di `database.sql:103`). ⚠️ Harus diganti sebelum deploy.
- Process ID untuk DSS01 = `1`, DSS05 = `2` (`helpers/functions.php` & `PenilaianController`). Disimpan sebagai integer literal — bukan FK lookup by kode.

### Arsitektur / Code Smell
- **Business logic di `helpers/functions.php`** — `calculateCapabilityLevel`, `saveResult`, `generateRecommendations` seharusnya pindah ke `services/` atau model. Saat ini tercampur dengan helper presentasi.
- **Tidak ada Service/Repository layer** — Controller langsung panggil model PDO. Trade-off: simpel, tapi sulit di-test.
- **Tidak ada transaction wrapper** — `saveResult` melakukan SELECT lalu UPDATE/INSERT terpisah. Risiko race condition saat 2 request bersamaan untuk responden yang sama.
- **Duplikasi render tabel DF lama** — Versi `views/design-factor/index.php` awal punya tabel DF hardcoded 10 baris (DF1-DF10). Sudah diganti dengan loop DB, tapi bekas commented-out block masih ada di history (lihat git diff).
- **Inline CSS besar di `views/laporan/pdf.php`** — Template PDF 100% inline `<style>`, tidak ada file terpisah.
- **Magic numbers**: ID proses DSS01/DSS05 hardcoded, target level, range capability 0-5, dsb.

### Keamanan
- `htmlspecialchars` hanya lewat `sanitize()` — beberapa view render teks panjang (`processes.tujuan`) tanpa escape eksplisit. ⚠️ Review XSS.
- Tidak ada rate limiting pada login.
- Session fixation tidak dicegah (tidak ada `session_regenerate_id(true)` setelah login).
- CSRF protection ada di helper tapi tidak konsisten dipakai (lihat Bugs #2).
- File `.sql/.log/.ini` diproteksi via `.htaccess` — OK untuk Apache, tapi tidak untuk Nginx.
- `password_hash`/`password_verify` benar (bcrypt), tapi tidak ada kebijakan password (minimum length, complexity).

### Missing
- **Tidak ada test** (unit, integration, e2e). ⚠️ Harus ditambah minimal untuk helper perhitungan.
- **Tidak ada migration system** (semua schema di `database.sql`, harus di-import manual).
- **Tidak ada logger** — error hanya `die()` di koneksi DB, sisanya silent.
- **Tidak ada audit log** — siapa yang input/edit data tidak tercatat.
- **Tidak ada soft delete** — `DELETE` langsung hilang dari DB.
- **Tidak ada pagination** di view admin — `getAll()` mengembalikan semua baris (berisiko untuk data besar).

### Performance
- N+1 risk: `PenilaianController::dss01/dss05` melakukan `getByProcessId` lalu loop manual — OK untuk 6 pertanyaan, tapi akan jadi masalah jika bertambah.
- `DashboardController::index` melakukan 6 query terpisah. Bisa digabung.
- `Result::getAll($tanggal)` + `Result::getByProcessId` + `Result::getAggregateByProcess` — multiple big queries di halaman yang sama.
- Tidak ada index pada `kode_df` (DF), `kode_domain` (processes), `process_id` FK (sudah ada via FK otomatis), `created_at` (untuk filter tanggal).

### Coupling
- View `views/laporan/pdf.php` harus selalu independen (tidak extend layout) — ada risiko lupa.
- Hardcoded process_id di `PenilaianController` & `DashboardController` untuk hitung `totalQuestionsDSS01`/`DSS05`.

### Dependensi
- **Dompdf 2.0.3** sudah lama; tidak ada lock major upgrade. ⚠️ CVE?
- **CDN-based frontend** — single point of failure jika CDN down. Untuk production, idealnya di-bundle lokal.
