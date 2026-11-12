-- migration_french_to_english.sql
-- Run this script to migrate French column names to English

-- Step 1: Rename prestataires table to providers
RENAME TABLE prestataires TO providers;

-- Step 2: Rename signalements table to reports
RENAME TABLE signalements TO reports;

-- Step 3: Rename columns in clients table
ALTER TABLE clients
    CHANGE COLUMN Id_client client_id INT AUTO_INCREMENT,
    CHANGE COLUMN NOM name VARCHAR(100) NOT NULL,
    CHANGE COLUMN Email email VARCHAR(100) NOT NULL UNIQUE,
    CHANGE COLUMN Mot_de_passe password VARCHAR(255) NOT NULL;

-- Step 4: Rename columns in providers table
ALTER TABLE providers
    CHANGE COLUMN Id_prestataire provider_id INT AUTO_INCREMENT,
    CHANGE COLUMN nom name VARCHAR(100) NOT NULL,
    CHANGE COLUMN email email VARCHAR(100) NOT NULL UNIQUE,
    CHANGE COLUMN mot_de_passe password VARCHAR(255) NOT NULL,
    CHANGE COLUMN telephone phone VARCHAR(20) NOT NULL,
    CHANGE COLUMN metier profession VARCHAR(100) NOT NULL,
    CHANGE COLUMN profile profile_photo VARCHAR(255),
    CHANGE COLUMN competence skills TEXT,
    CHANGE COLUMN localisation location VARCHAR(100) NOT NULL,
    CHANGE COLUMN disponibilite availability VARCHAR(50) NOT NULL;

-- Step 5: Rename columns in reviews table
ALTER TABLE reviews
    CHANGE COLUMN Id_prestataire provider_id INT NOT NULL,
    CHANGE COLUMN note rating INT NOT NULL,
    CHANGE COLUMN contenu content TEXT,
    CHANGE COLUMN date_commentaire created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Step 6: Rename columns in reports table
ALTER TABLE reports
    CHANGE COLUMN motif reason TEXT NOT NULL,
    CHANGE COLUMN statut status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    CHANGE COLUMN date_signalement created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Step 7: Update foreign key references
-- Reviews table foreign keys
ALTER TABLE reviews
    DROP FOREIGN KEY reviews_ibfk_1,
    DROP FOREIGN KEY reviews_ibfk_2;

ALTER TABLE reviews
    ADD CONSTRAINT reviews_ibfk_1 FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE,
    ADD CONSTRAINT reviews_ibfk_2 FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE;

-- Reports table foreign keys
ALTER TABLE reports
    DROP FOREIGN KEY reports_ibfk_1,
    DROP FOREIGN KEY reports_ibfk_2;

ALTER TABLE reports
    ADD CONSTRAINT reports_ibfk_1 FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE SET NULL,
    ADD CONSTRAINT reports_ibfk_2 FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE SET NULL;

-- Provider posts image_path column

-- Provider posts image_path column
ALTER TABLE provider_posts ADD COLUMN image_path VARCHAR(255) NULL;

-- Create bookings table if not exists
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

-- Create payments table if not exists
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create platform_settings table
CREATE TABLE IF NOT EXISTS platform_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_fee_percent DECIMAL(5,2) DEFAULT 5.00,
    provider_fee_percent DECIMAL(10,2) DEFAULT 10.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed platform settings if not exists
INSERT IGNORE INTO platform_settings (client_fee_percent, provider_fee_percent) VALUES (5.00, 10.00);

-- Add phone, id columns to clients
ALTER TABLE clients ADD COLUMN phone VARCHAR(20) NOT NULL;
ALTER TABLE clients ADD COLUMN id_card_front VARCHAR(255);
ALTER TABLE clients ADD COLUMN id_card_back VARCHAR(255);
ALTER TABLE clients ADD COLUMN id_verified TINYINT(1) DEFAULT 0;
ALTER TABLE clients ADD COLUMN id_verified_at TIMESTAMP NULL;

-- Create provider_services table
CREATE TABLE IF NOT EXISTS provider_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
);

-- Provider posts foreign key
ALTER TABLE provider_posts
    DROP FOREIGN KEY provider_posts_ibfk_1;

ALTER TABLE provider_posts
    ADD CONSTRAINT provider_posts_ibfk_1 FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE;
