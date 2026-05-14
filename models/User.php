<?php
/**
 * Model User
 * Mengelola data pengguna sistem
 */
require_once __DIR__ . '/../helpers/functions.php';

class User {
    private PDO $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Login user
     * @param string $username Username
     * @param string $password Password
     * @return array|false Data user jika berhasil, false jika gagal
     */
    public function login(string $username, string $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get user by ID
     * @param int $id User ID
     * @return array|false
     */
    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Update user profile
     * @param int $id User ID
     * @param array $data Data yang akan diupdate
     * @return bool
     */
    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        
        if (!empty($data['nama_lengkap'])) {
            $fields[] = 'nama_lengkap = ?';
            $values[] = sanitize($data['nama_lengkap']);
        }
        if (!empty($data['email'])) {
            $fields[] = 'email = ?';
            $values[] = sanitize($data['email']);
        }
        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $values[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Get total admin count
     * @return int
     */
    public function getTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
        return (int) $stmt->fetch()['total'];
    }
}
