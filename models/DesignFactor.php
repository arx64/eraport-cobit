<?php
/**
 * Model DesignFactor
 * Mengelola data design factor COBIT 2019
 */
require_once __DIR__ . '/../helpers/functions.php';

class DesignFactor {
    private PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get all design factors
     * @return array
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM design_factors ORDER BY id");
        return $stmt->fetchAll();
    }
    
    /**
     * Get design factor by ID
     * @param int $id Design Factor ID
     * @return array|false
     */
    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM design_factors WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get total design factors count
     * @return int
     */
    public function getTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM design_factors");
        return (int) $stmt->fetch()['total'];
    }
    
    /**
     * Get design factors by status
     * @param string $status Status (Relevan/Tidak Relevan)
     * @return array
     */
    public function getByStatus(string $status): array {
        $stmt = $this->db->prepare("SELECT * FROM design_factors WHERE status = ? ORDER BY id");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
}
