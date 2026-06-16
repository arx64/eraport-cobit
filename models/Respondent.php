<?php
/**
 * Model Respondent
 * Mengelola data responden penilaian
 */
require_once __DIR__ . '/../helpers/functions.php';

class Respondent {
    private PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get all respondents
     * @return array
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM respondents ORDER BY nama ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Get respondent by ID
     * @param int $id Respondent ID
     * @return array|false
     */
    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM respondents WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create new respondent
     * @param array $data Respondent data
     * @return int|false Inserted ID
     */
    public function create(array $data) {
        $stmt = $this->db->prepare("
            INSERT INTO respondents (nama, jabatan, unit, no_hp, email, tanggal_input)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            sanitize($data['nama']),
            sanitize($data['jabatan']),
            sanitize($data['unit']),
            !empty($data['no_hp']) ? sanitize($data['no_hp']) : null,
            !empty($data['email']) ? sanitize($data['email']) : null,
            $data['tanggal_input']
        ]);
        
        return $result ? (int) $this->db->lastInsertId() : false;
    }
    
    /**
     * Update respondent
     * @param int $id Respondent ID
     * @param array $data Data yang akan diupdate
     * @return bool
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE respondents 
            SET nama = ?, jabatan = ?, unit = ?, no_hp = ?, email = ?, tanggal_input = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            sanitize($data['nama']),
            sanitize($data['jabatan']),
            sanitize($data['unit']),
            !empty($data['no_hp']) ? sanitize($data['no_hp']) : null,
            !empty($data['email']) ? sanitize($data['email']) : null,
            $data['tanggal_input'],
            $id
        ]);
    }
    
    /**
     * Delete respondent
     * @param int $id Respondent ID
     * @return bool
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM respondents WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get total respondents count
     * @return int
     */
    public function getTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM respondents");
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Get respondents for dropdown
     * @return array
     */
    public function getForDropdown(): array {
        $stmt = $this->db->query("SELECT id, nama, jabatan FROM respondents ORDER BY nama");
        return $stmt->fetchAll();
    }
    
    /**
     * Check if respondent has answered questions for a process
     * @param int $respondentId Respondent ID
     * @param int $processId Process ID
     * @return bool
     */
    public function hasAnswered(int $respondentId, int $processId): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM assessment_answers aa
            JOIN assessment_questions aq ON aa.question_id = aq.id
            WHERE aa.respondent_id = ? AND aq.process_id = ?
        ");
        $stmt->execute([$respondentId, $processId]);
        return (int) $stmt->fetch()['count'] > 0;
    }
}
