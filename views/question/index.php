<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-question-circle me-2"></i>Data Pertanyaan
            </h4>
            <p class="text-muted mb-0">Kelola data pertanyaan penilaian</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Pertanyaan
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#resetAllModal">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Semua Jawaban
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5><i class="bi bi-list me-2"></i>Daftar Pertanyaan</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="questionTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Kode</th>
                            <th>Domain</th>
                            <th>Pertanyaan</th>
                            <th>Komponen</th>
                            <th class="text-center">Urutan</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $i => $q): ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td><strong><?= sanitize($q['kode_pertanyaan']) ?></strong></td>
                            <td><?= sanitize($q['kode_domain']) ?></td>
                            <td><?= sanitize($q['pertanyaan']) ?></td>
                            <td><?= sanitize($q['komponen']) ?></td>
                            <td class="text-center"><?= (int) $q['urutan'] ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editQuestionModal<?= $q['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="<?= BASE_URL ?>/pertanyaan/delete?id=<?= $q['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($questions)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data pertanyaan
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Pertanyaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/pertanyaan/save" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Domain <span class="text-danger">*</span></label>
                            <select class="form-select" name="process_id" required>
                                <option value="">-- Pilih Domain --</option>
                                <?php foreach ($processes as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= sanitize($p['kode_domain']) ?> - <?= sanitize($p['nama_domain']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kode Pertanyaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode_pertanyaan" placeholder="Contoh: DSS01-A.1" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Urutan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="urutan" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="pertanyaan" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Komponen <span class="text-danger">*</span></label>
                        <select class="form-select" name="komponen" required>
                            <option value="">-- Pilih Komponen --</option>
                            <option value="Process">Process</option>
                            <option value="People">People</option>
                            <option value="Technology">Technology</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($questions as $q): ?>
<div class="modal fade" id="editQuestionModal<?= $q['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Pertanyaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/pertanyaan/update" method="POST">
                <input type="hidden" name="id" value="<?= $q['id'] ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Domain <span class="text-danger">*</span></label>
                            <select class="form-select" name="process_id" required>
                                <option value="">-- Pilih Domain --</option>
                                <?php foreach ($processes as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $p['id'] === (int) $q['process_id'] ? 'selected' : '' ?>>
                                    <?= sanitize($p['kode_domain']) ?> - <?= sanitize($p['nama_domain']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kode Pertanyaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode_pertanyaan" 
                                   value="<?= sanitize($q['kode_pertanyaan']) ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Urutan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="urutan" min="1" 
                                   value="<?= (int) $q['urutan'] ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="pertanyaan" rows="3" required><?= sanitize($q['pertanyaan']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Komponen <span class="text-danger">*</span></label>
                        <select class="form-select" name="komponen" required>
                            <option value="">-- Pilih Komponen --</option>
                            <option value="Process" <?= $q['komponen'] === 'Process' ? 'selected' : '' ?>>Process</option>
                            <option value="People" <?= $q['komponen'] === 'People' ? 'selected' : '' ?>>People</option>
                            <option value="Technology" <?= $q['komponen'] === 'Technology' ? 'selected' : '' ?>>Technology</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Reset All Modal -->
<div class="modal fade" id="resetAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Reset Semua Jawaban</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <i class="bi bi-arrow-counterclockwise fs-1 text-danger mb-3 d-block"></i>
                    <h6 class="fw-bold text-danger">PERHATIAN!</h6>
                    <p class="mb-0">
                        Tindakan ini akan <strong>menghapus semua</strong> data jawaban penilaian dan hasil analisis yang telah tersimpan.
                    </p>
                    <p class="mt-2 mb-0 text-muted">
                        Data pertanyaan itu sendiri tidak akan terhapus.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= BASE_URL ?>/pertanyaan/reset-all" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Apakah Anda yakin? Semua jawaban dan hasil analisis akan dihapus secara permanen!')">
                        <i class="bi bi-check-lg me-1"></i>Ya, Reset Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#questionTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    }
});
</script>
