# DATABASE_CONTEXT

> Referensi tunggal skema database **eraport_cobit**. Sumber kebenaran: `database.sql` (satu file dump) + definisi runtime di `models/*.php`.

---

## 1. Database Overview

| Item | Nilai |
|---|---|
| **DB Engine** | MySQL / MariaDB |
| **Versi Minimum** | 5.7+ (per README) ⚠️ Perlu Verifikasi untuk MariaDB |
| **Charset** | `utf8mb4` |
| **Collation** | `utf8mb4_unicode_ci` |
| **ORM** | Tidak ada — native PDO dengan prepared statements |
| **Migration System** | Tidak ada — schema didefinisikan & di-seed di `database.sql` (file tunggal) |
| **Schema Source** | `database.sql` (cek file ini untuk definisi paling otoritatif) |
| **DB Name** | `eraport_cobit` (lihat `config/database.php`) |
| **Connection** | Singleton PDO via class `Database::getInstance()` di `config/database.php:24` |

---

## 2. Entity Relationship Overview

```
┌──────────────┐         ┌──────────────────┐
│   users      │         │   respondents    │
│ (admin only) │         │ (data responden) │
└──────────────┘         └────────┬─────────┘
                                  │ 1
                                  │
                                  │ N
                         ┌────────▼─────────┐         ┌──────────────────┐
                         │assessment_answers│◄────────┤assessment_quest. │
                         │  (jawaban 0-5)   │  N   1  │ (per domain)     │
                         └────────┬─────────┘         └────────┬─────────┘
                                  │                            │ N
                                  │ N                          │ 1
                                  │                  ┌─────────▼────────┐
                                  │                  │   processes      │
                                  │                  │ (DSS01, DSS05)   │
                                  │                  └──────────────────┘
                                  │ 1
                         ┌────────▼─────────┐
                         │     results      │ (1 per responden × domain)
                         │  (capability)    │
                         └──────────────────┘

┌────────────────────┐
│ design_factors     │  ← Tabel independen, tidak punya FK ke tabel lain.
│ (DF1..DF11 + cust) │
└────────────────────┘
```

---

## 3. Tables

### 3.1 `users`

**Purpose**: Akun admin yang bisa login ke sistem. Single-role (`admin`).

| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | INT AUTO_INCREMENT PK | No | ID user |
| `username` | VARCHAR(50) UNIQUE | No | Username login |
| `password` | VARCHAR(255) | No | bcrypt hash |
| `nama_lengkap` | VARCHAR(100) | No | Nama tampilan |
| `email` | VARCHAR(100) | No | Email |
| `role` | ENUM('admin') DEFAULT 'admin' | No | Role (saat ini selalu 'admin') |
| `created_at` | TIMESTAMP | No | Auto |
| `updated_at` | TIMESTAMP ON UPDATE | No | Auto |

**Keys**:
- PK: `id`
- UNIQUE: `username`

**Relationships**: Tidak ada FK. Tabel independen.

**Business**: Saat ini hanya ada 1 admin (seed). Sistem tidak support multi-role. Login cek `password_verify($_POST['password'], $user['password'])`.

> ⚠️ Perlu Verifikasi: apakah `role` ENUM akan diekspansi (mis. `auditor`, `viewer`).

---

### 3.2 `respondents`

**Purpose**: Data responden penilaian (bukan user yang login; hanya data yang dinilai).

| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | INT AUTO_INCREMENT PK | No | ID |
| `nama` | VARCHAR(100) | No | Nama lengkap |
| `jabatan` | VARCHAR(100) | No | Jabatan |
| `unit` | VARCHAR(100) | No | Unit/Departemen |
| `no_hp` | VARCHAR(20) | Yes | No HP (nullable) |
| `email` | VARCHAR(100) | Yes | Email (nullable) |
| `tanggal_input` | DATE | No | Tanggal input data |
| `created_at` | TIMESTAMP | No | Auto |

**Keys**:
- PK: `id`

**Relationships**:
- One-to-Many → `assessment_answers` (FK cascade)
- One-to-Many → `results` (FK cascade)

**Business**: Subjek yang dinilai/diwawancara untuk audit. Tanggal input digunakan sebagai filter analisis per-periode.

---

### 3.3 `processes`

**Purpose**: Master domain COBIT 2019 yang dinilai. Saat ini hanya DSS01 & DSS05.

| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | INT AUTO_INCREMENT PK | No | ID |
| `kode_domain` | VARCHAR(10) | No | Kode singkat (DSS01, DSS05) |
| `nama_domain` | VARCHAR(100) | No | Nama lengkap |
| `deskripsi` | TEXT | No | Deskripsi naratif |
| `tujuan` | TEXT | No | Tujuan (multi-baris, dipisah `\n`) |
| `aktif` | TINYINT(1) DEFAULT 1 | No | Flag aktif |
| `created_at` | TIMESTAMP | No | Auto |

**Keys**:
- PK: `id`

**Relationships**:
- One-to-Many → `assessment_questions`
- One-to-Many → `results`

**Business**: DSS01 = Manage Operations, DSS05 = Manage Security Services. `aktif` flag saat ini **tidak** dipakai di query; bisa untuk soft-disable domain tanpa hapus.

> ⚠️ ID `1` diasumsikan DSS01 dan `2` DSS05 (hardcoded di `PenilaianController` & `DashboardController`). Tambah domain baru = perhatikan konvensi ini.

---

### 3.4 `assessment_questions`

**Purpose**: Bank pertanyaan penilaian per domain. 6 pertanyaan per domain.

| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | INT AUTO_INCREMENT PK | No | ID |
| `process_id` | INT FK | No | → `processes.id` (CASCADE) |
| `kode_pertanyaan` | VARCHAR(20) | No | Kode (mis. `DSS01-A.1`) |
| `pertanyaan` | TEXT | No | Teks pertanyaan |
| `komponen` | VARCHAR(50) | No | `Process` / `People` / `Technology` |
| `urutan` | INT | No | Urutan tampil |
| `created_at` | TIMESTAMP | No | Auto |

**Keys**:
- PK: `id`
- FK: `process_id` → `processes(id)` ON DELETE CASCADE

**Relationships**:
- Many-to-One → `processes`
- One-to-Many → `assessment_answers`

**Business**: Penilaian capability level 0-5 dilakukan per pertanyaan. Komponen adalah kategori COBIT 2019 (Process/People/Technology) — saat ini di view hanya ditampilkan sebagai badge.

---

### 3.5 `assessment_answers`

**Purpose**: Jawaban responden per pertanyaan.

| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | INT AUTO_INCREMENT PK | No | ID |
| `respondent_id` | INT FK | No | → `respondents.id` (CASCADE) |
| `question_id` | INT FK | No | → `assessment_questions.id` (CASCADE) |
| `nilai` | INT CHECK 0..5 | No | Skor 0-5 |
| `keterangan` | TEXT | Yes | Catatan responden |
| `created_at` | TIMESTAMP | No | Auto |

**Keys**:
- PK: `id`
- UNIQUE: `(respondent_id, question_id)` — satu jawaban per (responden, pertanyaan)
- FK: `respondent_id`, `question_id` (CASCADE)

**Relationships**:
- Many-to-One → `respondents`
- Many-to-One → `assessment_questions`

**Business**: UNIQUE constraint memastikan tidak ada double-input. Upsert di `Answer::save()` (cek existing lalu UPDATE/INSERT). `created_at` jadi dasar filter tanggal di dashboard/analisis/laporan.

---

### 3.6 `results`

**Purpose**: Hasil kalkulasi capability level per (responden, domain). Cache dari `assessment_answers`.

| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | INT AUTO_INCREMENT PK | No | ID |
| `respondent_id` | INT FK | No | → `respondents.id` (CASCADE) |
| `process_id` | INT FK | No | → `processes.id` (CASCADE) |
| `total_nilai` | INT | No | SUM(nilai) |
| `rata_rata` | DECIMAL(4,2) | No | Rata-rata nilai (0-5) |
| `capability_level` | DECIMAL(4,2) | No | Normalisasi 0-1 (rata-rata / 5) |
| `current_level` | VARCHAR(50) | No | Label (`Optimized`, `Managed and Measurable`, dll) |
| `target_level` | INT DEFAULT 4 | No | Target (⚠️ saat ini sumbernya `TARGET_LEVEL` di helper, bukan literal 4) |
| `gap` | DECIMAL(4,2) | No | target_level - rata-rata (atau `capability_level` ? — lihat catatan) |
| `status` | VARCHAR(50) | No | Label status (`Sangat Baik`/`Baik`/`Cukup`/`Kurang`/`Sangat Kurang`) |
| `created_at` | TIMESTAMP | No | Auto |

**Keys**:
- PK: `id`
- UNIQUE: `(respondent_id, process_id)` — satu hasil per (responden, domain)
- FK: `respondent_id`, `process_id` (CASCADE)

**Relationships**:
- Many-to-One → `respondents`
- Many-to-One → `processes`

**Business**: Tabel ini adalah materialized result. Dihitung ulang oleh `saveResult()` (`helpers/functions.php:383`) setiap kali jawaban disimpan/diupdate. Definisi field:
- `rata_rata = SUM(nilai) / COUNT(*)` — skala 0-5
- `capability_level = rata_rata / 5` — skala 0-1
- `current_level` = label dari `getCapabilityLabel(rata_rata)` (helper)
- `gap = TARGET_LEVEL - rata_rata` (skala 0-5) — bukan dari `capability_level`
- `status` = label dari `getGapStatus(gap)`

> ⚠️ Perlu Verifikasi: ada inkonsistensi rumus — README menulis `Capability Level = Total Nilai / (Jumlah Responden × Jumlah Pertanyaan)` (skala 0-1) sedangkan kode pakai `rata-rata / 5` (skala 0-1). Untuk single-responden keduanya sama; untuk multi-responden akan berbeda.

---

### 3.7 `design_factors`

**Purpose**: Design Factor COBIT 2019 yang relevan/tidak untuk penelitian ini. Master list 11 + tambahan custom.

| Column | Type | Nullable | Description |
|---|---|---|---|
| `id` | INT AUTO_INCREMENT PK | No | ID |
| `kode_df` | VARCHAR(10) | No | Kode (DF1..DF11 atau custom) |
| `nama_df` | VARCHAR(200) | No | Nama design factor |
| `status` | ENUM('Relevan','Tidak Relevan') | No | Status |
| `keterangan` | TEXT | No | Deskripsi/keterangan |
| `created_at` | TIMESTAMP | No | Auto |

**Keys**:
- PK: `id`
- ⚠️ Tidak ada UNIQUE pada `kode_df` — kode boleh duplikat (tidak diinginkan). Lihat Risks.

**Relationships**: Tidak ada FK. Tabel independen.

**Business**: 11 master (DF1..DF11) di-seed otomatis oleh `DesignFactor::ensureDefaults()` (saat halaman Design Factor dibuka) bila belum ada. Tiap halaman buka, master yang belum ada akan di-insert dengan status default `Tidak Relevan`. User bisa edit status, tambah DF baru, hapus DF kustom. ⚠️ Master DF akan di-reseed otomatis bila dihapus.

---

## 4. Business Meaning — Per Tabel

| Tabel | Fungsi Bisnis |
|---|---|
| `users` | Autentikasi admin. Hanya 1 role (`admin`). |
| `respondents` | Subjek audit (Kepala Sekolah, Operator, Guru, dll). Data diinput manual oleh admin. |
| `processes` | Master domain COBIT yang dinilai. Domain merepresentasikan area proses TI. |
| `assessment_questions` | Instrumen penilaian. Satu set pertanyaan = satu domain. |
| `assessment_answers` | Hasil kuesioner. Setiap responden menjawab 6 pertanyaan per domain. |
| `results` | **Materialized** hasil kalkulasi — duplikat computed dari answers. Memudapkan query analitik tanpa JOIN ke banyak tabel. |
| `design_factors` | Daftar faktor desain COBIT 2019 yang digunakan sebagai justifikasi scope penelitian. Status "Relevan" = DF dipakai dalam skripsi ini. |

---

## 5. Important Queries

### Authentication
```sql
SELECT * FROM users WHERE username = ? LIMIT 1
-- (models/User.php:22)
```

### Dashboard — Statistik Umum
```sql
SELECT COUNT(*) FROM respondents;
SELECT COUNT(*) FROM assessment_questions WHERE process_id = 1; -- DSS01
SELECT COUNT(*) FROM assessment_questions WHERE process_id = 2; -- DSS05
SELECT COUNT(*) FROM assessment_answers WHERE DATE(created_at) = ?;
```

### Dashboard — Aggregate per Domain
```sql
SELECT 
  p.id, p.kode_domain, p.nama_domain,
  AVG(r.capability_level) AS avg_capability,
  AVG(r.gap)               AS avg_gap,
  AVG(r.rata_rata)         AS avg_rata_rata,
  COUNT(DISTINCT r.respondent_id) AS total_responden
FROM processes p
LEFT JOIN results r ON p.id = r.process_id
WHERE DATE(r.created_at) = ?
GROUP BY p.id, p.kode_domain, p.nama_domain
ORDER BY p.id;
```

### Penilaian — Get soal + nilai existing
```sql
SELECT aq.* FROM assessment_questions aq
WHERE aq.process_id = ? ORDER BY aq.urutan;

SELECT aa.*, aq.kode_pertanyaan, aq.pertanyaan, aq.komponen
FROM assessment_answers aa
JOIN assessment_questions aq ON aa.question_id = aq.id
WHERE aa.respondent_id = ? AND aq.process_id = ?
ORDER BY aq.urutan;
```

### Hitung Capability Level (di `helpers/functions.php`)
```sql
SELECT SUM(nilai) AS total, COUNT(*) AS jumlah
FROM assessment_answers aa
JOIN assessment_questions aq ON aa.question_id = aq.id
WHERE aa.respondent_id = ? AND aq.process_id = ?;
```

### Analisis & Laporan — Rekap
```sql
SELECT r.*, res.nama AS respondent_name, res.jabatan, p.kode_domain, p.nama_domain
FROM results r
JOIN respondents res ON r.respondent_id = res.id
JOIN processes p ON r.process_id = p.id
WHERE DATE(r.created_at) = ?
ORDER BY r.created_at DESC;
```

### Statistik Ringkas
```sql
SELECT 
  AVG(capability_level) AS avg_capability,
  AVG(gap)               AS avg_gap,
  MIN(capability_level) AS min_capability,
  MAX(capability_level) AS max_capability
FROM results
WHERE DATE(created_at) = ?;
```

### Daftar Tanggal yang Punya Data
```sql
SELECT DISTINCT DATE(created_at) AS tanggal FROM results ORDER BY tanggal DESC;
```

### Reset Semua (Tombol di `views/question/index.php`)
```sql
DELETE FROM assessment_answers;
DELETE FROM results;
-- (QuestionController::resetAll)
```

---

## 6. Migration History Summary

> Tidak ada migration tool. Semua perubahan schema ada di `database.sql` (file tunggal).

| Versi/Seksi | Perubahan |
|---|---|
| `database.sql:1-94` | Schema 7 tabel (users, respondents, processes, assessment_questions, assessment_answers, results, design_factors) |
| `database.sql:101-103` | Seed 1 user admin (password: `password` — lihat komentar) |
| `database.sql:106-112` | Seed 6 responden dummy |
| `database.sql:115-117` | Seed 2 domain: DSS01 & DSS05 |
| `database.sql:120-135` | Seed 12 pertanyaan (6 per domain) |
| `database.sql:138-144` | Seed 5 design factor Relevan (DF2, DF3, DF4, DF6, DF7) |
| `database.sql:146-188` | Seed jawaban dummy untuk 3 responden pertama |
| `database.sql:191-196` | Seed hasil (results) untuk 3 responden × 2 domain |

> ⚠️ Perlu Verifikasi: apakah ada versi revisi `database.sql` yang disimpan di tempat lain (mis. `migrations/`, git tag).

---

## 7. Data Integrity Rules

### Cascade Delete
- Hapus `respondents` → cascade ke `assessment_answers` + `results`.
- Hapus `processes` → cascade ke `assessment_questions` (implikasi: juga ke answers & results via answers).
- Hapus `assessment_questions` → cascade ke `assessment_answers`.

### Constraints
| Constraint | Tabel | Field |
|---|---|---|
| UNIQUE | `users` | `username` |
| UNIQUE | `assessment_answers` | `(respondent_id, question_id)` |
| UNIQUE | `results` | `(respondent_id, process_id)` |
| CHECK | `assessment_answers` | `nilai BETWEEN 0 AND 5` |
| ENUM | `users` | `role IN ('admin')` |
| ENUM | `design_factors` | `status IN ('Relevan','Tidak Relevan')` |
| FK | `assessment_questions.process_id` | → `processes(id)` CASCADE |
| FK | `assessment_answers.respondent_id` | → `respondents(id)` CASCADE |
| FK | `assessment_answers.question_id` | → `assessment_questions(id)` CASCADE |
| FK | `results.respondent_id` | → `respondents(id)` CASCADE |
| FK | `results.process_id` | → `processes(id)` CASCADE |

### Validation (di kode)
- `Respondent::create`/`update` → `sanitize()` + nullable `no_hp`/`email`.
- `Answer::save` → cek `nilai BETWEEN 0 AND 5` di controller (`PenilaianController::savePenilaian`).
- `DesignFactorController` → cek `kode_df` & `nama_df` non-empty; `status` ∈ enum.

### Referential Integrity
- ✅ Penuh via FK. Tidak ada nullable FK.
- ⚠️ `design_factors` tidak ada FK constraint (sesuai desain: tabel independen).

---

## 8. Performance Considerations

### Indexes
- **PK** (otomatis clustered) di semua tabel: `id`.
- **UNIQUE** otomatis membuat index: `users.username`, `(assessment_answers.respondent_id, question_id)`, `(results.respondent_id, process_id)`.
- **FK** otomatis membuat index: semua kolom FK di atas.
- ⚠️ **Tidak ada** index pada:
  - `assessment_answers.created_at` — padahal query filter `DATE(created_at)` dipakai di dashboard/analisis/laporan.
  - `results.created_at` — sama, untuk filter tanggal.
  - `design_factors.kode_df` — padahal `ensureDefaults()` & lookup by kode sering dipakai (tapi tabel kecil, OK).

### Potensi Bottleneck
- **Multiple aggregate queries** di `DashboardController::index` & `AnalisisController::index` — 4-6 query berbeda per load. Untuk volume data kecil (ratusan baris) masih OK; untuk ribuan akan terasa.
- **Tidak ada pagination** — `getAll()` mengembalikan semua baris. Risiko memory untuk view admin.
- **`getAll($tanggal)` di `Result`** melakukan JOIN ke `respondents` & `processes` + filter `DATE(created_at)` — bisa lambat tanpa index `created_at`.
- **Dompdf** render PDF melakukan parse HTML lengkap — halaman laporan kompleks dengan tabel besar akan lambat.

### Tabel Besar (Potensi)
- `assessment_answers` — N responden × N pertanyaan per domain (12). Untuk 1000 responden = 12.000 rows. Index `(respondent_id, question_id)` sudah cukup untuk query by responden.
- `results` — N responden × N domain (2). Untuk 1000 responden = 2.000 rows. Index `(respondent_id, process_id)` cukup.

---

## 9. Database Risks

### Missing Indexes
- `assessment_answers.created_at`
- `results.created_at`
- `design_factors.kode_df` (low priority — tabel kecil)
- `respondents.tanggal_input` (low priority — biasanya filter via `results.created_at`)

### N+1 / Loop Risk
- `PenilaianController::dss01/dss05`: setelah ambil `$questions` dan `$answers`, view loop pertanyaan. **OK** untuk 6 pertanyaan, tapi tidak scalable.
- `Result::getByProcessId($processId, $tanggal)` melakukan JOIN ke `respondents` — **OK** karena single query.

### Unused / Redundant Fields
- `processes.aktif` (TINYINT) — tidak dipakai di query manapun. Bisa dihapus atau dipakai untuk filter.
- `results.capability_level` (skala 0-1) — redundan dengan `rata_rata` (skala 0-5). Bisa dihitung on-the-fly.
- `users.role` — selalu `admin`. Saat ini tidak ada variasi role.

### Data Quality
- `database.sql` seed mengandung `password = 'password'` (default) — **wajib diganti** saat deploy pertama.
- `database.sql` seed jawaban & hasil dummy untuk 3 responden — akan污染污染污染 data production jika tidak dihapus.
- `results.target_level` di DB punya `DEFAULT 4`, tapi runtime pakai `TARGET_LEVEL` (saat ini `5`). Inkonsisten. Lihat PROJECT_CONTEXT §10.

### Backup & Recovery
- ⚠️ Tidak ada backup otomatis. Export `mysqldump` berkala harus dilakukan manual.
- Tidak ada Point-in-Time Recovery setup.

### Keamanan DB
- Koneksi pakai user `root` kosong (default XAMPP). ⚠️ Untuk production harus pakai user dedicated dengan privilege terbatas.
- Password DB hardcoded di `config/database.php` — bukan di env. ⚠️
- Tidak ada prepared statement untuk query `design_factors` di `ensureDefaults()`? Cek — semua INSERT di model sudah pakai prepared statement. **OK**.
