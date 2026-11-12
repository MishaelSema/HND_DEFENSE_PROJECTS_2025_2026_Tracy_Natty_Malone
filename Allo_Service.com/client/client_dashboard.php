<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}

$client_id = $_SESSION['client_id'];

// Fetch client details
$stmt = $conn->prepare("SELECT * FROM clients WHERE client_id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$client = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Check if client needs to complete KYC
$needs_kyc = empty($client['id_card_front']) || empty($client['id_card_back']);

// Fetch bookings
$bookings = [];
$res_bookings = $conn->query("SELECT b.*, p.name as provider_name, p.profession, p.profile_photo FROM bookings b JOIN providers p ON b.provider_id = p.provider_id WHERE b.client_id = $client_id ORDER BY b.created_at DESC");
while ($row = $res_bookings->fetch_assoc()) $bookings[] = $row;

// Fetch reviews left by this client
$reviews = [];
$res_revs = $conn->query("SELECT r.*, p.name as provider_name, p.profession FROM reviews r JOIN providers p ON r.provider_id = p.provider_id WHERE r.client_id = $client_id ORDER BY r.created_at DESC");
while ($row = $res_revs->fetch_assoc()) $reviews[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | Client</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --p-green: #007A5E; --light: #f1f5f9; }
        body { font-family: 'Inter', sans-serif; background: var(--light); color: #1e293b; padding: 20px; }
        .dashboard-container { max-width: 900px; margin: 40px auto; background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: #f8fafc; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; }
        .stat-card i { font-size: 24px; color: var(--p-green); margin-bottom: 10px; }
        .stat-card h2 { font-size: 28px; font-weight: 800; margin: 5px 0; }
        .stat-card p { font-size: 13px; color: #64748b; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .section-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #334155; }
        .review-card { padding: 20px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 15px; }
        .review-card .provider { font-weight: 700; color: var(--p-green); }
        .review-card .meta { font-size: 12px; color: #94a3b8; margin-top: 5px; }
        .btn-back { display: inline-block; padding: 10px 20px; background: #f1f5f9; color: #475569; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: 0.3s; }
        .btn-back:hover { background: #e2e8f0; }
        .btn-logout { color: #ef4444; font-weight: 700; text-decoration: none; font-size: 14px; border: 1px solid #fecaca; padding: 8px 15px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php if ($needs_kyc): ?>
        <div style="background: #fef3c7; border: 1px solid #fcd34d; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 24px; color: #92400e;"></i>
            <div style="flex: 1;">
                <strong style="color: #92400e; display: block; margin-bottom: 5px;">Verification Required</strong>
                <p style="color: #78350f; font-size: 14px; margin: 0;">Please complete your ID verification to book services and contact providers.</p>
            </div>
            <a href="client_kyc.php" style="background: #92400e; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">Complete Verification</a>
        </div>
        <?php elseif ($client['id_verified'] == 0): ?>
        <div style="background: #fef3c7; border: 1px solid #fcd34d; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
            <i class="fas fa-clock" style="font-size: 24px; color: #92400e;"></i>
            <div style="flex: 1;">
                <strong style="color: #92400e; display: block; margin-bottom: 5px;">Verification Pending</strong>
                <p style="color: #78350f; font-size: 14px; margin: 0;">Your ID documents are being reviewed. This usually takes 24-48 hours.</p>
            </div>
        </div>
        <?php else: ?>
        <div style="background: #dcfce7; border: 1px solid #86efac; padding: 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
            <i class="fas fa-check-circle" style="font-size: 24px; color: #166534;"></i>
            <div style="flex: 1;">
                <strong style="color: #166534; display: block; margin-bottom: 5px;">Verified</strong>
                <p style="color: #15803d; font-size: 14px; margin: 0;">Your account is verified. You can now book services!</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="header">
            <div>
                <p style="color: #64748b; font-size: 14px; margin: 0;">Welcome back,</p>
                <h1 style="font-size: 24px; margin: 5px 0;"><?= htmlspecialchars($client['name']) ?></h1>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <a href="../index.php" class="btn-back"><i class="fas fa-home"></i> Main Site</a>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-calendar-check"></i>
                <h2><?= count($bookings) ?></h2>
                <p>My Bookings</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-comment-dots"></i>
                <h2><?= count($reviews) ?></h2>
                <p>Reviews Left</p>
            </div>
        </div>

        <h3 class="section-title"><i class="fas fa-calendar"></i> My Bookings</h3>
        <?php if (empty($bookings)): ?>
            <div style="padding: 40px; text-align: center; color: #94a3b8; border: 2px dashed #f1f5f9; border-radius: 12px;">
                <i class="fas fa-calendar-plus" style="font-size: 40px; margin-bottom: 15px;"></i>
                <p>No bookings yet. Find a professional and book a service!</p>
                <a href="../all_services.php" style="color: var(--p-green); font-weight: 700; text-decoration: none;">Browse Services &rarr;</a>
            </div>
            <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <a href="../booking_details.php?booking_id=<?= $booking['booking_id'] ?>" style="text-decoration: none; color: inherit; display: block;">
                <div class="review-card" style="border-left: 4px solid <?= $booking['status'] == 'confirmed' ? '#10b981' : ($booking['status'] == 'completed' ? '#007a33' : '#f59e0b') ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <img src="../<?= htmlspecialchars($booking['profile_photo'] ?: 'assets/img/default-avatar.png') ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <span class="provider"><?= htmlspecialchars($booking['provider_name']) ?></span>
                                <span style="font-size: 12px; color: #94a3b8;"> - <?= htmlspecialchars($booking['service_type']) ?></span>
                                <div style="font-size: 13px; color: #64748b; margin-top: 3px;">
                                    <i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($booking['booking_date'])) ?> at <?= date('g:i A', strtotime($booking['booking_time'])) ?>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <?php 
                            $status_colors = ['pending' => '#f59e0b', 'confirmed' => '#10b981', 'in_progress' => '#3b82f6', 'completed' => '#007a33', 'cancelled' => '#ef4444'];
                            $status_color = $status_colors[$booking['status']] ?? '#64748b';
                            ?>
                            <span style="background: <?= $status_color ?>22; color: <?= $status_color ?>; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase;"><?= htmlspecialchars($booking['status']) ?></span>
                            <div style="font-size: 14px; font-weight: 700; margin-top: 5px;"><?= number_format($booking['price'], 0, ',', ' ') ?> XAF</div>
                        </div>
                    </div>
                    <?php if ($booking['payment_status'] == 'paid'): ?>
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9; display: flex; gap: 15px; flex-wrap: wrap;">
                        <span style="font-size: 12px; color: #10b981;"><i class="fas fa-check-circle"></i> Payment Secured (Escrow)</span>
                        <?php if ($booking['status'] == 'confirmed'): ?>
                        <a href="../complete_booking.php?booking_id=<?= $booking['booking_id'] ?>" onclick="event.stopPropagation()" style="font-size: 12px; color: #007a33; font-weight: 600;"><i class="fas fa-check"></i> Mark Complete</a>
                        <?php endif; ?>
                    </div>
                    <?php elseif ($booking['payment_status'] == 'pending' && $booking['status'] != 'cancelled'): ?>
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; flex-wrap: wrap;">
                        <span style="font-size: 12px; color: #f59e0b;"><i class="fas fa-clock"></i> Awaiting Payment</span>
                        <a href="../payment.php?booking_id=<?= $booking['booking_id'] ?>" onclick="event.stopPropagation()" style="font-size: 12px; background: #007a33; color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600; text-decoration: none;"><i class="fas fa-credit-card"></i> Pay Now</a>
                    </div>
                    <?php endif; ?>
                </div>
                </a>
            <?php endforeach; ?>
            <?php endif; ?>

        <h3 class="section-title"><i class="fas fa-history"></i> My Recent Reviews</h3>
        <?php if (empty($reviews)): ?>
            <div style="padding: 40px; text-align: center; color: #94a3b8; border: 2px dashed #f1f5f9; border-radius: 12px;">
                <i class="fas fa-pencil-alt" style="font-size: 40px; margin-bottom: 15px;"></i>
                <p>You haven't left any reviews yet. Start exploring services to find pros!</p>
                <a href="../all_services.php" style="color: var(--p-green); font-weight: 700; text-decoration: none;">Browse Services &rarr;</a>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $rev): ?>
                <div class="review-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <span class="provider"><?= htmlspecialchars($rev['provider_name']) ?></span>
                            <span style="font-size: 12px; color: #94a3b8;"> (<?= htmlspecialchars($rev['profession']) ?>)</span>
                            <div style="color: #fcd116; margin-top: 5px; font-size: 14px;">
                                <?= str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']) ?>
                            </div>
                        </div>
                        <span class="meta"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                    </div>
                    <p style="margin: 15px 0 0; font-size: 14px; color: #475569; line-height: 1.6;"><?= nl2br(htmlspecialchars($rev['content'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
