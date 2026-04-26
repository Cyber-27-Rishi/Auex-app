<?php
// ============================================
// Aurex - Remove from Cart Handler
// ============================================
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$key = clean($_POST['key'] ?? '');

if (empty($key) || !isset($_SESSION['cart'][$key])) {
    echo json_encode(['success' => false, 'error' => 'Item not found in cart']);
    exit;
}

unset($_SESSION['cart'][$key]);

echo json_encode(['success' => true]);
