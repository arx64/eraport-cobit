<?php
/**
 * Model Result
 * Mengelola data hasil perhitungan capability level
 */
require_once __DIR__ . '/../helpers/functions.php';

class Result {
    private PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get all results
     * @param string|null $tanggal Optional date filter (Y-m-d)
     * @return array
     */
    public function getAll(?string $tanggal = null): array {
        $sql = "
            SELECT r.*, res.nama as respondent_name, res.jabatan, p.kode_domain, p.nama_domain
            FROM results r
            JOIN respondents res ON r.respondent_id = res.id
            JOIN processes p ON r.process_id = p.id
        ";
        if ($tanggal) {
            $sql .= " WHERE r.tanggal_penilaian = ?";
        }
        $sql .= " ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($tanggal) {
            $stmt->execute([$tanggal]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Get results by process ID
     * @param int $processId Process ID
     * @param string|null $tanggal Optional date filter (Y-m-d)
     * @return array
     */
    public function getByProcessId(int $processId, ?string $tanggal = null): array {
        $sql = "
            SELECT r.*, res.nama as respondent_name, res.jabatan
            FROM results r
            JOIN respondents res ON r.respondent_id = res.id
            WHERE r.process_id = ?
        ";
        if ($tanggal) {
            $sql .= " AND r.tanggal_penilaian = ?";
        }
        $sql .= " ORDER BY r.capability_level DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($tanggal) {
            $stmt->execute([$processId, $tanggal]);
        } else {
            $stmt->execute([$processId]);
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Get result by respondent and process for a specific date
     * @param int $respondentId Respondent ID
     * @param int $processId Process ID
     * @param string|null $tanggal Optional date filter (Y-m-d). If null, returns latest.
     * @return array|false
     */
    public function getByRespondentAndProcess(int $respondentId, int $processId, ?string $tanggal = null) {
        $sql = "
            SELECT r.*, res.nama as respondent_name, p.kode_domain, p.nama_domain
            FROM results r
            JOIN respondents res ON r.respondent_id = res.id
            JOIN processes p ON r.process_id = p.id
            WHERE r.respondent_id = ? AND r.process_id = ?
        ";
        $params = [$respondentId, $processId];

        if ($tanggal !== null) {
            $sql .= " AND r.tanggal_penilaian = ?";
            $params[] = $tanggal;
        }

        $sql .= " ORDER BY r.created_at DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Get aggregate results by process
     * @param string|null $tanggal Optional date filter (Y-m-d)
     * @return array
     */
    public function getAggregateByProcess(?string $tanggal = null): array {
        $sql = "
            SELECT 
                p.id as process_id,
                p.kode_domain,
                p.nama_domain,
                AVG(r.capability_level) as avg_capability,
                AVG(r.gap) as avg_gap,
                AVG(r.rata_rata) as avg_rata_rata,
                COUNT(DISTINCT r.respondent_id) as total_responden
            FROM processes p
            LEFT JOIN results r ON p.id = r.process_id
        ";
        if ($tanggal) {
            $sql .= " AND r.tanggal_penilaian = ?";
        }
        $sql .= " GROUP BY p.id, p.kode_domain, p.nama_domain ORDER BY p.id";
        
        $stmt = $this->db->prepare($sql);
        if ($tanggal) {
            $stmt->execute([$tanggal]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Get overall statistics
     * @param string|null $tanggal Optional date filter (Y-m-d)
     * @return array
     */
    public function getStatistics(?string $tanggal = null): array {
        $sql = "
            SELECT 
                AVG(capability_level) as avg_capability,
                AVG(gap) as avg_gap,
                MIN(capability_level) as min_capability,
                MAX(capability_level) as max_capability
            FROM results
        ";
        if ($tanggal) {
            $sql .= " WHERE tanggal_penilaian = ?";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($tanggal) {
            $stmt->execute([$tanggal]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetch();
    }
    
    /**
     * Delete result by ID
     * @param int $id Result ID
     * @return bool
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM results WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete all results
     * @return bool
     */
    public function deleteAll(): bool {
        return $this->db->query("DELETE FROM results") !== false;
    }

    /**
     * Get dates that have result data, with counts of assessments, evaluations and respondents
     * @return array Each row: { tanggal, total_penilaian, total_evaluasi, total_responden }
     */
    public function getDatesWithData(): array {
        $stmt = $this->db->query("
            SELECT 
                r.tanggal_penilaian as tanggal,
                (SELECT COUNT(*) 
                 FROM assessment_answers aa 
                 WHERE aa.tanggal_penilaian = r.tanggal_penilaian) as total_penilaian,
                COUNT(*) as total_evaluasi,
                COUNT(DISTINCT r.respondent_id) as total_responden
            FROM results r
            WHERE r.tanggal_penilaian IS NOT NULL
            GROUP BY r.tanggal_penilaian
            ORDER BY r.tanggal_penilaian DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get dates that have assessment answer data only (may have answers but no results yet)
     * @return array Each row: { tanggal, total_penilaian }
     */
    public function getDatesWithAnswers(): array {
        $stmt = $this->db->query("
            SELECT 
                tanggal_penilaian as tanggal,
                COUNT(*) as total_penilaian
            FROM assessment_answers
            WHERE tanggal_penilaian IS NOT NULL
            GROUP BY tanggal_penilaian
            ORDER BY tanggal_penilaian DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get all dates containing assessment/evaluation data, unioned from both tables
     * @return array Each row: { tanggal, total_penilaian, total_evaluasi, total_responden }
     */
    public function getAllDatesWithData(): array {
        $stmt = $this->db->query("
            SELECT 
                d.tanggal,
                COALESCE(a.total_penilaian, 0) as total_penilaian,
                COALESCE(r.total_evaluasi, 0) as total_evaluasi,
                COALESCE(r.total_responden, 0) as total_responden
            FROM (
                SELECT tanggal_penilaian as tanggal FROM assessment_answers WHERE tanggal_penilaian IS NOT NULL
                UNION
                SELECT tanggal_penilaian as tanggal FROM results WHERE tanggal_penilaian IS NOT NULL
            ) d
            LEFT JOIN (
                SELECT tanggal_penilaian, COUNT(*) as total_penilaian 
                FROM assessment_answers 
                WHERE tanggal_penilaian IS NOT NULL
                GROUP BY tanggal_penilaian
            ) a ON a.tanggal_penilaian = d.tanggal
            LEFT JOIN (
                SELECT tanggal_penilaian, COUNT(*) as total_evaluasi, COUNT(DISTINCT respondent_id) as total_responden
                FROM results
                WHERE tanggal_penilaian IS NOT NULL
                GROUP BY tanggal_penilaian
            ) r ON r.tanggal_penilaian = d.tanggal
            ORDER BY d.tanggal DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get result by respondent, process, and specific date
     * @param int $respondentId Respondent ID
     * @param int $processId Process ID
     * @param string $tanggal Tanggal (Y-m-d)
     * @return array|false
     */
    private function getByRespondentProcessDate(int $respondentId, int $processId, string $tanggal) {
        $stmt = $this->db->prepare("
            SELECT * FROM results 
            WHERE respondent_id = ? AND process_id = ? AND tanggal_penilaian = ?
            LIMIT 1
        ");
        $stmt->execute([$respondentId, $processId, $tanggal]);
        return $stmt->fetch();
    }

    /**
     * Create or update result for a specific date
     * @param int $respondentId Respondent ID
     * @param int $processId Process ID
     * @param array $calculation Calculation result
     * @param string|null $tanggal Tanggal (Y-m-d). Default: today.
     * @return bool
     */
    public function save(int $respondentId, int $processId, array $calculation, ?string $tanggal = null): bool {
        if ($tanggal === null) {
            $tanggal = date('Y-m-d');
        }

        $existing = $this->getByRespondentProcessDate($respondentId, $processId, $tanggal);
        
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE results SET
                    total_nilai = ?,
                    rata_rata = ?,
                    capability_level = ?,
                    current_level = ?,
                    target_level = ?,
                    gap = ?,
                    status = ?
                WHERE respondent_id = ? AND process_id = ? AND tanggal_penilaian = ?
            ");
            return $stmt->execute([
                $calculation['total_nilai'],
                $calculation['rata_rata'],
                $calculation['capability_level'],
                $calculation['current_level'],
                $calculation['target_level'],
                $calculation['gap'],
                $calculation['status'],
                $respondentId,
                $processId,
                $tanggal
            ]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO results 
                (respondent_id, process_id, tanggal_penilaian, total_nilai, rata_rata, capability_level, 
                 current_level, target_level, gap, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $respondentId,
                $processId,
                $tanggal,
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
}
