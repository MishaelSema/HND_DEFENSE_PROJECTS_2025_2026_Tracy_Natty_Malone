<?php
// Database configuration for AfroTouch
$db_host = 'localhost';
$db_name = 'afrotouch';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function to get all products
if (!function_exists('getProducts')) {
function getProducts($pdo, $limit = null, $category = null, $featured = false, $newArrivals = false, $bestSellers = false) {
    $sql = "SELECT p.*, c.name as category_name, s.store_name, s.is_verified 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN sellers s ON p.seller_id = s.id 
            WHERE p.status = 'active'";
    
    $params = [];
    
    if ($category) {
        $sql .= " AND c.name = ?";
        $params[] = $category;
    }
    
    if ($featured) {
        $sql .= " AND p.is_featured = 1";
    }
    
    if ($newArrivals) {
        $sql .= " AND p.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    }
    
    if ($bestSellers) {
        $sql .= " ORDER BY p.sales_count DESC";
    } else {
        $sql .= " ORDER BY p.created_at DESC";
    }
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
}

// Helper function to get all categories
if (!function_exists('getCategories')) {
function getCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}
}

// Helper function to get verified sellers/artisans
if (!function_exists('getVerifiedSellers')) {
function getVerifiedSellers($pdo, $limit = null) {
    $sql = "SELECT s.*, COUNT(p.id) as product_count 
            FROM sellers s 
            LEFT JOIN products p ON s.id = p.seller_id AND p.status = 'active'
            WHERE s.is_verified = 1 
            GROUP BY s.id 
            ORDER BY s.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}
}

// Helper function to get orders
if (!function_exists('getOrders')) {
function getOrders($pdo, $seller_id = null, $buyer_id = null, $status = null) {
    $sql = "SELECT o.*, p.name as product_name, p.image as product_image, s.store_name, u.name as buyer_name, u.email as buyer_email
            FROM orders o
            LEFT JOIN products p ON o.product_id = p.id
            LEFT JOIN sellers s ON p.seller_id = s.id
            LEFT JOIN users u ON o.buyer_id = u.id
            WHERE 1=1";
    
    $params = [];
    
    if ($seller_id) {
        $sql .= " AND p.seller_id = ?";
        $params[] = $seller_id;
    }
    
    if ($buyer_id) {
        $sql .= " AND o.buyer_id = ?";
        $params[] = $buyer_id;
    }
    
    if ($status) {
        $sql .= " AND o.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY o.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
}

// Helper function to create order
if (!function_exists('createOrder')) {
function createOrder($pdo, $buyer_id, $product_id, $quantity, $shipping_address, $payment_method) {
    $stmt = $pdo->prepare("SELECT price, seller_id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        return false;
    }
    
    $total = $product['price'] * $quantity;
    $order_number = 'AFT-' . rand(1000, 9999);
    
    $sql = "INSERT INTO orders (order_number, buyer_id, product_id, quantity, total, shipping_address, payment_method, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$order_number, $buyer_id, $product_id, $quantity, $total, $shipping_address, $payment_method]);
}
}

// Helper function to get buyer orders
if (!function_exists('getBuyerOrders')) {
function getBuyerOrders($pdo, $buyer_id) {
    $stmt = $pdo->prepare("SELECT o.*, p.name as product_name, p.image as product_image, s.store_name
            FROM orders o
            LEFT JOIN products p ON o.product_id = p.id
            LEFT JOIN sellers s ON p.seller_id = s.id
            WHERE o.buyer_id = ?
            ORDER BY o.created_at DESC");
    $stmt->execute([$buyer_id]);
    return $stmt->fetchAll();
}
}

// Helper function to get single product
if (!function_exists('getProduct')) {
function getProduct($pdo, $id) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, s.store_name, s.is_verified
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN sellers s ON p.seller_id = s.id 
            WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
}

// Helper function to get cart items
if (!function_exists('getCartItems')) {
function getCartItems($pdo, $session_id) {
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image, p.seller_id
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.session_id = ?");
    $stmt->execute([$session_id]);
    return $stmt->fetchAll();
}
}

// Helper function to add to cart
if (!function_exists('addToCart')) {
function addToCart($pdo, $session_id, $product_id, $quantity = 1) {
    $stmt = $pdo->prepare("INSERT INTO cart (session_id, product_id, quantity, created_at) 
            VALUES (?, ?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE quantity = quantity + ?");
    return $stmt->execute([$session_id, $product_id, $quantity, $quantity]);
}
}

// Helper function to remove from cart
if (!function_exists('removeFromCart')) {
function removeFromCart($pdo, $session_id, $product_id) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ? AND product_id = ?");
    return $stmt->execute([$session_id, $product_id]);
}
}

// Helper function to get cart count
if (!function_exists('getCartCount')) {
function getCartCount($pdo, $session_id) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE session_id = ?");
    $stmt->execute([$session_id]);
    $result = $stmt->fetch();
    return $result['count'] ?: 0;
}
}

// Helper function to get seller by user ID
if (!function_exists('getSellerByUserId')) {
function getSellerByUserId($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM sellers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}
}

// Helper function to get all sellers (for admin)
if (!function_exists('getAllSellers')) {
function getAllSellers($pdo) {
    $stmt = $pdo->query("SELECT s.*, u.name, u.email, COUNT(p.id) as product_count
            FROM sellers s
            LEFT JOIN users u ON s.user_id = u.id
            LEFT JOIN products p ON s.id = p.seller_id AND p.status = 'active'
            GROUP BY s.id
            ORDER BY s.created_at DESC");
    return $stmt->fetchAll();
}
}

// Helper function to get all buyers (for admin)
if (!function_exists('getAllBuyers')) {
function getAllBuyers($pdo) {
    $stmt = $pdo->query("SELECT u.*, COUNT(o.id) as order_count
            FROM users u
            LEFT JOIN orders o ON u.id = o.buyer_id
            WHERE u.role = 'buyer'
            GROUP BY u.id
            ORDER BY u.created_at DESC");
    return $stmt->fetchAll();
}
}

// Helper function to get all users (for admin)
if (!function_exists('getAllUsers')) {
function getAllUsers($pdo) {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}
}

// Helper function to get dashboard stats
if (!function_exists('getSellerStats')) {
function getSellerStats($pdo, $seller_id) {
    $stats = [];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE seller_id = ? AND status = 'active'");
    $stmt->execute([$seller_id]);
    $stats['products'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders o JOIN products p ON o.product_id = p.id WHERE p.seller_id = ?");
    $stmt->execute([$seller_id]);
    $stats['orders'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT SUM(total) as total FROM orders o JOIN products p ON o.product_id = p.id WHERE p.seller_id = ? AND o.status = 'delivered'");
    $stmt->execute([$seller_id]);
    $stats['revenue'] = $stmt->fetch()['total'] ?: 0;
    
    return $stats;
}
}

// Helper function to get admin stats
if (!function_exists('getAdminStats')) {
function getAdminStats($pdo) {
    $stats = [];
    
    $stats['total_sellers'] = $pdo->query("SELECT COUNT(*) FROM sellers")->fetchColumn();
    $stats['total_buyers'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'buyer'")->fetchColumn();
    $stats['total_products'] = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
    $stats['total_orders'] = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $stats['total_revenue'] = $pdo->query("SELECT SUM(total) FROM orders WHERE status = 'delivered'")->fetchColumn() ?: 0;
    
    return $stats;
}
}

// Helper function to search products
if (!function_exists('searchProducts')) {
function searchProducts($pdo, $query) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, s.store_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN sellers s ON p.seller_id = s.id 
            WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
            AND p.status = 'active'");
    $search = "%$query%";
    $stmt->execute([$search, $search, $search]);
    return $stmt->fetchAll();
}
}

// Helper function to create product
if (!function_exists('createProduct')) {
function createProduct($pdo, $seller_id, $data, $images) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name'])));
    $slug .= '-' . rand(1000, 9999);
    
    $image = !empty($images[0]) ? $images[0] : null;
    $images_json = !empty($images) ? json_encode($images) : null;
    
    $stmt = $pdo->prepare("INSERT INTO products (seller_id, category_id, name, slug, description, price, compare_price, image, images, stock, sku, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
    
    return $stmt->execute([
        $seller_id,
        $data['category_id'],
        $data['name'],
        $slug,
        $data['description'],
        $data['price'],
        $data['compare_price'] ?? null,
        $image,
        $images_json,
        $data['stock'] ?? 0,
        $data['sku'] ?? null
    ]);
}
}

// Helper function to get seller products
if (!function_exists('getSellerProducts')) {
function getSellerProducts($pdo, $seller_id, $status = null) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.seller_id = ?";
    
    $params = [$seller_id];
    
    if ($status) {
        $sql .= " AND p.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
}