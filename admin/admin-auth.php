<?php
// ============================================
// Aurex - Admin Auth Check
// ============================================
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

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

define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('SITE_URL', 'http://localhost/Auex-app');

if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
}
