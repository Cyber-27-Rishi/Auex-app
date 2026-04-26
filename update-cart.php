<?php
// ============================================
// Aurex - Update Cart Handler
// ============================================
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$key = clean($_POST['key'] ?? '');
$action = clean($_POST['action'] ?? '');

if (empty($key) || !isset($_SESSION['cart'][$key])) {
    echo json_encode(['success' => false, 'error' => 'Item not found in cart']);
    exit;
}

if ($action === 'increase') {
    $_SESSION['cart'][$key]['quantity']++;
} elseif ($action === 'decrease') {
    if ($_SESSION['cart'][$key]['quantity'] > 1) {
        $_SESSION['cart'][$key]['quantity']--;
    }
}

echo json_encode(['success' => true]);
