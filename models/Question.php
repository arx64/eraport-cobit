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

    /**
     * Get all questions with process name
     * @return array
     */
    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT aq.*, p.kode_domain, p.nama_domain
            FROM assessment_questions aq
            JOIN processes p ON aq.process_id = p.id
            ORDER BY aq.process_id, aq.urutan
        ");
        return $stmt->fetchAll();
    }

    /**
     * Create a new question
     * @param array $data
     * @return int|false
     */
    public function create(array $data) {
        $stmt = $this->db->prepare("
            INSERT INTO assessment_questions (process_id, kode_pertanyaan, pertanyaan, komponen, urutan)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['process_id'],
            $data['kode_pertanyaan'],
            $data['pertanyaan'],
            $data['komponen'],
            $data['urutan']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Update a question
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE assessment_questions SET
                process_id = ?,
                kode_pertanyaan = ?,
                pertanyaan = ?,
                komponen = ?,
                urutan = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['process_id'],
            $data['kode_pertanyaan'],
            $data['pertanyaan'],
            $data['komponen'],
            $data['urutan'],
            $id
        ]);
    }

    /**
     * Delete a question
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM assessment_questions WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
