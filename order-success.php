<?php
// ============================================
// Aurex - Order Success Page
// ============================================

// Include only backend logic first (no HTML output)
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Get the order ID from URL
$orderId = (int)($_GET['order_id'] ?? 0);
if ($orderId === 0) {
    header("Location: orders.php");
    exit;
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id");
$stmt->execute([':order_id' => $orderId, ':user_id' => $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php");
    exit;
}

// Get order items
$itemStmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :order_id");
$itemStmt->execute([':order_id' => $orderId]);
$orderItems = $itemStmt->fetchAll();

// NOW include header for HTML output
$pageTitle = 'Order Placed';
require_once 'includes/header.php';
?>

<!-- Order Success Section -->
<section class="order-success-section">
    <div class="container">
        <div class="success-container">
            <!-- Success Icon -->
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <!-- Success Message -->
            <h1 class="success-title">Order Placed Successfully!</h1>
            <p class="success-subtitle">Thank you for your purchase. Your order has been confirmed.</p>
            
            <!-- Order Details Card -->
            <div class="order-details-card">
                <div class="order-info-row">
                    <span class="label">Order ID</span>
                    <span class="value">#<?php echo $order['id']; ?></span>
                </div>
                <div class="order-info-row">
                    <span class="label">Order Date</span>
                    <span class="value"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="order-info-row">
                    <span class="label">Payment Method</span>
                    <span class="value"><?php echo ucfirst($order['payment_method']); ?></span>
                </div>
                <div class="order-info-row">
                    <span class="label">Order Status</span>
                    <span class="value status-badge status-<?php echo $order['status']; ?>"><?php
                        $statusLabels = [
                            'pending' => 'Pending',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'order_confirm' => 'Order Confirm',
                            'order_ship' => 'Order Ship',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled'
                        ];
                        echo $statusLabels[$order['status']] ?? ucfirst($order['status']);
                    ?></span>
                </div>
                <div class="order-info-row total">
                    <span class="label">Total Amount</span>
                    <span class="value"><?php echo formatPrice($order['total_amount']); ?></span>
                </div>
            </div>
            
            <!-- Order Items Summary -->
            <div class="order-items-summary">
                <h3>Order Items</h3>
                <?php foreach ($orderItems as $item): ?>
                    <div class="order-item-row">
                        <div class="item-image">
                            <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                                <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <?php else: ?>
                                <div class="placeholder-image"><i class="fas fa-tshirt"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <span class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                            <span class="item-meta">Size: <?php echo $item['size']; ?> × <?php echo $item['quantity']; ?></span>
                        </div>
                        <span class="item-price"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Shipping Address -->
            <div class="shipping-address-card">
                <h3>Shipping Address</h3>
                <p class="address-text">
                    <strong><?php echo htmlspecialchars($order['full_name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['address']); ?><br>
                    <?php echo htmlspecialchars($order['city']); ?><?php if ($order['state']): ?>, <?php echo htmlspecialchars($order['state']); ?><?php endif; ?><?php if ($order['pincode']): ?> - <?php echo htmlspecialchars($order['pincode']); ?><?php endif; ?><br>
                    <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($order['phone']); ?>
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="products.php" class="btn btn-glow">Continue Shopping</a>
                <a href="orders.php" class="btn btn-dark-custom">View My Orders</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
