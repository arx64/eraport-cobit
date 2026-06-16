<?php
/**
 * Model DesignFactor
 * Mengelola data design factor COBIT 2019
 */
require_once __DIR__ . '/../helpers/functions.php';

class DesignFactor {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    /**
     * Get all design factors (urut natural DF1..DF11, lalu tambahan)
     * @return array
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM design_factors");
        $rows = $stmt->fetchAll();
        usort($rows, function ($a, $b) {
            return strnatcmp($a['kode_df'], $b['kode_df']);
        });
        return $rows;
    }

    /**
     * Get design factor by ID
     * @param int $id Design Factor ID
     * @return array|false
     */
    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM design_factors WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get design factor by kode_df
     * @param string $kode
     * @return array|false
     */
    public function getByKode(string $kode) {
        $stmt = $this->db->prepare("SELECT * FROM design_factors WHERE kode_df = ? LIMIT 1");
        $stmt->execute([$kode]);
        return $stmt->fetch();
    }

    /**
     * Get total design factors count
     * @return int
     */
    public function getTotal(): int {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM design_factors");
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Get design factors by status
     * @param string $status Status (Relevan/Tidak Relevan)
     * @return array
     */
    public function getByStatus(string $status): array {
        $stmt = $this->db->prepare("SELECT * FROM design_factors WHERE status = ? ORDER BY kode_df");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new design factor
     * @param array $data
     * @return int|false
     */
    public function create(array $data) {
        $stmt = $this->db->prepare("
            INSERT INTO design_factors (kode_df, nama_df, status, keterangan)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['kode_df'],
            $data['nama_df'],
            $data['status'],
            $data['keterangan']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Update a design factor
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE design_factors SET
                kode_df = ?,
                nama_df = ?,
                status = ?,
                keterangan = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['kode_df'],
            $data['nama_df'],
            $data['status'],
            $data['keterangan'],
            $id
        ]);
    }

    /**
     * Delete a design factor
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM design_factors WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Daftar master 11 Design Factor COBIT 2019.
     * Digunakan untuk auto-seed bila belum ada di DB.
     * @return array
     */
    public static function masterList(): array {
        return [
            ['kode_df' => 'DF1',  'nama_df' => 'Enterprise Strategy',          'keterangan' => 'Strategi organisasi dalam mencapai tujuan bisnis melalui tata kelola TI.'],
            ['kode_df' => 'DF2',  'nama_df' => 'Enterprise Goals',             'keterangan' => 'Tujuan strategis organisasi yang ingin dicapai melalui dukungan TI.'],
            ['kode_df' => 'DF3',  'nama_df' => 'Risk Profile',                 'keterangan' => 'Profil risiko organisasi terhadap ancaman dan gangguan TI.'],
            ['kode_df' => 'DF4',  'nama_df' => 'IT-related Issues',            'keterangan' => 'Permasalahan TI yang dihadapi organisasi dan mempengaruhi operasional.'],
            ['kode_df' => 'DF5',  'nama_df' => 'Threat Landscape',             'keterangan' => 'Kondisi ancaman keamanan dan risiko eksternal yang mempengaruhi sistem TI.'],
            ['kode_df' => 'DF6',  'nama_df' => 'Compliance Requirements',      'keterangan' => 'Persyaratan kepatuhan terhadap regulasi, kebijakan, dan standar organisasi.'],
            ['kode_df' => 'DF7',  'nama_df' => 'Role of IT',                   'keterangan' => 'Peran teknologi informasi dalam mendukung proses bisnis organisasi.'],
            ['kode_df' => 'DF8',  'nama_df' => 'Sourcing Model for IT',        'keterangan' => 'Model pengadaan dan pengelolaan layanan TI organisasi.'],
            ['kode_df' => 'DF9',  'nama_df' => 'IT Implementation Methods',    'keterangan' => 'Metode implementasi dan pengembangan teknologi informasi organisasi.'],
            ['kode_df' => 'DF10', 'nama_df' => 'Technology Adoption Strategy', 'keterangan' => 'Strategi organisasi dalam mengadopsi teknologi baru.'],
            ['kode_df' => 'DF11', 'nama_df' => 'Size of the Enterprise',       'keterangan' => 'Ukuran organisasi yang mempengaruhi kompleksitas tata kelola TI.'],
        ];
    }

    /**
     * Pastikan 11 master DF ada di DB.
     * - Hanya menambahkan kode_df yang belum ada (tidak menimpa status user).
     * - Baris yang baru dimasukkan berstatus "Tidak Relevan" sebagai default.
     */
    public function ensureDefaults(): void {
        $existingCodes = array_column($this->getAll(), 'kode_df');

        $stmt = $this->db->prepare("
            INSERT INTO design_factors (kode_df, nama_df, status, keterangan)
            VALUES (?, ?, 'Tidak Relevan', ?)
        ");

        foreach (self::masterList() as $df) {
            if (!in_array($df['kode_df'], $existingCodes, true)) {
                $stmt->execute([
                    $df['kode_df'],
                    $df['nama_df'],
                    $df['keterangan']
                ]);
            }
        }
    }
}
