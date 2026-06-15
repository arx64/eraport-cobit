<?php

/**
 * PDF Laporan View
 * Template Formal Audit Akademik
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Audit TI e-Raport</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
            padding: 30px;
        }

        .judul {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 25px;
        }

        .judul h3 {
            font-size: 15px;
            margin-bottom: 5px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 25px;
        }

        .info-table td {
            padding: 4px;
            vertical-align: top;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        p {
            text-align: justify;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        table.data-table th {
            text-align: center;
            background: #d9d9d9;
        }

        .text-center {
            text-align: center;
        }

        ol {
            margin-left: 20px;
        }

        ol li {
            margin-bottom: 5px;
            text-align: justify;
        }

        .signature {
            margin-top: 70px;
            width: 100%;
        }

        .signature td {
            text-align: center;
            vertical-align: top;
        }

        .ttd-space {
            height: 80px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <!-- JUDUL -->
    <div class="judul">

        <h3>LAPORAN HASIL AUDIT</h3>
        <h3>ANALISIS RISIKO TEKNOLOGI INFORMASI</h3>
        <h3>SISTEM e-RAPORT</h3>

    </div>

    <!-- INFORMASI -->
    <table class="info-table">

        <tr>
            <td width="30%">Tanggal Audit</td>
            <td width="5%">:</td>
            <td><?= sanitize($tanggal) ?></td>
        </tr>

        <tr>
            <td>Tempat Audit</td>
            <td>:</td>
            <td>SMKN 1 Teluk Mengkudu</td>
        </tr>

        <tr>
            <td>Framework</td>
            <td>:</td>
            <td>COBIT 2019</td>
        </tr>

        <tr>
            <td>Domain Audit</td>
            <td>:</td>
            <td>DSS01 dan DSS05</td>
        </tr>

        <tr>
            <td>Jumlah Responden</td>
            <td>:</td>
            <td><?= count($respondents) ?> Responden</td>
        </tr>

    </table>

    <!-- PENDAHULUAN -->
    <div class="section">

        <div class="section-title">
            I. PENDAHULUAN
        </div>

        <p>
            Audit teknologi informasi ini dilakukan untuk mengevaluasi tingkat
            kapabilitas pengelolaan layanan dan keamanan sistem e-Raport
            menggunakan framework COBIT 2019 pada domain DSS01
            (Manage Operations) dan DSS05 (Manage Security Services).
        </p>

    </div>

    <!-- HASIL ANALISIS -->
    <div class="section">

        <div class="section-title">
            II. HASIL ANALISIS CAPABILITY LEVEL
        </div>

        <table class="data-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Domain</th>
                    <th>Rata-rata</th>
                    <th>Current Level</th>
                    <th>Target Level</th>
                    <th>Gap</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($aggregateResults as $i => $result):

                    $rataRata = (float) ($result['avg_rata_rata'] ?? 0);
                    $gap = (float) ($result['avg_gap'] ?? 4);

                ?>

                    <tr>

                        <td class="text-center">
                            <?= $i + 1 ?>
                        </td>

                        <td>
                            <strong><?= sanitize($result['kode_domain']) ?></strong>
                            -
                            <?= sanitize($result['nama_domain']) ?>
                        </td>

                        <td class="text-center">
                            <?= number_format($rataRata, 2) ?>
                        </td>

                        <td class="text-center">
                            <?= getCapabilityLabel($rataRata) ?>
                        </td>

                        <td class="text-center">
                            4
                        </td>

                        <td class="text-center">
                            <?= number_format($gap, 2) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <!-- DETAIL RESPONDEN -->
    <div class="section">

        <div class="section-title">
            III. DETAIL HASIL RESPONDEN
        </div>

        <table class="data-table">

            <thead>

                <tr>
                    <th>No</th>
                    <th>Responden</th>
                    <th>Jabatan</th>
                    <th>Domain</th>
                    <th>Total Nilai</th>
                    <th>Rata-rata</th>
                    <th>Gap</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($allResults as $i => $r): ?>

                    <tr>

                        <td class="text-center">
                            <?= $i + 1 ?>
                        </td>

                        <td>
                            <?= sanitize($r['respondent_name']) ?>
                        </td>

                        <td>
                            <?= sanitize($r['jabatan']) ?>
                        </td>

                        <td class="text-center">
                            <?= sanitize($r['kode_domain']) ?>
                        </td>

                        <td class="text-center">
                            <?= $r['total_nilai'] ?>
                        </td>

                        <td class="text-center">
                            <?= number_format($r['rata_rata'], 2) ?>
                        </td>

                        <td class="text-center">
                            <?= number_format($r['gap'], 2) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <!-- REKOMENDASI -->
    <div class="section">

        <div class="section-title">
            IV. REKOMENDASI PERBAIKAN
        </div>

        <?php foreach ($recommendations as $domain => $recommendationList):

            $processData = array_filter(
                $aggregateResults,
                fn($r) => $r['kode_domain'] === $domain
            );

            $processData = array_values($processData)[0] ?? null;

        ?>

            <div style="margin-bottom:15px;">

                <p>
                    <strong>
                        <?= sanitize($domain) ?>
                        -
                        <?= sanitize($processData['nama_domain'] ?? '') ?>
                    </strong>
                </p>

                <ol>

                    <?php foreach ($recommendationList as $rec): ?>

                        <li><?= sanitize($rec) ?></li>

                    <?php endforeach; ?>

                </ol>

            </div>

        <?php endforeach; ?>

    </div>

    <!-- KESIMPULAN -->
    <div class="section">

        <div class="section-title">
            V. KESIMPULAN
        </div>

        <p>
            Berdasarkan hasil audit yang dilakukan menggunakan framework
            COBIT 2019, sistem e-Raport di SMKN 1 Teluk Mengkudu masih
            memerlukan peningkatan pada aspek pengelolaan operasional
            dan keamanan layanan sistem agar dapat mencapai target
            capability level yang diharapkan.
        </p>

    </div>

    <!-- TANDA TANGAN -->
    <table class="signature">

        <tr>

            <td width="50%">
                Mengetahui,
                <br>
                Kepala Sekolah
            </td>

            <td width="50%">
                Teluk Mengkudu,
                <?= sanitize($tanggal) ?>
                <br>
                Auditor / Peneliti
            </td>

        </tr>

        <tr>
            <td class="ttd-space"></td>
            <td class="ttd-space"></td>
        </tr>

        <tr>

            <td>
                <u>(................................)</u>
            </td>

            <td>
                <u>(................................)</u>
            </td>

        </tr>

    </table>

    <!-- FOOTER -->
    <div class="footer">

        <p>
            Laporan Audit TI Sistem e-Raport Menggunakan COBIT 2019
        </p>

        <p>
            SMKN 1 Teluk Mengkudu © <?= sanitize($tahun) ?>
        </p>

    </div>

</body>

</html>