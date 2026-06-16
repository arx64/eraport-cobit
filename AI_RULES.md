# AI_RULES

> Aturan pengembangan untuk AI Agent yang bekerja pada project **e-Raport COBIT 2019**. Baca **# START HERE** di akhir dokumen ini sebelum mulai bekerja.

---

## 1. Project Development Rules

### Pola Arsitektur: Custom MVC Ringan

Project ini **bukan** Laravel/Symfony/CodeIgniter. Ini PHP native dengan struktur:
- `index.php` = router (array `$routes`).
- `controllers/<Name>Controller.php` = 1 class per file, 1 method per route.
- `models/<Name>.php` = 1 class per file, pegang 1 tabel (+ helper). Tidak ada interface/abstract.
- `views/<area>/<page>.php` = template HTML + inline PHP.
- `helpers/functions.php` = utilitas + business logic non-Model.
- Tidak ada Service layer, Repository pattern, DTO, atau Middleware class.

**Wajib dipatuhi**:
- Tambah route = tambah baris di `$routes` di `index.php`.
- Tambah halaman = buat controller + view + (kalau perlu) tambah route.
- Tambah tabel = tambah `CREATE TABLE` di `database.sql` + buat model class.
- Business logic query/aggregate = letakkan di **Model**, bukan di Controller.

---

## 2. Coding Conventions

### Naming
| Aspek | Konvensi | Contoh |
|---|---|---|
| Class | PascalCase, singular noun | `Respondent`, `DesignFactor` |
| Method | camelCase, verb/noun | `getAll`, `getByProcessId`, `saveResult` |
| Property | camelCase | `$dfModel`, `$respondentModel` |
| Variabel lokal | camelCase | `$respondentId`, `$currentPage` |
| Konstanta | UPPER_SNAKE_CASE | `TARGET_LEVEL`, `BASE_URL`, `DB_HOST` |
| File class | PascalCase.php | `DesignFactor.php` |
| File view | lowercase, underscore/dash | `index.php`, `dss01.php` |
| Folder view | lowercase | `penilaian/`, `laporan/` |
| Tabel DB | snake_case, plural | `respondents`, `assessment_answers`, `design_factors` |
| Kolom DB | snake_case | `kode_domain`, `nilai`, `created_at` |
| Route path | lowercase, dash | `penilaian/save-responden`, `design-factor/update` |
| Route name (implisit) | method class | `PenilaianController::saveResponden` → `/penilaian/save-responden` |

### Folder Convention
- Models di `models/`, **1 file = 1 class**, file require `helpers/functions.php` di top.
- Controllers di `controllers/`, **1 file = 1 class**, file require model yang dipakai di top.
- Views di `views/<area>/<page>.php`. Area sesuai domain: `auth`, `dashboard`, `framework`, `design-factor`, `penilaian`, `analisis`, `laporan`, `question`, `layouts`.
- Layout di `views/layouts/`: `header`, `sidebar`, `topbar`, `footer` (di-require otomatis oleh `view()` kecuali pakai layout=false).

### Import / Require Convention
- `require_once` (bukan `require` atau `use`) — PHP native tidak punya autoloader.
- Model require `helpers/functions.php` di baris pertama (untuk `db()` & `sanitize()`).
- Controller require model yang dipakai, **tidak** require `helpers/functions.php` (sudah di-load via model).
- View tidak boleh `require` apa-apa — data datang dari `extract($data)` di `view()`.
- Untuk Dompdf: `require_once __DIR__ . '/../vendor/autoload.php';` di `LaporanController.php`.

### Error Handling
- **Koneksi DB gagal**: `die("Koneksi database gagal: ...")` di `config/database.php`.
- **Validasi input gagal**: `setFlash('error', "...")` + `redirect(...)` di controller.
- **Query gagal**: return `false` / `0` (lihat pattern `Respondent::create` return `(int) $this->db->lastInsertId() : false`).
- **Tidak ada exception custom** — semua pakai try/catch implisit via PDO `ERRMODE_EXCEPTION` (catch tidak ada di kode, hanya `die()` di config).
- ⚠️ **Tidak ada logger** — error silent di banyak tempat.

### Validation Pattern
1. Cek `$_SERVER['REQUEST_METHOD'] !== 'POST'` → `redirect(...)`.
2. `validateCsrfToken()` untuk POST (lihat `PenilaianController::saveResponden`).
3. Validasi field wajib `empty()` → `setFlash('error', ...)` + `redirect(...)`.
4. Sanitize dengan `sanitize()` untuk string, `(int)` cast untuk integer, `?:` untuk nullable.
5. Tipe-enforce enum (`in_array(..., ['Relevan', 'Tidak Relevan'], true)`).

### Sanitize Pattern
- **Output**: pakai `sanitize()` (alias `htmlspecialchars(trim(...), ENT_QUOTES, 'UTF-8')`).
- **Input DB**: sanitize string saat insert (`models/Respondent.php:47`), bind params via prepared statement.
- **URL**: hardcode `BASE_URL` prefix → `<?= BASE_URL ?>/path`.

---

## 3. Existing Patterns (Pertahankan)

### Pattern 1: Singleton PDO
- Class `Database::getInstance()` di `config/database.php:24` — **satu** koneksi untuk seluruh app.
- Diakses via helper `db()`.
- ❌ Jangan instansiasi `new PDO(...)` di model/controller.

### Pattern 2: `view($path, $data, $useLayout=true)`
- Central rendering function di `helpers/functions.php:466`.
- Extract `$data` ke scope lokal.
- Require layout (header/sidebar/topbar) → view → footer (kecuali `$useLayout=false`).
- Pemanggil TIDAK perlu `extract` manual.

### Pattern 3: Flash Message
- `setFlash('success'|'error'|'warning'|'info', $message)` di `helpers/functions.php:74`.
- Ditampilkan otomatis di `views/layouts/topbar.php:44` via `<?= showFlash() ?>`.
- ❌ Jangan echo flash manual di view.

### Pattern 4: CSRF
- `generateCsrfToken()` di `helpers/functions.php:42` — auto regenerate.
- `validateCsrfToken()` di `helpers/functions.php:53` — return true untuk non-POST otomatis.
- Pemakaian: `<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">` di form.
- ⚠️ **Tidak konsisten** — beberapa controller tidak validasi (lihat `updateResponden`). Pertahankan pola yang sudah validasi, **perbaiki** yang belum.

### Pattern 5: `requireLogin()`
- Di `helpers/functions.php:31` — cek session, redirect ke `/login` dengan flash error jika belum.
- Dipanggil di awal method controller yang butuh auth (bukan `AuthController`).
- ❌ Jangan pakai `isLoggedIn()` saja tanpa redirect.

### Pattern 6: Sort by kode_df Natural
- `models/DesignFactor.php::getAll()` pakai `strnatcmp` agar `DF1..DF11` urut benar.
- ✅ Pakai pola yang sama untuk tabel lain dengan kode sortable (jangan `ORDER BY kode_df` string biasa).

### Pattern 7: Helper Function Biasa (Bukan Static Method)
- `getCapabilityLabel`, `getGapStatus`, `generateRecommendations`, `calculateCapabilityLevel`, `saveResult`, `formatDate` — semua **function global** di `helpers/functions.php`, bukan method class.
- Dipanggil langsung: `getCapabilityLabel($nilai)`.
- Pertahankan pola ini untuk helper yang reusable lintas controller.

### Pattern 8: Auto-seed Master Data
- `models/DesignFactor.php::ensureDefaults()` — dipanggil tiap buka halaman, insert master jika belum ada.
- Pola ini juga dipakai untuk pertanyaan (seed di `database.sql`) dan proses (seed di `database.sql`).
- ⚠️ Trade-off: master di-reseed otomatis. Untuk tabel lain yang master, **pikirkan** apakah auto-seed atau one-time seed lebih sesuai.

### Pattern 9: `array_column($rows, 'field')` untuk Cek Keanggotaan
- `in_array('DF1', array_column($designFactors, 'kode_df'))` untuk cek apakah kode ada di hasil query.
- ✅ Pertahankan untuk lookup ringan tanpa query tambahan.

### Pattern 10: Inline Chart.js + DataTables
- JS ditulis inline di akhir view (lihat `views/analisis/index.php`, `views/dashboard/index.php`).
- Data dilempar via `<?= json_encode(...) ?>`.
- ✅ Pertahankan inline. Untuk halaman sangat kompleks, **consider** extract ke `assets/js/`.

---

## 4. AI Modification Rules

### ❌ JANGAN
1. **Jangan ubah struktur folder** (tambah/hapus/rename) tanpa alasan kuat dan diskusi dengan maintainer.
2. **Jangan ubah schema database** (tambah kolom/tabel/constraint) tanpa update `database.sql` + cek dampak ke model.
3. **Jangan hapus endpoint** yang dipakai view manapun. Cari dulu `grep` referensi.
4. **Jangan ubah public API controller** (nama method, signature, route path) tanpa cek semua view yang link ke route.
5. **Jangan hardcode asset path** (mis. `assets/img/x.jpeg`) — selalu `<?= BASE_URL ?>/assets/img/x.jpeg`.
6. **Jangan hardcode magic number** untuk process_id (1=DSS01, 2=DSS05) tanpa lookup. Jika memungkinkan, ganti ke `Process::getByCode('DSS01')['id']`.
7. **Jangan pakai `echo` di Model** — model harus return value, bukan render.
8. **Jangan pakai `$_GET`/`$_POST` di Model** — controller yang handle input.
9. **Jangan pakai raw SQL concatenation** — selalu prepared statement.
10. **Jangan render HTML di Controller** — selalu lewat `view()`.

### ✅ BOLEH dengan Pertimbangan
1. Tambah route baru → tambah di `$routes` di `index.php` + buat method controller + (kalau perlu) view.
2. Tambah method di model → perhatikan naming camelCase + return type.
3. Refactor helper function → pastikan signature output tetap kompatibel dengan caller.
4. Tambah kolom di tabel → update `database.sql` + ALTER TABLE query + update model getter.
5. Tambah view → letakkan di `views/<area>/`, pakai layout default.
6. Tambah library Composer → update `composer.json` + jalankan `composer install` + require autoloader.

### ⚠️ BACKWARD COMPATIBILITY
- `view()` signature: `view(string $view, array $data = [], bool $useLayout = true): void` — JANGAN ubah tanpa update semua caller.
- `db()` signature: `db(): PDO` — JANGAN terima parameter.
- Konstanta `BASE_URL`, `DB_*`, `TARGET_LEVEL` — JANGAN rename.
- Route name → method name mapping: `/penilaian/save-responden` → `PenilaianController::saveResponden`. Pertahankan konvensi `kebab-case ↔ camelCase`.

---

## 5. Safe Refactoring Guidelines

### Area Aman untuk Refactor
- **CSS/JS statis** — split `views/laporan/pdf.php` inline `<style>` ke file terpisah (`assets/css/pdf.css`) dan include via `<link>`. Tdk ada dampak runtime karena Dompdf handle inline.
- **Helper duplikat** — jika ada helper yang sama di 2 tempat, satukan di `helpers/functions.php`.
- **Query model panjang** — extract jadi method khusus, mis. `Result::getStatisticsByDate()`.
- **Validation** — tambahkan method `validate()` private di controller yang handle input (lihat `DesignFactorController::validate/collectFromPost`).
- **Form serialization** — extract JavaScript handler untuk form besar (mis. auto-save) ke `assets/js/`.

### Refactor yang Membutuhkan Test (Tidak Ada Test Saat Ini)
- `calculateCapabilityLevel` (`helpers/functions.php:332`) — logika bisnis inti, harus punya unit test sebelum refactor.
- `saveResult` (`helpers/functions.php:383`) — UPSERT logic, sensitif terhadap race condition.
- `generateRecommendations` (`helpers/functions.php:244`) — branching by gap value, rapuh.

### Refactor yang BERBAHAYA (Hindari)
- **Schema migration besar** — tidak ada migration tool, ALTER TABLE manual.
- **Hapus kolom DB** — bisa break model & view.
- **Rename route** — harus update semua view `href`.
- **Ganti session backend** — kode sangat terikat native `$_SESSION`.
- **Ganti Dompdf** — coupled di `LaporanController` & composer.json.

---

## 6. Dangerous Areas (Risiko Tinggi)

| Area | File | Risiko | Alasan |
|---|---|---|---|
| **Authentication** | `controllers/AuthController.php`, `models/User.php`, `helpers/functions.php` (session/csrf) | 🔴 Tinggi | Compromise = full access. Password di-bcrypt ✅ tapi `session_regenerate_id` tidak dipanggil setelah login (session fixation risk). |
| **Session/CSRF** | `helpers/functions.php:7-58` | 🔴 Tinggi | Jantung proteksi. Pemakaian CSRF tidak konsisten di controller. |
| **Penilaian → Results** | `helpers/functions.php:332-438`, `PenilaianController::savePenilaian` | 🔴 Tinggi | UPSERT tanpa transaction, bisa terjadi partial write. |
| **Konstanta `TARGET_LEVEL`** | `helpers/functions.php:11` | 🟡 Sedang | Nilai saat ini `5` (kode), README & seed `4`. Mengubah = mengubah seluruh analisis gap. |
| **Dompdf** | `controllers/LaporanController.php`, `views/laporan/pdf.php` | 🟡 Sedang | Versi lama (2.0.3). HTML kompleks bisa gagal render. |
| **Auto-seed Design Factor** | `models/DesignFactor.php::ensureDefaults` | 🟡 Sedang | Master DF tidak bisa dihapus permanen. |
| **Kredensial DB** | `config/database.php` | 🔴 Tinggi | Hardcoded, user `root` kosong. |
| **Base URL** | `config/database.php` | 🟡 Sedang | Hardcoded subfolder `/eraport-cobit`. |
| **`.htaccess`** | root | 🟡 Sedang | `RewriteBase` hardcoded. Dipindah host = harus edit. |
| **Seeder `database.sql`** | `database.sql:101-103` | 🔴 Tinggi | Default password `password` untuk admin. **Wajib** diganti sebelum deploy. |

---

## 7. Testing Requirements

### Status Saat Ini
- ❌ **Tidak ada test sama sekali** (tidak ada folder `tests/`, tidak ada PHPUnit, tidak ada Pest).

### Standar Minimum ke Depan
1. **Framework**: PHPUnit (paling umum di PHP). Install via composer: `composer require --dev phpunit/phpunit`.
2. **Lokasi**: `tests/` di root, dengan subfolder `tests/Unit/`, `tests/Integration/`.
3. **Wajib ditest**:
   - `calculateCapabilityLevel` — berbagai input (0, 1, 5, dst) dan empty-state.
   - `saveResult` — UPSERT, insert, update, race condition.
   - `generateRecommendations` — semua branch gap untuk DSS01 & DSS05.
   - `getCapabilityLabel`, `getGapStatus`, `getGapBadge` — boundary values.
   - `DesignFactor::ensureDefaults` — idempotent (running 2x tidak insert duplikat).
   - `User::login` — password benar/salah.
4. **Cara menjalankan** (setelah setup):
   ```bash
   ./vendor/bin/phpunit
   ```
5. **Coverage target**: minimum 70% untuk `helpers/functions.php`, minimum 50% untuk model. Controller/view sulit di-test (butuh HTTP-level / E2E).

### Integration Test
- Bisa pakai DB testing schema (drop & re-import `database.sql` per test class).
- Atau SQLite in-memory (tapi beberapa syntax MySQL mungkin tidak kompatibel).

### E2E / Browser Test
- Bisa pakai Dusk (Laravel) atau Codeception. Tapi berhubung bukan framework, **consider** manual QA + screenshot untuk saat ini.

---

## 8. AI Onboarding Prompt

### # START HERE

**Project**: Sistem Analisis Risiko TI e-Raport - COBIT 2019. Aplikasi web admin (single-role) untuk analisis capability level sistem e-Raport di SMKN 1 Teluk Mengkudu. Fokus pada 2 domain COBIT: DSS01 (Manage Operations) dan DSS05 (Manage Security Services).

**Arsitektur**: PHP 8.x native, custom MVC ringan, MySQL 5.7+ via PDO. Frontend Bootstrap 5 + Chart.js + Bootstrap Icons (semua CDN). PDF via Dompdf 2.0.3. Session-based auth (satu role `admin`).

**Struktur direktori**:
- `index.php` = router (array `$routes`).
- `controllers/` = 1 class = 1 file, 1 method = 1 route.
- `models/` = 1 class = 1 tabel, return array.
- `views/` = template HTML + inline PHP.
- `helpers/functions.php` = utilitas + business logic (kapabilitas, rekomendasi).
- `config/database.php` = konstanta + PDO singleton.
- `assets/` = CSS/JS/img statis.

**Database**: 7 tabel di `eraport_cobit` — `users`, `respondents`, `processes` (DSS01, DSS05), `assessment_questions` (6/domain), `assessment_answers`, `results` (materialized), `design_factors` (master 11 DF + custom). Schema di `database.sql`. Tidak ada migration tool.

**Fitur utama**:
1. Login admin (session + CSRF + bcrypt).
2. CRUD responden & pertanyaan.
3. Form penilaian 0-5 per pertanyaan.
4. Kalkulasi capability level otomatis + rekomendasi per gap.
5. Dashboard dengan chart & filter tanggal.
6. Hasil analisis + laporan PDF (Dompdf) + export CSV.
7. Design Factor COBIT 2019 (master 11 + auto-seed via `ensureDefaults()`).

**Konstanta kritis**:
- `BASE_URL` di `config/database.php` — pakai `<?= BASE_URL ?>/...` untuk semua asset & link.
- `TARGET_LEVEL` di `helpers/functions.php:11` — saat ini `5` (⚠️ README & bisnis nyatakan `4`, inkonsisten).
- Process ID: DSS01 = `1`, DSS05 = `2` (hardcoded).

**Aturan penting**:
- **Wajib pakai `BASE_URL`** untuk path absolut di `src`/`href` agar tidak 404 di subfolder.
- **Wajib sanitasi output** dengan `sanitize()` (htmlspecialchars).
- **Wajib prepared statement** untuk semua query.
- **Tidak boleh hardcode magic number** untuk ID proses tanpa lookup.
- **Tidak boleh echo di Model**, **tidak boleh SQL di View**.
- **Tambah route** = tambah baris di `$routes` + buat method controller.
- **Tambah tabel** = update `database.sql` + buat model.
- **CSRF** harus divalidasi di semua POST handler (beberapa belum, perbaiki saat menyentuh).
- **Tidak ada test** — refactor business logic di `helpers/functions.php` dengan hati-hati.

**Known issues yang harus diketahui**:
- Bug `$gap` undefined di `helpers/functions.php:355`.
- `TARGET_LEVEL` inkonsisten (kode `5` vs README `4`).
- `PenilaianController::updateResponden` tidak validasi CSRF.
- Seed `database.sql` punya password default `password` — harus diganti.
- Auto-seed DF master (`ensureDefaults()`) — DF1-DF11 akan di-reseed jika dihapus.

**Sebelum menyentuh kode**: baca `PROJECT_CONTEXT.md` (peta project), `DATABASE_CONTEXT.md` (skema & relasi), lalu file spesifik yang akan diubah. Validasi syntax PHP dengan `php -l <file>` setelah edit. Cek dependency Composer (`composer install`) jika menambah library.
