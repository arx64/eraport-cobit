<?php
/**
 * Main Router
 * Sistem Analisis Risiko TI e-Raport - COBIT 2019
 * 
 * Router sederhana yang mengarahkan request ke controller yang sesuai
 */

require_once __DIR__ . '/helpers/functions.php';

// Ambil URL path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$basePath = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
$path = trim(str_replace($basePath, '', $requestUri), '/');

// Hapus query string dari path
$path = explode('?', $path)[0];

// Route definitions
$routes = [
    // Auth routes
    '' => ['AuthController', 'login'],
    'login' => ['AuthController', 'login'],
    'authenticate' => ['AuthController', 'authenticate'],
    'logout' => ['AuthController', 'logout'],
    
    // Dashboard
    'dashboard' => ['DashboardController', 'index'],
    
    // Framework COBIT
    'framework' => ['FrameworkController', 'index'],
    'framework/domain' => ['FrameworkController', 'domain'],
    
    // Design Factor
    'design-factor' => ['DesignFactorController', 'index'],
    'design-factor/save' => ['DesignFactorController', 'save'],
    'design-factor/update' => ['DesignFactorController', 'update'],
    'design-factor/delete' => ['DesignFactorController', 'delete'],

    // Data Penilaian
    'penilaian/responden' => ['PenilaianController', 'responden'],
    'penilaian/save-responden' => ['PenilaianController', 'saveResponden'],
    'penilaian/update-responden' => ['PenilaianController', 'updateResponden'],
    'penilaian/delete-responden' => ['PenilaianController', 'deleteResponden'],
    'penilaian/dss01' => ['PenilaianController', 'dss01'],
    'penilaian/dss05' => ['PenilaianController', 'dss05'],
    'penilaian/save-penilaian' => ['PenilaianController', 'savePenilaian'],
    'penilaian/delete-penilaian' => ['PenilaianController', 'deletePenilaian'],
    
    // Data Pertanyaan
    'pertanyaan' => ['QuestionController', 'index'],
    'pertanyaan/save' => ['QuestionController', 'save'],
    'pertanyaan/update' => ['QuestionController', 'update'],
    'pertanyaan/delete' => ['QuestionController', 'delete'],
    'pertanyaan/reset-all' => ['QuestionController', 'resetAll'],
    
    // Hasil Analisis
    'analisis' => ['AnalisisController', 'index'],
    
    // Laporan
    'laporan' => ['LaporanController', 'index'],
    'laporan/pdf' => ['LaporanController', 'pdf'],
    'laporan/export' => ['LaporanController', 'export'],
];

// Cari route yang cocok
if (isset($routes[$path])) {
    [$controllerName, $methodName] = $routes[$path];
    
    // Load controller file
    $controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        // Instantiate controller and call method
        $controller = new $controllerName();
        if (method_exists($controller, $methodName)) {
            $controller->$methodName();
        } else {
            http_response_code(500);
            echo "Error: Method '{$methodName}' tidak ditemukan dalam controller '{$controllerName}'.";
        }
    } else {
        http_response_code(500);
        echo "Error: Controller file '{$controllerFile}' tidak ditemukan.";
    }
} else {
    // 404 Not Found
    http_response_code(404);
    
    // Tampilkan halaman 404 yang sesuai
    if (isLoggedIn()) {
        // User sudah login, tampilkan 404 dalam layout
        $data = ['title' => '404 - Halaman Tidak Ditemukan'];
        view("auth/404", $data);
    } else {
        // User belum login, redirect ke login
        $_SESSION['error'] = "Halaman tidak ditemukan.";
        redirect("login");
    }
}
