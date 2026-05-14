<?php
/**
 * Responden View
 * Halaman data responden penilaian
 */
?>
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-people me-2"></i>Data Responden
            </h4>
            <p class="text-muted mb-0">Kelola data responden penilaian</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRespondenModal">
                <i class="bi bi-plus-lg me-1"></i>Tambah Responden
            </button>
        </div>
    </div>
</div>

<!-- Responden Table -->
<div class="row g-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5><i class="bi bi-list me-2"></i>Daftar Responden</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="respondenTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Unit</th>
                            <th>No HP</th>
                            <th>Tanggal</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($respondents as $i => $r): ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td>
                                <strong><?= sanitize($r['nama']) ?></strong>
                                <?php if ($r['email']): ?>
                                <br><small class="text-muted"><?= sanitize($r['email']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= sanitize($r['jabatan']) ?></td>
                            <td><?= sanitize($r['unit']) ?></td>
                            <td><?= sanitize($r['no_hp'] ?? '-') ?></td>
                            <td><?= formatDate($r['tanggal_input']) ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editRespondenModal<?= $r['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="<?= BASE_URL ?>/penilaian/delete-responden?id=<?= $r['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Yakin ingin menghapus responden ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($respondents)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data responden
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Responden Modal -->
<div class="modal fade" id="addRespondenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Responden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/penilaian/save-responden" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="jabatan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit/Departemen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="unit" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" class="form-control" name="no_hp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Input</label>
                        <input type="date" class="form-control" name="tanggal_input" value="<?= date('Y-m-d') ?>">
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

<!-- Edit Responden Modals -->
<?php foreach ($respondents as $r): ?>
<div class="modal fade" id="editRespondenModal<?= $r['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Responden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/penilaian/update-responden" method="POST">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" 
                               value="<?= sanitize($r['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="jabatan" 
                               value="<?= sanitize($r['jabatan']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit/Departemen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="unit" 
                               value="<?= sanitize($r['unit']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" class="form-control" name="no_hp" 
                               value="<?= sanitize($r['no_hp'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" 
                               value="<?= sanitize($r['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Input</label>
                        <input type="date" class="form-control" name="tanggal_input" 
                               value="<?= $r['tanggal_input'] ?>">
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
// DataTable untuk tabel responden
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#respondenTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    }
});
</script>
