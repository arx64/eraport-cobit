<?php
/**
 * Auth Controller
 * Menangani autentikasi pengguna
 */
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Tampilkan halaman login
     */
    public function login(): void {
        if (isLoggedIn()) {
            redirect("dashboard");
        }
        
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        
        view("auth/login", ['error' => $error, 'title' => 'Login'], false);
    }
    
    /**
     * Proses login
     */
    public function authenticate(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("login");
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Username dan password wajib diisi.";
            redirect("login");
        }
        
        $user = $this->userModel->login($username, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            
            setFlash('success', "Selamat datang, " . $user['nama_lengkap'] . "!");
            redirect("dashboard");
        } else {
            $_SESSION['error'] = "Username atau password salah.";
            redirect("login");
        }
    }
    
    /**
     * Logout user
     */
    public function logout(): void {
        session_unset();
        session_destroy();
        redirect("login");
    }
}
