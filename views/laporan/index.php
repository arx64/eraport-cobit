<?php
/**
 * Laporan View
 * Halaman laporan dan export
 */
?>
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-file-earmark-pdf me-2"></i>Laporan
            </h4>
            <p class="text-muted mb-0">Cetak dan export laporan analisis</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="<?= BASE_URL ?>/laporan/pdf" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Cetak PDF
                </a>
                <a href="<?= BASE_URL ?>/laporan/export?type=csv" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export CSV
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Report Preview -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="report-preview">
            <!-- Report Header -->
            <div class="report-header">
                <div class="text-center mb-4">
                    <h4 class="mb-1">LAPORAN ANALISIS RISIKO TEKNOLOGI INFORMASI</h4>
                    <h5 class="mb-1">Sistem e-Raport</h5>
                    <p class="mb-0">SMKN 1 Teluk Mengkudu</p>
                    <p class="text-muted"><?= formatDate(date('Y-m-d')) ?></p>
                </div>
            </div>
            
            <!-- Executive Summary -->
            <div class="report-section">
                <h6 class="report-section-title">I. Ringkasan Eksekutif</h6>
                <p>
                    Laporan ini menyajikan hasil analisis risiko teknologi informasi pada sistem e-Raport 
                    di SMKN 1 Teluk Mengkudu menggunakan framework COBIT 2019. Analisis difokuskan pada 
                    dua domain utama: <strong>DSS01 - Manage Operations</strong> dan 
                    <strong>DSS05 - Manage Security Services</strong>.
                </p>
                
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <div class="summary-stat">
                            <span class="stat-label">Total Responden</span>
                            <span class="stat-value"><?= count($respondents) ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-stat">
                            <span class="stat-label">Domain COBIT</span>
                            <span class="stat-value">2</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-stat">
                            <span class="stat-label">Total Pertanyaan</span>
                            <span class="stat-value">12</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hasil Analisis -->
            <div class="report-section">
                <h6 class="report-section-title">II. Hasil Analisis Capability Level</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Domain</th>
                                <th class="text-center">Rata-rata</th>
                                <th class="text-center">Current Level</th>
                                <th class="text-center">Target</th>
                                <th class="text-center">Gap</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aggregateResults as $result): 
                                $rataRata = (float) ($result['avg_rata_rata'] ?? 0);
                                $gap = (float) ($result['avg_gap'] ?? 4);
                            ?>
                            <tr>
                                <td>
                                    <strong><?= sanitize($result['kode_domain']) ?></strong> - 
                                    <?= sanitize($result['nama_domain']) ?>
                                </td>
                                <td class="text-center"><?= number_format($rataRata, 2) ?></td>
                                <td class="text-center"><?= getCapabilityLabel($rataRata) ?></td>
                                <td class="text-center">Managed and Measurable (4)</td>
                                <td class="text-center"><?= number_format($gap, 2) ?></td>
                                <td class="text-center"><?= getGapStatus($gap) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="report-section">
                <h6 class="report-section-title">III. Grafik Analisis</h6>
                <div class="row">
                    <div class="col-lg-6">
                        <canvas id="reportCapabilityChart"></canvas>
                    </div>
                    <div class="col-lg-6">
                        <canvas id="reportGapChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Rekomendasi -->
            <div class="report-section">
                <h6 class="report-section-title">IV. Rekomendasi</h6>
                <?php foreach ($recommendations as $domain => $recommendationList): 
                    $processData = array_filter($aggregateResults, fn($r) => $r['kode_domain'] === $domain);
                    $processData = array_values($processData)[0] ?? null;
                ?>
                <div class="mb-3">
                    <p class="fw-bold mb-1"><?= sanitize($domain) ?> - <?= sanitize($processData['nama_domain'] ?? '') ?></p>
                    <ol>
                        <?php foreach ($recommendationList as $rec): ?>
                        <li><?= sanitize($rec) ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Footer -->
            <div class="report-footer text-center mt-4 pt-4 border-top">
                <p class="mb-0 text-muted">
                    <small>
                        Sistem Analisis Risiko TI e-Raport menggunakan COBIT 2019<br>
                        SMKN 1 Teluk Mengkudu &copy; <?= date('Y') ?>
                    </small>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Report Charts
const repCapCtx = document.getElementById('reportCapabilityChart').getContext('2d');
new Chart(repCapCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_map(fn($r) => $r['kode_domain'], $aggregateResults)) ?>,
        datasets: [{
            label: 'Rata-rata Capability Level',
            data: <?= json_encode(array_map(fn($r) => (float) $r['avg_rata_rata'], $aggregateResults)) ?>,
            backgroundColor: [
                'rgba(13, 110, 253, 0.8)',
                'rgba(25, 135, 84, 0.8)'
            ],
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: 'Capability Level per Domain' },
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } }
        }
    }
});

const repGapCtx = document.getElementById('reportGapChart').getContext('2d');
new Chart(repGapCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_map(fn($r) => $r['kode_domain'], $aggregateResults)) ?>,
        datasets: [{
            label: 'Gap',
            data: <?= json_encode(array_map(fn($r) => (float) $r['avg_gap'], $aggregateResults)) ?>,
            backgroundColor: [
                'rgba(220, 53, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)'
            ],
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: 'Gap Analysis per Domain' },
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Nilai Gap' } }
        }
    }
});
</script>
