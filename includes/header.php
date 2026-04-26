<?php
// ============================================
// Aurex - Site Header
// ============================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | Aurex' : 'Aurex - Premium Streetwear'; ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Incompleeta:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <div class="loader">
            <span class="loader-text">Aurex</span>
            <div class="loader-bar"></div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <span class="brand-text">Aurex</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link <?php echo isset($_GET['gender']) && $_GET['gender'] == 'Men' ? 'active' : ''; ?>" href="products.php?gender=Men">Men</a>
                        <a class="nav-link dropdown-toggle-split" href="#" data-bs-toggle="dropdown" style="padding-left: 2px; padding-right: 8px; font-size: 0.7rem;"><i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu dropdown-dark-custom">
                            <li><a class="dropdown-item" href="products.php?gender=Men&category=T-Shirt">T-Shirts</a></li>
                            <li><a class="dropdown-item" href="products.php?gender=Men&category=Sweatshirt">Sweatshirts</a></li>
                            <li><a class="dropdown-item" href="products.php?gender=Men&category=Hoodie">Hoodies</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link <?php echo isset($_GET['gender']) && $_GET['gender'] == 'Women' ? 'active' : ''; ?>" href="products.php?gender=Women">Women</a>
                        <a class="nav-link dropdown-toggle-split" href="#" data-bs-toggle="dropdown" style="padding-left: 2px; padding-right: 8px; font-size: 0.7rem;"><i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu dropdown-dark-custom">
                            <li><a class="dropdown-item" href="products.php?gender=Women&category=T-Shirt">T-Shirts</a></li>
                            <li><a class="dropdown-item" href="products.php?gender=Women&category=Sweatshirt">Sweatshirts</a></li>
                            <li><a class="dropdown-item" href="products.php?gender=Women&category=Hoodie">Hoodies</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="nav-right d-flex align-items-center gap-3">
                    <?php if (isLoggedIn()): ?>
                        <a href="cart.php" class="nav-icon position-relative">
                            <i class="fas fa-shopping-bag"></i>
                            <?php if ($cartCount > 0): ?>
                                <span class="cart-badge"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown">
                            <a href="#" class="nav-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-dark-custom">
                                <li><span class="dropdown-item-text text-muted"><?php echo clean($_SESSION['user_name']); ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-box me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light btn-sm nav-btn">Login</a>
                        <a href="register.php" class="btn btn-glow btn-sm nav-btn">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
