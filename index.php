<?php
// ============================================
// Aurex - Home Page
// ============================================
$pageTitle = 'Home';
require_once 'includes/header.php';

// Get featured products
$featuredProducts = getProductsByCategory($pdo, null, 6);
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-bg"></div>
    <div class="hero-bg-mobile" style="background: linear-gradient(135deg, rgba(10,10,10,0.7) 0%, rgba(10,10,10,0.4) 50%, rgba(10,10,10,0.8) 100%), url('https://res.cloudinary.com/dwygc7urf/image/upload/v1777043710/WhatsApp_Image_2026-04-24_at_8.40.18_PM_lnzpr6.jpg') center/cover no-repeat;"></div>
    <div class="hero-content">
        <div class="hero-cta">
            <a href="products.php" class="btn btn-glow btn-lg">Shop Now <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<!-- Feature Highlights -->
<section class="features-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h5>Premium Quality</h5>
                    <p>240+ GSM heavyweight cotton crafted for durability and comfort</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h5>Unique Designs</h5>
                    <p>Original artwork and graphics you won't find anywhere else</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h5>Limited Edition</h5>
                    <p>Small batch drops that sell out — once it's gone, it's gone</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Men's Section -->
<section class="category-section">
    <div class="container">
        <div class="section-header">
            <p class="section-label">Men's Collection</p>
            <h2 class="section-title">Shop Men</h2>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <a href="products.php?gender=Men&category=T-Shirt" class="category-card">
                    <img src="https://res.cloudinary.com/dwygc7urf/image/upload/v1777011017/T_shirt_brorot.jpg" alt="Men's T-Shirts">
                    <div class="category-overlay">
                        <h3 class="category-name">T-Shirts</h3>
                        <p class="category-count">Premium Oversized Tees</p>
                    </div>
                    <div class="category-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="products.php?gender=Men&category=Sweatshirt" class="category-card">
                    <img src="https://res.cloudinary.com/dwygc7urf/image/upload/v1777011062/hoodie_njqnmy.jpg" alt="Men's Sweatshirts">
                    <div class="category-overlay">
                        <h3 class="category-name">Sweatshirts</h3>
                        <p class="category-count">Heavyweight Crewnecks</p>
                    </div>
                    <div class="category-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="products.php?gender=Men&category=Hoodie" class="category-card">
                    <img src="https://res.cloudinary.com/dwygc7urf/image/upload/v1777011082/Sweatshirt_glnngl.jpg" alt="Men's Hoodies">
                    <div class="category-overlay">
                        <h3 class="category-name">Hoodies</h3>
                        <p class="category-count">Premium Fleece Hoodies</p>
                    </div>
                    <div class="category-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Women's Section -->
<section class="category-section" style="padding-top: 40px;">
    <div class="container">
        <div class="section-header">
            <p class="section-label">Women's Collection</p>
            <h2 class="section-title">Shop Women</h2>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <a href="products.php?gender=Women&category=T-Shirt" class="category-card">
                    <img src="https://res.cloudinary.com/dwygc7urf/image/upload/v1777011017/T_shirt_brorot.jpg" alt="Women's T-Shirts">
                    <div class="category-overlay">
                        <h3 class="category-name">T-Shirts</h3>
                        <p class="category-count">Premium Oversized Tees</p>
                    </div>
                    <div class="category-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="products.php?gender=Women&category=Sweatshirt" class="category-card">
                    <img src="https://res.cloudinary.com/dwygc7urf/image/upload/v1777011062/hoodie_njqnmy.jpg" alt="Women's Sweatshirts">
                    <div class="category-overlay">
                        <h3 class="category-name">Sweatshirts</h3>
                        <p class="category-count">Heavyweight Crewnecks</p>
                    </div>
                    <div class="category-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="products.php?gender=Women&category=Hoodie" class="category-card">
                    <img src="https://res.cloudinary.com/dwygc7urf/image/upload/v1777011082/Sweatshirt_glnngl.jpg" alt="Women's Hoodies">
                    <div class="category-overlay">
                        <h3 class="category-name">Hoodies</h3>
                        <p class="category-count">Premium Fleece Hoodies</p>
                    </div>
                    <div class="category-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <p class="section-label">New Arrivals</p>
            <h2 class="section-title">Featured Drops</h2>
            <div class="section-divider"></div>
        </div>
        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
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
        <div class="text-center mt-5">
            <a href="products.php" class="btn btn-outline-light">View All Products <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
