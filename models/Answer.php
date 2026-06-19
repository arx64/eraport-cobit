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
     * @param string|null $tanggal Filter by date (Y-m-d). If null, returns all.
     * @return array
     */
    public function getByRespondentAndProcess(int $respondentId, int $processId, ?string $tanggal = null): array {
        $sql = "
            SELECT aa.*, aq.kode_pertanyaan, aq.pertanyaan, aq.komponen
            FROM assessment_answers aa
            JOIN assessment_questions aq ON aa.question_id = aq.id
            WHERE aa.respondent_id = ? AND aq.process_id = ?
        ";
        $params = [$respondentId, $processId];

        if ($tanggal !== null) {
            $sql .= " AND aa.tanggal_penilaian = ?";
            $params[] = $tanggal;
        }

        $sql .= " ORDER BY aq.urutan";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get distinct assessment dates for a respondent and process
     * @param int $respondentId Respondent ID
     * @param int $processId Process ID
     * @return array List of distinct dates (Y-m-d)
     */
    public function getDatesByRespondentAndProcess(int $respondentId, int $processId): array {
        $stmt = $this->db->prepare("
            SELECT DISTINCT aa.tanggal_penilaian as tanggal
            FROM assessment_answers aa
            JOIN assessment_questions aq ON aa.question_id = aq.id
            WHERE aa.respondent_id = ? AND aq.process_id = ?
            ORDER BY tanggal DESC
        ");
        $stmt->execute([$respondentId, $processId]);
        return array_column($stmt->fetchAll(), 'tanggal');
    }
    
    /**
     * Get answer by respondent, question, and specific date
     * @param int $respondentId Respondent ID
     * @param int $questionId Question ID
     * @param string $tanggal Tanggal penilaian (Y-m-d)
     * @return array|false
     */
    private function getByRespondentQuestionDate(int $respondentId, int $questionId, string $tanggal) {
        $stmt = $this->db->prepare("
            SELECT * FROM assessment_answers 
            WHERE respondent_id = ? AND question_id = ? AND tanggal_penilaian = ?
            LIMIT 1
        ");
        $stmt->execute([$respondentId, $questionId, $tanggal]);
        return $stmt->fetch();
    }
    
    /**
     * Create or update answer for a specific date
     * @param int $respondentId Respondent ID
     * @param int $questionId Question ID
     * @param int $nilai Nilai (0-5)
     * @param string|null $keterangan Keterangan
     * @param string|null $tanggal Tanggal penilaian (Y-m-d). Default: hari ini.
     * @return bool
     */
    public function save(int $respondentId, int $questionId, int $nilai, ?string $keterangan = null, ?string $tanggal = null): bool {
        if ($tanggal === null) {
            $tanggal = date('Y-m-d');
        }
        
        $existing = $this->getByRespondentQuestionDate($respondentId, $questionId, $tanggal);
        
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE assessment_answers 
                SET nilai = ?, keterangan = ?
                WHERE respondent_id = ? AND question_id = ? AND tanggal_penilaian = ?
            ");
            return $stmt->execute([$nilai, $keterangan, $respondentId, $questionId, $tanggal]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO assessment_answers (respondent_id, question_id, tanggal_penilaian, nilai, keterangan)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$respondentId, $questionId, $tanggal, $nilai, $keterangan]);
        }
    }
    
    /**
     * Delete answer
     * @param int $id Answer ID
     * @return bool
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM assessment_answers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
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
        $stmt->execute([$respondentId, $processId]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Get total answers count
     * @param string|null $tanggal Optional date filter (Y-m-d)
     * @return int
     */
    public function getTotal(?string $tanggal = null): int {
        if ($tanggal) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM assessment_answers WHERE tanggal_penilaian = ?");
            $stmt->execute([$tanggal]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM assessment_answers");
        }
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Get answers summary for dashboard
     * @param string|null $tanggal Optional date filter (Y-m-d)
     * @return array
     */
    public function getSummary(?string $tanggal = null): array {
        $sql = "
            SELECT 
                aq.process_id,
                p.kode_domain,
                COUNT(*) as total_jawaban,
                AVG(aa.nilai) as rata_rata
            FROM assessment_answers aa
            JOIN assessment_questions aq ON aa.question_id = aq.id
            JOIN processes p ON aq.process_id = p.id
        ";
        if ($tanggal) {
            $sql .= " WHERE aa.tanggal_penilaian = ?";
        }
        $sql .= " GROUP BY aq.process_id, p.kode_domain";
        
        $stmt = $this->db->prepare($sql);
        if ($tanggal) {
            $stmt->execute([$tanggal]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Delete all answers
     * @return bool
     */
    public function deleteAll(): bool {
        return $this->db->query("DELETE FROM assessment_answers") !== false;
    }
}
