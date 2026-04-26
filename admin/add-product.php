<?php
// ============================================
// Aurex - Admin Add Product
// ============================================
require_once 'admin-auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $gender = trim($_POST['gender'] ?? 'Men');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $discount = floatval($_POST['discount'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $sizes = trim($_POST['sizes_available'] ?? 'S,M,L,XL,XXL');
    $stock = (int)($_POST['stock'] ?? 100);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    // Validation
    if (empty($name) || empty($category) || $price <= 0) {
        $error = 'Name, category, and price are required';
    } else {
        $imagePath = '';

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = 'Invalid image format. Allowed: jpg, jpeg, png, webp';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error = 'Image too large. Max: 5MB';
            } else {
                $newName = uniqid('auex_') . '.' . $ext;
                $targetPath = UPLOAD_PATH . $newName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = 'uploads/' . $newName;
                }
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO products (name, gender, category, price, discount, description, image, sizes_available, stock, is_active) VALUES (:name, :gender, :category, :price, :discount, :description, :image, :sizes, :stock, :is_active)");
            $stmt->execute([
                ':name' => $name,
                ':gender' => $gender,
                ':category' => $category,
                ':price' => $price,
                ':discount' => $discount,
                ':description' => $description,
                ':image' => $imagePath,
                ':sizes' => $sizes,
                ':stock' => $stock,
                ':is_active' => $isActive
            ]);

            header('Location: products.php?msg=added');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Aurex Admin</title>
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
            <h1>Add Product</h1>
        </div>
        <a href="products.php" class="btn btn-dark-custom btn-sm"><i class="fas fa-arrow-left me-2"></i>Back to Products</a>
    </div>

    <?php if ($error): ?>
        <div class="flash-message flash-error" style="position:static; animation:none; margin-bottom: 20px;"><?php echo $error; ?></div>
    <?php endif; ?>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 30px; max-width: 700px;">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted);">Product Name *</label>
                <input type="text" name="name" class="form-input" placeholder="Enter product name" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted);">Gender *</label>
                    <select name="gender" class="form-input" required>
                        <option value="Men">Men</option>
                        <option value="Women">Women</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted);">Category *</label>
                    <select name="category" class="form-input" required>
                        <option value="">Select Category</option>
                        <option value="T-Shirt">T-Shirt</option>
                        <option value="Sweatshirt">Sweatshirt</option>
                        <option value="Hoodie">Hoodie</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted);">Price (₹) *</label>
                    <input type="number" name="price" class="form-input" placeholder="0.00" step="0.01" min="0" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted);">Discount (%)</label>
                    <input type="number" name="discount" class="form-input" placeholder="0" min="0" max="100" value="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted);">Stock</label>
                    <input type="number" name="stock" class="form-input" placeholder="100" min="0" value="100">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted);">Sizes Available</label>
                <input type="text" name="sizes_available" class="form-input" placeholder="S,M,L,XL,XXL" value="S,M,L,XL,XXL">
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted);">Description</label>
                <textarea name="description" class="form-input" rows="4" placeholder="Enter product description" style="height: auto; padding: 12px 16px;"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--text-muted);">Product Image</label>
                <input type="file" name="image" id="productImage" class="form-input" accept="image/*" style="padding: 10px 16px;">
                <img id="imagePreview" class="image-preview" alt="Preview">
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked style="accent-color: var(--accent);">
                    <label class="form-check-label" for="isActive" style="color: var(--text-secondary); font-size: 0.9rem;">Active (visible on store)</label>
                </div>
            </div>

            <button type="submit" class="btn btn-glow"><i class="fas fa-plus me-2"></i>Add Product</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
