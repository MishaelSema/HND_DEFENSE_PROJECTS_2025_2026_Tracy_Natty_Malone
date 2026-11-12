-- Database for Community Issue Reporting System

CREATE DATABASE IF NOT EXISTS issue_reporting;
USE issue_reporting;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Issues table
CREATE TABLE IF NOT EXISTS issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    location VARCHAR(255) NOT NULL,
    image_path VARCHAR(255),
    status ENUM('Pending', 'In Progress', 'Resolved') DEFAULT 'Pending',
    admin_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin account (admin@admin.com / admin123)
    INSERT INTO users (username, email, password, role) 
    VALUES ('Admin', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Add is_public column to issues table
    ALTER TABLE issues ADD COLUMN is_public TINYINT(1) DEFAULT 0 AFTER image_path;

-- Add upvotes and downvotes columns
    ALTER TABLE issues ADD COLUMN upvotes INT DEFAULT 0 AFTER is_public;
    ALTER TABLE issues ADD COLUMN downvotes INT DEFAULT 0 AFTER upvotes;

-- Create table to track user votes (one vote per user per issue)
CREATE TABLE IF NOT EXISTS user_votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    issue_id INT NOT NULL,
    vote_type ENUM('upvote', 'downvote') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vote (user_id, issue_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (issue_id) REFERENCES issues(id) ON DELETE CASCADE
);
-- Create replies table
CREATE TABLE IF NOT EXISTS issue_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    issue_id INT NOT NULL,
    admin_id INT NOT NULL,
    reply_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (issue_id) REFERENCES issues(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);
