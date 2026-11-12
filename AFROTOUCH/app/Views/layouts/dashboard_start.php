<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AfroTouch - Seller Dashboard' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/AFROTOUCH/public/assets/css/dashboard_new.css">
</head>
<body class="dashboard-body">
    <aside class="dashboard-sidebar" id="sidebar">
        <div class="dashboard-logo" onclick="location.href='/AFROTOUCH/public/'">
            AFRO TOUCH
            <span>By Afro Generation</span>
        </div>
        
        <a href="/AFROTOUCH/public/dashboard/add-product" class="btn-add-product">
            <i data-lucide="plus"></i> Add Product
        </a>
        
        <nav>
            <div class="dashboard-nav-label">Dashboard</div>
            <ul class="dashboard-nav-menu">
                <li class="dashboard-nav-item <?= $current_page === 'overview' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/dashboard">
                        <i data-lucide="layout-dashboard"></i> Overview
                    </a>
                </li>
                <li class="dashboard-nav-item <?= $current_page === 'products' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/dashboard/products">
                        <i data-lucide="package"></i> Products
                    </a>
                </li>
                <li class="dashboard-nav-item <?= $current_page === 'orders' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/dashboard/orders">
                        <i data-lucide="shopping-cart"></i> Orders
                    </a>
                </li>
                <li class="dashboard-nav-item <?= $current_page === 'analytics' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/dashboard/analytics">
                        <i data-lucide="bar-chart-2"></i> Analytics
                    </a>
                </li>
                <li class="dashboard-nav-item <?= $current_page === 'shop' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/dashboard/shop">
                        <i data-lucide="store"></i> My Shop
                    </a>
                </li>
                <li class="dashboard-nav-item <?= $current_page === 'settings' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/dashboard/settings">
                        <i data-lucide="settings"></i> Settings
                    </a>
                </li>
            </ul>
            
            <div class="dashboard-nav-label">Account</div>
            <ul class="dashboard-nav-menu">
                <?php if(\App\Services\Auth::check()): ?>
                <li class="dashboard-nav-item">
                    <a href="/AFROTOUCH/public/logout" style="color: #ff6b6b;">
                        <i data-lucide="log-out"></i> Logout
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>
    
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="page-title">
                <button class="btn-burger" onclick="toggleSidebar()">
                    <i data-lucide="menu"></i>
                </button>
            </div>
            <div class="top-actions">
                <?php if(isset($seller_name)): ?>
                    <span style="color: #6D5D57; font-size: 14px;">
                        <i data-lucide="user" style="width:18px;height:18px;vertical-align:middle;"></i>
                        <?= $seller_name ?>
                    </span>
                <?php endif; ?>
            </div>
        </header>
        <div class="dashboard-content" id="content-area">