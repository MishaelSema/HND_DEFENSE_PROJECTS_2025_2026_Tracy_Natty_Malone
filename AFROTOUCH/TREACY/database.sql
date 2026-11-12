-- AfroTouch Database Schema
-- Run this SQL to create all required tables

-- Fix for key length error on some MySQL versions
SET GLOBAL innodb_file_format = BARRACUDA;
SET GLOBAL innodb_large_prefix = 1;

-- Create database
CREATE DATABASE IF NOT EXISTS afrotouch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE afrotouch;

-- Users table (buyers, sellers, admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    avatar VARCHAR(500),
    role ENUM('buyer', 'seller', 'admin') DEFAULT 'buyer',
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(500),
    parent_id INT DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sellers table
CREATE TABLE IF NOT EXISTS sellers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    store_name VARCHAR(255) NOT NULL,
    store_slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    logo VARCHAR(500),
    banner VARCHAR(500),
    country VARCHAR(100),
    city VARCHAR(100),
    address TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    verification_status ENUM('pending', 'processing', 'verified', 'rejected') DEFAULT 'pending',
    verification_data TEXT,
    plan ENUM('free', 'basic', 'premium') DEFAULT 'free',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table (no foreign keys for simpler import)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    compare_price DECIMAL(10,2),
    image VARCHAR(500),
    images TEXT,
    stock INT DEFAULT 0,
    sku VARCHAR(100),
    status ENUM('active', 'inactive', 'draft') DEFAULT 'active',
    is_featured BOOLEAN DEFAULT FALSE,
    is_verified BOOLEAN DEFAULT FALSE,
    sales_count INT DEFAULT 0,
    view_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    buyer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_country VARCHAR(100),
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'canceled') DEFAULT 'pending',
    tracking_number VARCHAR(100),
    notes TEXT,
    name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_cart (session_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    buyer_id INT NOT NULL,
    rating INT NOT NULL,
    title VARCHAR(255),
    comment TEXT,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pricing Plans table
CREATE TABLE IF NOT EXISTS pricing_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    billing_interval ENUM('month', 'year') DEFAULT 'month',
    features TEXT,
    product_limit INT,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO categories (name, slug, description) VALUES
('Clothing', 'clothing', 'Traditional and modern African clothing'),
('Jewelry', 'jewelry', 'Handmade African jewelry and accessories'),
('Home Decor', 'home-decor', 'African inspired home decor items'),
('Art', 'art', 'African art and sculptures'),
('Textiles', 'textiles', 'African fabrics and textiles'),
('Pottery', 'pottery', 'Handcrafted African pottery'),
('Woodwork', 'woodwork', 'Wooden crafts and carvings'),
('Basketry', 'basketry', 'Woven baskets and containers');

INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@afrotouch.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Felix Artisan', 'felix@afrotouch.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller'),
('Maria Buyer', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer');

INSERT INTO sellers (user_id, store_name, store_slug, description, country, city, is_verified, verification_status) VALUES
(2, 'Felix Crafts', 'felix-crafts', 'Handmade African crafts from Cameroon', 'Cameroon', 'Douala', TRUE, 'verified');

INSERT INTO pricing_plans (name, slug, price, billing_interval, features, product_limit, is_featured) VALUES
('Free', 'free', 0, 'month', '["Basic listing", "5 products maximum", "Standard support"]', 5, FALSE),
('Basic', 'basic', 5994, 'month', '["Priority listing", "50 products", "Email support", "Analytics"]', 50, FALSE),
('Premium', 'premium', 17994, 'month', '["Featured listing", "Unlimited products", "24/7 support", "Advanced analytics", "Custom storefront"]', 9999, TRUE);

INSERT INTO products (seller_id, category_id, name, slug, description, price, compare_price, image, stock, is_featured, sales_count) VALUES
(1, 1, 'Handmade African Cap', 'handmade-african-cap', 'Beautiful handcrafted African cap made by master artisans', 27000, 33000, 'https://api.dicebear.com/7.x/initials/svg?seed=AC&backgroundColor=D35400', 20, TRUE, 15),
(1, 2, 'Beaded Zulu Necklace', 'beaded-zulu-necklace', 'Traditional Zulu beaded necklace', 7500, 9000, 'https://api.dicebear.com/7.x/initials/svg?seed=ZN&backgroundColor=2D1F1A', 50, TRUE, 28),
(1, 8, 'Geometric Woven Basket', 'geometric-woven-basket', 'Beautiful geometric basket woven by master artisans in Cameroon', 31200, 39000, 'https://api.dicebear.com/7.x/initials/svg?seed=GB&backgroundColor=D35400', 15, TRUE, 42),
(1, 4, 'Bronze Ife Head Replica', 'bronze-ife-head-replica', 'Replica of the famous Ife head sculpture', 51000, 60000, 'https://api.dicebear.com/7.x/initials/svg?seed=BI&backgroundColor=6D5D57', 5, FALSE, 8),
(1, 5, 'Kente Cloth Table Runner', 'kente-cloth-table-runner', 'Authentic Kente cloth table runner', 9000, 12000, 'https://api.dicebear.com/7.x/initials/svg?seed=KC&backgroundColor=D35400', 30, TRUE, 22),
(1, 4, 'Ebony Wood Carving', 'ebony-wood-carving', 'Intricate ebony wood carving', 19200, 24000, 'https://api.dicebear.com/7.x/initials/svg?seed=EC&backgroundColor=2D1F1A', 12, FALSE, 18),
(1, 3, 'Woven Sisal Basket', 'woven-sisal-basket', 'Traditional sisal basket for home decor', 16800, 21000, 'https://api.dicebear.com/7.x/initials/svg?seed=WB&backgroundColor=F1C40F', 25, FALSE, 35),
(1, 2, 'Gold Coast Earrings', 'gold-coast-earrings', 'Traditional gold coast style earrings', 10800, 13200, 'https://api.dicebear.com/7.x/initials/svg?seed=GE&backgroundColor=DAA520', 40, FALSE, 12);
