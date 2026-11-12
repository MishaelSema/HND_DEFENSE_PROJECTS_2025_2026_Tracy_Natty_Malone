-- AfroTouch Database Migration
-- Created: 2026
-- Import this file in phpMyAdmin to set up the database

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS AFROTOUCH;
USE AFROTOUCH;

-- ============================================
-- USERS TABLE
-- ============================================
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(191) NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('buyer', 'seller', 'admin') DEFAULT 'buyer',
    phone VARCHAR(50) DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    shop_name VARCHAR(255) DEFAULT NULL,
    shop_description TEXT DEFAULT NULL,
    shop_logo VARCHAR(255) DEFAULT NULL,
    shop_location VARCHAR(255) DEFAULT NULL,
    is_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- PRODUCTS TABLE
-- ============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100) DEFAULT NULL,
    stock INT DEFAULT 0,
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- ORDERS TABLE
-- ============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT DEFAULT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- ORDER ITEMS TABLE
-- ============================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================
-- INDEXES (for performance)
-- ============================================
CREATE INDEX idx_products_seller ON products(seller_id);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_orders_buyer ON orders(buyer_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
