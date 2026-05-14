<?php
/**
 * Sidebar Layout
 * Navigasi samping kiri
 */
$currentPage = currentPage();
?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-shield-check"></i>
        </div>
        <div class="brand-text">
            <h5 class="mb-0">e-Raport COBIT</h5>
            <small>SMKN 1 Teluk Mengkudu</small>
        </div>
        <button class="sidebar-close d-lg-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>/dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Framework COBIT -->
            <li class="nav-item">
                <a class="nav-link <?= in_array($currentPage, ['framework', 'domain']) ? 'active' : '' ?>" 
                   href="<?= BASE_URL ?>/framework">
                    <i class="bi bi-book"></i>
                    <span>Framework COBIT</span>
                </a>
            </li>
            
            <!-- Design Factor -->
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'design-factor' ? 'active' : '' ?>" 
                   href="<?= BASE_URL ?>/design-factor">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Design Factor</span>
                </a>
            </li>
            
            <!-- Data Penilaian -->
            <li class="nav-item">
                <a class="nav-link <?= in_array($currentPage, ['responden', 'dss01', 'dss05']) ? 'active' : '' ?>" 
                   data-bs-toggle="collapse" href="#penilaianSubmenu" role="button"
                   aria-expanded="<?= in_array($currentPage, ['responden', 'dss01', 'dss05']) ? 'true' : 'false' ?>">
                    <i class="bi bi-pencil-square"></i>
                    <span>Data Penilaian</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse <?= in_array($currentPage, ['responden', 'dss01', 'dss05']) ? 'show' : '' ?>" id="penilaianSubmenu">
                    <ul class="nav flex-column submenu">
                        <li>
                            <a class="nav-link <?= $currentPage === 'responden' ? 'active' : '' ?>" 
                               href="<?= BASE_URL ?>/penilaian/responden">
                                <i class="bi bi-people"></i>
                                <span>Data Responden</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link <?= $currentPage === 'dss01' ? 'active' : '' ?>" 
                               href="<?= BASE_URL ?>/penilaian/dss01">
                                <i class="bi bi-clipboard-check"></i>
                                <span>Penilaian DSS01</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link <?= $currentPage === 'dss05' ? 'active' : '' ?>" 
                               href="<?= BASE_URL ?>/penilaian/dss05">
                                <i class="bi bi-shield-lock"></i>
                                <span>Penilaian DSS05</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Hasil Analisis -->
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'analisis' ? 'active' : '' ?>" 
                   href="<?= BASE_URL ?>/analisis">
                    <i class="bi bi-graph-up"></i>
                    <span>Hasil Analisis</span>
                </a>
            </li>
            
            <!-- Laporan -->
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'laporan' ? 'active' : '' ?>" 
                   href="<?= BASE_URL ?>/laporan">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Laporan</span>
                </a>
            </li>
            
            <!-- Divider -->
            <li class="nav-item mt-3">
                <hr class="sidebar-divider">
            </li>
            
            <!-- Logout -->
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?= BASE_URL ?>/logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
