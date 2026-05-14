<?php
/**
 * Domain Detail View
 * Halaman detail domain COBIT
 */
?>
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-book me-2"></i>Domain <?= sanitize($process['kode_domain']) ?>
            </h4>
            <p class="text-muted mb-0"><?= sanitize($process['nama_domain']) ?></p>
        </div>
        <div class="col-auto">
            <a href="<?= BASE_URL ?>/framework" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>

<!-- Domain Detail -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="content-card-header d-flex align-items-center">
                <span class="badge bg-primary fs-6 me-3"><?= sanitize($process['kode_domain']) ?></span>
                <h5 class="mb-0"><?= sanitize($process['nama_domain']) ?></h5>
            </div>
            <div class="content-card-body">
                <h6 class="fw-bold"><i class="bi bi-file-text me-2"></i>Deskripsi</h6>
                <p class="mb-4"><?= sanitize($process['deskripsi']) ?></p>
                
                <h6 class="fw-bold"><i class="bi bi-bullseye me-2"></i>Tujuan</h6>
                <div class="row">
                    <?php foreach ($tujuanList as $i => $tujuan): 
                        if (empty(trim($tujuan))) continue;
                        $cleanTujuan = preg_replace('/^\d+\.\s*/', '', trim($tujuan));
                    ?>
                    <div class="col-md-6 mb-3">
                        <div class="tujuan-item">
                            <div class="tujuan-number"><?= $i + 1 ?></div>
                            <div class="tujuan-text"><?= sanitize($cleanTujuan) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Komponen Penilaian -->
<div class="row g-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5><i class="bi bi-list-check me-2"></i>Komponen Penilaian</h5>
            </div>
            <div class="content-card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="komponen-card">
                            <div class="komponen-icon bg-primary">
                                <i class="bi bi-gear"></i>
                            </div>
                            <h6>Process</h6>
                                Proses dan prosedur yang mendukung pengelolaan domain ini.
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="komponen-card">
                            <div class="komponen-icon bg-success">
                                <i class="bi bi-people"></i>
                            </div>
                            <h6>People</h6>
                                Sumber daya manusia dan kompetensi yang dibutuhkan.
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="komponen-card">
                            <div class="komponen-icon bg-info">
                                <i class="bi bi-layers"></i>
                            </div>
                            <h6>Technology</h6>
                                Infrastruktur dan teknologi yang digunakan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
