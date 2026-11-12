<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - AfroTouch</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="/AFROTOUCH/public/assets/css/dashboard_new.css">
    <style>
        .admin-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .admin-badge-seller { background: #E3F2FD; color: #1565C0; }
        .admin-badge-buyer { background: #FFF3E0; color: #E65100; }
        .admin-badge-admin { background: #F3E5F5; color: #7B1FA2; }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        .admin-table th {
            padding: 15px;
            text-align: left;
            font-size: 13px;
            color: #6D5D57;
            font-weight: 500;
            background: #fafafa;
            border-bottom: 2px solid #E8E2DE;
        }
        .admin-table td {
            padding: 15px;
            border-bottom: 1px solid #E8E2DE;
        }
        .admin-table tr:hover {
            background: #fafafa;
        }
        .btn-danger {
            background: #C62828;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-danger:hover {
            background: #B71C1C;
        }
    </style>
</head>
<body class="dashboard-body">
    <aside class="dashboard-sidebar" id="sidebar">
        <div class="dashboard-logo" onclick="location.href='/AFROTOUCH/public/'">
            AFRO TOUCH
            <span>Admin Panel</span>
        </div>
        
        <nav>
            <div class="dashboard-nav-label">Admin</div>
            <ul class="dashboard-nav-menu">
                <li class="dashboard-nav-item <?= $current_page === 'dashboard' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/admin">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="dashboard-nav-item <?= $current_page === 'users' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/admin/users">
                        <i data-lucide="users"></i> Users
                    </a>
                </li>
                <li class="dashboard-nav-item <?= $current_page === 'shops' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/admin/shops">
                        <i data-lucide="store"></i> Shops
                    </a>
                </li>
                <li class="dashboard-nav-item <?= $current_page === 'products' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/admin/products">
                        <i data-lucide="package"></i> Products
                    </a>
                </li>
                <li class="dashboard-nav-item <?= $current_page === 'orders' ? 'active' : '' ?>">
                    <a href="/AFROTOUCH/public/admin/orders">
                        <i data-lucide="shopping-cart"></i> Orders
                    </a>
                </li>
            </ul>
            
            <div class="dashboard-nav-label">Account</div>
            <ul class="dashboard-nav-menu">
                <li class="dashboard-nav-item">
                    <a href="/AFROTOUCH/public/">
                        <i data-lucide="home"></i> Back to Site
                    </a>
                </li>
                <li class="dashboard-nav-item">
                    <a href="/AFROTOUCH/public/logout" style="color: #ff6b6b;">
                        <i data-lucide="log-out"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="page-title">
                <button class="btn-burger" onclick="toggleSidebar()">
                    <i data-lucide="menu"></i>
                </button>
                <span>Admin - <?= $page_title ?? 'Dashboard' ?></span>
            </div>
        </header>
        <div class="dashboard-content" id="content-area">
