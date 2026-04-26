<?php
// ============================================
// Aurex - Register Page
// ============================================
$pageTitle = 'Register';
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/otp-functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
    if (empty($phone)) $errors[] = 'Phone is required';
    if (strlen($phone) < 10) $errors[] = 'Invalid phone number';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
    if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'error' => implode(', ', $errors)]);
        exit;
    }

    // Register user
    $result = registerUser($pdo, $name, $email, $phone, $password);

    if ($result['success']) {
        // Auto login after registration
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        echo json_encode([
            'success' => true,
            'redirect' => 'index.php'
        ]);
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
    <title>Register | Aurex</title>
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
            <h2>Create Account</h2>
            <p>Join the premium streetwear community</p>
        </div>

        <div class="auth-card">
            <form id="registerForm" action="register.php" method="POST">
                <div class="form-group-custom">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-input" placeholder="Enter your full name" required>
                </div>

                <div class="form-group-custom">
                    <label>Email Address</label>
                    <input type="email" name="email" id="identifier" class="form-input" placeholder="Enter your email" required>
                </div>

                <div class="form-group-custom">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" class="form-input" placeholder="Enter your phone number" required>
                </div>

                <div class="form-group-custom">
                    <label>Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Create a password (min 6 chars)" required>
                </div>

                <div class="form-group-custom">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="btn btn-glow w-100">Create Account</button>
            </form>

            <div class="auth-divider"><span>or</span></div>

            <div class="text-center">
                <a href="login.php" class="btn btn-dark-custom w-100">Already have an account? Sign In</a>
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
