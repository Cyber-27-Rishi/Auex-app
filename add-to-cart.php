<?php
// ============================================
// Aurex - Add to Cart Handler
// ============================================
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$productId = (int)($_POST['product_id'] ?? 0);
$size = clean($_POST['size'] ?? 'M');
$quantity = (int)($_POST['quantity'] ?? 1);

if ($productId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product']);
    exit;
}

// Get product from database
$product = getProductById($pdo, $productId);
if (!$product) {
    echo json_encode(['success' => false, 'error' => 'Product not found']);
    exit;
}

// Check if size is valid
$sizes = explode(',', $product['sizes_available']);
if (!in_array($size, $sizes)) {
    $size = trim($sizes[0]); // Default to first size
}

// Create unique cart key (product_id + size)
$cartKey = $productId . '_' . $size;

// Add to session cart
if (isset($_SESSION['cart'][$cartKey])) {
    $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
} else {
    $discountedPrice = getDiscountedPrice($product['price'], $product['discount']);
    $_SESSION['cart'][$cartKey] = [
        'product_id' => $productId,
        'name' => $product['name'],
        'price' => $discountedPrice,
        'original_price' => $product['price'],
        'discount' => $product['discount'],
        'image' => $product['image'],
        'size' => $size,
        'quantity' => $quantity,
        'category' => $product['category']
    ];
}

$cartCount = getCartCount();

echo json_encode([
    'success' => true,
    'message' => $product['name'] . ' added to cart',
    'cart_count' => $cartCount
]);
