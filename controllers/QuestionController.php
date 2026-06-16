<?php
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Process.php';
require_once __DIR__ . '/../models/Answer.php';
require_once __DIR__ . '/../models/Result.php';

class QuestionController {
    private Question $questionModel;
    private Process $processModel;
    private Answer $answerModel;
    private Result $resultModel;

    public function __construct() {
        $this->questionModel = new Question();
        $this->processModel = new Process();
        $this->answerModel = new Answer();
        $this->resultModel = new Result();
    }

    public function index(): void {
        requireLogin();

        $data = [
            'title' => 'Data Pertanyaan',
            'questions' => $this->questionModel->getAll(),
            'processes' => $this->processModel->getAll()
        ];

        view("question/index", $data);
    }

    public function save(): void {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("pertanyaan");
        }

        if (!validateCsrfToken()) {
            setFlash('error', "Token keamanan tidak valid.");
            redirect("pertanyaan");
        }

        $data = [
            'process_id' => (int) ($_POST['process_id'] ?? 0),
            'kode_pertanyaan' => trim($_POST['kode_pertanyaan'] ?? ''),
            'pertanyaan' => trim($_POST['pertanyaan'] ?? ''),
            'komponen' => trim($_POST['komponen'] ?? ''),
            'urutan' => (int) ($_POST['urutan'] ?? 0)
        ];

        if (empty($data['process_id']) || empty($data['kode_pertanyaan']) || empty($data['pertanyaan']) || empty($data['komponen']) || empty($data['urutan'])) {
            setFlash('error', "Semua field wajib diisi.");
            redirect("pertanyaan");
        }

        $id = $this->questionModel->create($data);

        if ($id) {
            setFlash('success', "Pertanyaan berhasil ditambahkan.");
        } else {
            setFlash('error', "Gagal menambahkan pertanyaan.");
        }

        redirect("pertanyaan");
    }

    public function update(): void {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("pertanyaan");
        }

        $id = (int) ($_POST['id'] ?? 0);

        $data = [
            'process_id' => (int) ($_POST['process_id'] ?? 0),
            'kode_pertanyaan' => trim($_POST['kode_pertanyaan'] ?? ''),
            'pertanyaan' => trim($_POST['pertanyaan'] ?? ''),
            'komponen' => trim($_POST['komponen'] ?? ''),
            'urutan' => (int) ($_POST['urutan'] ?? 0)
        ];

        if (empty($data['process_id']) || empty($data['kode_pertanyaan']) || empty($data['pertanyaan']) || empty($data['komponen']) || empty($data['urutan'])) {
            setFlash('error', "Semua field wajib diisi.");
            redirect("pertanyaan");
        }

        if ($this->questionModel->update($id, $data)) {
            setFlash('success', "Pertanyaan berhasil diperbarui.");
        } else {
            setFlash('error', "Gagal memperbarui pertanyaan.");
        }

        redirect("pertanyaan");
    }

    public function delete(): void {
        requireLogin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id && $this->questionModel->delete($id)) {
            setFlash('success', "Pertanyaan berhasil dihapus.");
        } else {
            setFlash('error', "Gagal menghapus pertanyaan.");
        }

        redirect("pertanyaan");
    }

    public function resetAll(): void {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("pertanyaan");
        }

        if (!validateCsrfToken()) {
            setFlash('error', "Token keamanan tidak valid.");
            redirect("pertanyaan");
        }

        $deletedResults = $this->resultModel->deleteAll();
        $deletedAnswers = $this->answerModel->deleteAll();

        if ($deletedResults && $deletedAnswers) {
            setFlash('success', "Semua data jawaban dan hasil penilaian berhasil direset.");
        } else {
            setFlash('error', "Gagal mereset data.");
        }

        redirect("pertanyaan");
    }
}
