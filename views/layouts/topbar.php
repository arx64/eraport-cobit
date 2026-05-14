<?php
/**
 * Topbar Layout
 * Bagian atas konten dengan breadcrumb dan info user
 */
?>
<!-- Main Content Wrapper -->
<div class="main-content">
    <!-- Topbar -->
    <nav class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?= BASE_URL ?>/dashboard">
                            <i class="bi bi-house-door"></i>
                        </a>
                    </li>
                    <?php if (isset($title) && $title !== 'Dashboard'): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= sanitize($title) ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        <div class="topbar-right">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-details d-none d-md-block">
                    <span class="user-name"><?= sanitize($_SESSION['nama_lengkap'] ?? 'Admin') ?></span>
                    <span class="user-role"><?= sanitize($_SESSION['role'] ?? 'Administrator') ?></span>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Content Area -->
    <div class="content-wrapper">
        <!-- Flash Messages -->
        <?= showFlash() ?>
