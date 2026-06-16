<?php
/**
 * Design Factor View
 * Halaman CRUD design factor COBIT 2019
 */
$relevan = array_filter($designFactors, fn($df) => ($df['status'] ?? '') === 'Relevan');
$relevanCount = count($relevan);
$totalCount = count($designFactors);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-clipboard-data me-2"></i>Design Factor
            </h4>
            <p class="text-muted mb-0">Kelola Design Factor COBIT 2019 untuk analisis risiko TI</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDfModal">
                <i class="bi bi-plus-lg me-1"></i>Tambah Design Factor
            </button>
        </div>
    </div>
</div>

<!-- Statistik Singkat -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="content-card border-primary">
            <div class="content-card-body text-center">
                <i class="bi bi-collection fs-1 text-primary"></i>
                <h3 class="mt-2 mb-0"><?= $totalCount ?></h3>
                <small class="text-muted">Total Design Factor</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="content-card border-success">
            <div class="content-card-body text-center">
                <i class="bi bi-check-circle fs-1 text-success"></i>
                <h3 class="mt-2 mb-0"><?= $relevanCount ?></h3>
                <small class="text-muted">Design Factor Relevan</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="content-card border-secondary">
            <div class="content-card-body text-center">
                <i class="bi bi-x-circle fs-1 text-secondary"></i>
                <h3 class="mt-2 mb-0"><?= $totalCount - $relevanCount ?></h3>
                <small class="text-muted">Design Factor Tidak Relevan</small>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Design Factor -->
<div class="row g-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5><i class="bi bi-table me-2"></i>Daftar Design Factor COBIT 2019</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dfTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th style="width: 110px;">Kode DF</th>
                            <th>Nama Design Factor</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th>Keterangan</th>
                            <th class="text-center" style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($designFactors as $i => $df):
                            $isRelevan = ($df['status'] ?? '') === 'Relevan';
                        ?>
                        <tr class="<?= $isRelevan ? 'table-success' : '' ?>">
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td>
                                <span class="badge bg-dark"><?= sanitize($df['kode_df']) ?></span>
                            </td>
                            <td><strong><?= sanitize($df['nama_df']) ?></strong></td>
                            <td class="text-center">
                                <?php if ($isRelevan): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Relevan
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Tidak Relevan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= sanitize($df['keterangan']) ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editDfModal<?= sanitize($df['kode_df']) ?>"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php if (!empty($df['id'])): ?>
                                <a href="<?= BASE_URL ?>/design-factor/delete?id=<?= (int) $df['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Yakin ingin menghapus Design Factor ini?')"
                                   title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Baris dengan <span class="badge bg-success">latar hijau</span> menandakan Design Factor berstatus <strong>Relevan</strong>
                    pada database. Design Factor yang tidak ada di database ditampilkan otomatis dengan status <em>Tidak Relevan</em>.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addDfModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Design Factor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/design-factor/save" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kode DF <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode_df" placeholder="DF12" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nama Design Factor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_df" placeholder="Nama design factor" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="Relevan">Relevan</option>
                            <option value="Tidak Relevan" selected>Tidak Relevan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3" placeholder="Deskripsi singkat design factor"></textarea>
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

<!-- Modal Edit (satu per baris) -->
<?php foreach ($designFactors as $df):
    $modalId = 'editDfModal' . sanitize($df['kode_df']);
?>
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit Design Factor <?= sanitize($df['kode_df']) ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/design-factor/update" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="id" value="<?= (int) ($df['id'] ?? 0) ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kode DF <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode_df"
                                   value="<?= sanitize($df['kode_df']) ?>" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nama Design Factor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_df"
                                   value="<?= sanitize($df['nama_df']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <?php $currentStatus = $df['status'] ?? 'Tidak Relevan'; ?>
                        <select class="form-select" name="status" required>
                            <option value="Relevan" <?= $currentStatus === 'Relevan' ? 'selected' : '' ?>>Relevan</option>
                            <option value="Tidak Relevan" <?= $currentStatus === 'Tidak Relevan' ? 'selected' : '' ?>>Tidak Relevan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"><?= sanitize($df['keterangan']) ?></textarea>
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

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#dfTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [5] }
            ]
        });
    }
});
</script>
