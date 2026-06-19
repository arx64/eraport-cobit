<?php
/**
 * Dashboard Controller
 * Menangani tampilan dashboard
 */
require_once __DIR__ . '/../models/Respondent.php';
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Answer.php';
require_once __DIR__ . '/../models/Result.php';

class DashboardController {
    private Respondent $respondentModel;
    private Question $questionModel;
    private Answer $answerModel;
    private Result $resultModel;
    
    public function __construct() {
        $this->respondentModel = new Respondent();
        $this->questionModel = new Question();
        $this->answerModel = new Answer();
        $this->resultModel = new Result();
    }
    
    /**
     * Tampilkan dashboard
     */
    public function index(): void {
        requireLogin();
        
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $datesWithData = $this->resultModel->getAllDatesWithData();
        
        $data = [
            'title' => 'Dashboard',
            'totalResponden' => $this->respondentModel->getTotal(),
            'totalQuestionsDSS01' => $this->questionModel->getTotalByProcessId(1),
            'totalQuestionsDSS05' => $this->questionModel->getTotalByProcessId(2),
            'totalPenilaian' => $this->answerModel->getTotal($tanggal),
            'aggregateResults' => $this->resultModel->getAggregateByProcess($tanggal),
            'statistics' => $this->resultModel->getStatistics($tanggal),
            'answerSummary' => $this->answerModel->getSummary($tanggal),
            'tanggal' => $tanggal,
            'datesWithData' => $datesWithData
        ];
        
        view("dashboard/index", $data);
    }

    /**
     * AJAX endpoint: kembalikan daftar tanggal yang punya data (JSON)
     */
    public function apiDatesWithData(): void {
        requireLogin();
        header('Content-Type: application/json');
        echo json_encode($this->resultModel->getAllDatesWithData());
        exit;
    }
}
