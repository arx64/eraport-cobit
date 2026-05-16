<?php

/**
 * Framework View
 * Halaman informasi framework COBIT
 */
?>
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-book me-2"></i>Framework COBIT
            </h4>
            <p class="text-muted mb-0">Informasi domain COBIT 2019</p>
        </div>
    </div>
</div>

<!-- COBIT Overview -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="content-card-header">
                <h5><i class="bi bi-info-circle me-2"></i>Tentang COBIT 2019</h5>
            </div>
            <div class="content-card-body">
                <p>
                    <strong>COBIT (Control Objectives for Information and Related Technologies) 2019</strong>
                    adalah framework yang menyediakan panduan komprehensif untuk tata kelola dan manajemen
                    teknologi informasi. COBIT 2019 membantu organisasi menciptakan nilai dari teknologi informasi
                    dengan menjaga keseimbangan antara pemanfaatan manfaat, optimalisasi risiko, dan penggunaan sumber daya.
                </p>
                <p class="mb-0">
                    Sistem ini menggunakan dua domain utama dari COBIT 2019 yang relevan dengan sistem e-Raport:
                    <strong>DSS01 - Manage Operations</strong> dan <strong>DSS05 - Manage Security Services</strong>.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- COBIT Domains Overview -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5>
                    <i class="bi bi-diagram-3 me-2"></i>
                    Domain COBIT 2019
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Digunakan</th>
                            <th style="width: 120px;">Domain</th>
                            <th>Nama Domain</th>
                            <th>Penjelasan Singkat</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-x-lg"></i>
                                </span>
                            </td>
                            <td><strong>EDM</strong></td>
                            <td>Evaluate, Direct and Monitor</td>
                            <td>
                                Berfokus pada tata kelola TI, evaluasi strategi, pengawasan,
                                dan pengambilan keputusan organisasi terkait teknologi informasi.
                            </td>
                        </tr>

                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-x-lg"></i>
                                </span>
                            </td>
                            <td><strong>APO</strong></td>
                            <td>Align, Plan and Organize</td>
                            <td>
                                Berfokus pada perencanaan, strategi, pengorganisasian,
                                dan pengelolaan sumber daya TI agar selaras dengan tujuan organisasi.
                            </td>
                        </tr>

                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-x-lg"></i>
                                </span>
                            </td>
                            <td><strong>BAI</strong></td>
                            <td>Build, Acquire and Implement</td>
                            <td>
                                Mengatur proses pengembangan, pengadaan, implementasi,
                                dan perubahan solusi maupun sistem teknologi informasi.
                            </td>
                        </tr>

                        <tr class="table-success">
                            <td class="text-center">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                            </td>
                            <td><strong>DSS</strong></td>
                            <td>Deliver, Service and Support</td>
                            <td>
                                Berfokus pada operasional layanan TI, keamanan sistem,
                                dukungan pengguna, pengelolaan layanan, dan keberlangsungan operasional.
                                Domain ini digunakan dalam penelitian sistem e-Raport.
                            </td>
                        </tr>

                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-x-lg"></i>
                                </span>
                            </td>
                            <td><strong>MEA</strong></td>
                            <td>Monitor, Evaluate and Assess</td>
                            <td>
                                Berfokus pada monitoring, evaluasi performa, kepatuhan,
                                dan penilaian efektivitas tata kelola TI organisasi.
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="p-3">
                <div class="alert alert-success mb-0">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Penelitian ini menggunakan domain <strong>DSS (Deliver, Service and Support)</strong>,
                    khususnya proses <strong>DSS01 - Manage Operations</strong> dan
                    <strong>DSS05 - Manage Security Services</strong> karena relevan
                    dengan pengelolaan operasional dan keamanan sistem e-Raport.
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Domain Cards -->
<div class="row g-4 mb-4">
    <?php foreach ($processes as $process):
        $colorClass = $process['kode_domain'] === 'DSS01' ? 'border-primary' : 'border-success';
        $bgClass = $process['kode_domain'] === 'DSS01' ? 'bg-primary' : 'bg-success';
        $icon = $process['kode_domain'] === 'DSS01' ? 'bi-gear' : 'bi-shield-check';
        $tujuanList = explode("\n", $process['tujuan']);
    ?>
        <div class="col-lg-6">
            <div class="domain-card <?= $colorClass ?>">
                <div class="domain-card-header <?= $bgClass ?>">
                    <div class="d-flex align-items-center">
                        <i class="bi <?= $icon ?> domain-icon me-3"></i>
                        <div>
                            <h5 class="mb-0"><?= sanitize($process['kode_domain']) ?></h5>
                            <small><?= sanitize($process['nama_domain']) ?></small>
                        </div>
                    </div>
                </div>
                <div class="domain-card-body">
                    <h6 class="fw-bold mb-2">Deskripsi</h6>
                    <p class="text-muted"><?= sanitize($process['deskripsi']) ?></p>

                    <h6 class="fw-bold mb-2">Tujuan</h6>
                    <ul class="list-unstyled">
                        <?php foreach ($tujuanList as $tujuan):
                            if (empty(trim($tujuan))) continue;
                            $cleanTujuan = preg_replace('/^\d+\.\s*/', '', trim($tujuan));
                        ?>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <?= sanitize($cleanTujuan) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="mt-3">
                        <a href="<?= BASE_URL ?>/framework/domain?kode=<?= $process['kode_domain'] ?>"
                            class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Indikator Penilaian -->
<div class="row g-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5><i class="bi bi-list-check me-2"></i>Indikator Penilaian Capability Level</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">Level</th>
                            <th>Label</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-danger">0</span>
                            </td>
                            <td><strong>Non-existent</strong></td>
                            <td>Praktik tidak ada atau belum diimplementasikan sama sekali.</td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark">1</span>
                            </td>
                            <td><strong>Initial</strong></td>
                            <td>Praktik telah diinisiasi namun belum terdokumentasi secara formal.</td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark">2</span>
                            </td>
                            <td><strong>Repeatable</strong></td>
                            <td>Praktik dilakukan secara rutin namun belum terstandardisasi.</td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">3</span>
                            </td>
                            <td><strong>Defined</strong></td>
                            <td>Praktik telah terdokumentasi, terstandardisasi, dan diimplementasikan secara konsisten.</td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-primary">4</span>
                            </td>
                            <td><strong>Managed and Measurable</strong></td>
                            <td>Praktik dipantau dan diukur dengan metrik yang jelas untuk peningkatan berkelanjutan.</td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-success">5</span>
                            </td>
                            <td><strong>Optimized</strong></td>
                            <td>Praktik telah dioptimalkan dengan continuous improvement berbasis data dan teknologi terkini.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>