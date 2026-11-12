-- setup.sql
-- Allo Service Database Schema (English)
-- Run this script to set up the database

CREATE DATABASE IF NOT EXISTS allo_service_database CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE allo_service_database;

-- Clients Table
CREATE TABLE IF NOT EXISTS clients (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    selfie VARCHAR(255),
    id_card_front VARCHAR(255),
    id_card_back VARCHAR(255),
    id_verified TINYINT(1) DEFAULT 0,
    id_verified_at TIMESTAMP NULL,
    suspension_reason VARCHAR(500),
    status ENUM('active', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Services (Categories) Table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image_path VARCHAR(255),
    base_price DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Providers (Service Providers) Table
CREATE TABLE IF NOT EXISTS providers (
    provider_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    selfie VARCHAR(255),
    profession VARCHAR(100) NOT NULL,
    profile_photo VARCHAR(255),
    cover_photo VARCHAR(255),
    description TEXT,
    skills TEXT,
    location VARCHAR(100) NOT NULL,
    availability VARCHAR(50) NOT NULL,
    id_card_front VARCHAR(255),
    id_card_back VARCHAR(255),
    id_verified TINYINT(1) DEFAULT 0,
    id_verified_at TIMESTAMP NULL,
    warning_count INT DEFAULT 0,
    suspension_reason VARCHAR(500),
    is_online TINYINT(1) DEFAULT 0,
    online_since DATETIME DEFAULT NULL,
    payout_account VARCHAR(50),
    wallet_balance DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'approved', 'suspended') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Contacts Table
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reports Table
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    provider_id INT,
    reason TEXT NOT NULL,
    description TEXT,
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    resolution VARCHAR(50),
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE SET NULL,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE SET NULL
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    provider_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
);

-- Provider Posts (Portfolio) Table
CREATE TABLE IF NOT EXISTS provider_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    image_path VARCHAR(255),
    caption TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
);

-- Provider Locations Table
CREATE TABLE IF NOT EXISTS provider_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
);

-- Post Images Table (Multiple images per post)
CREATE TABLE IF NOT EXISTS post_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES provider_posts(id) ON DELETE CASCADE
);

-- Password Reset Tokens Table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    user_type ENUM('client', 'provider') NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email)
);

-- Bookings Table (Escrow System)
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    provider_id INT NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    description TEXT,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    address VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    platform_fee DECIMAL(10,2) DEFAULT 0,
    professional_earning DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'disputed') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
);

-- Payments Table (Transaction Tracking)
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    client_id INT NOT NULL,
    provider_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    platform_fee DECIMAL(10,2) NOT NULL,
    professional_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'mobile_money',
    transaction_ref VARCHAR(100) UNIQUE,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
);

-- Platform Settings Table
CREATE TABLE IF NOT EXISTS platform_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_fee_percent DECIMAL(5,2) DEFAULT 5.00,
    provider_fee_percent DECIMAL(5,2) DEFAULT 10.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Provider Service Prices Table
CREATE TABLE IF NOT EXISTS provider_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
);

-- Provider Notifications Table
CREATE TABLE IF NOT EXISTS provider_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    booking_id INT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
);

-- Client Notifications Table
CREATE TABLE IF NOT EXISTS client_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    booking_id INT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE
);

-- Seed Platform Settings
INSERT IGNORE INTO platform_settings (client_fee_percent, provider_fee_percent) VALUES (5.00, 10.00);

-- Seed Default Admin
INSERT IGNORE INTO admin (email, password) VALUES 
('admin@alloservice.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Seed Core Services
INSERT IGNORE INTO services (name, slug, description, image_path) VALUES 
('Plumbing', 'plumbing', 'Reliable installation and emergency repair of pipes, faucets, and water systems.', 'assets/img/services/plumbing.png'),
('Electricity', 'electricity', 'Certified electricians for wiring, safety checks, and lighting installations.', 'assets/img/services/electricity.png'),
('Cleaning', 'cleaning', 'From quick touch-ups to deep cleaning for homes and workplaces.', 'assets/img/services/cleaning.jpg'),
('Gardening', 'gardening', 'Keep your outdoor space fresh, clean, and beautifully maintained.', 'assets/img/services/gardening.png'),
('Hairdressing', 'hairdressing', 'Professional home styling and grooming tailored to your look.', 'assets/img/services/hairdressing.jpg'),
('Mechanics', 'mechanics', 'On-the-spot troubleshooting and maintenance for cars and bikes.', 'assets/img/services/mechanics.png'),
('Painting', 'painting', 'Clean finishes that bring life back to your walls and spaces.', 'assets/img/services/painting.png'),
('Carpentry', 'carpentry', 'Custom woodwork, repairs, and furniture crafted with precision.', 'assets/img/services/carpentry.png'),
('Pest Control', 'pest-control', 'Effective pest control services to keep your home safe and pest-free.', 'assets/img/services/pest-control.png'),
('Welding', 'welding', 'Professional welding services for metal fabrication and repairs.', 'assets/img/services/welding.png');
