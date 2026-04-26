<?php
// ============================================
// Aurex - Verify OTP Handler
// ============================================
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/otp-functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$identifier = clean($_POST['identifier'] ?? '');
$otp = clean($_POST['otp'] ?? '');
$purpose = clean($_POST['purpose'] ?? 'register');

if (empty($identifier) || empty($otp)) {
    echo json_encode(['success' => false, 'error' => 'Identifier and OTP required']);
    exit;
}

if (verifyOTP($pdo, $identifier, $otp, $purpose)) {
    $redirect = isLoggedIn() ? 'index.php' : 'login.php';
    echo json_encode(['success' => true, 'redirect' => $redirect]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid or expired OTP']);
}
