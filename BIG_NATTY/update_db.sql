-- Update database for new features

USE issue_reporting;

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
