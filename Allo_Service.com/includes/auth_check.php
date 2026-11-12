<?php
// includes/auth_check.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

function redirectIfLoggedIn() {
    if (isset($_SESSION['admin_id'])) {
        header("Location: admin/admin_dashboard.php");
        exit();
    }
    if (isset($_SESSION['client_id'])) {
        header("Location: client/client_dashboard.php");
        exit();
    }
    if (isset($_SESSION['provider_id'])) {
        header("Location: provider/provider_profile.php");
        exit();
    }
}

function protectAdminPage() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin/admin_login.php");
        exit();
    }
}

function protectClientPage() {
    if (!isset($_SESSION['client_id'])) {
        header("Location: client/client_login.php");
        exit();
    }
}

function protectProviderPage() {
    if (!isset($_SESSION['provider_id'])) {
        header("Location: provider/provider_login.php");
        exit();
    }
}

function restrictAdminFromStorefront() {
    if (isset($_SESSION['admin_id'])) {
        $current_page = basename($_SERVER['PHP_SELF']);
        $storefront_pages = ['index.php', 'about.php', 'contact.php', 'all_services.php', 'choose_role.php'];
        if (in_array($current_page, $storefront_pages)) {
            header("Location: admin/admin_dashboard.php");
            exit();
        }
    }
}

function isLoggedIn() {
    return isset($_SESSION['client_id']) || isset($_SESSION['provider_id']) || isset($_SESSION['admin_id']);
}

function getCurrentUserType() {
    if (isset($_SESSION['admin_id'])) return 'admin';
    if (isset($_SESSION['client_id'])) return 'client';
    if (isset($_SESSION['provider_id'])) return 'provider';
    return 'guest';
}

function getCurrentUserId() {
    if (isset($_SESSION['admin_id'])) return $_SESSION['admin_id'];
    if (isset($_SESSION['client_id'])) return $_SESSION['client_id'];
    if (isset($_SESSION['provider_id'])) return $_SESSION['provider_id'];
    return null;
}
?>
