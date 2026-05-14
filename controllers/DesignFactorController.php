<?php
/**
 * Design Factor Controller
 * Menangali tampilan design factor COBIT
 */
require_once __DIR__ . '/../models/DesignFactor.php';

class DesignFactorController {
    private DesignFactor $dfModel;
    
    public function __construct() {
        $this->dfModel = new DesignFactor();
    }
    
    /**
     * Tampilkan halaman design factor
     */
    public function index(): void {
        requireLogin();
        
        $data = [
            'title' => 'Design Factor',
            'designFactors' => $this->dfModel->getAll()
        ];
        
        view("design-factor/index", $data);
    }
}
