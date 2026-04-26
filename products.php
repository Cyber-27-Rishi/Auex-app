<?php
// ============================================
// Aurex - Product Listing Page
// ============================================
$pageTitle = 'Products';
require_once 'includes/header.php';

$category = isset($_GET['category']) ? clean($_GET['category']) : null;
$gender = isset($_GET['gender']) ? clean($_GET['gender']) : null;
$validCategories = ['T-Shirt', 'Sweatshirt', 'Hoodie'];
$validGenders = ['Men', 'Women'];

if ($category && !in_array($category, $validCategories)) {
    $category = null;
}
if ($gender && !in_array($gender, $validGenders)) {
    $gender = null;
}

$products = getProductsByCategory($pdo, $category, null, $gender);

// Build page title
$titleParts = [];
if ($gender) $titleParts[] = $gender . "'s";
if ($category) $titleParts[] = $category . 's';
$pageTitle = !empty($titleParts) ? implode(' ', $titleParts) : 'All Products';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1><?php echo $pageTitle; ?></h1>
        <p>Premium streetwear crafted with uncompromising quality</p>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a>
            <span>/</span>
            <span class="current"><?php echo $pageTitle; ?></span>
        </div>
    </div>
</div>

<!-- Gender & Category Filter -->
<section class="py-4 border-bottom border-custom" style="border-color: var(--border-color) !important;">
    <div class="container">
        <!-- Gender Filter -->
        <div class="d-flex gap-3 flex-wrap justify-content-center mb-3">
            <a href="products.php<?php echo $category ? '?category=' . $category : ''; ?>" class="btn <?php echo !$gender ? 'btn-glow' : 'btn-dark-custom'; ?> btn-sm">All</a>
            <?php foreach ($validGenders as $g): ?>
                <a href="products.php?gender=<?php echo $g; ?><?php echo $category ? '&category=' . $category : ''; ?>" class="btn <?php echo $gender === $g ? 'btn-glow' : 'btn-dark-custom'; ?> btn-sm"><?php echo $g; ?></a>
            <?php endforeach; ?>
        </div>
        <!-- Category Filter -->
        <div class="d-flex gap-3 flex-wrap justify-content-center">
            <?php foreach ($validCategories as $cat): ?>
                <a href="products.php?category=<?php echo $cat; ?><?php echo $gender ? '&gender=' . $gender : ''; ?>" class="btn <?php echo $category === $cat ? 'btn-glow' : 'btn-dark-custom'; ?> btn-sm"><?php echo $cat; ?>s</a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="products-section">
    <div class="container">
        <?php if (empty($products)): ?>
            <div class="empty-cart">
                <i class="fas fa-box-open"></i>
                <h3>No Products Found</h3>
                <p>Products in this category are coming soon.</p>
                <a href="products.php" class="btn btn-glow">Browse All Products</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php
                        $discountedPrice = getDiscountedPrice($product['price'], $product['discount']);
                        $hasImage = !empty($product['image']) && file_exists($product['image']);
                    ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($hasImage): ?>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo clean($product['name']); ?>">
                            <?php else: ?>
                                <div class="placeholder-img"><i class="fas fa-tshirt"></i></div>
                            <?php endif; ?>
                            <?php if ($product['discount'] > 0): ?>
                                <span class="discount-badge">-<?php echo $product['discount']; ?>%</span>
                            <?php endif; ?>
                            <div class="product-hover-overlay">
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-glow btn-sm">View Product</a>
                            </div>
                        </div>
                        <div class="product-info">
                            <p class="product-category-tag"><?php echo $product['category']; ?></p>
                            <h4 class="product-name"><?php echo clean($product['name']); ?></h4>
                            <div class="product-price">
                                <span class="price-current"><?php echo formatPrice($discountedPrice); ?></span>
                                <?php if ($product['discount'] > 0): ?>
                                    <span class="price-original"><?php echo formatPrice($product['price']); ?></span>
                                    <span class="price-discount">-<?php echo $product['discount']; ?>%</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
