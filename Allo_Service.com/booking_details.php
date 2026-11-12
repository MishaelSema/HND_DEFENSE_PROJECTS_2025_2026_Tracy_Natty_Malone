<?php
session_start();
require_once 'includes/db.php';

$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) {
    header("Location: client/client_dashboard.php");
    exit;
}

// Get booking with provider details
$booking = $conn->query("SELECT b.*, p.name as provider_name, p.phone as provider_phone, p.whatsapp, p.profession, p.profile_photo, p.location as provider_location 
FROM bookings b 
JOIN providers p ON b.provider_id = p.provider_id 
WHERE b.booking_id = $booking_id")->fetch_assoc();

if (!$booking) {
    die("Booking not found");
}

// Check ownership
if (!isset($_SESSION['client_id']) || $_SESSION['client_id'] != $booking['client_id']) {
    header("Location: client/client_login.php");
    exit;
}

// Show contact only if paid AND not completed
$show_contact = $booking['payment_status'] == 'paid' && $booking['status'] != 'completed' && $booking['status'] != 'cancelled';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details | Allo Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; }
        .container { max-width: 700px; margin: 40px auto; padding: 20px; }
        .card { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .header h1 { font-size: 20px; color: #007a33; }
        .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; }
        .btn-back:hover { color: #007a33; }
        
        .status-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; text-transform: uppercase; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #dcfce7; color: #166534; }
        .status-in_progress { background: #dbeafe; color: #1d4ed8; }
        .status-completed { background: #007a3322; color: #007a33; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        
        .payment-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; text-transform: uppercase; margin-left: 10px; }
        .payment-paid { background: #dcfce7; color: #166534; }
        .payment-pending { background: #fef3c7; color: #92400e; }
        .payment-refunded { background: #f1f5f9; color: #64748b; }
        
        .section { margin-bottom: 25px; padding-bottom: 25px; border-bottom: 1px solid #e2e8f0; }
        .section:last-child { border-bottom: none; }
        .section-title { font-size: 14px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px; }
        
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; }
        .detail-label { color: #64748b; }
        .detail-value { font-weight: 600; }
        
        .provider-card { display: flex; gap: 20px; align-items: center; background: #f8fafc; padding: 20px; border-radius: 12px; }
        .provider-avatar { width: 70px; height: 70px; border-radius: 50%; object-fit: cover; }
        .provider-info h3 { margin-bottom: 5px; }
        .provider-info p { color: #64748b; font-size: 14px; }
        
        .contact-section { background: linear-gradient(135deg, #007a33, #00b894); border-radius: 12px; padding: 25px; color: white; text-align: center; }
        .contact-section h3 { margin-bottom: 15px; }
        .contact-btn { display: inline-flex; align-items: center; gap: 10px; background: white; color: #007a33; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; margin: 5px; }
        .contact-btn:hover { transform: translateY(-2px); }
        
        .warning { background: #fef3c7; border: 1px solid #fcd34d; padding: 15px; border-radius: 10px; color: #92400e; font-size: 14px; }
        
        .action-buttons { display: flex; gap: 15px; margin-top: 20px; }
        .btn { flex: 1; padding: 14px; border-radius: 10px; text-decoration: none; font-weight: 700; text-align: center; }
        .btn-primary { background: #007a33; color: white; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        
        .completed-info { background: #f8fafc; padding: 20px; border-radius: 12px; text-align: center; color: #64748b; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="card">
        <div class="header">
            <h1><i class="fas fa-calendar-check"></i> Booking #<?= $booking['booking_id'] ?></h1>
            <a href="client/client_dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        
        <div style="margin-bottom: 25px;">
            <span class="status-badge status-<?= $booking['status'] ?>"><?= $booking['status'] ?></span>
            <span class="payment-badge payment-<?= $booking['payment_status'] ?>"><?= $booking['payment_status'] ?></span>
        </div>
        
        <div class="section">
            <div class="section-title">Service Details</div>
            <div class="detail-row">
                <span class="detail-label">Service</span>
                <span class="detail-value"><?= htmlspecialchars($booking['service_type']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value"><?= date('F j, Y', strtotime($booking['booking_date'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time</span>
                <span class="detail-value"><?= date('g:i A', strtotime($booking['booking_time'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Your Address</span>
                <span class="detail-value"><?= htmlspecialchars($booking['address']) ?></span>
            </div>
            <?php if (!empty($booking['description'])): ?>
            <div class="detail-row">
                <span class="detail-label">Description</span>
                <span class="detail-value" style="max-width: 300px; text-align: right;"><?= htmlspecialchars($booking['description']) ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <div class="section-title">Payment Details</div>
            <div class="detail-row">
                <span class="detail-label">Service Price</span>
                <span class="detail-value"><?= number_format($booking['price'], 0, ',', ' ') ?> XAF</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Platform Fee</span>
                <span class="detail-value"><?= number_format($booking['platform_fee'], 0, ',', ' ') ?> XAF</span>
            </div>
            <div class="detail-row" style="font-size: 18px; font-weight: 700; color: #007a33;">
                <span class="detail-label">Total Paid</span>
                <span class="detail-value"><?= number_format($booking['price'] + $booking['platform_fee'], 0, ',', ' ') ?> XAF</span>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Professional</div>
            <div class="provider-card">
                <img src="<?= htmlspecialchars($booking['profile_photo'] ?: 'assets/img/default-avatar.png') ?>" alt="Provider" class="provider-avatar">
                <div class="provider-info">
                    <h3><?= htmlspecialchars($booking['provider_name']) ?></h3>
                    <p><?= htmlspecialchars($booking['profession']) ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($booking['provider_location']) ?></p>
                </div>
            </div>
        </div>
        
        <?php if ($show_contact): ?>
        <div class="section">
            <div class="contact-section">
                <h3><i class="fas fa-phone-alt"></i> Contact Your Professional</h3>
                <p style="margin-bottom: 15px; opacity: 0.9;">You can contact <?= htmlspecialchars($booking['provider_name']) ?> for this service.</p>
                <?php if (!empty($booking['whatsapp'])): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $booking['whatsapp']) ?>" target="_blank" class="contact-btn">
                    <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                </a>
                <?php endif; ?>
                <a href="tel:<?= $booking['provider_phone'] ?>" class="contact-btn">
                    <i class="fas fa-phone-alt"></i> Call Professional
                </a>
            </div>
            <div class="warning" style="margin-top: 15px;">
                <i class="fas fa-info-circle"></i> Please do not make any additional payments outside the platform.
            </div>
        </div>
        <?php elseif ($booking['status'] == 'completed'): ?>
        <div class="completed-info">
            <i class="fas fa-check-circle" style="font-size: 30px; color: #007a33; margin-bottom: 10px;"></i>
            <p>This service has been completed.</p>
            <p style="font-size: 13px;">Contact details are no longer available for completed bookings.</p>
        </div>
        <?php elseif ($booking['payment_status'] == 'pending'): ?>
        <div class="action-buttons">
            <a href="payment.php?booking_id=<?= $booking['booking_id'] ?>" class="btn btn-primary">
                <i class="fas fa-credit-card"></i> Pay Now (<?= number_format($booking['price'] + $booking['platform_fee'], 0, ',', ' ') ?> XAF)
            </a>
        </div>
        <?php endif; ?>
        
        <?php if ($booking['status'] == 'confirmed' && $booking['payment_status'] == 'paid'): ?>
        <div style="margin-top: 20px;">
            <a href="complete_booking.php?booking_id=<?= $booking['booking_id'] ?>" class="btn btn-primary" style="display: block; text-decoration: none;">
                <i class="fas fa-check"></i> Mark as Completed
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
