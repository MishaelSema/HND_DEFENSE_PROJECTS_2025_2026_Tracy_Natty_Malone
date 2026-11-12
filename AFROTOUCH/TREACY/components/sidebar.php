<?php
function get_active_class($page, $current_page) {
    return $page === $current_page ? 'active' : '';
}
$user_role = isset($_SESSION['user_id']) ? ($_SESSION['role'] ?? 'buyer') : 'guest';
?>
<aside class="sidebar" id="sidebar">
    <div class="logo" onclick="location.href='../home.php'" style="cursor:pointer">
        AFRO TOUCH
        <span>By Afro Generation</span>
    </div>
    
    <?php if ($user_role === 'guest'): ?>
    <div style="padding: 2rem 1rem; text-align: center;">
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Please log in to access your dashboard</p>
        <a href="../login.php" class="btn-login" style="display: block; background: var(--primary-color); color: white; padding: 0.75rem; border-radius: 8px; text-decoration: none; font-weight: 600;">Sign In</a>
    </div>
    
    <?php elseif ($user_role === 'seller'): ?>
    <button class="btn-add-product" onclick="location.href='add-product.php'">
        <i data-lucide="plus"></i> Add Product
    </button>
    
    <nav>
        <div class="nav-label">Dashboard</div>
        <ul class="nav-menu">
            <li class="nav-item <?php echo get_active_class('overview', $current_page); ?>">
                <a href="overview.php">
                    <i data-lucide="layout-dashboard"></i> Overview
                </a>
            </li>
            <li class="nav-item <?php echo get_active_class('products', $current_page); ?>">
                <a href="products.php">
                    <i data-lucide="package"></i> Products
                </a>
            </li>
            <li class="nav-item <?php echo get_active_class('orders', $current_page); ?>">
                <a href="orders.php">
                    <i data-lucide="shopping-cart"></i> Orders
                </a>
            </li>
            <li class="nav-item <?php echo get_active_class('analytics', $current_page); ?>">
                <a href="analytics.php">
                    <i data-lucide="bar-chart-2"></i> Analytics
                </a>
            </li>
        </ul>
        
        <div class="nav-label">My Shop</div>
        <ul class="nav-menu">
            <li class="nav-item <?php echo get_active_class('shop-view', $current_page); ?>">
                <a href="shop.php">
                    <i data-lucide="store"></i> My Shop
                </a>
            </li>
        </ul>
        
        <div class="nav-label">Account</div>
        <ul class="nav-menu">
            <li class="nav-item <?php echo get_active_class('settings', $current_page); ?>">
                <a href="settings.php">
                    <i data-lucide="settings"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a href="../logout.php">
                    <i data-lucide="log-out"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
    
    <?php elseif ($user_role === 'buyer'): ?>
    <nav>
        <div class="nav-label">Dashboard</div>
        <ul class="nav-menu">
            <li class="nav-item <?php echo get_active_class('buyer-dashboard', $current_page); ?>">
                <a href="dashboard.php">
                    <i data-lucide="layout-dashboard"></i> Overview
                </a>
            </li>
            <li class="nav-item <?php echo get_active_class('buyer-orders', $current_page); ?>">
                <a href="orders.php">
                    <i data-lucide="shopping-cart"></i> My Orders
                </a>
            </li>
        </ul>
        
        <div class="nav-label">Account</div>
        <ul class="nav-menu">
            <li class="nav-item <?php echo get_active_class('buyer-settings', $current_page); ?>">
                <a href="settings.php">
                    <i data-lucide="user"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a href="../logout.php">
                    <i data-lucide="log-out"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
    
    <?php elseif ($user_role === 'admin'): ?>
    <nav>
        <div class="nav-label">Dashboard</div>
        <ul class="nav-menu">
            <li class="nav-item <?php echo get_active_class('admin-dashboard', $current_page); ?>">
                <a href="dashboard.php">
                    <i data-lucide="layout-dashboard"></i> Overview
                </a>
            </li>
            <li class="nav-item <?php echo get_active_class('admin-users', $current_page); ?>">
                <a href="users.php">
                    <i data-lucide="users"></i> Users
                </a>
            </li>
            <li class="nav-item <?php echo get_active_class('admin-sellers', $current_page); ?>">
                <a href="sellers.php">
                    <i data-lucide="store"></i> Sellers
                </a>
            </li>
            <li class="nav-item <?php echo get_active_class('admin-orders', $current_page); ?>">
                <a href="orders.php">
                    <i data-lucide="shopping-cart"></i> Orders
                </a>
            </li>
        </ul>
        
        <div class="nav-label">Settings</div>
        <ul class="nav-menu">
            <li class="nav-item <?php echo get_active_class('admin-settings', $current_page); ?>">
                <a href="settings.php">
                    <i data-lucide="settings"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a href="../logout.php">
                    <i data-lucide="log-out"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</aside>