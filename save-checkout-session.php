<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

// FIX Bug 3: Ensure session is active before writing to it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Please login first']);
    exit;
}

$_SESSION['checkout_full_name'] = clean($_POST['full_name'] ?? '');
$_SESSION['checkout_email']     = clean($_POST['email']     ?? '');
$_SESSION['checkout_phone']     = clean($_POST['phone']     ?? '');
$_SESSION['checkout_address']   = clean($_POST['address']   ?? '');
$_SESSION['checkout_city']      = clean($_POST['city']      ?? '');
$_SESSION['checkout_state']     = clean($_POST['state']     ?? '');
$_SESSION['checkout_pincode']   = clean($_POST['pincode']   ?? '');

echo json_encode(['success' => true]);