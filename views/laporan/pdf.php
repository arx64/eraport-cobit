<?php
/**
 * PDF Laporan View
 * Template untuk generate laporan PDF
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Risiko TI e-Raport</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px double #333;
        }
        .report-header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .report-header h3 {
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 5px 10px;
            background: #f0f0f0;
            border-left: 4px solid #0d6efd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background: #0d6efd;
            color: white;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-primary { background: #cce5ff; color: #004085; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-dark { background: #d6d8d9; color: #1b1e21; }
        .mb-3 { margin-bottom: 15px; }
        .mt-4 { margin-top: 20px; }
        .text-muted { color: #666; }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
        }
        .stat-box {
            display: table-cell;
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            width: 33.33%;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
        }
        ol { margin-left: 20px; }
        ol li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="report-header">
        <h2>LAPORAN ANALISIS RISIKO TEKNOLOGI INFORMASI</h2>
        <h3>Sistem e-Raport</h3>
        <p>SMKN 1 Teluk Mengkudu</p>
        <p class="text-muted"><?= sanitize($tanggal) ?></p>
    </div>
    
    <!-- Ringkasan -->
    <div class="section">
        <div class="section-title">I. Ringkasan Eksekutif</div>
        <p>
            Laporan ini menyajikan hasil analisis risiko teknologi informasi pada sistem e-Raport 
            di SMKN 1 Teluk Mengkudu menggunakan framework COBIT 2019, dengan fokus pada domain 
            DSS01 (Manage Operations) dan DSS05 (Manage Security Services).
        </p>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value"><?= count($respondents) ?></div>
                <div class="stat-label">Total Responden</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">2</div>
                <div class="stat-label">Domain COBIT</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">12</div>
                <div class="stat-label">Total Pertanyaan</div>
            </div>
        </div>
    </div>
    
    <!-- Hasil Analisis -->
    <div class="section">
        <div class="section-title">II. Hasil Analisis Capability Level</div>
        <table>
            <thead>
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
                    if ($rataRata >= 4.5) {
                        $levelClass = 'badge-success';
                    } elseif ($rataRata >= 3.5) {
                        $levelClass = 'badge-primary';
                    } elseif ($rataRata >= 2.5) {
                        $levelClass = 'badge-info';
                    } elseif ($rataRata >= 1.5) {
                        $levelClass = 'badge-warning';
                    } elseif ($rataRata >= 0.5) {
                        $levelClass = 'badge-warning';
                    } else {
                        $levelClass = 'badge-danger';
                    }

                    if ($gap > 2.0) {
                        $gapClass = 'badge-danger';
                    } elseif ($gap > 1.5) {
                        $gapClass = 'badge-warning';
                    } elseif ($gap > 1.0) {
                        $gapClass = 'badge-warning';
                    } elseif ($gap > 0.5) {
                        $gapClass = 'badge-primary';
                    } else {
                        $gapClass = 'badge-success';
                    }
                ?>
                <tr>
                    <td>
                        <strong><?= sanitize($result['kode_domain']) ?></strong> - 
                        <?= sanitize($result['nama_domain']) ?>
                    </td>
                    <td class="text-center"><?= number_format($rataRata, 2) ?></td>
                    <td class="text-center"><span class="badge <?= $levelClass ?>"><?= getCapabilityLabel($rataRata) ?></span></td>
                    <td class="text-center">Managed and Measurable (4)</td>
                    <td class="text-center"><span class="badge <?= $gapClass ?>"><?= number_format($gap, 2) ?></span></td>
                    <td class="text-center"><span class="badge <?= $gapClass ?>"><?= getGapStatus($gap) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Detail Hasil -->
    <div class="section">
        <div class="section-title">III. Detail Hasil per Responden</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Responden</th>
                    <th>Domain</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Rata-rata</th>
                    <th class="text-center">Current Level</th>
                    <th class="text-center">Gap</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allResults as $i => $r):
                    if ($r['rata_rata'] >= 4.5) {
                        $levelClass = 'badge-success';
                    } elseif ($r['rata_rata'] >= 3.5) {
                        $levelClass = 'badge-primary';
                    } elseif ($r['rata_rata'] >= 2.5) {
                        $levelClass = 'badge-info';
                    } elseif ($r['rata_rata'] >= 1.5) {
                        $levelClass = 'badge-warning';
                    } elseif ($r['rata_rata'] >= 0.5) {
                        $levelClass = 'badge-warning';
                    } else {
                        $levelClass = 'badge-danger';
                    }
                ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= sanitize($r['respondent_name']) ?><br><small class="text-muted"><?= sanitize($r['jabatan']) ?></small></td>
                    <td><?= sanitize($r['kode_domain']) ?></td>
                    <td class="text-center"><?= $r['total_nilai'] ?></td>
                    <td class="text-center"><?= number_format($r['rata_rata'], 2) ?></td>
                    <td class="text-center"><span class="badge <?= $levelClass ?>"><?= sanitize($r['current_level']) ?></span></td>
                    <td class="text-center"><?= number_format($r['gap'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Rekomendasi -->
    <div class="section">
        <div class="section-title">IV. Rekomendasi Perbaikan</div>
        <?php foreach ($recommendations as $domain => $recommendationList): 
            $processData = array_filter($aggregateResults, fn($r) => $r['kode_domain'] === $domain);
            $processData = array_values($processData)[0] ?? null;
        ?>
        <div class="mb-3">
            <p><strong><?= sanitize($domain) ?> - <?= sanitize($processData['nama_domain'] ?? '') ?></strong></p>
            <ol>
                <?php foreach ($recommendationList as $rec): ?>
                <li><?= sanitize($rec) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Kesimpulan -->
    <div class="section">
        <div class="section-title">V. Kesimpulan</div>
        <p>
            Berdasarkan hasil analisis menggunakan framework COBIT 2019, sistem e-Raport di SMKN 1 Teluk Mengkudu 
            memiliki tingkat kematangan yang perlu ditingkatkan pada kedua domain yang dievaluasi. 
            Rekomendasi yang diberikan diharapkan dapat menjadi panduan untuk perbaikan berkelanjutan 
            dalam pengelolaan dan keamanan sistem e-Raport.
        </p>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Sistem Analisis Risiko TI e-Raport menggunakan COBIT 2019</p>
        <p>SMKN 1 Teluk Mengkudu &copy; <?= sanitize($tahun) ?></p>
    </div>
</body>
</html>
