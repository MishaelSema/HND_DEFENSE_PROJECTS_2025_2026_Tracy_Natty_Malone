<?php

use App\Services\Router;
use App\Services\Auth;

require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/ProductController.php';
require_once __DIR__ . '/../app/Controllers/OrderController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/VendorController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';
require_once __DIR__ . '/../app/Controllers/CheckoutController.php';
require_once __DIR__ . '/../app/Controllers/StaticController.php';

Router::addMiddleware('auth', function() {
    if (!Auth::check()) {
        Router::redirect('/AFROTOUCH/public/login');
    }
});

// Public Routes
Router::get('/', ['App\Controllers\HomeController', 'index']);
Router::get('/home', ['App\Controllers\HomeController', 'index']);

Router::get('/products', ['App\Controllers\ProductController', 'index']);
Router::get('/products/{id}', ['App\Controllers\ProductController', 'show']);

// Vendor Shop Routes
Router::get('/shop/{id}', ['App\Controllers\VendorController', 'shop']);
Router::get('/shops', ['App\Controllers\VendorController', 'index']);

// Auth Routes
Router::get('/login', ['App\Controllers\AuthController', 'showLogin']);
Router::post('/login', ['App\Controllers\AuthController', 'login']);
Router::get('/register', ['App\Controllers\AuthController', 'showRegister']);
Router::post('/register', ['App\Controllers\AuthController', 'register']);
Router::get('/logout', ['App\Controllers\AuthController', 'logout']);

// Dashboard Routes (require auth)
Router::get('/dashboard', ['App\Controllers\DashboardController', 'overview']);
Router::get('/dashboard/overview', ['App\Controllers\DashboardController', 'overview']);
Router::get('/dashboard/products', ['App\Controllers\DashboardController', 'products']);
Router::get('/dashboard/orders', ['App\Controllers\DashboardController', 'orders']);
Router::get('/dashboard/analytics', ['App\Controllers\DashboardController', 'analytics']);
Router::get('/dashboard/add-product', ['App\Controllers\DashboardController', 'addProduct']);
Router::post('/dashboard/store-product', ['App\Controllers\DashboardController', 'storeProduct']);
Router::get('/dashboard/shop', ['App\Controllers\DashboardController', 'shop']);
Router::get('/dashboard/settings', ['App\Controllers\DashboardController', 'settings']);

// Admin Routes (require admin role)
Router::get('/admin', ['App\Controllers\AdminController', 'index']);
Router::get('/admin/users', ['App\Controllers\AdminController', 'users']);
Router::get('/admin/shops', ['App\Controllers\AdminController', 'shops']);
Router::get('/admin/products', ['App\Controllers\AdminController', 'products']);
Router::get('/admin/orders', ['App\Controllers\AdminController', 'orders']);
Router::post('/admin/users/{id}/delete', ['App\Controllers\AdminController', 'deleteUser']);
Router::post('/admin/products/{id}/delete', ['App\Controllers\AdminController', 'deleteProduct']);

// API Routes for orders
Router::get('/orders', ['App\Controllers\OrderController', 'index']);
Router::get('/orders/{id}', ['App\Controllers\OrderController', 'show']);
Router::post('/orders', ['App\Controllers\OrderController', 'store']);
Router::put('/orders/{id}', ['App\Controllers\OrderController', 'update']);

// Checkout Routes
Router::get('/cart', ['App\Controllers\CheckoutController', 'index']);
Router::get('/checkout', ['App\Controllers\CheckoutController', 'checkoutForm']);
Router::post('/checkout', ['App\Controllers\CheckoutController', 'process']);
Router::get('/checkout/success', ['App\Controllers\CheckoutController', 'success']);
Router::post('/cart/add/{id}', ['App\Controllers\CheckoutController', 'addToCart']);
Router::post('/cart/update', ['App\Controllers\CheckoutController', 'updateCart']);
Router::post('/cart/remove', ['App\Controllers\CheckoutController', 'removeFromCart']);

// Static Pages
Router::get('/contact', ['App\Controllers\StaticController', 'contact']);
Router::get('/faq', ['App\Controllers\StaticController', 'faq']);
Router::get('/terms', ['App\Controllers\StaticController', 'terms']);
Router::get('/privacy', ['App\Controllers\StaticController', 'privacy']);
Router::get('/seller-guide', ['App\Controllers\StaticController', 'sellerGuide']);
Router::get('/pricing', ['App\Controllers\StaticController', 'pricing']);
Router::get('/verification', ['App\Controllers\StaticController', 'verification']);
Router::get('/help', ['App\Controllers\StaticController', 'help']);
Router::get('/shipping', ['App\Controllers\StaticController', 'shipping']);
Router::get('/returns', ['App\Controllers\StaticController', 'returns']);

// Buyer Orders
Router::get('/orders', ['App\Controllers\BuyerOrderController', 'myOrders']);
Router::get('/orders/{id}', ['App\Controllers\BuyerOrderController', 'show']);