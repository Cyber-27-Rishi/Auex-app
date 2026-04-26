<?php
// ============================================
// Aurex - Product Detail Page
// ============================================
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($pdo, $id);

if (!$product) {
    setFlash('error', 'Product not found');
    redirect('products.php');
}

$pageTitle = $product['name'];
$discountedPrice = getDiscountedPrice($product['price'], $product['discount']);
$hasImage = !empty($product['image']) && file_exists($product['image']);
$sizes = explode(',', $product['sizes_available']);
$relatedProducts = getRelatedProducts($pdo, $product['category'], $product['id'], 4);
?>

<!-- Product Detail -->
<section class="product-detail-section">
    <div class="container">
        <div class="row">
            <!-- Product Gallery -->
            <div class="col-lg-6">
                <div class="product-gallery">
                    <div class="main-image">
                        <?php if ($hasImage): ?>
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo clean($product['name']); ?>" id="mainProductImage">
                        <?php else: ?>
                            <div class="placeholder-img" style="aspect-ratio:1;"><i class="fas fa-tshirt"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="thumbnail-row">
                        <?php if ($hasImage): ?>
                            <div class="thumbnail active">
                                <img src="<?php echo $product['image']; ?>" alt="Thumbnail 1">
                            </div>
                            <div class="thumbnail">
                                <img src="<?php echo $product['image']; ?>" alt="Thumbnail 2">
                            </div>
                            <div class="thumbnail">
                                <img src="<?php echo $product['image']; ?>" alt="Thumbnail 3">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-detail-info">
                    <p class="detail-category"><?php echo $product['category']; ?></p>
                    <h1 class="detail-name"><?php echo clean($product['name']); ?></h1>

                    <div class="detail-rating">
                        <span class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </span>
                        <span class="rating-text">4.5 (128 reviews)</span>
                    </div>

                    <div class="detail-price">
                        <span class="current"><?php echo formatPrice($discountedPrice); ?></span>
                        <?php if ($product['discount'] > 0): ?>
                            <span class="original"><?php echo formatPrice($product['price']); ?></span>
                            <span class="save">You save <?php echo formatPrice($product['price'] - $discountedPrice); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Size Selection -->
                    <div class="size-section">
                        <p class="size-label">Select Size</p>
                        <div class="size-options">
                            <?php foreach ($sizes as $size): ?>
                                <button class="size-btn" data-size="<?php echo trim($size); ?>"><?php echo trim($size); ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="quantity-section">
                        <p class="quantity-label">Quantity</p>
                        <div class="quantity-selector">
                            <button class="qty-btn qty-minus"><i class="fas fa-minus"></i></button>
                            <input type="number" class="qty-input" value="1" min="1" max="10">
                            <button class="qty-btn qty-plus"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button class="btn btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-shopping-bag me-2"></i>Add to Cart
                        </button>
                        <a href="checkout.php?buy_now=<?php echo $product['id']; ?>" class="btn btn-buy-now">
                            <i class="fas fa-bolt me-2"></i>Buy Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Description & Features -->
<section class="product-description">
    <div class="container">
        <div class="desc-tabs">
            <button class="desc-tab active" data-tab="description">Description</button>
            <button class="desc-tab" data-tab="features">Features</button>
        </div>
        <div class="desc-pane active" id="description">
            <div class="desc-content">
                <p><?php echo nl2br(clean($product['description'])); ?></p>
            </div>
        </div>
        <div class="desc-pane" id="features" style="display:none;">
            <div class="desc-content">
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Premium heavyweight cotton for lasting comfort</li>
                    <li><i class="fas fa-check-circle"></i> Unique original design — not found anywhere else</li>
                    <li><i class="fas fa-check-circle"></i> Fast shipping — delivered within 5-7 business days</li>
                    <li><i class="fas fa-check-circle"></i> Pre-shrunk fabric with color-lock technology</li>
                    <li><i class="fas fa-check-circle"></i> Reinforced stitching for durability</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="related-section">
    <div class="container">
        <div class="section-header">
            <p class="section-label">You May Also Like</p>
            <h2 class="section-title">Related Products</h2>
            <div class="section-divider"></div>
        </div>
        <div class="product-grid">
            <?php foreach ($relatedProducts as $related): ?>
                <?php
                    $relPrice = getDiscountedPrice($related['price'], $related['discount']);
                    $relHasImage = !empty($related['image']) && file_exists($related['image']);
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($relHasImage): ?>
                            <img src="<?php echo $related['image']; ?>" alt="<?php echo clean($related['name']); ?>">
                        <?php else: ?>
                            <div class="placeholder-img"><i class="fas fa-tshirt"></i></div>
                        <?php endif; ?>
                        <?php if ($related['discount'] > 0): ?>
                            <span class="discount-badge">-<?php echo $related['discount']; ?>%</span>
                        <?php endif; ?>
                        <div class="product-hover-overlay">
                            <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-glow btn-sm">View Product</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <p class="product-category-tag"><?php echo $related['category']; ?></p>
                        <h4 class="product-name"><?php echo clean($related['name']); ?></h4>
                        <div class="product-price">
                            <span class="price-current"><?php echo formatPrice($relPrice); ?></span>
                            <?php if ($related['discount'] > 0): ?>
                                <span class="price-original"><?php echo formatPrice($related['price']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Description Tab & Buy Now Update JS -->
<script>
    $(document).ready(function() {
        // Handle tabs
        $('.desc-tab').on('click', function() {
            var target = $(this).data('tab');
            $('.desc-tab').removeClass('active');
            $(this).addClass('active');
            $('.desc-pane').hide().removeClass('active');
            $('#' + target).show().addClass('active');
        });

        // Handle Buy Now URL updates
        function updateBuyNowLink() {
            var size = $('.size-btn.active').data('size') || '';
            var qty  = $('.qty-input').val() || 1;
            var productId = '<?php echo $product['id']; ?>';
            var newHref = 'checkout.php?buy_now=' + productId + '&size=' + encodeURIComponent(size) + '&qty=' + qty;
            $('.btn-buy-now').attr('href', newHref);
        }

        // Listen for changes
        $(document).on('click', '.size-btn, .qty-btn', function() {
            setTimeout(updateBuyNowLink, 50); // Small delay to let other JS finish
        });
        $('.qty-input').on('change', updateBuyNowLink);

        // Run once on load to set default size/qty
        updateBuyNowLink();
    });
</script>

<?php require_once 'includes/footer.php'; ?>
