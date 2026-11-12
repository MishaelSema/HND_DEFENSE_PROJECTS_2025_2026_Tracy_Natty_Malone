<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$provider_id = $_SESSION['provider_id'];

// Update booking status (mark as in progress or completed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = clean_input($_POST['status']);
    $conn->query("UPDATE bookings SET status = '$new_status' WHERE booking_id = $booking_id AND provider_id = $provider_id");
    header("Location: provider_bookings.php");
    exit();
}

// Fetch bookings for this provider
$bookings = [];
$res = $conn->query("SELECT b.*, c.name as client_name, c.phone as client_phone, c.email as client_email 
FROM bookings b 
JOIN clients c ON b.client_id = c.client_id 
WHERE b.provider_id = $provider_id 
ORDER BY b.created_at DESC");
while ($row = $res->fetch_assoc()) $bookings[] = $row;

$pending = count(array_filter($bookings, fn($b) => $b['status'] == 'pending'));
$confirmed = count(array_filter($bookings, fn($b) => $b['status'] == 'confirmed'));
$completed = count(array_filter($bookings, fn($b) => $b['status'] == 'completed'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | ALLO_SERVICE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --p-green: #007A5E; --light: #f1f5f9; }
        body { font-family: 'Inter', sans-serif; background: var(--light); color: #1e293b; }
        .container { max-width: 900px; margin: 40px auto; padding: 20px; }
        .card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; margin: 0; color: var(--p-green); }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #f8fafc; padding: 20px; border-radius: 12px; text-align: center; border: 1px solid #e2e8f0; }
        .stat-card h2 { font-size: 28px; font-weight: 800; margin: 5px 0; }
        .stat-card p { font-size: 12px; color: #64748b; margin: 0; text-transform: uppercase; }
        .booking-card { padding: 20px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 15px; border-left: 4px solid #f59e0b; }
        .booking-card.confirmed { border-left-color: #10b981; }
        .booking-card.completed { border-left-color: #007a33; }
        .booking-card.cancelled { border-left-color: #ef4444; }
        .booking-row { display: flex; justify-content: space-between; align-items: flex-start; }
        .client-info { display: flex; gap: 15px; align-items: center; }
        .client-avatar { width: 50px; height: 50px; background: var(--p-green); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; }
        .service-name { font-weight: 700; color: #1e293b; }
        .service-meta { font-size: 13px; color: #64748b; margin-top: 3px; }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .price { font-size: 18px; font-weight: 700; color: var(--p-green); }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; }
        .btn-primary { background: var(--p-green); color: white; }
        .btn-back { background: #f1f5f9; color: #475569; }
        select { padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1><i class="fas fa-calendar-check"></i> My Bookings</h1>
                <a href="provider_profile.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h2 style="color: #f59e0b;"><?= $pending ?></h2>
                    <p>Pending</p>
                </div>
                <div class="stat-card">
                    <h2 style="color: #10b981;"><?= $confirmed ?></h2>
                    <p>Confirmed</p>
                </div>
                <div class="stat-card">
                    <h2 style="color: #007a33;"><?= $completed ?></h2>
                    <p>Completed</p>
                </div>
            </div>

            <?php if (empty($bookings)): ?>
                <div style="padding: 50px; text-align: center; color: #94a3b8;">
                    <i class="fas fa-calendar-plus" style="font-size: 50px; margin-bottom: 15px;"></i>
                    <p>No bookings yet. Share your profile to get clients!</p>
                    <a href="../profile.php?id=<?= $provider_id ?>" style="color: var(--p-green); font-weight: 700;">View My Profile</a>
                </div>
            <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                    <?php 
                    $status_classes = ['pending' => '', 'confirmed' => 'confirmed', 'in_progress' => 'confirmed', 'completed' => 'completed', 'cancelled' => 'cancelled'];
                    $status_class = $status_classes[$b['status']] ?? '';
                    $status_colors = ['pending' => '#f59e0b', 'confirmed' => '#10b981', 'in_progress' => '#3b82f6', 'completed' => '#007a33', 'cancelled' => '#ef4444'];
                    $status_color = $status_colors[$b['status']] ?? '#64748b';
                    ?>
                    <div class="booking-card <?= $status_class ?>">
                        <div class="booking-row">
                            <div class="client-info">
                                <div class="client-avatar"><?= strtoupper(substr($b['client_name'], 0, 1)) ?></div>
                                <div>
                                    <div class="service-name"><?= htmlspecialchars($b['service_type']) ?></div>
                                    <div class="service-meta">
                                        <i class="fas fa-user"></i> <?= htmlspecialchars($b['client_name']) ?> &nbsp;
                                        <i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($b['booking_date'])) ?> at <?= date('g:i A', strtotime($b['booking_time'])) ?>
                                    </div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($b['address']) ?>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="status-badge" style="background: <?= $status_color ?>22; color: <?= $status_color ?>;"><?= $b['status'] ?></span>
                                <div class="price" style="margin-top: 8px;"><?= number_format($b['price'], 0, ',', ' ') ?> XAF</div>
                                <?php if ($b['payment_status'] == 'paid'): ?>
                                <div style="font-size: 11px; color: #10b981; margin-top: 3px;"><i class="fas fa-check-circle"></i> Paid (Escrow)</div>
                                <?php elseif ($b['payment_status'] == 'refunded'): ?>
                                <div style="font-size: 11px; color: #ef4444; margin-top: 3px;"><i class="fas fa-undo"></i> Refunded</div>
                                <?php else: ?>
                                <div style="font-size: 11px; color: #f59e0b; margin-top: 3px;"><i class="fas fa-clock"></i> Awaiting Payment</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($b['status'] != 'cancelled' && $b['status'] != 'completed'): ?>
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                            <?php if ($b['payment_status'] == 'paid'): ?>
                                <?php if ($b['status'] == 'confirmed'): ?>
                                <form method="post">
                                    <input type="hidden" name="update_status" value="1">
                                    <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="">Update Status...</option>
                                        <option value="in_progress">Start Working</option>
                                        <option value="cancelled">Decline Booking</option>
                                    </select>
                                </form>
                                <?php elseif ($b['status'] == 'in_progress'): ?>
                                <span style="font-size: 12px; color: #3b82f6;"><i class="fas fa-tools"></i> Work in progress...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <span style="font-size: 12px; color: #64748b;">
                                <i class="fas fa-phone"></i> <?= htmlspecialchars($b['client_phone']) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
