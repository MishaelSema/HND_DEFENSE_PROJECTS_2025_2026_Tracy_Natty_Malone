<header class="main-header">
    <div class="logo">
        <a href="/AFROTOUCH/public/">
            <span class="text-white">AFRO</span><span class="text-yellow">TOUCH</span>
        </a>
    </div>
    
    <nav class="main-nav" id="mainNav">
        <a href="/AFROTOUCH/public/" class="nav-link">Home</a>
        
        <div class="nav-dropdown">
            <button class="nav-dropbtn">Shop <i data-lucide="chevron-down"></i></button>
            <div class="nav-dropdown-content">
                <a href="/AFROTOUCH/public/products">All Products</a>
                <a href="/AFROTOUCH/public/products?sort=new">New Arrivals</a>
                <a href="/AFROTOUCH/public/products?sort=bestsellers">Best Sellers</a>
                <a href="/AFROTOUCH/public/shops">Verified Artisans</a>
            </div>
        </div>
        
        <div class="nav-dropdown">
            <button class="nav-dropbtn">Sell <i data-lucide="chevron-down"></i></button>
            <div class="nav-dropdown-content">
                <a href="/AFROTOUCH/public/register">Become a Seller</a>
                <a href="/AFROTOUCH/public/seller-guide">Seller Guide</a>
                <a href="/AFROTOUCH/public/pricing">Pricing Plans</a>
                <a href="/AFROTOUCH/public/verification">Verification</a>
            </div>
        </div>
        
        <div class="nav-dropdown">
            <button class="nav-dropbtn">Support <i data-lucide="chevron-down"></i></button>
            <div class="nav-dropdown-content">
                <a href="/AFROTOUCH/public/help">Help Center</a>
                <a href="/AFROTOUCH/public/contact">Contact Us</a>
                <a href="/AFROTOUCH/public/faq">FAQ</a>
                <a href="/AFROTOUCH/public/shipping">Shipping Info</a>
            </div>
        </div>
        
        <a href="/AFROTOUCH/public/shops" class="nav-link">Artisans</a>
    </nav>
    
    <div class="header-actions">
        <a href="/AFROTOUCH/public/cart" class="cart-icon">
            <i data-lucide="shopping-cart"></i>
            <span class="cart-count" id="cartCount">0</span>
        </a>
        
        <?php 
        $user = null;
        $isLoggedIn = \App\Services\Auth::check();
        if ($isLoggedIn) {
            try {
                $user = \App\Services\Auth::user();
            } catch (Exception $e) {
                $isLoggedIn = false;
            }
        }
        ?>
        
        <?php if ($isLoggedIn && $user): ?>
            <?php if ($user->role === 'seller'): ?>
                <a href="/AFROTOUCH/public/dashboard" class="btn-dashboard">Seller Dashboard</a>
            <?php elseif ($user->role === 'admin'): ?>
                <a href="/AFROTOUCH/public/admin" class="btn-dashboard">Admin Panel</a>
            <?php else: ?>
                <a href="/AFROTOUCH/public/orders" class="btn-login">My Orders</a>
            <?php endif; ?>
            <a href="/AFROTOUCH/public/logout" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="/AFROTOUCH/public/login" class="btn-login">Login</a>
            <a href="/AFROTOUCH/public/register?role=seller" class="btn-primary-small">Start Selling</a>
        <?php endif; ?>
        
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i data-lucide="menu"></i>
        </button>
    </div>
</header>

<style>
.main-header {
    background: #241611;
    padding: 12px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo a {
    font-family: 'Instrument Serif', serif;
    font-size: 1.5rem;
    font-weight: bold;
}

.text-white { color: #fff; }
.text-yellow { color: #F1C40F; }

.main-nav {
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-link {
    color: rgba(255,255,255,0.85);
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.2s;
    font-size: 14px;
}

.nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}

.nav-dropdown {
    position: relative;
}

.nav-dropbtn {
    background: none;
    border: none;
    color: rgba(255,255,255,0.85);
    padding: 8px 16px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 4px;
    font-family: inherit;
}

.nav-dropbtn:hover {
    color: #fff;
}

.nav-dropdown-content {
    display: none;
    position: absolute;
    background: white;
    min-width: 180px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    overflow: hidden;
    top: 100%;
    left: 0;
    z-index: 1001;
}

.nav-dropdown:hover .nav-dropdown-content {
    display: block;
}

.nav-dropdown-content a {
    color: #333;
    padding: 12px 20px;
    display: block;
    font-size: 14px;
    transition: background 0.2s;
}

.nav-dropdown-content a:hover {
    background: #FAF7F5;
    color: #D35400;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.cart-icon {
    position: relative;
    color: white;
    padding: 8px;
}

.cart-count {
    position: absolute;
    top: 0;
    right: 0;
    background: #D35400;
    color: white;
    font-size: 10px;
    font-weight: bold;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-primary-small {
    background: #D35400;
    color: white;
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.2s;
}

.btn-primary-small:hover {
    background: #b94700;
}

.btn-dashboard {
    background: #F1C40F;
    color: #241611;
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 13px;
}

.btn-login {
    color: rgba(255,255,255,0.85);
    font-size: 14px;
}

.btn-logout {
    color: #ff6b6b;
    font-size: 14px;
}

.mobile-menu-btn {
    display: none;
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 8px;
}

@media (max-width: 900px) {
    .main-nav {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #241611;
        flex-direction: column;
        padding: 20px;
        gap: 10px;
    }
    
    .main-nav.active {
        display: flex;
    }
    
    .nav-dropdown-content {
        position: static;
        box-shadow: none;
        background: rgba(255,255,255,0.05);
    }
    
    .nav-dropdown-content a {
        color: rgba(255,255,255,0.8);
    }
    
    .mobile-menu-btn {
        display: block;
    }
}
</style>

<script>
function toggleMobileMenu() {
    document.getElementById('mainNav').classList.toggle('active');
}

function updateCartCount() {
    const cart = <?= json_encode($_SESSION['cart'] ?? []) ?>;
    let count = 0;
    for (let key in cart) {
        count += cart[key];
    }
    document.getElementById('cartCount').textContent = count;
}
updateCartCount();
</script>
