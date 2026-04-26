<?php
// ============================================
// Aurex - Database Configuration
// ============================================

// FIX: Guard prevents "session already started" warning from
// corrupting JSON responses in AJAX files (save-checkout-session,
// razorpay-order, razorpay-verify). A PHP warning in the output
// breaks JSON parsing and causes the checkout to hang silently.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

// Load Environment Variables
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (Exception $e) {
    // If .env is missing in production, environment variables should be set on the server
}

$host     = $_ENV['DB_HOST']   ?? 'localhost';
$dbname   = $_ENV['DB_NAME']   ?? 'auex_store';
$username = $_ENV['DB_USER']   ?? 'root';
$password = $_ENV['DB_PASS']   ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

define('SITE_NAME', 'Aurex');
define('SITE_URL', $_ENV['SITE_URL'] ?? 'http://localhost/Auex-app');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads');

// Add your real keys from dashboard.razorpay.com → Settings → API Keys
define('RAZORPAY_KEY_ID',     $_ENV['RAZORPAY_KEY_ID']     ?? '');
define('RAZORPAY_KEY_SECRET', $_ENV['RAZORPAY_KEY_SECRET'] ?? '');

if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
