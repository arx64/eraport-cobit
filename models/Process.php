<?php
/**
 * Model Process
 * Mengelola data domain/proses COBIT
 */
require_once __DIR__ . '/../helpers/functions.php';

class Process {
    private PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get all processes
     * @return array
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM processes ORDER BY id");
        return $stmt->fetchAll();
    }
    
    /**
     * Get process by ID
     * @param int $id Process ID
     * @return array|false
     */
    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM processes WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get process by domain code
     * @param string $kodeDomain Domain code
     * @return array|false
     */
    public function getByCode(string $kodeDomain) {
        $stmt = $this->db->prepare("SELECT * FROM processes WHERE kode_domain = ? LIMIT 1");
        $stmt->execute([$kodeDomain]);
        return $stmt->fetch();
    }
}
