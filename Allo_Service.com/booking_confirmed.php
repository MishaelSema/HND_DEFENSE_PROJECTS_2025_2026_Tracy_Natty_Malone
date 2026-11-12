<?php
session_start();
require_once 'includes/db.php';

$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) {
    header("Location: index.php");
    exit;
}

// Get booking with provider details
$booking = $conn->query("SELECT b.*, p.name as provider_name, p.phone as provider_phone, p.whatsapp, p.profession, p.profile_photo FROM bookings b JOIN providers p ON b.provider_id = p.provider_id WHERE b.booking_id = $booking_id")->fetch_assoc();

if (!$booking) {
    die("Booking not found");
}

// Check ownership and booking status
if (!isset($_SESSION['client_id']) || $_SESSION['client_id'] != $booking['client_id']) {
    header("Location: client/client_login.php");
    exit;
}

// Only show contact for confirmed/paid bookings
$show_contact = in_array($booking['status'], ['confirmed', 'in_progress', 'completed']) && $booking['payment_status'] == 'paid';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed | Allo_Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; }
        .container { max-width: 600px; margin: 40px auto; padding: 20px; }
        .card { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .success-header { text-align: center; margin-bottom: 30px; }
        .success-header i { font-size: 60px; color: #10b981; margin-bottom: 15px; }
        .success-header h1 { color: #10b981; margin-bottom: 10px; }
        .success-header p { color: #64748b; }
        .booking-details { background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; }
        .detail-value { font-weight: 600; }
        .contact-section { background: linear-gradient(135deg, #007a33, #00b894); border-radius: 12px; padding: 25px; color: white; text-align: center; margin-bottom: 25px; }
        .contact-section h2 { margin-bottom: 20px; }
        .contact-btn { display: inline-flex; align-items: center; gap: 10px; background: white; color: #007a33; padding: 14px 25px; border-radius: 10px; text-decoration: none; font-weight: 700; margin: 5px; transition: 0.3s; }
        .contact-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .warning { background: #fef3c7; border: 1px solid #fcd34d; padding: 15px; border-radius: 10px; color: #92400e; text-align: center; margin-bottom: 20px; }
        .btn-back { display: block; text-align: center; color: #64748b; text-decoration: none; margin-top: 20px; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="card">
        <div class="success-header">
            <i class="fas fa-check-circle"></i>
            <h1>Booking Confirmed!</h1>
            <p>Your service has been booked. Use the details below to contact the professional.</p>
        </div>
        
        <div class="booking-details">
            <div class="detail-row">
                <span class="detail-label">Professional</span>
                <span class="detail-value"><?= htmlspecialchars($booking['provider_name']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Service</span>
                <span class="detail-value"><?= htmlspecialchars($booking['service_type']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value"><?= date('M j, Y', strtotime($booking['booking_date'])) ?> at <?= date('g:i A', strtotime($booking['booking_time'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Your Address</span>
                <span class="detail-value"><?= htmlspecialchars($booking['address']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount Paid</span>
                <span class="detail-value" style="color: #10b981;"><?= number_format($booking['price'] + $booking['platform_fee'], 0, ',', ' ') ?> XAF</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value" style="text-transform: uppercase; color: <?= $booking['status'] == 'confirmed' ? '#10b981' : '#64748b' ?>;"><?= htmlspecialchars($booking['status']) ?></span>
            </div>
        </div>
        
        <?php if ($show_contact): ?>
        <div class="contact-section">
            <h2><i class="fas fa-phone-alt"></i> Contact Your Professional</h2>
            <p style="margin-bottom: 20px; opacity: 0.9;">You can now contact <?= htmlspecialchars($booking['provider_name']) ?> directly for this service.</p>
            
            <?php if (!empty($booking['whatsapp'])): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $booking['whatsapp']) ?>" target="_blank" class="contact-btn">
                <i class="fab fa-whatsapp"></i> Chat on WhatsApp
            </a>
            <?php endif; ?>
            
            <a href="tel:<?= $booking['provider_phone'] ?>" class="contact-btn">
                <i class="fas fa-phone-alt"></i> Call Professional
            </a>
        </div>
        
        <div class="warning">
            <i class="fas fa-info-circle"></i> <strong>Important:</strong> Please do not make any additional payments outside the platform. If you encounter any issues, report through the app.
        </div>
        <?php else: ?>
        <div style="background: #f1f5f9; padding: 20px; border-radius: 12px; text-align: center;">
            <i class="fas fa-clock" style="font-size: 30px; color: #64748b; margin-bottom: 10px;"></i>
            <p style="color: #64748b;">Contact details will be available once your booking is confirmed and payment is processed.</p>
        </div>
        <?php endif; ?>
        
        <a href="client/client_dashboard.php" class="btn-back">← Back to My Dashboard</a>
        <a href="booking_details.php?booking_id=<?= $booking_id ?>" class="btn-back" style="margin-left: 20px;"><i class="fas fa-info-circle"></i> View Full Details</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
