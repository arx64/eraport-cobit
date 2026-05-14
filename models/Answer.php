<?php
/**
 * Model Answer
 * Mengelola data jawaban penilaian
 */
require_once __DIR__ . '/../helpers/functions.php';

class Answer {
    private PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get answers by respondent and process
     * @param int $respondentId Respondent ID
     * @param int $processId Process ID
     * @return array
     */
    public function getByRespondentAndProcess(int $respondentId, int $processId): array {
        $stmt = $this->db->prepare("
            SELECT aa.*, aq.kode_pertanyaan, aq.pertanyaan, aq.komponen
            FROM assessment_answers aa
            JOIN assessment_questions aq ON aa.question_id = aq.id
            WHERE aa.respondent_id = ? AND aq.process_id = ?
            ORDER BY aq.urutan
        ");
        $stmt->execute([$respondentId, $processId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get answer by respondent and question
     * @param int $respondentId Respondent ID
     * @param int $questionId Question ID
     * @return array|false
     */
    public function getByRespondentAndQuestion(int $respondentId, int $questionId) {
        $stmt = $this->db->prepare("
            SELECT * FROM assessment_answers 
            WHERE respondent_id = ? AND question_id = ? 
            LIMIT 1
        ");
        $stmt->execute([$respondentId, $questionId]);
        return $stmt->fetch();
    }
    
    /**
     * Create or update answer
     * @param int $respondentId Respondent ID
     * @param int $questionId Question ID
     * @param int $nilai Nilai (0-5)
     * @param string|null $keterangan Keterangan
     * @return bool
     */
    public function save(int $respondentId, int $questionId, int $nilai, ?string $keterangan = null): bool {
        $existing = $this->getByRespondentAndQuestion($respondentId, $questionId);
        
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE assessment_answers 
                SET nilai = ?, keterangan = ?
                WHERE respondent_id = ? AND question_id = ?
            ");
            return $stmt->execute([$nilai, $keterangan, $respondentId, $questionId]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO assessment_answers (respondent_id, question_id, nilai, keterangan)
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$respondentId, $questionId, $nilai, $keterangan]);
        }
    }
    
    /**
     * Delete answer
     * @param int $id Answer ID
     * @return bool
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM assessment_answers WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Delete all answers for a respondent and process
     * @param int $respondentId Respondent ID
     * @param int $processId Process ID
     * @return bool
     */
    public function deleteByRespondentAndProcess(int $respondentId, int $processId): bool {
        $stmt = $this->db->prepare("
            DELETE aa FROM assessment_answers aa
            JOIN assessment_questions aq ON aa.question_id = aq.id
            WHERE aa.respondent_id = ? AND aq.process_id = ?
        ");
        return $stmt->execute([$respondentId, $processId]);
    }
    
    /**
     * Get total answers count
     * @return int
     */
    public function getTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM assessment_answers");
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Get answers summary for dashboard
     * @return array
     */
    public function getSummary(): array {
        $stmt = $this->db->query("
            SELECT 
                aq.process_id,
                p.kode_domain,
                COUNT(*) as total_jawaban,
                AVG(aa.nilai) as rata_rata
            FROM assessment_answers aa
            JOIN assessment_questions aq ON aa.question_id = aq.id
            JOIN processes p ON aq.process_id = p.id
            GROUP BY aq.process_id, p.kode_domain
        ");
        return $stmt->fetchAll();
    }
}
