<?php
// ============================================
// Aurex - Send OTP Handler
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
$purpose = clean($_POST['purpose'] ?? 'register');

if (empty($identifier)) {
    echo json_encode(['success' => false, 'error' => 'Identifier required']);
    exit;
}

$result = sendOTP($pdo, $identifier, $purpose);

echo json_encode($result);
