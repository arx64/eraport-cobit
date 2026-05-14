<?php
/**
 * Model Question
 * Mengelola data pertanyaan penilaian
 */
require_once __DIR__ . '/../helpers/functions.php';

class Question {
    private PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get questions by process ID
     * @param int $processId Process ID
     * @return array
     */
    public function getByProcessId(int $processId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM assessment_questions 
            WHERE process_id = ? 
            ORDER BY urutan
        ");
        $stmt->execute([$processId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get total questions count
     * @return int
     */
    public function getTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM assessment_questions");
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Get total questions by process ID
     * @param int $processId Process ID
     * @return int
     */
    public function getTotalByProcessId(int $processId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM assessment_questions WHERE process_id = ?
        ");
        $stmt->execute([$processId]);
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Get question by ID
     * @param int $id Question ID
     * @return array|false
     */
    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM assessment_questions WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
