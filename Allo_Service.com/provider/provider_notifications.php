<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['provider_id'])) { header("Location: provider_login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications | ALLO_SERVICE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding: 40px; text-align: center; } h1 { color: #007A5E; }</style>
</head>
<body>
    <div style="max-width: 800px; margin: 40px auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: left;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="margin:0; font-size: 24px; color: #007A5E;"><i class="fas fa-bell"></i> Notifications</h1>
            <a href="provider_profile.php" style="background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>

        <?php
        $id = $_SESSION['provider_id'];
        
        // Mark notifications as read
        $conn->query("UPDATE provider_notifications SET is_read = 1 WHERE provider_id = $id AND is_read = 0");
        
        // Get booking notifications
        $res = $conn->query("SELECT * FROM provider_notifications WHERE provider_id = $id ORDER BY created_at DESC LIMIT 20");
        
        if ($res && $res->num_rows > 0):
            while ($notif = $res->fetch_assoc()):
                $icon = $notif['type'] === 'booking' ? 'fa-calendar-check' : 'fa-bell';
                $bg = $notif['type'] === 'booking' ? '#007A5E22' : '#f59e0b22';
                $color = $notif['type'] === 'booking' ? '#007A5E' : '#f59e0b';
        ?>
            <div style="padding: 15px 20px; border-left: 4px solid <?= $color ?>; background: #f8fafc; border-radius: 0 8px 8px 0; margin-bottom: 12px; display: flex; align-items: center; gap: 15px;">
                <div style="background: <?= $bg ?>; color: <?= $color ?>; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas <?= $icon ?>"></i>
                </div>
                <div style="flex-grow: 1;">
                    <p style="margin:0; font-size: 14px; color: #334155;"><?= htmlspecialchars($notif['message']) ?></p>
                    <small style="color: #94a3b8;"><?= date('M d, Y h:i A', strtotime($notif['created_at'])) ?></small>
                </div>
                <?php if ($notif['type'] === 'booking' && $notif['booking_id']): ?>
                <a href="provider_bookings.php" style="color: #64748b;"><i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        <?php 
            endwhile;
        else:
        ?>
            <div style="text-align: center; padding: 50px; color: #94a3b8;">
                <i class="fas fa-bell-slash" style="font-size: 50px; margin-bottom: 20px; display: block;"></i>
                <p>No new notifications. Everything is on track!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
