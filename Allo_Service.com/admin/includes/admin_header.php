<?php
// admin/includes/admin_header.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | ALLO_SERVICE</title>
    <link rel="stylesheet" href="assets/css/admin.css?v=1.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>ALLO<span>SERVICE</span></h2>
        <small>Administration Panel</small>
    </div>
    
    <ul class="nav-links">
        <li>
            <a href="admin_dashboard.php" class="<?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="admin_pros.php" class="<?= $current_page == 'admin_pros.php' ? 'active' : '' ?>">
                <i class="fas fa-user-tie"></i> Professionals
            </a>
        </li>
        <li>
            <a href="admin_clients.php" class="<?= $current_page == 'admin_clients.php' ? 'active' : '' ?>">
                <i class="fas fa-user"></i> Clients
            </a>
        </li>
        <li>
            <a href="admin_bookings.php" class="<?= $current_page == 'admin_bookings.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
        </li>
        <li>
            <a href="admin_categories.php" class="<?= $current_page == 'admin_categories.php' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Categories
            </a>
        </li>
        <li>
            <a href="admin_reviews.php" class="<?= $current_page == 'admin_reviews.php' ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Reviews
            </a>
        </li>
        <li>
            <a href="contacts.php" class="<?= $current_page == 'contacts.php' ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i> Inquiries
            </a>
        </li>
        <li>
            <a href="admin_reports.php" class="<?= $current_page == 'admin_reports.php' ? 'active' : '' ?>">
                <i class="fas fa-exclamation-triangle"></i> Reports
            </a>
        </li>
        
        <li class="logout-link">
            <a href="../logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="admin-header">
        <div class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>
        <h1><?php 
            switch($current_page) {
                case 'admin_dashboard.php': echo 'Dashboard Overview'; break;
                case 'users.php': echo 'Manage Users & Pros'; break;
                case 'admin_pros.php': echo 'Manage Professionals'; break;
                case 'admin_clients.php': echo 'Manage Clients'; break;
                case 'admin_bookings.php': echo 'Manage Bookings'; break;
                case 'admin_categories.php': echo 'Service Categories'; break;
                case 'admin_reviews.php': echo 'Reviews & Ratings'; break;
                case 'contacts.php': echo 'Support Inquiries'; break;
                case 'admin_reports.php': echo 'System Analytics'; break;
                default: echo 'Admin Panel';
            }
        ?></h1>
        
        <div class="admin-profile" style="display:flex; align-items:center; gap:10px;">
            <div style="text-align:right;">
                <p style="font-weight:700; font-size:14px;"><?= $_SESSION['admin_email'] ?? 'Administrator' ?></p>
                <small style="color:#64748b;">Full Access</small>
            </div>
            <div style="width:40px; height:40px; background:var(--primary); color:white; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:800;">
                A
            </div>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    </script>
