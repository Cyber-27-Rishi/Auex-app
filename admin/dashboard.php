<?php
// ============================================
// Aurex - Admin Dashboard
// ============================================
require_once 'admin-auth.php';

// Stats
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$revenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

// Recent orders
$recentOrders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Aurex Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Incompleeta:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Content -->
<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-dark-custom btn-sm d-lg-none me-2" id="adminToggle"><i class="fas fa-bars"></i></button>
            <h1>Dashboard</h1>
        </div>
        <span style="color: var(--text-muted); font-size: 0.9rem;"><?php echo date('F j, Y'); ?></span>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 24px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase; margin: 0;">Products</p>
                        <h3 style="font-family: var(--font-heading); font-weight: 800; margin: 8px 0 0;"><?php echo $productCount; ?></h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--bg-elevated); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--accent);"><i class="fas fa-tshirt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 24px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase; margin: 0;">Orders</p>
                        <h3 style="font-family: var(--font-heading); font-weight: 800; margin: 8px 0 0;"><?php echo $orderCount; ?></h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--bg-elevated); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--accent);"><i class="fas fa-shopping-bag"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 24px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase; margin: 0;">Revenue</p>
                        <h3 style="font-family: var(--font-heading); font-weight: 800; margin: 8px 0 0;">₹<?php echo number_format($revenue, 0); ?></h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--bg-elevated); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--accent);"><i class="fas fa-rupee-sign"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 24px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p style="color: var(--text-muted); font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase; margin: 0;">Pending</p>
                        <h3 style="font-family: var(--font-heading); font-weight: 800; margin: 8px 0 0;"><?php echo $pendingOrders; ?></h3>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--bg-elevated); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--warning);"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 24px;">
        <h4 style="font-family: var(--font-heading); font-weight: 700; letter-spacing: 1px; margin-bottom: 20px;">Recent Orders</h4>
        <?php if (empty($recentOrders)): ?>
            <p style="color: var(--text-muted);">No orders yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td style="color: var(--accent); font-weight: 600;">#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td style="font-weight: 600;">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                <td style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
