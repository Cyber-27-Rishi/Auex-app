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

$razorpayPaymentId = clean($_POST['razorpay_payment_id'] ?? '');
$razorpayOrderId   = clean($_POST['razorpay_order_id']   ?? '');
$razorpaySignature = clean($_POST['razorpay_signature']  ?? '');

if (empty($razorpayPaymentId) || empty($razorpayOrderId) || empty($razorpaySignature)) {
    echo json_encode(['success' => false, 'error' => 'Payment details missing']);
    exit;
}

try {
    require_once __DIR__ . '/vendor/autoload.php';

    $api = new Razorpay\Api\Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

    $attributes = [
        'razorpay_order_id'   => $razorpayOrderId,
        'razorpay_payment_id' => $razorpayPaymentId,
        'razorpay_signature'  => $razorpaySignature
    ];

    $api->utility->verifyPaymentSignature($attributes);

    // FIX Bug 4: Use session cart directly instead of getCartTotal()
    // getCartTotal() reads $_SESSION['cart'] which may be stale for buy-now flow.
    // We compute total from the actual cart items in session to stay consistent.
    $cartItems  = $_SESSION['cart'] ?? [];
    $cartTotal  = 0;
    foreach ($cartItems as $item) {
        $cartTotal += $item['price'] * $item['quantity'];
    }
    $grandTotal = $cartTotal; // Free shipping for Razorpay

    $fullName = clean($_SESSION['checkout_full_name'] ?? '');
    $email    = clean($_SESSION['checkout_email']     ?? '');
    $phone    = clean($_SESSION['checkout_phone']     ?? '');
    $address  = clean($_SESSION['checkout_address']   ?? '');
    $city     = clean($_SESSION['checkout_city']      ?? '');
    $state    = clean($_SESSION['checkout_state']     ?? '');
    $pincode  = clean($_SESSION['checkout_pincode']   ?? '');

    if (empty($fullName) || empty($phone) || empty($address) || empty($city)) {
        echo json_encode(['success' => false, 'error' => 'Shipping details missing. Please go back and re-enter your details.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, full_name, email, phone, address, city, state, pincode, payment_method, total_amount, status) VALUES (:user_id, :full_name, :email, :phone, :address, :city, :state, :pincode, :payment_method, :total_amount, :status)");
    $stmt->execute([
        ':user_id'        => $_SESSION['user_id'],
        ':full_name'      => $fullName,
        ':email'          => $email,
        ':phone'          => $phone,
        ':address'        => $address,
        ':city'           => $city,
        ':state'          => $state,
        ':pincode'        => $pincode,
        ':payment_method' => 'razorpay',
        ':total_amount'   => $grandTotal,
        ':status'         => 'processing'
    ]);

    $orderId = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, size, price) VALUES (:order_id, :product_id, :quantity, :size, :price)");
    foreach ($cartItems as $item) {
        $itemStmt->execute([
            ':order_id'   => $orderId,
            ':product_id' => $item['product_id'],
            ':quantity'   => $item['quantity'],
            ':size'       => $item['size'],
            ':price'      => $item['price']
        ]);
    }

    $_SESSION['cart'] = [];
    unset(
        $_SESSION['razorpay_order_id'],
        $_SESSION['checkout_full_name'],
        $_SESSION['checkout_email'],
        $_SESSION['checkout_phone'],
        $_SESSION['checkout_address'],
        $_SESSION['checkout_city'],
        $_SESSION['checkout_state'],
        $_SESSION['checkout_pincode']
    );

    echo json_encode([
        'success'  => true,
        'order_id' => $orderId,
        'redirect' => 'order-success.php?order_id=' . $orderId
    ]);

} catch (Razorpay\Api\Errors\SignatureVerificationError $e) {
    echo json_encode(['success' => false, 'error' => 'Payment verification failed. Please contact support.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}