-- Create replies table for timeline responses

USE issue_reporting;

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
