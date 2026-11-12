<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$provider_id = $_SESSION['provider_id'];
$message = '';

// Get provider details
$stmt = $conn->prepare("SELECT payout_account, wallet_balance FROM providers WHERE provider_id = ?");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payout_account = clean_input($_POST['payout_account'] ?? '');
    
    if (empty($payout_account)) {
        $message = "Please enter a valid payout account (MTN or Orange number).";
    } else {
        $stmt = $conn->prepare("UPDATE providers SET payout_account = ? WHERE provider_id = ?");
        $stmt->bind_param("si", $payout_account, $provider_id);
        $stmt->execute();
        $stmt->close();
        $provider['payout_account'] = $payout_account;
        $message = "Payout account updated successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payout Settings | ALLO_SERVICE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #007A5E; }
        body { font-family: 'Inter', sans-serif; background: #1e293b; color: white; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #334155; padding: 40px; border-radius: 16px; }
        h1 { font-size: 24px; margin-bottom: 10px; }
        p { color: rgba(255,255,255,0.7); margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input { width: 100%; padding: 14px; border: 2px solid rgba(255,255,255,0.2); border-radius: 10px; background: #1e293b; color: white; font-size: 16px; }
        .form-group input:focus { outline: none; border-color: #10b981; }
        .btn { background: #10b981; color: white; padding: 14px 30px; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; width: 100%; }
        .btn:hover { background: #059669; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .alert-success { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
        .alert-error { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #f87171; }
        .wallet-card { background: linear-gradient(135deg, #10b981, #059669); padding: 25px; border-radius: 12px; margin-bottom: 30px; text-align: center; }
        .wallet-card .label { font-size: 14px; opacity: 0.9; }
        .wallet-card .amount { font-size: 36px; font-weight: 700; margin-top: 10px; }
        .info { background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; font-size: 14px; line-height: 1.6; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: rgba(255,255,255,0.6); text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-wallet"></i> Payout Settings</h1>
        
        <div class="wallet-card">
            <div class="label">Available Balance</div>
            <div class="amount"><?= number_format($provider['wallet_balance'] ?? 0, 0, ',', ' ') ?> XAF</div>
        </div>
        
        <?php if ($message): ?>
        <div class="alert <?= strpos($message, 'success') ? 'alert-success' : 'alert-error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Payout Account (Mobile Money)</label>
                <input type="tel" name="payout_account" value="<?= htmlspecialchars($provider['payout_account'] ?? '') ?>" placeholder="237XXXXXXXXX" required>
            </div>
            
            <button type="submit" class="btn">Save Payout Account</button>
        </form>
        
        <div class="info">
            <i class="fas fa-info-circle"></i> <strong>How it works:</strong><br>
            • Client pays for service → money goes to platform escrow<br>
            • When service is completed → money released to your wallet<br>
            • You can withdraw funds from your wallet anytime<br>
            • Make sure your payout account is correct to receive payments
        </div>
        
        <a href="provider_profile.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
