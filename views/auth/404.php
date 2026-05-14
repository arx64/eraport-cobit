<?php
/**
 * 404 Not Found View
 */
?>
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-exclamation-triangle me-2"></i>404 - Halaman Tidak Ditemukan
            </h4>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="content-card text-center py-5">
            <i class="bi bi-search" style="font-size: 80px; color: #dee2e6;"></i>
            <h3 class="mt-4">Halaman Tidak Ditemukan</h3>
            <p class="text-muted">Maaf, halaman yang Anda cari tidak ditemukan atau tidak tersedia.</p>
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary mt-3">
                <i class="bi bi-house-door me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
