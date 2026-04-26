<?php
// ============================================
// Aurex - Admin Sidebar
// ============================================
$currentAdminPage = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-sidebar" id="adminSidebar">
    <div class="brand">Aurex</div>
    <div class="brand-sub">Admin Panel</div>
    <ul class="admin-nav">
        <li>
            <a href="dashboard.php" class="<?php echo $currentAdminPage === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="products.php" class="<?php echo $currentAdminPage === 'products.php' ? 'active' : ''; ?>">
                <i class="fas fa-tshirt"></i> Products
            </a>
        </li>
        <li>
            <a href="add-product.php" class="<?php echo $currentAdminPage === 'add-product.php' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i> Add Product
            </a>
        </li>
        <li>
            <a href="orders.php" class="<?php echo $currentAdminPage === 'orders.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag"></i> Orders
            </a>
        </li>
        <li>
            <a href="../index.php">
                <i class="fas fa-external-link-alt"></i> View Store
            </a>
        </li>
        <li>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>
