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
     * @return array
     */
    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT r.*, res.nama as respondent_name, res.jabatan, p.kode_domain, p.nama_domain
            FROM results r
            JOIN respondents res ON r.respondent_id = res.id
            JOIN processes p ON r.process_id = p.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Get results by process ID
     * @param int $processId Process ID
     * @return array
     */
    public function getByProcessId(int $processId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, res.nama as respondent_name, res.jabatan
            FROM results r
            JOIN respondents res ON r.respondent_id = res.id
            WHERE r.process_id = ?
            ORDER BY r.capability_level DESC
        ");
        $stmt->execute([$processId]);
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
     * @return array
     */
    public function getAggregateByProcess(): array {
        $stmt = $this->db->query("
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
            GROUP BY p.id, p.kode_domain, p.nama_domain
            ORDER BY p.id
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Get overall statistics
     * @return array
     */
    public function getStatistics(): array {
        $stmt = $this->db->query("
            SELECT 
                AVG(capability_level) as avg_capability,
                AVG(gap) as avg_gap,
                MIN(capability_level) as min_capability,
                MAX(capability_level) as max_capability
            FROM results
        ");
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
}
