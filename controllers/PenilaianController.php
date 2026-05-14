<?php
/**
 * Penilaian Controller
 * Menangani CRUD data penilaian
 */
require_once __DIR__ . '/../models/Respondent.php';
require_once __DIR__ . '/../models/Process.php';
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Answer.php';

class PenilaianController {
    private Respondent $respondentModel;
    private Process $processModel;
    private Question $questionModel;
    private Answer $answerModel;
    
    public function __construct() {
        $this->respondentModel = new Respondent();
        $this->processModel = new Process();
        $this->questionModel = new Question();
        $this->answerModel = new Answer();
    }
    
    /**
     * Tampilkan halaman responden
     */
    public function responden(): void {
        requireLogin();
        
        $data = [
            'title' => 'Data Responden',
            'respondents' => $this->respondentModel->getAll()
        ];
        
        view("penilaian/responden", $data);
    }
    
    /**
     * Simpan responden baru
     */
    public function saveResponden(): void {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("penilaian/responden");
        }
        
        if (!validateCsrfToken()) {
            setFlash('error', "Token keamanan tidak valid.");
            redirect("penilaian/responden");
        }
        
        $data = [
            'nama' => $_POST['nama'] ?? '',
            'jabatan' => $_POST['jabatan'] ?? '',
            'unit' => $_POST['unit'] ?? '',
            'no_hp' => $_POST['no_hp'] ?? '',
            'email' => $_POST['email'] ?? '',
            'tanggal_input' => $_POST['tanggal_input'] ?? date('Y-m-d')
        ];
        
        // Validasi
        if (empty($data['nama']) || empty($data['jabatan']) || empty($data['unit'])) {
            setFlash('error', "Nama, jabatan, dan unit wajib diisi.");
            redirect("penilaian/responden");
        }
        
        $id = $this->respondentModel->create($data);
        
        if ($id) {
            setFlash('success', "Data responden berhasil ditambahkan.");
        } else {
            setFlash('error', "Gagal menambahkan data responden.");
        }
        
        redirect("penilaian/responden");
    }
    
    /**
     * Update responden
     */
    public function updateResponden(): void {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("penilaian/responden");
        }
        
        $id = (int) ($_POST['id'] ?? 0);
        
        $data = [
            'nama' => $_POST['nama'] ?? '',
            'jabatan' => $_POST['jabatan'] ?? '',
            'unit' => $_POST['unit'] ?? '',
            'no_hp' => $_POST['no_hp'] ?? '',
            'email' => $_POST['email'] ?? '',
            'tanggal_input' => $_POST['tanggal_input'] ?? date('Y-m-d')
        ];
        
        if (empty($data['nama']) || empty($data['jabatan']) || empty($data['unit'])) {
            setFlash('error', "Nama, jabatan, dan unit wajib diisi.");
            redirect("penilaian/responden");
        }
        
        if ($this->respondentModel->update($id, $data)) {
            setFlash('success', "Data responden berhasil diperbarui.");
        } else {
            setFlash('error', "Gagal memperbarui data responden.");
        }
        
        redirect("penilaian/responden");
    }
    
    /**
     * Hapus responden
     */
    public function deleteResponden(): void {
        requireLogin();
        
        $id = (int) ($_GET['id'] ?? 0);
        
        if ($id && $this->respondentModel->delete($id)) {
            setFlash('success', "Data responden berhasil dihapus.");
        } else {
            setFlash('error', "Gagal menghapus data responden.");
        }
        
        redirect("penilaian/responden");
    }
    
    /**
     * Tampilkan form penilaian DSS01
     */
    public function dss01(): void {
        requireLogin();
        
        $respondentId = (int) ($_GET['respondent_id'] ?? 0);
        
        $data = [
            'title' => 'Penilaian DSS01',
            'process' => $this->processModel->getByCode('DSS01'),
            'questions' => $this->questionModel->getByProcessId(1),
            'respondents' => $this->respondentModel->getForDropdown(),
            'respondentId' => $respondentId,
            'answers' => []
        ];
        
        if ($respondentId) {
            $answers = $this->answerModel->getByRespondentAndProcess($respondentId, 1);
            foreach ($answers as $answer) {
                $data['answers'][$answer['question_id']] = $answer;
            }
        }
        
        view("penilaian/dss01", $data);
    }
    
    /**
     * Tampilkan form penilaian DSS05
     */
    public function dss05(): void {
        requireLogin();
        
        $respondentId = (int) ($_GET['respondent_id'] ?? 0);
        
        $data = [
            'title' => 'Penilaian DSS05',
            'process' => $this->processModel->getByCode('DSS05'),
            'questions' => $this->questionModel->getByProcessId(2),
            'respondents' => $this->respondentModel->getForDropdown(),
            'respondentId' => $respondentId,
            'answers' => []
        ];
        
        if ($respondentId) {
            $answers = $this->answerModel->getByRespondentAndProcess($respondentId, 2);
            foreach ($answers as $answer) {
                $data['answers'][$answer['question_id']] = $answer;
            }
        }
        
        view("penilaian/dss05", $data);
    }
    
    /**
     * Simpan jawaban penilaian
     */
    public function savePenilaian(): void {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("penilaian/responden");
        }
        
        $respondentId = (int) ($_POST['respondent_id'] ?? 0);
        $processId = (int) ($_POST['process_id'] ?? 0);
        
        if (!$respondentId || !$processId) {
            setFlash('error', "Data tidak lengkap.");
            redirect("penilaian/responden");
        }
        
        $questions = $this->questionModel->getByProcessId($processId);
        $saved = 0;
        
        foreach ($questions as $question) {
            $nilai = (int) ($_POST['nilai_' . $question['id']] ?? -1);
            $keterangan = $_POST['keterangan_' . $question['id']] ?? null;
            
            if ($nilai >= 0 && $nilai <= 5) {
                if ($this->answerModel->save($respondentId, $question['id'], $nilai, $keterangan)) {
                    $saved++;
                }
            }
        }
        
        // Hitung dan simpan capability level
        if ($saved > 0) {
            saveResult($respondentId, $processId);
            setFlash('success', "Penilaian berhasil disimpan. {$saved} jawaban tersimpan.");
        } else {
            setFlash('error', "Tidak ada jawaban yang disimpan.");
        }
        
        $redirectPage = $processId === 1 ? 'penilaian/dss01' : 'penilaian/dss05';
        redirect($redirectPage . '?respondent_id=' . $respondentId);
    }
    
    /**
     * Hapus penilaian responden
     */
    public function deletePenilaian(): void {
        requireLogin();
        
        $respondentId = (int) ($_GET['respondent_id'] ?? 0);
        $processId = (int) ($_GET['process_id'] ?? 0);
        
        if ($respondentId && $processId) {
            $this->answerModel->deleteByRespondentAndProcess($respondentId, $processId);
            setFlash('success', "Penilaian berhasil dihapus.");
        } else {
            setFlash('error', "Gagal menghapus penilaian.");
        }
        
        redirect("analisis");
    }
}
