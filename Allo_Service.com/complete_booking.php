<?php
session_start();
require_once 'includes/db.php';

$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) {
    header("Location: index.php");
    exit;
}

// Get booking with provider details
$booking = $conn->query("SELECT b.*, p.name as provider_name, p.payout_account FROM bookings b JOIN providers p ON b.provider_id = p.provider_id WHERE b.booking_id = $booking_id")->fetch_assoc();

if (!$booking) {
    die("Booking not found");
}

// Check ownership - client OR admin
$is_client = isset($_SESSION['client_id']) && $_SESSION['client_id'] == $booking['client_id'];
$is_admin = isset($_SESSION['admin_id']);

if (!$is_client && !$is_admin) {
    header("Location: client/client_login.php");
    exit;
}

// Only allow completion if paid and confirmed
if ($booking['payment_status'] != 'paid' || $booking['status'] != 'confirmed') {
    die("Invalid booking status. Only confirmed bookings with payment can be marked complete.");
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update booking status
    $conn->query("UPDATE bookings SET status = 'completed' WHERE booking_id = $booking_id");
    
    // Update payment status
    $conn->query("UPDATE payments SET status = 'completed' WHERE booking_id = $booking_id");
    
    // Credit provider wallet (only if they have payout account)
    if (!empty($booking['payout_account'])) {
        $provider_id = $booking['provider_id'];
        $earning = $booking['professional_earning'];
        $conn->query("UPDATE providers SET wallet_balance = wallet_balance + $earning WHERE provider_id = $provider_id");
    }
    
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Booking | Allo_Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; padding: 40px 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 16px; text-align: center; }
        h1 { color: #007a33; margin-bottom: 20px; }
        .btn { display: inline-block; background: #007a33; color: white; padding: 14px 30px; border-radius: 10px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; margin: 5px; }
        .btn:hover { background: #006028; }
        .btn-secondary { background: #e2e8f0; color: #475569; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .info-box { background: #f8fafc; padding: 20px; border-radius: 12px; margin: 20px 0; text-align: left; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64748b; }
        .info-value { font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <h1><i class="fas fa-check-circle" style="color: #10b981;"></i> Service Completed!</h1>
            <p>Payment has been released to the professional's wallet.</p>
            <p style="margin: 20px 0; color: #64748b;">Thank you for using Allo_Service!</p>
            <a href="client/client_dashboard.php" class="btn">Back to Dashboard</a>
        <?php else: ?>
            <h1>Confirm Service Completion</h1>
            <p>Have you received the service from the professional?</p>
            
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Professional</span>
                    <span class="info-value"><?= htmlspecialchars($booking['provider_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Service</span>
                    <span class="info-value"><?= htmlspecialchars($booking['service_type']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Service Amount</span>
                    <span class="info-value"><?= number_format($booking['price'], 0, ',', ' ') ?> XAF</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Provider Earnings</span>
                    <span class="info-value" style="color: #10b981;"><?= number_format($booking['professional_earning'], 0, ',', ' ') ?> XAF</span>
                </div>
            </div>
            
            <form method="POST">
                <button type="submit" class="btn"><i class="fas fa-check"></i> Yes, Confirm Completion</button>
            </form>
            <a href="client/client_dashboard.php" class="btn btn-secondary">Cancel</a>
            
            <p style="margin-top: 20px; font-size: 12px; color: #94a3b8;">
                <i class="fas fa-info-circle"></i> By confirming, payment will be released to the professional's wallet.
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
