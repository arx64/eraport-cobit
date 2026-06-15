<?php
/**
 * Login View
 * Halaman login sistem
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Analisis Risiko TI e-Raport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/login.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Login Header -->
            <div class="login-header">
                <div class="login-icon">
                    <!-- <i class="bi bi-shield-check"></i> -->
                     <img src="assets/img/logo-smk.jpeg" alt="Logo SMKN 1 Teluk Mengkudu" width="100%">
                </div>
                <h3>Sistem Analisis Risiko TI</h3>
                <p class="mb-0">e-Raport menggunakan COBIT 2019</p>
                <small>SMKN 1 Teluk Mengkudu</small>
            </div>
            
            <!-- Login Form -->
            <div class="login-form">
                <?php if ($error ?? false): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= sanitize($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form action="<?= BASE_URL ?>/authenticate" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Username" required autofocus>
                        <label for="username"><i class="bi bi-person me-2"></i>Username</label>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required>
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                    </div>
                    
                    <button type="submit" class="btn btn-login w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </form>
                
                <div class="login-footer">
                    <p class="text-muted mb-0">
                        <!-- <small>Default: admin / password</small> -->
                    </p>
                </div>
            </div>
        </div>
        
        <div class="login-copyright">
            <p>&copy; <?= date('Y') ?> SMKN 1 Teluk Mengkudu. All rights reserved.</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
