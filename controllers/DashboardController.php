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
        
        $data = [
            'title' => 'Dashboard',
            'totalResponden' => $this->respondentModel->getTotal(),
            'totalQuestionsDSS01' => $this->questionModel->getTotalByProcessId(1),
            'totalQuestionsDSS05' => $this->questionModel->getTotalByProcessId(2),
            'totalPenilaian' => $this->answerModel->getTotal(),
            'aggregateResults' => $this->resultModel->getAggregateByProcess(),
            'statistics' => $this->resultModel->getStatistics(),
            'answerSummary' => $this->answerModel->getSummary()
        ];
        
        view("dashboard/index", $data);
    }
}
