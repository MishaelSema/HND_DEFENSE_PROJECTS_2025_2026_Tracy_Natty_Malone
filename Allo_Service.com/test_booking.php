<?php
// Simple test endpoint - remove after debugging
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: text/plain');
echo "Test endpoint working!\n";
echo "POST: " . print_r($_POST, true);
echo "SESSION client_id: " . ($_SESSION['client_id'] ?? 'NOT SET - this is the problem!');
echo "\nAll session data:\n" . print_r($_SESSION, true);
exit();
?>
