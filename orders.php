<?php
// ============================================
// Aurex - Orders Page
// ============================================
$pageTitle = 'My Orders';
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>My Orders</h1>
        <p>Track your orders and delivery status</p>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a>
            <span>/</span>
            <span class="current">My Orders</span>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (hasFlash('success')): ?>
    <div class="flash-message flash-success">
        <?php echo getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php if (hasFlash('error')): ?>
    <div class="flash-message flash-error">
        <?php echo getFlash('error'); ?>
    </div>
<?php endif; ?>

<!-- Orders Section -->
<section class="orders-section">
    <div class="container">
        <?php if (empty($orders)): ?>
            <div class="empty-cart">
                <i class="fas fa-box-open"></i>
                <h3>No Orders Yet</h3>
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="btn btn-glow">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <?php
                    // Get order items
                    $itemStmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :order_id");
                    $itemStmt->execute([':order_id' => $order['id']]);
                    $orderItems = $itemStmt->fetchAll();
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">Order #<?php echo $order['id']; ?></span>
                            <span style="color: var(--text-muted); font-size: 0.85rem; margin-left: 12px;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <span class="order-status status-<?php echo $order['status']; ?>"><?php
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
                    <?php foreach ($orderItems as $item): ?>
                        <div class="d-flex gap-3 align-items-center mb-2">
                            <div style="width: 50px; height: 50px; border-radius: var(--radius-sm); overflow: hidden; flex-shrink: 0; background: var(--bg-elevated);">
                                <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                                    <img src="<?php echo $item['image']; ?>" style="width:100%;height:100%;object-fit:cover;">
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <p style="font-size: 0.9rem; font-weight: 600; margin: 0;"><?php echo clean($item['product_name']); ?></p>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 2px 0 0;">Size: <?php echo $item['size']; ?> × <?php echo $item['quantity']; ?></p>
                            </div>
                            <span style="font-weight: 600; color: var(--accent);"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between mt-3 pt-3" style="border-top: 1px solid var(--border-color);">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Total</span>
                        <span style="font-weight: 700; color: var(--accent); font-size: 1.1rem;"><?php echo formatPrice($order['total_amount']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
