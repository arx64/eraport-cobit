<?php
/**
 * Konfigurasi Database
 * Sistem Analisis Risiko TI e-Raport - COBIT 2019
 */

define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'eraport_cobit');
define('BASE_URL', 'http://localhost/eraport-cobit');

/**
 * Class Database
 * Mengelola koneksi ke database MySQL menggunakan PDO
 */
class Database {
    private static ?PDO $instance = null;

    /**
     * Mendapatkan instance koneksi PDO (Singleton pattern)
     * @return PDO
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                self::$instance = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                self::$instance->exec("SET time_zone = '+07:00'");
            } catch (PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    /**
     * Mencegah clone instance
     */
    private function __clone() {}
}

/**
 * Fungsi helper untuk mendapatkan koneksi database
 * @return PDO
 */
function db(): PDO {
    return Database::getInstance();
}
