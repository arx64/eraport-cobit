<?php
/**
 * Helper Functions
 * Sistem Analisis Risiko TI e-Raport - COBIT 2019
 */

session_start();

require_once __DIR__ . '/../config/database.php';

define('TARGET_LEVEL', 5);

/**
 * Redirect ke URL tertentu
 * @param string $path Path tujuan
 */
function redirect(string $path): void {
    header("Location: " . BASE_URL . "/" . $path);
    exit();
}

/**
 * Cek apakah user sudah login
 * @return bool
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Cek apakah user sudah login, jika belum redirect ke login
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Silakan login terlebih dahulu.";
        redirect("login");
    }
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validasi CSRF token
 * @return bool
 */
function validateCsrfToken(): bool {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return true;
    
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Sanitasi input string
 * @param string $input Input yang akan disanitasi
 * @return string
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Set flash message
 * @param string $type Jenis pesan (success, error, warning, info)
 * @param string $message Isi pesan
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get dan hapus flash message
 * @return array|null
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Tampilkan flash message sebagai HTML
 * @return string
 */
function showFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    
    $alertClasses = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    $alertClass = $alertClasses[$flash['type']] ?? 'alert-info';
    
    $icons = [
        'success' => 'check-circle-fill',
        'error' => 'exclamation-triangle-fill',
        'warning' => 'exclamation-triangle-fill',
        'info' => 'info-circle-fill'
    ];
    $icon = $icons[$flash['type']] ?? 'info-circle-fill';
    
    return sprintf(
        '<div class="alert %s alert-dismissible fade show" role="alert">
            <i class="bi bi-%s me-2"></i>%s
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>',
        $alertClass, $icon, sanitize($flash['message'])
    );
}

/**
 * Format tanggal Indonesia
 * @param string $date Tanggal format Y-m-d
 * @return string
 */
function formatDate(string $date): string {
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $parts = explode('-', $date);
    return $parts[2] . ' ' . $bulan[$parts[1]] . ' ' . $parts[0];
}

/**
 * Get capability level label berdasarkan nilai
 * @param float $nilai Nilai capability (0-5)
 * @return string
 */
function getCapabilityLabel(float $nilai): string {
    if ($nilai >= 4.5) {
        return 'Optimized';
    } elseif ($nilai >= 3.5) {
        return 'Managed and Measurable';
    } elseif ($nilai >= 2.5) {
        return 'Defined';
    } elseif ($nilai >= 1.5) {
        return 'Repeatable';
    } elseif ($nilai >= 0.5) {
        return 'Initial';
    }
    return 'Non-existent';
}

/**
 * Get capability level color
 * @param float $nilai Nilai capability (0-5)
 * @return string
 */
function getCapabilityColor(float $nilai): string {
    if ($nilai >= 4.5) {
        return '#198754';
    } elseif ($nilai >= 3.5) {
        return '#0d6efd';
    } elseif ($nilai >= 2.5) {
        return '#0dcaf0';
    } elseif ($nilai >= 1.5) {
        return '#ffc107';
    } elseif ($nilai >= 0.5) {
        return '#fd7e14';
    }
    return '#dc3545';
}

/**
 * Get capability badge class
 * @param float $nilai Nilai capability (0-5)
 * @return string
 */
function getCapabilityBadge(float $nilai): string {
    if ($nilai >= 4.5) {
        return 'bg-success';
    } elseif ($nilai >= 3.5) {
        return 'bg-primary';
    } elseif ($nilai >= 2.5) {
        return 'bg-info text-dark';
    } elseif ($nilai >= 1.5) {
        return 'bg-warning text-dark';
    } elseif ($nilai >= 0.5) {
        return 'bg-warning text-dark';
    }
    return 'bg-danger';
}

/**
 * Get gap status
 * @param float $gap Nilai gap
 * @return string
 */
function getGapStatus(float $gap): string {
    if ($gap > 2.0) {
        return 'Sangat Kurang';
    } elseif ($gap > 1.5) {
        return 'Kurang';
    } elseif ($gap > 1.0) {
        return 'Cukup';
    } elseif ($gap > 0.5) {
        return 'Baik';
    }
    return 'Sangat Baik';
}

/**
 * Get gap badge class
 * @param float $gap Nilai gap
 * @return string
 */
function getGapBadge(float $gap): string {
    if ($gap > 2.0) {
        return 'bg-danger';
    } elseif ($gap > 1.5) {
        return 'bg-warning text-dark';
    } elseif ($gap > 1.0) {
        return 'bg-warning text-dark';
    } elseif ($gap > 0.5) {
        return 'bg-primary';
    }
    return 'bg-success';
}

/**
 * Generate rekomendasi berdasarkan domain dan gap
 * @param string $domain Kode domain
 * @param float $gap Nilai gap
 * @return array
 */
function generateRecommendations(string $domain, float $gap): array {
    $recommendations = [];
    
    if ($domain === 'DSS01') {
        if ($gap > 2.0) {
            $recommendations = [
                'Segera menyusun SOP pengelolaan operasional sistem e-Raport',
                'Menyediakan sistem monitoring otomatis untuk ketersediaan sistem',
                'Menyusun rencana pemeliharaan dan backup data yang terjadwal',
                'Melakukan pelatihan untuk operator sistem e-Raport',
                'Menyusun rencana kontinuitas layanan (Business Continuity Plan)'
            ];
        } elseif ($gap > 1.5) {
            $recommendations = [
                'Melengkapi dokumentasi prosedur operasional',
                'Mengimplementasikan sistem ticketing untuk permintaan layanan',
                'Melakukan evaluasi kapasitas sistem secara berkala',
                'Meningkatkan frekuensi backup data',
                'Menyusun laporan pemantauan operasional rutin'
            ];
        } elseif ($gap > 1.0) {
            $recommendations = [
                'Memperbarui prosedur operasional sesuai kondisi terkini',
                'Meningkatkan cakupan monitoring sistem',
                'Melakukan review jadwal pemeliharaan',
                'Melengkapi dokumentasi insiden'
            ];
        } elseif ($gap > 0.5) {
            $recommendations = [
                'Melakukan fine-tuning pada parameter monitoring',
                'Memperbarui dokumentasi sesuai perubahan sistem',
                'Melakukan evaluasi efektivitas prosedur yang ada'
            ];
        } else {
            $recommendations = [
                'Pertahankan praktik pengelolaan operasional yang ada',
                'Lakukan continuous improvement',
                'Dokumentasikan best practices'
            ];
        }
    } elseif ($domain === 'DSS05') {
        if ($gap > 2.0) {
            $recommendations = [
                'Segera menyusun kebijakan keamanan informasi yang komprehensif',
                'Mengimplementasikan kontrol akses berbasis peran (RBAC)',
                'Mengaktifkan enkripsi data sensitif dalam sistem e-Raport',
                'Menyusun prosedur respons insiden keamanan',
                'Melakukan audit keamanan menyeluruh',
                'Menerapkan sistem deteksi intrusi (IDS)'
            ];
        } elseif ($gap > 1.5) {
            $recommendations = [
                'Melengkapi kebijakan keamanan informasi',
                'Meningkatkan pengawasan aktivitas pengguna',
                'Melakukan audit hak akses secara berkala',
                'Mengimplementasikan sistem logging yang lebih baik',
                'Menyusun rencana pemulihan bencana (DRP)'
            ];
        } elseif ($gap > 1.0) {
            $recommendations = [
                'Memperbarui kebijakan keamanan sesuai ancaman terkini',
                'Meningkatkan frekuensi audit keamanan',
                'Melakukan penyegaran kesadaran keamanan bagi pengguna'
            ];
        } elseif ($gap > 0.5) {
            $recommendations = [
                'Melakukan review kontrol keamanan yang ada',
                'Memperbarui daftar ancaman dan mitigasi',
                'Melakukan penetration testing ringan'
            ];
        } else {
            $recommendations = [
                'Pertahankan standar keamanan yang ada',
                'Lakukan continuous monitoring',
                'Ikuti perkembangan ancaman keamanan terkini'
            ];
        }
    }
    
    return $recommendations;
}

/**
 * Hitung capability level otomatis
 * @param int $respondentId ID responden
 * @param int $processId ID proses/domain
 * @return array
 */
function calculateCapabilityLevel(int $respondentId, int $processId): array {
    $db = db();
    
    // Hitung total nilai
    $stmt = $db->prepare("
        SELECT SUM(nilai) as total, COUNT(*) as jumlah 
        FROM assessment_answers aa
        JOIN assessment_questions aq ON aa.question_id = aq.id
        WHERE aa.respondent_id = ? AND aq.process_id = ?
    ");
    $stmt->execute([$respondentId, $processId]);
    $result = $stmt->fetch();
    
    $totalNilai = (int) ($result['total'] ?? 0);
    $jumlahPertanyaan = (int) ($result['jumlah'] ?? 0);
    
    if ($jumlahPertanyaan === 0) {
        return [
            'total_nilai' => 0,
            'rata_rata' => 0,
            'capability_level' => 0,
            'current_level' => 'Non-existent',
            'target_level' => TARGET_LEVEL,
            'gap' => $gap,
            'status' => 'Sangat Kurang'
        ];
    }
    
    $rataRata = round($totalNilai / $jumlahPertanyaan, 2);
    $capabilityLevel = round($rataRata / 5, 2); // Normalisasi ke skala 0-1
    $currentLevel = getCapabilityLabel($rataRata);
    $targetLevel = TARGET_LEVEL;
    $gap = round($targetLevel - $rataRata, 2);
    $status = getGapStatus($gap);
    
    return [
        'total_nilai' => $totalNilai,
        'rata_rata' => $rataRata,
        'capability_level' => $capabilityLevel,
        'current_level' => $currentLevel,
        'target_level' => $targetLevel,
        'gap' => $gap,
        'status' => $status
    ];
}

/**
 * Simpan atau update hasil perhitungan
 * @param int $respondentId ID responden
 * @param int $processId ID proses/domain
 */
function saveResult(int $respondentId, int $processId): void {
    $calculation = calculateCapabilityLevel($respondentId, $processId);
    $db = db();
    
    // Cek apakah sudah ada hasil
    $stmt = $db->prepare("
        SELECT id FROM results 
        WHERE respondent_id = ? AND process_id = ?
    ");
    $stmt->execute([$respondentId, $processId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update
        $stmt = $db->prepare("
            UPDATE results SET
                total_nilai = ?,
                rata_rata = ?,
                capability_level = ?,
                current_level = ?,
                target_level = ?,
                gap = ?,
                status = ?
            WHERE respondent_id = ? AND process_id = ?
        ");
        $stmt->execute([
            $calculation['total_nilai'],
            $calculation['rata_rata'],
            $calculation['capability_level'],
            $calculation['current_level'],
            $calculation['target_level'],
            $calculation['gap'],
            $calculation['status'],
            $respondentId,
            $processId
        ]);
    } else {
        // Insert baru
        $stmt = $db->prepare("
            INSERT INTO results 
            (respondent_id, process_id, total_nilai, rata_rata, capability_level, 
             current_level, target_level, gap, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $respondentId,
            $processId,
            $calculation['total_nilai'],
            $calculation['rata_rata'],
            $calculation['capability_level'],
            $calculation['current_level'],
            $calculation['target_level'],
            $calculation['gap'],
            $calculation['status']
        ]);
    }
}

/**
 * Get current page name
 * @return string
 */
function currentPage(): string {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $parts = explode('/', trim($uri, '/'));
    return end($parts) ?: 'dashboard';
}

/**
 * Check if menu item is active
 * @param string $page Halaman yang akan dicek
 * @return string
 */
function isActive(string $page): string {
    return currentPage() === $page ? 'active' : '';
}

/**
 * Render view dengan layout
 * @param string $view Path view
 * @param array $data Data yang akan dipass ke view
 * @param bool $useLayout Gunakan layout atau tidak
 */
function view(string $view, array $data = [], bool $useLayout = true): void {
    extract($data);
    
    if ($useLayout) {
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/layouts/sidebar.php';
        require_once __DIR__ . '/../views/layouts/topbar.php';
        require_once __DIR__ . '/../views/' . $view . '.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    } else {
        require_once __DIR__ . '/../views/' . $view . '.php';
    }
}
