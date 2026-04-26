<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

// FIX Bug 3: Ensure session is active
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

// FIX Bug 5: Harden amount — reject non-numeric and zero values
$rawAmount = $_POST['amount'] ?? '';
if (!is_numeric($rawAmount)) {
    echo json_encode(['success' => false, 'error' => 'Invalid amount']);
    exit;
}
$amount = floatval($rawAmount);
$cartItems = $_SESSION['cart'] ?? [];

if (empty($cartItems) || $amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid cart or amount']);
    exit;
}

$amountPaise = (int) round($amount * 100); // FIX: cast to int, Razorpay requires integer paise

try {
    require_once __DIR__ . '/vendor/autoload.php';

    $api = new Razorpay\Api\Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

    $orderData = [
        'receipt'         => 'order_rcpt_' . time(),
        'amount'          => $amountPaise,
        'currency'        => 'INR',
        'payment_capture' => 1
    ];

    $razorpayOrder   = $api->order->create($orderData);
    $razorpayOrderId = $razorpayOrder['id'];

    $_SESSION['razorpay_order_id'] = $razorpayOrderId;

    echo json_encode([
        'success'     => true,
        'order_id'    => $razorpayOrderId,
        'amount'      => $amountPaise,
        'key_id'      => RAZORPAY_KEY_ID,
        'name'        => 'Aurex',
        'description' => 'Aurex Premium Streetwear Order',
        // FIX Bug 2: Read phone from session (saved by save-checkout-session.php)
        'prefill' => [
            'name'    => $_SESSION['user_name']          ?? '',
            'email'   => $_SESSION['user_email']         ?? '',
            'contact' => $_SESSION['checkout_phone']     ?? '',  // ← FIXED
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Razorpay error: ' . $e->getMessage()]);
}