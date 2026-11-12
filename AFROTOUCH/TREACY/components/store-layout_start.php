<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfroTouch - Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="store-style.css">
</head>
<body class="storefront">

<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$user_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? '';
?>

<header class="store-header" id="storeHeader">
    <div class="header-left">
        <button class="mobile-menu-btn" id="menuToggle">
            <i data-lucide="menu" size="28"></i>
        </button>
        <a href="home.php" class="store-logo">AFRO <span>TOUCH</span></a>
    </div>
    
    <nav class="main-nav" id="mainNav">
        <a href="home.php" class="nav-link">Home</a>
        <a href="products-store.php" class="nav-link">Categories</a>
        <a href="new-arrivals.php" class="nav-link">New Arrivals</a>
        <a href="best-sellers.php" class="nav-link">Best Sellers</a>
        <a href="verified-artisans.php" class="nav-link">Artisans</a>
        <a href="sell.php" class="nav-link">Sell</a>
    </nav>
    
    <div class="header-right">
        <a href="cart.php" class="cart-icon">
            <i data-lucide="shopping-cart" size="24"></i>
            <?php if ($cart_count > 0): ?>
            <span class="cart-badge"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
        
        <?php if ($user_logged_in): ?>
        <div class="user-menu">
            <button class="user-btn" id="userMenuBtn">
                <i data-lucide="user" size="24"></i>
            </button>
            <div class="user-dropdown" id="userDropdown">
                <?php if ($user_role === 'seller'): ?>
                <a href="dashboard.php">Seller Dashboard</a>
                <a href="my-shop.php">My Shop</a>
                <?php elseif ($user_role === 'buyer'): ?>
                <a href="buyer-dashboard.php">My Dashboard</a>
                <a href="buyer-orders.php">My Orders</a>
                <?php elseif ($user_role === 'admin'): ?>
                <a href="admin-dashboard.php">Admin Dashboard</a>
                <?php endif; ?>
                <a href="buyer-settings.php">Settings</a>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </div>
        <?php else: ?>
        <a href="register.php" class="btn-signup">Sign Up</a>
        <a href="login.php" class="btn-login">Sign In</a>
        <?php endif; ?>
    </div>
</header>

<main class="store-content">

<style>
.store-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    background: white;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.store-logo {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-dark);
    text-decoration: none;
}

.store-logo span {
    color: var(--accent-orange);
}

.main-nav {
    display: flex;
    gap: 2rem;
}

.nav-link {
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: color 0.2s;
    position: relative;
}

.nav-link:hover {
    color: var(--accent-orange);
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--accent-orange);
    transition: width 0.2s;
}

.nav-link:hover::after {
    width: 100%;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.mobile-menu-btn {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
}

.btn-login {
    background: #D35400 !important;
    color: white !important;
    padding: 0.6rem 1.5rem;
    border-radius: 100px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: transform 0.2s;
    display: inline-block;
}

.btn-login:hover {
    transform: scale(1.05);
    background: #b84700 !important;
}

.btn-signup {
    background: transparent !important;
    color: #D35400 !important;
    padding: 0.6rem 1.5rem;
    border-radius: 100px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    border: 2px solid #D35400;
    transition: all 0.2s;
    display: inline-block;
    margin-right: 0.5rem;
}

.btn-signup:hover {
    background: #D35400 !important;
    color: white !important;
}

.user-menu {
    position: relative;
}

.user-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    color: var(--text-dark);
}

.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    min-width: 180px;
    padding: 0.5rem;
    display: none;
    z-index: 100;
}

.user-dropdown.show {
    display: block;
}

.user-dropdown a {
    display: block;
    padding: 0.75rem 1rem;
    color: var(--text-dark);
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.user-dropdown a:hover {
    background: #f5f5f5;
}

.user-dropdown .logout {
    color: #C62828;
    border-top: 1px solid var(--border-color);
    margin-top: 0.5rem;
    padding-top: 0.75rem;
}

.cart-icon {
    position: relative;
    cursor: pointer;
    text-decoration: none;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    padding: 0.5rem;
}

.cart-icon:hover {
    color: var(--accent-orange);
}

.cart-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: var(--accent-orange);
    color: white;
    font-size: 0.65rem;
    padding: 2px 6px;
    border-radius: 100px;
    font-weight: 700;
}

@media (max-width: 1024px) {
    .main-nav {
        position: fixed;
        top: 70px;
        left: 0;
        width: 100%;
        background: white;
        flex-direction: column;
        padding: 1rem 2rem;
        gap: 1rem;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        transform: translateY(-150%);
        transition: transform 0.3s ease;
        z-index: 999;
    }
    
    .main-nav.open {
        transform: translateY(0);
    }
    
    .mobile-menu-btn {
        display: block;
    }
    
    .nav-link::after {
        display: none;
    }
}

@media (max-width: 576px) {
    .store-header {
        padding: 1rem;
    }
    
    .store-logo {
        font-size: 1.25rem;
    }
    
    .btn-login {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('open');
        });
    }
    
    // User menu toggle
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
        
        document.addEventListener('click', function() {
            userDropdown.classList.remove('show');
        });
    }
    
    // Sticky header effect
    const header = document.getElementById('storeHeader');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 20) {
            header.style.boxShadow = '0 10px 30px rgba(0,0,0,0.08)';
        } else {
            header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.05)';
        }
    });
});
</script>
