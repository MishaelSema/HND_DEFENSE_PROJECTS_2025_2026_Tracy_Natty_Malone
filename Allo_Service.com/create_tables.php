<?php
require_once 'includes/db.php';

$conn->query("CREATE TABLE IF NOT EXISTS provider_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    booking_id INT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
)");

echo "Table created successfully!";
?>
