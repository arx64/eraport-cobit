<?php
/**
 * Analisis Controller
 * Menangani tampilan hasil analisis dan rekomendasi
 */
require_once __DIR__ . '/../models/Result.php';
require_once __DIR__ . '/../models/Respondent.php';
require_once __DIR__ . '/../models/Process.php';

class AnalisisController {
    private Result $resultModel;
    private Respondent $respondentModel;
    private Process $processModel;
    
    public function __construct() {
        $this->resultModel = new Result();
        $this->respondentModel = new Respondent();
        $this->processModel = new Process();
    }
    
    /**
     * Tampilkan halaman hasil analisis
     */
    public function index(): void {
        requireLogin();
        
        $processId = (int) ($_GET['process_id'] ?? 0);
        $respondentId = (int) ($_GET['respondent_id'] ?? 0);
        
        $results = [];
        $selectedResult = null;
        
        if ($processId && $respondentId) {
            $selectedResult = $this->resultModel->getByRespondentAndProcess($respondentId, $processId);
            $results = $this->resultModel->getByProcessId($processId);
        } elseif ($processId) {
            $results = $this->resultModel->getByProcessId($processId);
        } else {
            $results = $this->resultModel->getAll();
        }
        
        // Generate rekomendasi
        $recommendations = [];
        $aggregateResults = $this->resultModel->getAggregateByProcess();
        
        foreach ($aggregateResults as $result) {
            if ($result['avg_gap'] !== null) {
                $recommendations[$result['kode_domain']] = generateRecommendations(
                    $result['kode_domain'], 
                    (float) $result['avg_gap']
                );
            }
        }
        
        $data = [
            'title' => 'Hasil Analisis',
            'results' => $results,
            'selectedResult' => $selectedResult,
            'processes' => $this->processModel->getAll(),
            'respondents' => $this->respondentModel->getForDropdown(),
            'aggregateResults' => $aggregateResults,
            'statistics' => $this->resultModel->getStatistics(),
            'recommendations' => $recommendations,
            'processId' => $processId,
            'respondentId' => $respondentId
        ];
        
        view("analisis/index", $data);
    }
}
