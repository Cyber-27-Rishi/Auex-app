<?php
// ============================================
// Aurex - Login Page
// ============================================
$pageTitle = 'Login';
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $identifier = clean($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all fields']);
        exit;
    }

    $result = loginUser($pdo, $identifier, $password);

    if ($result['success']) {
        echo json_encode(['success' => true, 'redirect' => 'index.php']);
    } else {
        echo json_encode($result);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Aurex</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Incompleeta:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <div class="brand">Aurex</div>
            <h2>Welcome Back</h2>
            <p>Sign in to your account</p>
        </div>

        <div class="auth-card">
            <form id="loginForm" action="login.php" method="POST">
                <div class="form-group-custom">
                    <label>Email or Phone</label>
                    <input type="text" name="identifier" id="identifier" class="form-input" placeholder="Enter your email or phone" required>
                </div>

                <div class="form-group-custom">
                    <label>Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn btn-glow w-100">Sign In</button>
            </form>

            <div class="auth-divider"><span>or</span></div>

            <div class="text-center">
                <a href="register.php" class="btn btn-dark-custom w-100">Create New Account</a>
            </div>

            <div style="text-align: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                <a href="admin/index.php" style="color: var(--text-muted); font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; transition: var(--transition);">
                    <i class="fas fa-shield-alt me-1"></i> Admin Login
                </a>
            </div>
        </div>

        <div class="auth-footer">
            <a href="index.php"><i class="fas fa-arrow-left me-2"></i>Back to Store</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
