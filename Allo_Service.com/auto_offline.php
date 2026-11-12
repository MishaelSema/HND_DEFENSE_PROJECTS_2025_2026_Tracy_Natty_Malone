<?php
require_once 'includes/db.php';

// Auto-deactivate all providers at end of day (run via cron or on each request)
$conn->query("UPDATE providers SET is_online = 0, online_since = NULL WHERE DATE(online_since) < CURDATE() AND is_online = 1");

// Also set any providers who have been online for more than 24 hours to offline
$conn->query("UPDATE providers SET is_online = 0, online_since = NULL WHERE is_online = 1 AND online_since < DATE_SUB(NOW(), INTERVAL 24 HOUR)");

echo "Auto-offline completed. Providers deactivated: " . $conn->affected_rows;
?>
