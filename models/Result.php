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
            $sql .= " WHERE DATE(r.created_at) = ?";
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
            $sql .= " AND DATE(r.created_at) = ?";
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
     * Get result by respondent and process
     * @param int $respondentId Respondent ID
     * @param int $processId Process ID
     * @return array|false
     */
    public function getByRespondentAndProcess(int $respondentId, int $processId) {
        $stmt = $this->db->prepare("
            SELECT r.*, res.nama as respondent_name, p.kode_domain, p.nama_domain
            FROM results r
            JOIN respondents res ON r.respondent_id = res.id
            JOIN processes p ON r.process_id = p.id
            WHERE r.respondent_id = ? AND r.process_id = ?
            LIMIT 1
        ");
        $stmt->execute([$respondentId, $processId]);
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
            $sql .= " AND DATE(r.created_at) = ?";
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
            $sql .= " WHERE DATE(created_at) = ?";
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
        return $stmt->execute([$id]);
    }

    /**
     * Delete all results
     * @return bool
     */
    public function deleteAll(): bool {
        return $this->db->query("DELETE FROM results") !== false;
    }

    /**
     * Get dates that have result data
     * @return array
     */
    public function getDatesWithData(): array {
        $stmt = $this->db->query("
            SELECT DISTINCT DATE(created_at) as tanggal 
            FROM results 
            ORDER BY tanggal DESC
        ");
        return $stmt->fetchAll();
    }
}
