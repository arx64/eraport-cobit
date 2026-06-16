<?php
/**
 * Design Factor Controller
 * Menangani CRUD design factor COBIT 2019
 */
require_once __DIR__ . '/../models/DesignFactor.php';

class DesignFactorController {
    private DesignFactor $dfModel;

    public function __construct() {
        $this->dfModel = new DesignFactor();
    }

    /**
     * Tampilkan halaman design factor (read)
     */
    public function index(): void {
        requireLogin();

        $this->dfModel->ensureDefaults();

        $data = [
            'title' => 'Design Factor',
            'designFactors' => $this->dfModel->getAll()
        ];

        view("design-factor/index", $data);
    }

    /**
     * Simpan design factor baru
     */
    public function save(): void {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("design-factor");
        }

        if (!validateCsrfToken()) {
            setFlash('error', "Token keamanan tidak valid.");
            redirect("design-factor");
        }

        $data = $this->collectFromPost();

        if (!$this->validate($data)) {
            redirect("design-factor");
        }

        $id = $this->dfModel->create($data);

        if ($id) {
            setFlash('success', "Design Factor berhasil ditambahkan.");
        } else {
            setFlash('error', "Gagal menambahkan Design Factor.");
        }

        redirect("design-factor");
    }

    /**
     * Update design factor
     */
    public function update(): void {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("design-factor");
        }

        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->collectFromPost();

        if (!$id || !$this->validate($data)) {
            redirect("design-factor");
        }

        if ($this->dfModel->update($id, $data)) {
            setFlash('success', "Design Factor berhasil diperbarui.");
        } else {
            setFlash('error', "Gagal memperbarui Design Factor.");
        }

        redirect("design-factor");
    }

    /**
     * Hapus design factor
     */
    public function delete(): void {
        requireLogin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id && $this->dfModel->delete($id)) {
            setFlash('success', "Design Factor berhasil dihapus.");
        } else {
            setFlash('error', "Gagal menghapus Design Factor.");
        }

        redirect("design-factor");
    }

    /**
     * Ambil data dari POST
     * @return array
     */
    private function collectFromPost(): array {
        return [
            'kode_df' => strtoupper(trim($_POST['kode_df'] ?? '')),
            'nama_df' => trim($_POST['nama_df'] ?? ''),
            'status' => trim($_POST['status'] ?? 'Tidak Relevan'),
            'keterangan' => trim($_POST['keterangan'] ?? ''),
        ];
    }

    /**
     * Validasi input design factor
     * @param array $data
     * @return bool
     */
    private function validate(array $data): bool {
        if (empty($data['kode_df']) || empty($data['nama_df'])) {
            setFlash('error', "Kode DF dan Nama Design Factor wajib diisi.");
            return false;
        }
        if (!in_array($data['status'], ['Relevan', 'Tidak Relevan'], true)) {
            $data['status'] = 'Tidak Relevan';
        }
        return true;
    }
}
