<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ============================================
// Aurex - Checkout Page
// ============================================

// Include only backend logic (no HTML output) for POST processing
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    setFlash('error', 'Please login to checkout');
    header("Location: login.php");
    exit;
}

// Handle Buy First - skip cart calculations for faster load
if (isset($_GET['buy_now'])) {
    $buyNowId = (int)$_GET['buy_now'];
    $buyNowSize = clean($_GET['size'] ?? 'M');
    $buyNowQty  = max(1, (int)($_GET['qty'] ?? 1));

    $stmt = $pdo->prepare("SELECT id, name, price, discount, image, category FROM products WHERE id = :id AND is_active = 1 LIMIT 1");
    $stmt->execute([':id' => $buyNowId]);
    $product = $stmt->fetch();

    if ($product) {
        $discountedPrice = $product['price'] - ($product['price'] * $product['discount'] / 100);
        $cartItems = [
            'buynow_' . $buyNowId => [
                'product_id' => $buyNowId,
                'name' => $product['name'],
                'price' => $discountedPrice,
                'original_price' => $product['price'],
                'discount' => $product['discount'],
                'image' => $product['image'],
                'size' => $buyNowSize,
                'quantity' => $buyNowQty,
                'category' => $product['category']
            ]
        ];

        // SYNC: Put the "Buy Now" item into the session cart.
        // This allows Razorpay scripts (razorpay-order.php, razorpay-verify.php)
        // to correctly calculate the amount and save order items.
        $_SESSION['cart'] = $cartItems;

        $cartTotal = $discountedPrice * $buyNowQty;
        $shipping = 0;
        $codShippingFee = 50;
        $grandTotal = $cartTotal + $shipping;
    } else {
        header("Location: products.php");
        exit;
    }
} else {
    // Only calculate cart total for regular checkout
    $cartItems = $_SESSION['cart'] ?? [];
    if (empty($cartItems)) {
        header("Location: cart.php");
        exit;
    }
    $cartTotal = getCartTotal();
    $shipping = 0;
    $codShippingFee = 50;
    $grandTotal = $cartTotal + $shipping;
}

$user = [
    'name'  => $_SESSION['user_name']  ?? '',
    'email' => $_SESSION['user_email'] ?? '',
    'phone' => $_SESSION['user_phone'] ?? '',
];

// Handle COD form submission BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $fullName      = clean($_POST['full_name']       ?? '');
    $email         = clean($_POST['email']           ?? '');
    $phone         = clean($_POST['phone']           ?? '');
    $address       = clean($_POST['address']         ?? '');
    $city          = clean($_POST['city']            ?? '');
    $state         = clean($_POST['state']           ?? '');
    $pincode       = clean($_POST['pincode']         ?? '');
    $paymentMethod = clean($_POST['payment_method']  ?? 'cod');

    if (empty($fullName) || empty($phone) || empty($address) || empty($city)) {
        setFlash('error', 'Please fill in all required fields');
        header("Location: checkout.php");
        exit;
    } elseif ($paymentMethod === 'razorpay') {
        // Store shipping details in session for Razorpay verification
        $_SESSION['checkout_full_name'] = $fullName;
        $_SESSION['checkout_email']     = $email;
        $_SESSION['checkout_phone']     = $phone;
        $_SESSION['checkout_address']   = $address;
        $_SESSION['checkout_city']      = $city;
        $_SESSION['checkout_state']     = $state;
        $_SESSION['checkout_pincode']   = $pincode;
        // Razorpay handled via AJAX - exit here
        exit;
    } else {
        try {
            $finalTotal = $paymentMethod === 'cod' ? $grandTotal + $codShippingFee : $grandTotal;

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
                ':payment_method' => $paymentMethod,
                ':total_amount'   => $finalTotal,
                ':status'         => 'pending'
            ]);

            $orderId = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, size, price) VALUES (:order_id, :product_id, :quantity, :size, :price)");
            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    ':order_id'  => $orderId,
                    ':product_id'=> $item['product_id'],
                    ':quantity'  => $item['quantity'],
                    ':size'      => $item['size'],
                    ':price'     => $item['price']
                ]);
            }

            // Clear cart
            $_SESSION['cart'] = [];

            // Show order confirmation message and redirect
            setFlash('success', 'Your order is confirmed! Order ID: #' . $orderId);
            header("Location: order-success.php?order_id=" . $orderId);
            exit;
        } catch (Exception $e) {
            setFlash('error', 'Error placing order: ' . $e->getMessage());
            header("Location: checkout.php");
            exit;
        }
    }
}

// NOW include header for HTML output (after all redirects are handled)
$pageTitle = 'Checkout';
require_once 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Checkout</h1>
        <p>Complete your order</p>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a>
            <span>/</span>
            <a href="cart.php">Cart</a>
            <span>/</span>
            <span class="current">Checkout</span>
        </div>
    </div>
</div>

<!-- Checkout Section -->
<section class="checkout-section">
    <div class="container">
        <form method="POST" class="checkout-form" id="checkoutForm">
            <div class="row g-4">
                <!-- Shipping Details -->
                <div class="col-lg-8">
                    <div class="auth-card" style="padding: 30px;">
                        <h4 style="font-family: var(--font-heading); font-weight: 800; letter-spacing: 1px; margin-bottom: 24px;">
                            <i class="fas fa-truck me-2" style="color: var(--accent);"></i>Shipping Details
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo $user ? clean($user['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $user ? clean($user['email']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <!-- FIX Bug 2: Now correctly pulls phone from user array -->
                                <input type="tel" name="phone" class="form-control" value="<?php echo $user ? clean($user['phone']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control" placeholder="Enter pincode">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address *</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Enter your full address" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City *</label>
                                <input type="text" name="city" class="form-control" placeholder="Enter city" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" placeholder="Enter state">
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <h4 style="font-family: var(--font-heading); font-weight: 800; letter-spacing: 1px; margin: 30px 0 20px;">
                            <i class="fas fa-credit-card me-2" style="color: var(--accent);"></i>Payment Method
                        </h4>
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" name="payment_method" id="razorpay" value="razorpay" checked>
                                <i class="fas fa-bolt"></i>
                                <label for="razorpay">Pay with Razorpay (UPI / Card / Wallet)</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" name="payment_method" id="cod" value="cod">
                                <i class="fas fa-money-bill-wave"></i>
                                <label for="cod">Cash on Delivery</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4>Order Summary</h4>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex gap-3 mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                                <div style="width: 50px; height: 50px; border-radius: var(--radius-sm); overflow: hidden; flex-shrink: 0; background: var(--bg-elevated);">
                                    <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                                        <img src="<?php echo $item['image']; ?>" style="width:100%;height:100%;object-fit:cover;">
                                    <?php endif; ?>
                                </div>
                                <div style="flex:1;">
                                    <p style="font-size: 0.85rem; font-weight: 600; margin: 0;"><?php echo clean($item['name']); ?></p>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0;">Size: <?php echo $item['size']; ?> × <?php echo $item['quantity']; ?></p>
                                </div>
                                <span style="font-weight: 600; color: var(--accent); font-size: 0.9rem;"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                            </div>
                        <?php endforeach; ?>

                        <div class="summary-row">
                            <span class="label">Subtotal</span>
                            <span><?php echo formatPrice($cartTotal); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Shipping</span>
                            <span id="shippingDisplay">Free</span>
                        </div>
                        <div class="summary-row total">
                            <span class="label">Total</span>
                            <span id="grandTotal"><?php echo formatPrice($cartTotal); ?></span>
                        </div>

                        <button type="submit" class="btn btn-glow w-100 mt-4" id="placeOrderBtn">
                            <i class="fas fa-lock me-2"></i>Place Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<?php require_once 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    var baseTotal     = <?php echo $cartTotal; ?>;
    var codShippingFee = 50;

    // FIX Bug 7: Use a single clear variable for the displayed total
    function getDisplayTotal() {
        return $('input[name="payment_method"]:checked').val() === 'cod'
            ? baseTotal + codShippingFee
            : baseTotal;
    }

    function updateTotal() {
        if ($('input[name="payment_method"]:checked').val() === 'cod') {
            $('#shippingDisplay').text('₹50');
        } else {
            $('#shippingDisplay').text('Free');
        }
        $('#grandTotal').text('₹' + getDisplayTotal().toFixed(2));
    }

    $('input[name="payment_method"]').on('change', updateTotal);
    updateTotal();

    $('#checkoutForm').on('submit', function(e) {
        var selectedPayment = $('input[name="payment_method"]:checked').val();

        if (selectedPayment === 'razorpay') {
            e.preventDefault();

            // FIX Bug 4: Validate required fields before firing Razorpay AJAX
            var fullName = $('input[name="full_name"]').val().trim();
            var phone    = $('input[name="phone"]').val().trim();
            var address  = $('textarea[name="address"]').val().trim();
            var city     = $('input[name="city"]').val().trim();

            if (!fullName || !phone || !address || !city) {
                alert('Please fill in all required fields (Name, Phone, Address, City).');
                return false;
            }

            var formData = $(this).serialize();

            $.ajax({
                url: 'save-checkout-session.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function() {
                    $.ajax({
                        url: 'razorpay-order.php',
                        method: 'POST',
                        data: { amount: baseTotal },
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                var options = {
                                    key: res.key_id,
                                    amount: res.amount,
                                    currency: 'INR',
                                    name: res.name,
                                    description: res.description,
                                    order_id: res.order_id,
                                    prefill: res.prefill,
                                    theme: { color: '#c8a96e' },
                                    handler: function(response) {
                                        $.ajax({
                                            url: 'razorpay-verify.php',
                                            method: 'POST',
                                            data: {
                                                razorpay_payment_id: response.razorpay_payment_id,
                                                razorpay_order_id:   response.razorpay_order_id,
                                                razorpay_signature:  response.razorpay_signature
                                            },
                                            dataType: 'json',
                                            success: function(verifyRes) {
                                                if (verifyRes.success) {
                                                    window.location.href = verifyRes.redirect;
                                                } else {
                                                    alert('Payment verification failed: ' + verifyRes.error);
                                                    // Re-enable button so user can retry
                                                    $('#placeOrderBtn').prop('disabled', false)
                                                        .html('<i class="fas fa-lock me-2"></i>Place Order');
                                                }
                                            },
                                            error: function() {
                                                alert('Error verifying payment.');
                                                $('#placeOrderBtn').prop('disabled', false)
                                                    .html('<i class="fas fa-lock me-2"></i>Place Order');
                                            }
                                        });
                                    },
                                    modal: {
                                        // FIX Bug 5: Always re-enable button when modal is dismissed
                                        ondismiss: function() {
                                            $('#placeOrderBtn').prop('disabled', false)
                                                .html('<i class="fas fa-lock me-2"></i>Place Order');
                                        }
                                    }
                                };

                                var rzp = new Razorpay(options);
                                rzp.open();
                                // Disable button AFTER open so user can't double-submit
                                $('#placeOrderBtn').prop('disabled', true)
                                    .html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
                            } else {
                                alert('Error: ' + res.error);
                            }
                        },
                        error: function() {
                            alert('Error creating Razorpay order.');
                            $('#placeOrderBtn').prop('disabled', false)
                                .html('<i class="fas fa-lock me-2"></i>Place Order');
                        }
                    });
                },
                error: function() {
                    alert('Error saving checkout details.');
                    $('#placeOrderBtn').prop('disabled', false)
                        .html('<i class="fas fa-lock me-2"></i>Place Order');
                }
            });
        }
        // COD submits normally
    });
});
</script>