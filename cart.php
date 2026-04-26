<?php
// ============================================
// Aurex - Cart Page
// ============================================
$pageTitle = 'Cart';
require_once 'includes/header.php';

$cartItems = $_SESSION['cart'] ?? [];
$cartTotal = getCartTotal();
$shipping = 0; // Free shipping for all orders
$cartCount = getCartCount();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Shopping Cart</h1>
        <p><?php echo $cartCount; ?> item<?php echo $cartCount !== 1 ? 's' : ''; ?> in your cart</p>
        <div class="breadcrumb-custom">
            <a href="index.php">Home</a>
            <span>/</span>
            <span class="current">Cart</span>
        </div>
    </div>
</div>

<!-- Cart Section -->
<section class="cart-section">
    <div class="container">
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-bag"></i>
                <h3>Your Cart is Empty</h3>
                <p>Looks like you haven't added anything to your cart yet.</p>
                <a href="products.php" class="btn btn-glow">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <?php foreach ($cartItems as $key => $item): ?>
                        <?php $hasImage = !empty($item['image']) && file_exists($item['image']); ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <?php if ($hasImage): ?>
                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo clean($item['name']); ?>">
                                <?php else: ?>
                                    <div class="placeholder-img" style="border-radius: var(--radius-sm);"><i class="fas fa-tshirt" style="font-size:1.5rem;"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="cart-item-details">
                                <h4 class="cart-item-name"><?php echo clean($item['name']); ?></h4>
                                <p class="cart-item-meta">Size: <?php echo $item['size']; ?> | <?php echo $item['category']; ?></p>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <div class="quantity-selector" style="transform: scale(0.85); transform-origin: left;">
                                        <button class="qty-btn cart-qty-btn" data-key="<?php echo $key; ?>" data-action="decrease"><i class="fas fa-minus"></i></button>
                                        <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" min="1" readonly>
                                        <button class="qty-btn cart-qty-btn" data-key="<?php echo $key; ?>" data-action="increase"><i class="fas fa-plus"></i></button>
                                    </div>
                                    <span class="cart-item-price"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                                </div>
                            </div>
                            <button class="cart-item-remove" data-key="<?php echo $key; ?>">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4>Order Summary</h4>
                        <div class="summary-row">
                            <span class="label">Subtotal</span>
                            <span><?php echo formatPrice($cartTotal); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-row total">
                            <span class="label">Total</span>
                            <span><?php echo formatPrice($cartTotal); ?></span>
                        </div>

                        <?php if (isLoggedIn()): ?>
                            <a href="checkout.php" class="btn btn-glow w-100 mt-4">Proceed to Checkout</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-glow w-100 mt-4">Login to Checkout</a>
                        <?php endif; ?>

                        <a href="products.php" class="btn btn-dark-custom w-100 mt-2">Continue Shopping</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
