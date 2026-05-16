<?php
/**
 * Dashboard View
 * Halaman utama dashboard
 */
?>
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </h4>
            <p class="text-muted mb-0">Ringkasan analisis risiko TI e-Raport</p>
        </div>
        <div class="col-auto">
            <span class="badge bg-primary">
                <i class="bi bi-calendar3 me-1"></i><?= formatDate(date('Y-m-d')) ?>
            </span>
        </div>
    </div>
</div>

<!-- Statistic Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card-blue">
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-number"><?= $totalResponden ?></h3>
                <p class="stat-label">Total Responden</p>
            </div>
            <div class="stat-chart">
                <div class="stat-trend">
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card-green">
            <div class="stat-icon">
                <i class="bi bi-question-circle"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-number"><?= $totalQuestionsDSS01 ?></h3>
                <p class="stat-label">Pertanyaan DSS01</p>
            </div>
            <div class="stat-chart">
                <div class="stat-trend">
                    <i class="bi bi-clipboard-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card-orange">
            <div class="stat-icon">
                <i class="bi bi-question-circle"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-number"><?= $totalQuestionsDSS05 ?></h3>
                <p class="stat-label">Pertanyaan DSS05</p>
            </div>
            <div class="stat-chart">
                <div class="stat-trend">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card-purple">
            <div class="stat-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
            <div class="stat-info">
                <h3 class="stat-number"><?= $totalPenilaian ?></h3>
                <p class="stat-label">Total Penilaian</p>
            </div>
            <div class="stat-chart">
                <div class="stat-trend">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="bi bi-bar-chart-line me-2"></i>Grafik Capability Level</h5>
            </div>
            <div class="chart-body">
                <canvas id="capabilityChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="bi bi-graph-down me-2"></i>Grafik Gap Analysis</h5>
            </div>
            <div class="chart-body">
                <canvas id="gapChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Radar Chart -->
<div class="row g-4 mb-4">
    <div class="col-lg-8 mx-auto">
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="bi bi-bullseye me-2"></i>Perbandingan DSS01 vs DSS05</h5>
            </div>
            <div class="chart-body">
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Summary Table -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5><i class="bi bi-table me-2"></i>Ringkasan Hasil Analisis</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Nama Domain</th>
                            <th class="text-center">Current Level</th>
                            <th class="text-center">Target Level</th>
                            <th class="text-center">Gap</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aggregateResults as $result): 
                            $gap = (float) ($result['avg_gap'] ?? 4);
                            $rataRata = (float) ($result['avg_rata_rata'] ?? 0);
                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-dark"><?= $result['kode_domain'] ?></span>
                            </td>
                            <td><?= sanitize($result['nama_domain']) ?></td>
                            <td class="text-center">
                                <span class="badge <?= getCapabilityBadge($rataRata) ?>">
                                    <?= number_format($rataRata, 2) ?> - <?= getCapabilityLabel($rataRata) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">4.00 - Managed and Measurable</span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= getGapBadge($gap) ?>">
                                    <?= number_format($gap, 2) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= getGapBadge($gap) ?>">
                                    <?= getGapStatus($gap) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Capability Level Chart
const capabilityCtx = document.getElementById('capabilityChart').getContext('2d');
new Chart(capabilityCtx, {
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
            borderColor: [
                'rgba(13, 110, 253, 1)',
                'rgba(25, 135, 84, 1)'
            ],
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Level: ' + context.parsed.y.toFixed(2);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 5,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Gap Analysis Chart
const gapCtx = document.getElementById('gapChart').getContext('2d');
new Chart(gapCtx, {
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
            borderColor: [
                'rgba(220, 53, 69, 1)',
                'rgba(255, 193, 7, 1)'
            ],
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Nilai Gap'
                }
            }
        }
    }
});

// Radar Chart
const radarCtx = document.getElementById('radarChart').getContext('2d');
new Chart(radarCtx, {
    type: 'radar',
    data: {
        labels: ['Current Level', 'Target Level', 'Gap Analysis', 'Rata-rata'],
        datasets: [
            {
                label: 'DSS01 - Manage Operations',
                data: [
                    <?= (float) ($aggregateResults[0]['avg_rata_rata'] ?? 0) ?>,
                    4,
                    4 - <?= (float) ($aggregateResults[0]['avg_rata_rata'] ?? 0) ?>,
                    <?= (float) ($aggregateResults[0]['avg_rata_rata'] ?? 0) ?>
                ],
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(13, 110, 253, 1)'
            },
            {
                label: 'DSS05 - Manage Security Services',
                data: [
                    <?= (float) ($aggregateResults[1]['avg_rata_rata'] ?? 0) ?>,
                    4,
                    4 - <?= (float) ($aggregateResults[1]['avg_rata_rata'] ?? 0) ?>,
                    <?= (float) ($aggregateResults[1]['avg_rata_rata'] ?? 0) ?>
                ],
                backgroundColor: 'rgba(25, 135, 84, 0.2)',
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(25, 135, 84, 1)'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            r: {
                beginAtZero: true,
                max: 5,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
