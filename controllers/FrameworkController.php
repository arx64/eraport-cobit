<?php
/**
 * Framework Controller
 * Menangani tampilan informasi framework COBIT
 */
require_once __DIR__ . '/../models/Process.php';

class FrameworkController {
    private Process $processModel;
    
    public function __construct() {
        $this->processModel = new Process();
    }
    
    /**
     * Tampilkan halaman framework
     */
    public function index(): void {
        requireLogin();
        
        $data = [
            'title' => 'Framework COBIT',
            'processes' => $this->processModel->getAll()
        ];
        
        view("framework/index", $data);
    }
    
    /**
     * Tampilkan detail domain
     */
    public function domain(): void {
        requireLogin();
        
        $kode = $_GET['kode'] ?? '';
        $process = $this->processModel->getByCode($kode);
        
        if (!$process) {
            setFlash('error', "Domain tidak ditemukan.");
            redirect("framework");
        }
        
        // Parse tujuan
        $tujuanList = explode("\n", $process['tujuan']);
        
        $data = [
            'title' => 'Domain ' . $process['kode_domain'],
            'process' => $process,
            'tujuanList' => $tujuanList
        ];
        
        view("framework/domain", $data);
    }
}
