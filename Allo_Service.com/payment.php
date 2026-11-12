<?php
session_start();
require_once 'includes/db.php';

$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) {
    header("Location: index.php");
    exit;
}

// Get booking details
$booking = $conn->query("SELECT b.*, p.name as provider_name, p.phone as provider_phone, p.profession FROM bookings b JOIN providers p ON b.provider_id = p.provider_id WHERE b.booking_id = $booking_id")->fetch_assoc();

if (!$booking) {
    die("Booking not found");
}

// Check if client owns this booking
if (!isset($_SESSION['client_id']) || $_SESSION['client_id'] != $booking['client_id']) {
    header("Location: client/client_login.php");
    exit;
}

// Get platform settings
$settings = $conn->query("SELECT * FROM platform_settings LIMIT 1")->fetch_assoc();
$total_amount = $booking['price'] + $booking['platform_fee'];

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? 'mobile_money';
    $mobile_number = clean_input($_POST['mobile_number'] ?? '');
    
    if (empty($mobile_number)) {
        $error = 'Please enter your mobile money number';
    } else {
        // Simulate payment processing
        $transaction_ref = 'TXN' . time() . rand(1000, 9999);
        
        // Insert payment record
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, client_id, provider_id, amount, platform_fee, professional_amount, transaction_ref, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, 'completed', ?)");
        $stmt->bind_param("iiidddss", $booking_id, $_SESSION['client_id'], $booking['provider_id'], $booking['price'], $booking['platform_fee'], $booking['professional_earning'], $transaction_ref, $payment_method);
        $stmt->execute();
        $stmt->close();
        
        // Update booking
        $conn->query("UPDATE bookings SET payment_status = 'paid', status = 'confirmed' WHERE booking_id = $booking_id");
        
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment | Allo_Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; }
        .container { max-width: 500px; margin: 40px auto; padding: 20px; }
        .payment-card { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size: 24px; margin-bottom: 10px; }
        .provider-info { background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 20px; }
        .provider-info h3 { font-size: 18px; margin-bottom: 5px; }
        .provider-info p { color: #64748b; font-size: 14px; }
        .amount-box { background: linear-gradient(135deg, #007a33, #00b894); color: white; padding: 25px; border-radius: 12px; text-align: center; margin-bottom: 25px; }
        .amount-box .amount { font-size: 36px; font-weight: 700; }
        .amount-box .label { font-size: 14px; opacity: 0.9; }
        .breakdown { margin-bottom: 25px; }
        .breakdown-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e2e8f0; }
        .breakdown-row.total { font-weight: 700; font-size: 18px; border-bottom: none; }
        .payment-methods { margin-bottom: 20px; }
        .payment-method { display: flex; align-items: center; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; margin-bottom: 10px; cursor: pointer; transition: 0.3s; }
        .payment-method.selected { border-color: #007a33; background: #f0fdf4; }
        .payment-method input { display: none; }
        .payment-method i { font-size: 24px; margin-right: 15px; color: #007a33; }
        .payment-method .details { flex: 1; }
        .payment-method .details strong { display: block; }
        .payment-method .details small { color: #64748b; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input { width: 100%; padding: 14px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 16px; }
        .form-group input:focus { outline: none; border-color: #007a33; }
        .btn-pay { width: 100%; background: #007a33; color: white; border: none; padding: 16px; border-radius: 12px; font-size: 18px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-pay:hover { background: #006028; }
        .btn-back { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; }
        .success-box { text-align: center; padding: 40px 20px; }
        .success-box i { font-size: 64px; color: #10b981; margin-bottom: 20px; }
        .success-box h2 { color: #10b981; margin-bottom: 10px; }
        .success-box p { color: #64748b; margin-bottom: 20px; }
        .success-box .btn { display: inline-block; background: #007a33; color: white; padding: 14px 30px; border-radius: 10px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container">
    <?php if ($success): ?>
    <div class="payment-card">
        <div class="success-box">
            <i class="fas fa-check-circle"></i>
            <h2>Payment Successful!</h2>
            <p>Your booking has been confirmed. See contact details below.</p>
            <p style="font-weight: 600;">Transaction Ref: <?= $transaction_ref ?></p>
            <a href="booking_confirmed.php?booking_id=<?= $booking_id ?>" class="btn">View Booking & Contact Pro</a>
        </div>
    </div>
    <?php else: ?>
    <div class="payment-card">
        <h1><i class="fas fa-credit-card"></i> Payment</h1>
        
        <div class="provider-info">
            <h3><?= htmlspecialchars($booking['provider_name']) ?></h3>
            <p><?= htmlspecialchars($booking['profession']) ?></p>
            <p style="margin-top: 8px;"><i class="fas fa-calendar"></i> <?= date('M j, Y', strtotime($booking['booking_date'])) ?> at <?= date('g:i A', strtotime($booking['booking_time'])) ?></p>
        </div>
        
        <div class="amount-box">
            <div class="label">Total Amount to Pay</div>
            <div class="amount"><?= number_format($total_amount, 0, ',', ' ') ?> XAF</div>
        </div>
        
        <div class="breakdown">
            <div class="breakdown-row">
                <span>Service Price</span>
                <span><?= number_format($booking['price'], 0, ',', ' ') ?> XAF</span>
            </div>
            <div class="breakdown-row">
                <span>Platform Fee (<?= $settings['client_fee_percent'] ?? 5 ?>%)</span>
                <span><?= number_format($booking['platform_fee'], 0, ',', ' ') ?> XAF</span>
            </div>
            <div class="breakdown-row total">
                <span>Total</span>
                <span><?= number_format($total_amount, 0, ',', ' ') ?> XAF</span>
            </div>
        </div>
        
        <?php if ($error): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="payment-methods">
                <label class="payment-method selected">
                    <input type="radio" name="payment_method" value="mobile_money" checked>
                    <i class="fas fa-mobile-alt"></i>
                    <div class="details">
                        <strong>Mobile Money (MTN/Orange)</strong>
                        <small>Pay directly from your mobile money account</small>
                    </div>
                </label>
            </div>
            
            <div class="form-group">
                <label>Mobile Money Number</label>
                <input type="tel" name="mobile_number" placeholder="237XXXXXXXXX" required pattern="[0-9]{12}" title="Enter 12-digit number including 237">
            </div>
            
            <button type="submit" class="btn-pay">
                <i class="fas fa-lock"></i> Pay <?= number_format($total_amount, 0, ',', ' ') ?> XAF
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 20px; font-size: 12px; color: #94a3b8;">
            <i class="fas fa-shield-alt"></i> Your payment is secured with escrow protection
        </p>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
