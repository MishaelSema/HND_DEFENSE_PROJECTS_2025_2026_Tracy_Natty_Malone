<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$provider_id = $_SESSION['provider_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'go_online') {
        $conn->query("UPDATE providers SET is_online = 1, online_since = NOW() WHERE provider_id = $provider_id");
        $message = "You are now online! Clients can see your profile.";
    } elseif ($action === 'go_offline') {
        $conn->query("UPDATE providers SET is_online = 0, online_since = NULL WHERE provider_id = $provider_id");
        $message = "You are now offline.";
    }
    
    header("Location: provider_profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toggle Status | ALLO_SERVICE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding: 40px; text-align: center; }
    </style>
</head>
<body>
    <form method="post">
        <button type="submit" name="action" value="go_online" style="background: #10b981; color: white; padding: 15px 30px; border: none; border-radius: 10px; font-size: 16px; cursor: pointer; margin: 10px;">
            <i class="fas fa-toggle-on"></i> Go Online
        </button>
        <button type="submit" name="action" value="go_offline" style="background: #64748b; color: white; padding: 15px 30px; border: none; border-radius: 10px; font-size: 16px; cursor: pointer; margin: 10px;">
            <i class="fas fa-toggle-off"></i> Go Offline
        </button>
    </form>
    <p><a href="provider_profile.php">Back to Dashboard</a></p>
</body>
</html>
