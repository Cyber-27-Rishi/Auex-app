<?php
// ============================================
// Aurex - Admin Products List
// ============================================
require_once 'admin-auth.php';

$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute([':id' => $deleteId]);
    $product = $stmt->fetch();

    if ($product && !empty($product['image']) && file_exists('../' . $product['image'])) {
        unlink('../' . $product['image']);
    }

    $deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $deleteStmt->execute([':id' => $deleteId]);

    header('Location: products.php?msg=deleted');
    exit;
}

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Aurex Admin</title>
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
            <h1>Products</h1>
        </div>
        <a href="add-product.php" class="btn btn-glow btn-sm"><i class="fas fa-plus me-2"></i>Add Product</a>
    </div>

    <?php if ($msg === 'deleted'): ?>
        <div class="flash-message flash-success" style="position:static; animation:none; margin-bottom: 20px;">Product deleted successfully</div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="flash-message flash-success" style="position:static; animation:none; margin-bottom: 20px;">Product updated successfully</div>
    <?php elseif ($msg === 'added'): ?>
        <div class="flash-message flash-success" style="position:static; animation:none; margin-bottom: 20px;">Product added successfully</div>
    <?php endif; ?>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 24px;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">No products yet. <a href="add-product.php" style="color: var(--accent);">Add one</a></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($product['image']) && file_exists('../' . $product['image'])): ?>
                                        <img src="<?php echo '../' . $product['image']; ?>" class="product-thumb" alt="">
                                    <?php else: ?>
                                        <div style="width:50px;height:50px;border-radius:var(--radius-sm);background:var(--bg-elevated);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"><i class="fas fa-tshirt"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo isset($product['gender']) ? $product['gender'] : 'Men'; ?></td>
                                <td><?php echo $product['category']; ?></td>
                                <td style="font-weight: 600; color: var(--accent);">₹<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['discount'] > 0 ? '-' . $product['discount'] . '%' : '-'; ?></td>
                                <td>
                                    <span class="status-badge <?php echo $product['is_active'] ? 'status-delivered' : 'status-cancelled'; ?>">
                                        <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-dark-custom btn-sm" style="padding: 4px 12px; font-size: 0.75rem;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-dark-custom btn-sm" style="padding: 4px 12px; font-size: 0.75rem; color: var(--danger);" onclick="return confirm('Delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
