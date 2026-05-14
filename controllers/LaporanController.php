<?php
/**
 * Laporan Controller
 * Menangani generate laporan PDF dan export
 */
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../models/Result.php';
require_once __DIR__ . '/../models/Respondent.php';
require_once __DIR__ . '/../models/Process.php';

class LaporanController {
    private Result $resultModel;
    private Respondent $respondentModel;
    private Process $processModel;
    
    public function __construct() {
        $this->resultModel = new Result();
        $this->respondentModel = new Respondent();
        $this->processModel = new Process();
    }
    
    /**
     * Tampilkan halaman laporan
     */
    public function index(): void {
        requireLogin();
        
        $data = [
            'title' => 'Laporan',
            'aggregateResults' => $this->resultModel->getAggregateByProcess(),
            'allResults' => $this->resultModel->getAll(),
            'statistics' => $this->resultModel->getStatistics(),
            'respondents' => $this->respondentModel->getAll(),
            'processes' => $this->processModel->getAll()
        ];
        
        // Generate rekomendasi
        $recommendations = [];
        foreach ($data['aggregateResults'] as $result) {
            if ($result['avg_gap'] !== null) {
                $recommendations[$result['kode_domain']] = generateRecommendations(
                    $result['kode_domain'], 
                    (float) $result['avg_gap']
                );
            }
        }
        $data['recommendations'] = $recommendations;
        
        view("laporan/index", $data);
    }
    
    /**
     * Generate PDF report
     */
    public function pdf(): void {
        requireLogin();
        
        // Load data
        $aggregateResults = $this->resultModel->getAggregateByProcess();
        $allResults = $this->resultModel->getAll();
        $statistics = $this->resultModel->getStatistics();
        $respondents = $this->respondentModel->getAll();
        $processes = $this->processModel->getAll();
        
        // Generate rekomendasi
        $recommendations = [];
        foreach ($aggregateResults as $result) {
            if ($result['avg_gap'] !== null) {
                $recommendations[$result['kode_domain']] = generateRecommendations(
                    $result['kode_domain'], 
                    (float) $result['avg_gap']
                );
            }
        }
        
        // Render PDF content
        $data = [
            'title' => 'Laporan Analisis Risiko TI e-Raport',
            'aggregateResults' => $aggregateResults,
            'allResults' => $allResults,
            'statistics' => $statistics,
            'respondents' => $respondents,
            'processes' => $processes,
            'recommendations' => $recommendations,
            'tanggal' => date('d F Y'),
            'tahun' => date('Y')
        ];

        // Set header untuk download PDF
        // header('Content-Type: application/pdf');
        // header('Content-Disposition: attachment; filename="laporan-analisis-risiko-eraport-' . date('Ymd') . '.pdf"');

        // Generate HTML untuk PDF
        ob_start();

        view("laporan/pdf", $data, false);

        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream(
            'laporan-analisis-risiko-eraport-' . date('Ymd') . '.pdf',
            ['Attachment' => true]
        );

        exit;
        // ob_start();
        // view("laporan/pdf", $data, false);
        // $html = ob_get_clean();
        
        // // Gunakan Dompdf jika tersedia, jika tidak tampilkan HTML
        // if (class_exists('Dompdf\Dompdf')) {
        //     $dompdf = new \Dompdf\Dompdf();
        //     $dompdf->loadHtml($html);
        //     $dompdf->setPaper('A4', 'portrait');
        //     $dompdf->render();
        //     $dompdf->stream('laporan-analisis-risiko-eraport-' . date('Ymd') . '.pdf');
        // } else {
        //     // Fallback: tampilkan sebagai HTML printable
        //     echo $html;
        // }
    }
    
    /**
     * Export data ke Excel
     */
    public function export(): void {
        requireLogin();
        
        $type = $_GET['type'] ?? 'csv';
        
        $results = $this->resultModel->getAll();
        
        if ($type === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="hasil-analisis-' . date('Ymd') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // Header
            fputcsv($output, ['No', 'Responden', 'Jabatan', 'Domain', 'Total Nilai', 'Rata-rata', 'Capability Level', 'Current Level', 'Target Level', 'Gap', 'Status']);
            
            // Data
            foreach ($results as $i => $result) {
                fputcsv($output, [
                    $i + 1,
                    $result['respondent_name'],
                    $result['jabatan'],
                    $result['kode_domain'] . ' - ' . $result['nama_domain'],
                    $result['total_nilai'],
                    $result['rata_rata'],
                    $result['capability_level'],
                    $result['current_level'],
                    $result['target_level'],
                    $result['gap'],
                    $result['status']
                ]);
            }
            
            fclose($output);
        }
        
        exit();
    }
}
