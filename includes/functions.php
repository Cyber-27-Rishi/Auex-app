<?php
// ============================================
// Aurex - Helper Functions
// ============================================

/**
 * Sanitize input string
 */
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Calculate discounted price
 */
function getDiscountedPrice($price, $discount) {
    return $price - ($price * $discount / 100);
}

/**
 * Format price with currency symbol
 */
function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

/**
 * Get cart item count
 */
function getCartCount() {
    return isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
}

/**
 * Get cart total
 */
function getCartTotal() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) return 0;
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Flash message system
 */
function setFlash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlash($type) {
    if (isset($_SESSION['flash'][$type])) {
        $msg = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $msg;
    }
    return null;
}

function hasFlash($type) {
    return isset($_SESSION['flash'][$type]);
}

/**
 * Upload image with validation
 */
function uploadImage($file, $targetDir = null) {
    if ($targetDir === null) {
        $targetDir = UPLOAD_PATH;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    $fileName = $file['name'];
    $fileSize = $file['size'];
    $tmpName = $file['tmp_name'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate extension
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: jpg, jpeg, png, webp'];
    }

    // Validate size
    if ($fileSize > $maxSize) {
        return ['success' => false, 'error' => 'File too large. Max: 5MB'];
    }

    // Generate unique filename
    $newName = uniqid('auex_') . '.' . $ext;
    $targetPath = $targetDir . $newName;

    if (move_uploaded_file($tmpName, $targetPath)) {
        return ['success' => true, 'filename' => $newName, 'path' => 'uploads/' . $newName];
    }

    return ['success' => false, 'error' => 'Failed to upload image'];
}

/**
 * Get products by category
 */
function getProductsByCategory($pdo, $category = null, $limit = null, $gender = null) {
    $sql = "SELECT * FROM products WHERE is_active = 1";
    $params = [];

    if ($category) {
        $sql .= " AND category = :category";
        $params[':category'] = $category;
    }

    if ($gender) {
        $sql .= " AND gender = :gender";
        $params[':gender'] = $gender;
    }

    $sql .= " ORDER BY created_at DESC";

    if ($limit) {
        $sql .= " LIMIT :limit";
    }

    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    if ($limit) {
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get single product by ID
 */
function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND is_active = 1");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Get related products
 */
function getRelatedProducts($pdo, $category, $excludeId, $limit = 4) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = :category AND id != :id AND is_active = 1 ORDER BY RAND() LIMIT :limit");
    $stmt->bindValue(':category', $category);
    $stmt->bindValue(':id', $excludeId);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
