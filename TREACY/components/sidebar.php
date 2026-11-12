<?php
function get_active_class($page, $current_page) {
    return $page === $current_page ? 'active' : '';
}
?>
<aside class="sidebar" id="sidebar">
    <div class="logo" onclick="location.href='home.php'" style="cursor:pointer">
        AFRO TOUCH
        <span>By Afro Generation</span>
    </div>
    
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
        
        <div class="nav-label">Manage</div>
        <ul class="nav-menu">
            <li class="nav-item <?php echo get_active_class('manage', $current_page); ?>">
                <a href="manage.php">
                    <i data-lucide="layers"></i> Manage
                </a>
            </li>
            <li class="nav-item <?php echo get_active_class('shop', $current_page); ?>">
                <a href="my-shop.php">
                    <i data-lucide="store"></i> My Shop
                </a>
            </li>
            <li class="nav-item <?php echo get_active_class('settings', $current_page); ?>">
                <a href="settings.php">
                    <i data-lucide="settings"></i> Settings
                </a>
            </li>
        </ul>
    </nav>
</aside>
