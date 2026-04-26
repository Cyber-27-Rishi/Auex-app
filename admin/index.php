<?php
// ============================================
// Aurex - Admin Login
// ============================================
session_start();

$host = 'localhost';
$dbname = 'auex_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed");
}

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminUser = trim($_POST['username'] ?? '');
    $adminPass = $_POST['password'] ?? '';

    if (empty($adminUser) || empty($adminPass)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
        $stmt->execute([':username' => $adminUser]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($adminPass, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Aurex</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Incompleeta:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <div class="brand">Aurex</div>
            <h2>Admin Panel</h2>
            <p>Secure admin access</p>
        </div>

        <div class="auth-card">
            <?php if ($error): ?>
                <div class="flash-message flash-error" style="position:static; animation:none; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group-custom">
                    <label>Username</label>
                    <input type="text" name="username" class="form-input" placeholder="Enter admin username" required>
                </div>

                <div class="form-group-custom">
                    <label>Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter password" required>
                </div>

                <button type="submit" class="btn btn-glow w-100">Sign In</button>
            </form>
        </div>

        <div class="auth-footer">
            <a href="../index.php"><i class="fas fa-arrow-left me-2"></i>Back to Store</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
