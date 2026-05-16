<?php

/**
 * Design Factor View
 * Halaman tabel design factor COBIT
 */
?>
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-clipboard-data me-2"></i>Design Factor
            </h4>
            <p class="text-muted mb-0">Design Factor COBIT 2019 untuk analisis risiko</p>
        </div>
    </div>
</div>

<!-- Design Factor Info -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="content-card-header">
                <h5><i class="bi bi-info-circle me-2"></i>Penjelasan Design Factor</h5>
            </div>
            <div class="content-card-body">
                <p>
                    <strong>Design Factor (DF)</strong> adalah faktor-faktor yang mempengaruhi desain sistem tata kelola
                    TI dalam COBIT 2019. Design factor membantu organisasi menyesuaikan framework COBIT dengan
                    konteks dan kebutuhan spesifik mereka. Dalam sistem ini, design factor yang relevan dengan
                    analisis risiko TI pada sistem e-Raport diidentifikasi untuk memastikan penilaian yang komprehensif.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- COBIT Design Factor Overview -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5>
                    <i class="bi bi-diagram-3 me-2"></i>
                    Design Factor COBIT 2019
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 70px;">No</th>
                            <th style="width: 120px;">Kode DF</th>
                            <th>Nama Design Factor</th>
                            <th style="width: 160px;">Status</th>
                            <th>Keterangan Singkat</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr class="<?= in_array('DF1', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">1</td>
                            <td><span class="badge bg-dark">DF1</span></td>
                            <td><strong>Enterprise Strategy</strong></td>
                            <td>
                                <?php if (in_array('DF1', array_column($designFactors, 'kode_df'))): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Digunakan
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Tidak Digunakan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>Strategi organisasi dalam mencapai tujuan bisnis melalui tata kelola TI.</td>
                        </tr>

                        <tr class="<?= in_array('DF2', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">2</td>
                            <td><span class="badge bg-dark">DF2</span></td>
                            <td><strong>Enterprise Goals</strong></td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Digunakan
                                </span>
                            </td>
                            <td>Tujuan strategis organisasi yang ingin dicapai melalui dukungan TI.</td>
                        </tr>

                        <tr class="<?= in_array('DF3', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">3</td>
                            <td><span class="badge bg-dark">DF3</span></td>
                            <td><strong>Risk Profile</strong></td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Digunakan
                                </span>
                            </td>
                            <td>Profil risiko organisasi terhadap ancaman dan gangguan TI.</td>
                        </tr>

                        <tr class="<?= in_array('DF4', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">4</td>
                            <td><span class="badge bg-dark">DF4</span></td>
                            <td><strong>IT-related Issues</strong></td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Digunakan
                                </span>
                            </td>
                            <td>Permasalahan TI yang dihadapi organisasi dan mempengaruhi operasional.</td>
                        </tr>

                        <tr class="<?= in_array('DF5', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">5</td>
                            <td><span class="badge bg-dark">DF5</span></td>
                            <td><strong>Threat Landscape</strong></td>
                            <td>
                                <?php if (in_array('DF5', array_column($designFactors, 'kode_df'))): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Digunakan
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Tidak Digunakan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>Kondisi ancaman keamanan dan risiko eksternal yang mempengaruhi sistem TI.</td>
                        </tr>

                        <tr class="<?= in_array('DF6', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">6</td>
                            <td><span class="badge bg-dark">DF6</span></td>
                            <td><strong>Compliance Requirements</strong></td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Digunakan
                                </span>
                            </td>
                            <td>Persyaratan kepatuhan terhadap regulasi, kebijakan, dan standar organisasi.</td>
                        </tr>

                        <tr class="<?= in_array('DF7', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">7</td>
                            <td><span class="badge bg-dark">DF7</span></td>
                            <td><strong>Role of IT</strong></td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Digunakan
                                </span>
                            </td>
                            <td>Peran teknologi informasi dalam mendukung proses bisnis organisasi.</td>
                        </tr>

                        <tr class="<?= in_array('DF8', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">8</td>
                            <td><span class="badge bg-dark">DF8</span></td>
                            <td><strong>Sourcing Model for IT</strong></td>
                            <td>
                                <?php if (in_array('DF8', array_column($designFactors, 'kode_df'))): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Digunakan
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Tidak Digunakan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>Model pengadaan dan pengelolaan layanan TI organisasi.</td>
                        </tr>

                        <tr class="<?= in_array('DF9', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">9</td>
                            <td><span class="badge bg-dark">DF9</span></td>
                            <td><strong>IT Implementation Methods</strong></td>
                            <td>
                                <?php if (in_array('DF9', array_column($designFactors, 'kode_df'))): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Digunakan
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Tidak Digunakan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>Metode implementasi dan pengembangan teknologi informasi organisasi.</td>
                        </tr>

                        <tr class="<?= in_array('DF10', array_column($designFactors, 'kode_df')) ? 'table-success' : '' ?>">
                            <td class="text-center">10</td>
                            <td><span class="badge bg-dark">DF10</span></td>
                            <td><strong>Technology Adoption Strategy</strong></td>
                            <td>
                                <?php if (in_array('DF10', array_column($designFactors, 'kode_df'))): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Digunakan
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Tidak Digunakan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>Strategi organisasi dalam mengadopsi teknologi baru.</td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="p-3">
                <div class="alert alert-success mb-0">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Berdasarkan hasil identifikasi Design Factor COBIT 2019,
                    penelitian ini menggunakan beberapa Design Factor yang relevan
                    terhadap analisis risiko TI pada sistem e-Raport,
                    yaitu DF2, DF3, DF4, DF6, dan DF7.
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Design Factor Table -->
<div class="row g-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5><i class="bi bi-table me-2"></i>Tabel Design Factor</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">Kode DF</th>
                            <th>Nama Design Factor</th>
                            <th class="text-center" style="width: 120px;">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($designFactors as $df): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-dark"><?= sanitize($df['kode_df']) ?></span>
                                </td>
                                <td>
                                    <strong><?= sanitize($df['nama_df']) ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php if ($df['status'] === 'Relevan'): ?>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Design Factor Detail Cards -->
<!-- <div class="row g-4 mt-2">
    <?php foreach ($designFactors as $df):
        switch ($df['kode_df']) {
            case 'DF2':
                $icon = 'bi-bullseye';
                break;

            case 'DF3':
                $icon = 'bi-shield-exclamation';
                break;

            case 'DF4':
                $icon = 'bi-laptop';
                break;

            case 'DF6':
                $icon = 'bi-file-check';
                break;

            case 'DF7':
                $icon = 'bi-cpu';
                break;

            default:
                $icon = 'bi-clipboard-data';
                break;
        }
        $colorClass = $df['status'] === 'Relevan' ? 'border-success' : 'border-secondary';
    ?>
        <div class="col-md-6 col-lg-4">
            <div class="df-card <?= $colorClass ?>">
                <div class="df-card-header">
                    <div class="df-icon <?= $df['status'] === 'Relevan' ? 'bg-success' : 'bg-secondary' ?>">
                        <i class="bi <?= $icon ?>"></i>
                    </div>
                    <div class="df-info">
                        <span class="badge bg-dark"><?= sanitize($df['kode_df']) ?></span>
                        <h6 class="mb-0 mt-1"><?= sanitize($df['nama_df']) ?></h6>
                    </div>
                </div>
                <div class="df-card-body">
                    <p class="text-muted small mb-2"><?= sanitize($df['keterangan']) ?></p>
                    <span class="badge <?= $df['status'] === 'Relevan' ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $df['status'] === 'Relevan' ? '<i class="bi bi-check-circle me-1"></i>' : '<i class="bi bi-x-circle me-1"></i>' ?>
                        <?= $df['status'] ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div> -->