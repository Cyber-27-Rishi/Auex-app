<?php
// ============================================
// Aurex - Admin Orders
// ============================================
require_once 'admin-auth.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $newStatus = trim($_POST['status'] ?? '');

    $validStatuses = ['pending', 'processing', 'shipped', 'order_confirm', 'order_ship', 'delivered', 'cancelled'];
    if ($orderId > 0 && in_array($newStatus, $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $newStatus, ':id' => $orderId]);
        header('Location: orders.php?msg=updated');
        exit;
    }
}

$orders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Aurex Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Incompleeta:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-dark-custom btn-sm d-lg-none me-2" id="adminToggle"><i class="fas fa-bars"></i></button>
            <h1>Orders</h1>
        </div>
    </div>

    <?php if ($msg === 'updated'): ?>
        <div class="flash-message flash-success" style="position:static; animation:none; margin-bottom: 20px;">Order status updated</div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 60px; text-align: center;">
            <i class="fas fa-box-open" style="font-size: 3rem; color: var(--border-light); margin-bottom: 16px;"></i>
            <h4 style="margin-bottom: 8px;">No Orders Yet</h4>
            <p style="color: var(--text-muted);">Orders will appear here when customers make purchases.</p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
                $itemStmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :order_id");
                $itemStmt->execute([':order_id' => $order['id']]);
                $orderItems = $itemStmt->fetchAll();
            ?>
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 24px; margin-bottom: 16px;">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                    <div>
                        <span style="color: var(--accent); font-family: var(--font-heading); font-weight: 700;">Order #<?php echo $order['id']; ?></span>
                        <span style="color: var(--text-muted); font-size: 0.85rem; margin-left: 12px;"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                        <div style="margin-top: 6px; font-size: 0.85rem; color: var(--text-muted);">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($order['full_name']); ?>
                            <span class="ms-3"><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($order['phone']); ?></span>
                        </div>
                        <div style="margin-top: 4px; font-size: 0.85rem; color: var(--text-muted);">
                            <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($order['address'] . ', ' . $order['city'] . ($order['state'] ? ', ' . $order['state'] : '') . ($order['pincode'] ? ' - ' . $order['pincode'] : '')); ?>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="status-badge status-<?php echo $order['status']; ?>" style="font-size: 0.8rem;"><?php
                            $statusLabels = [
                                'pending' => 'Pending',
                                'order_confirm' => 'Order Confirm',
                                'order_ship' => 'Order Ship',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled'
                            ];
                            echo $statusLabels[$order['status']] ?? ucfirst($order['status']);
                        ?></span>
                        <div style="font-weight: 700; color: var(--accent); font-size: 1.2rem; margin-top: 8px;">₹<?php echo number_format($order['total_amount'], 2); ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;"><?php echo $order['payment_method']; ?></div>
                    </div>
                </div>

                <!-- Order Items -->
                <?php foreach ($orderItems as $item): ?>
                    <div class="d-flex gap-3 align-items-center mb-2">
                        <div style="width: 45px; height: 45px; border-radius: var(--radius-sm); overflow: hidden; flex-shrink: 0; background: var(--bg-elevated);">
                            <?php if (!empty($item['image']) && file_exists('../' . $item['image'])): ?>
                                <img src="<?php echo '../' . $item['image']; ?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php endif; ?>
                        </div>
                        <div style="flex:1;">
                            <span style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($item['product_name']); ?></span>
                            <span style="color: var(--text-muted); font-size: 0.8rem; margin-left: 8px;">Size: <?php echo $item['size']; ?> × <?php echo $item['quantity']; ?></span>
                        </div>
                        <span style="font-weight: 600; color: var(--accent); font-size: 0.9rem;">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>

                <!-- Status Update -->
                <div class="mt-3 pt-3" style="border-top: 1px solid var(--border-color);">
                    <form method="POST" class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <input type="hidden" name="update_status" value="1">
                        <label style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Update Status:</label>
                        <select name="status" class="form-input" style="width: auto; height: 36px; font-size: 0.85rem; padding: 0 12px;">
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="order_confirm" <?php echo $order['status'] === 'order_confirm' ? 'selected' : ''; ?>>Order Confirm</option>
                            <option value="order_ship" <?php echo $order['status'] === 'order_ship' ? 'selected' : ''; ?>>Order Ship</option>
                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-glow btn-sm" style="padding: 4px 16px; font-size: 0.75rem;">Update</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
