<?php
require_once 'includes/db.php';

// Add is_online column if not exists
$check = $conn->query("SHOW COLUMNS FROM providers LIKE 'is_online'");
if ($check->num_rows == 0) {
    $conn->query("ALTER TABLE providers ADD COLUMN is_online TINYINT(1) DEFAULT 0");
    $conn->query("ALTER TABLE providers ADD COLUMN online_since DATETIME DEFAULT NULL");
    echo "Columns added!";
} else {
    echo "Columns already exist.";
}
?>
