<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
protectAdminPage();

$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $_POST['status'];
    $conn->query("UPDATE bookings SET status = '$new_status' WHERE booking_id = $booking_id");
    $message = "Status updated!";
}

// Handle payment release
if (isset($_GET['release'])) {
    $booking_id = intval($_GET['release']);
    $conn->query("UPDATE bookings SET status = 'completed' WHERE booking_id = $booking_id");
    $conn->query("UPDATE payments SET status = 'completed' WHERE booking_id = $booking_id");
    $message = "Payment released to provider!";
}

// Handle refund
if (isset($_GET['refund'])) {
    $booking_id = intval($_GET['refund']);
    $conn->query("UPDATE bookings SET status = 'cancelled', payment_status = 'refunded' WHERE booking_id = $booking_id");
    $conn->query("UPDATE payments SET status = 'refunded' WHERE booking_id = $booking_id");
    $message = "Booking refunded!";
}

// Fetch bookings
$bookings = [];
$res = $conn->query("SELECT b.*, p.name as provider_name, p.profession, c.name as client_name, c.email as client_email 
FROM bookings b 
JOIN providers p ON b.provider_id = p.provider_id 
JOIN clients c ON b.client_id = c.client_id 
ORDER BY b.created_at DESC");
while ($row = $res->fetch_assoc()) $bookings[] = $row;

$total = count($bookings);
$completed = count(array_filter($bookings, fn($b) => $b['status'] == 'completed'));
$confirmed = count(array_filter($bookings, fn($b) => $b['status'] == 'confirmed'));
$pending = count(array_filter($bookings, fn($b) => $b['status'] == 'pending'));
$earnings = $conn->query("SELECT SUM(platform_fee) as total FROM payments WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookings | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css?v=1.1">
</head>
<body>
<?php include 'includes/admin_header.php'; 

if ($message):
?>
    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin: 0 30px;"><?= $message ?></div>
<?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Bookings</h3>
            <div class="value"><?= $total ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #22c55e;">
            <h3>Completed</h3>
            <div class="value" style="color: #22c55e;"><?= $completed ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #3b82f6;">
            <h3>Confirmed</h3>
            <div class="value" style="color: #3b82f6;"><?= $confirmed ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <h3>Pending</h3>
            <div class="value" style="color: #f59e0b;"><?= $pending ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #007a33;">
            <h3>Platform Earnings</h3>
            <div class="value" style="color: #007a33;"><?= number_format($earnings, 0, ',', ' ') ?></div>
        </div>
    </div>

    <div class="table-container" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc;">
                    <th style="padding: 15px; text-align: left;">ID</th>
                    <th>Client</th>
                    <th>Provider</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px; font-weight: bold; color: #64748b;">#<?= $b['booking_id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($b['client_name']) ?></strong><br>
                        <small style="color: #64748b;"><?= htmlspecialchars($b['client_email']) ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($b['provider_name']) ?><br>
                        <small style="color: #64748b;"><?= htmlspecialchars($b['profession']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($b['service_type']) ?></td>
                    <td><?= date('M j, Y', strtotime($b['booking_date'])) ?></td>
                    <td>
                        <strong><?= number_format($b['price'], 0, ',', ' ') ?></strong><br>
                        <small style="color: #64748b;">+<?= number_format($b['platform_fee'], 0, ',', ' ') ?> fee</small>
                    </td>
                    <td>
                        <?php $pay_colors = ['paid' => '#22c55e', 'pending' => '#f59e0b', 'refunded' => '#64748b']; ?>
                        <span style="background: <?= $pay_colors[$b['payment_status']] ?? '#64748b' ?>22; color: <?= $pay_colors[$b['payment_status']] ?? '#64748b' ?>; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                            <?= $b['payment_status'] ?>
                        </span>
                    </td>
                    <td>
                        <?php $colors = ['completed' => '#22c55e', 'confirmed' => '#3b82f6', 'pending' => '#f59e0b', 'cancelled' => '#ef4444', 'disputed' => '#8b5cf6']; ?>
                        <span style="background: <?= $colors[$b['status']] ?? '#64748b' ?>22; color: <?= $colors[$b['status']] ?? '#64748b' ?>; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                            <?= $b['status'] ?>
                        </span>
                    </td>
                    <td style="white-space: nowrap;">
                        <?php if ($b['payment_status'] == 'paid' && $b['status'] == 'confirmed'): ?>
                        <a href="?release=<?= $b['booking_id'] ?>" style="background: #22c55e; color: white; padding: 5px 10px; border-radius: 6px; text-decoration: none; font-size: 11px; margin-right: 5px;" onclick="return confirm('Release payment?')">Release</a>
                        <?php endif; ?>
                        <?php if ($b['payment_status'] == 'paid'): ?>
                        <a href="?refund=<?= $b['booking_id'] ?>" style="background: #ef4444; color: white; padding: 5px 10px; border-radius: 6px; text-decoration: none; font-size: 11px;" onclick="return confirm('Refund?')">Refund</a>
                        <?php endif; ?>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="status" onchange="this.form.submit()" style="padding: 5px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 11px;">
                                <option value="pending" <?= $b['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= $b['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="in_progress" <?= $b['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="completed" <?= $b['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $b['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                <option value="disputed" <?= $b['status'] == 'disputed' ? 'selected' : '' ?>>Disputed</option>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($bookings)): ?>
                <tr><td colspan="9" style="padding: 40px; text-align: center; color: #64748b;">No bookings yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
