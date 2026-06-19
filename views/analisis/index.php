<?php

/**
 * Analisis View
 * Halaman hasil analisis dan rekomendasi
 */
?>
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-graph-up me-2"></i>Hasil Analisis
            </h4>
            <p class="text-muted mb-0">Analisis capability level dan rekomendasi</p>
        </div>
        <div class="col-auto">
            <form method="GET" class="d-flex align-items-center gap-2" id="dateFilterForm">
                <input type="hidden" name="process_id" value="<?= $processId ?>">
                <input type="hidden" name="respondent_id" value="<?= $respondentId ?>">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    <input type="text" class="form-control" name="tanggal" id="tanggal"
                           value="<?= sanitize($tanggal) ?>" placeholder="Pilih tanggal" autocomplete="off" readonly>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
            </form>
            <div class="date-picker-legend justify-content-end">
                <span class="legend-item"><span class="legend-swatch has-dot"></span> Tersedia data</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const datesWithData = <?= json_encode($datesWithData) ?>;
    initDatePickerWithData('#tanggal', datesWithData, {
        formId: 'dateFilterForm',
        defaultDate: '<?= $tanggal ?>',
        ajaxUrl: '<?= BASE_URL ?>/api/dates-with-data'
    });
});
</script>

<!-- Capability Level Cards -->
<div class="row g-4 mb-4">
    <?php foreach ($aggregateResults as $result):
        $rataRata = (float) ($result['avg_rata_rata'] ?? 0);
        $gap = (float) ($result['avg_gap'] ?? TARGET_LEVEL);
        $cardClass = $result['kode_domain'] === 'DSS01' ? 'border-primary' : 'border-success';
        $headerClass = $result['kode_domain'] === 'DSS01' ? 'bg-primary' : 'bg-success';
    ?>
        <div class="col-lg-6">
            <div class="capability-card <?= $cardClass ?>">
                <div class="capability-header <?= $headerClass ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-dark"><?= sanitize($result['kode_domain']) ?></span>
                            <h5 class="mt-2 mb-0"><?= sanitize($result['nama_domain']) ?></h5>
                        </div>
                        <div class="capability-score">
                            <span class="score-value"><?= number_format($rataRata, 2) ?></span>
                            <span class="score-max">/ 5</span>
                        </div>
                    </div>
                </div>
                <div class="capability-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="metric-item">
                                <span class="metric-label">Current Level</span>
                                <span class="metric-value badge <?= getCapabilityBadge($rataRata) ?>">
                                    <?= getCapabilityLabel($rataRata) ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="metric-item">
                                <span class="metric-label">Target Level</span>
                                <span class="metric-value badge bg-primary"><?= number_format(TARGET_LEVEL, 2) ?> - <?= getCapabilityLabel(TARGET_LEVEL) ?></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="metric-item">
                                <span class="metric-label">Gap</span>
                                <span class="metric-value badge <?= getGapBadge($gap) ?>">
                                    <?= number_format($gap, 2) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-header">
                <h5><i class="bi bi-bar-chart-line me-2"></i>Grafik Capability Level per Domain</h5>
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
                <h5><i class="bi bi-bullseye me-2"></i>Radar Chart - Perbandingan Domain</h5>
            </div>
            <div class="chart-body">
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Analysis Table -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="table-card">
            <div class="table-header">
                <h5><i class="bi bi-table me-2"></i>Tabel Hasil Analisis Detail</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Responden</th>
                            <th>Domain</th>
                            <th class="text-center">Total Nilai</th>
                            <th class="text-center">Rata-rata</th>
                            <th class="text-center">Current Level</th>
                            <th class="text-center">Target</th>
                            <th class="text-center">Gap</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $r): ?>
                            <tr>
                                <td>
                                    <strong><?= sanitize($r['respondent_name'] ?? $r['nama']) ?></strong>
                                    <br><small class="text-muted"><?= sanitize($r['jabatan']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-dark"><?= sanitize($r['kode_domain']) ?></span>
                                </td>
                                <td class="text-center"><?= $r['total_nilai'] ?></td>
                                <td class="text-center"><?= number_format($r['rata_rata'], 2) ?></td>
                                <td class="text-center">
                                    <span class="badge <?= getCapabilityBadge($r['rata_rata']) ?>">
                                        <?= sanitize($r['current_level']) ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= $r['target_level'] ?></td>
                                <td class="text-center">
                                    <span class="badge <?= getGapBadge($r['gap']) ?>">
                                        <?= number_format($r['gap'], 2) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= getGapBadge($r['gap']) ?>">
                                        <?= sanitize($r['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data hasil analisis
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Rekomendasi Section -->
<div class="row g-4">
    <div class="col-12">
        <div class="content-card">
            <div class="content-card-header">
                <h5><i class="bi bi-lightbulb me-2"></i>Rekomendasi Perbaikan</h5>
            </div>
            <div class="content-card-body">
                <?php foreach ($recommendations as $domain => $recommendationList):
                    $processData = array_filter($aggregateResults, fn($r) => $r['kode_domain'] === $domain);
                    $processData = array_values($processData)[0] ?? null;
                    $gap = (float) ($processData['avg_gap'] ?? TARGET_LEVEL);
                    $badgeClass = getGapBadge($gap);
                ?>
                    <div class="recommendation-domain mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-dark me-2"><?= sanitize($domain) ?></span>
                            <h6 class="mb-0"><?= sanitize($processData['nama_domain'] ?? '') ?></h6>
                            <span class="badge <?= $badgeClass ?> ms-auto">
                                Gap: <?= number_format($gap, 2) ?>
                            </span>
                        </div>
                        <div class="recommendation-list">
                            <?php foreach ($recommendationList as $i => $rec): ?>
                                <div class="recommendation-item">
                                    <span class="recommendation-number"><?= $i + 1 ?></span>
                                    <span class="recommendation-text"><?= sanitize($rec) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Capability Chart
    const capCtx = document.getElementById('capabilityChart').getContext('2d');
    new Chart(capCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(fn($r) => $r['kode_domain'], $aggregateResults)) ?>,
            datasets: [{
                label: 'Rata-rata Capability Level',
                data: <?= json_encode(array_map(fn($r) => round((float)$r['avg_rata_rata'], 2), $aggregateResults)) ?>,
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
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Level: ' + Number(context.parsed.y).toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Number(value).toFixed(2);
                        }
                    },
                    title: {
                        display: true,
                        text: 'Nilai Rata-rata'
                    }
                }
            }
        }
    });

    // Gap Chart
    const gapCtx = document.getElementById('gapChart').getContext('2d');
    new Chart(gapCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(fn($r) => $r['kode_domain'], $aggregateResults)) ?>,
            datasets: [{
                    label: 'Current Level',
                    data: <?= json_encode(array_map(fn($r) => round((float)$r['avg_rata_rata'], 2), $aggregateResults)) ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.8)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    borderRadius: 6
                },
                {
                    label: 'Target Level',
                    data: Array(<?= count($aggregateResults) ?>).fill(<?= TARGET_LEVEL ?>),
                    backgroundColor: 'rgba(25, 135, 84, 0.3)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + Number(context.parsed.y).toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Number(value).toFixed(2);
                        }
                    },
                    title: {
                        display: true,
                        text: 'Level'
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
            labels: ['Current Level', 'Target Level', 'Gap Analysis'],
            datasets: [{
                    label: 'DSS01',
                    data: [
                        <?= round((float)($aggregateResults[0]['avg_rata_rata'] ?? 0), 2) ?>,
                        <?= TARGET_LEVEL ?>,
                        <?= round(TARGET_LEVEL - (float)($aggregateResults[0]['avg_rata_rata'] ?? 0), 2) ?>
                    ],
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2
                },
                {
                    label: 'DSS05',
                    data: [
                        <?= round((float)($aggregateResults[1]['avg_rata_rata'] ?? 0), 2) ?>,
                        <?= TARGET_LEVEL ?>,
                        <?= round(TARGET_LEVEL - (float)($aggregateResults[1]['avg_rata_rata'] ?? 0), 2) ?>
                    ],
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + Number(context.parsed.r).toFixed(2);
                        }
                    }
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Number(value).toFixed(2);
                        }
                    }
                }
            }
        }
    });
</script>