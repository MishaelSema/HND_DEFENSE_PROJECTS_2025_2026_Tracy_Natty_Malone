<?php
session_start();
// admin/admin_dashboard.php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
protectAdminPage();

// Count providers
$res = $conn->query("SELECT COUNT(*) AS total FROM providers");
$total_providers = $res->fetch_assoc()['total'];

// Count clients
$res_c = $conn->query("SELECT COUNT(*) AS total FROM clients");
$total_clients = $res_c->fetch_assoc()['total'];

// Count pending applications
$res_pending = $conn->query("SELECT COUNT(*) AS total FROM providers WHERE status='pending'");
$total_pending = $res_pending->fetch_assoc()['total'];

// Count contacts
$res_contacts = $conn->query("SELECT COUNT(*) AS total FROM contacts");
$total_new_contacts = $res_contacts->fetch_assoc()['total'];

// Count reviews
$res = $conn->query("SELECT COUNT(*) AS total FROM reviews");
$total_reviews = $res->fetch_assoc()['total'];

// Average rating
$res = $conn->query("SELECT AVG(rating) AS average FROM reviews");
$average_rating = ($row = $res->fetch_assoc()) && $row['average'] ? round($row['average'], 2) : 0;

// Add columns safely if they don't exist
$prov_cols = $conn->query("SHOW COLUMNS FROM providers")->fetch_all(MYSQLI_ASSOC);
$prov_existing = array_column($prov_cols, 'Field');

if (!in_array('id_verified', $prov_existing)) {
    $conn->query("ALTER TABLE providers ADD COLUMN id_verified TINYINT(1) DEFAULT 0");
}
if (!in_array('id_verified_at', $prov_existing)) {
    $conn->query("ALTER TABLE providers ADD COLUMN id_verified_at TIMESTAMP NULL");
}
if (!in_array('warning_count', $prov_existing)) {
    $conn->query("ALTER TABLE providers ADD COLUMN warning_count INT DEFAULT 0");
}

// Count verified providers
$res_verified = $conn->query("SELECT COUNT(*) AS total FROM providers WHERE id_verified = 1");
$verified_providers = $res_verified->fetch_assoc()['total'];

// Count suspended accounts
$res_suspended = $conn->query("SELECT COUNT(*) AS total FROM providers WHERE status = 'suspended'");
$suspended_count = $res_suspended->fetch_assoc()['total'];

// Count reports
$res_reports = $conn->query("SELECT COUNT(*) AS total FROM reports");
$total_reports = $res_reports->fetch_assoc()['total'];

// Count pending contacts
$res_pending_contacts = $conn->query("SELECT COUNT(*) AS total FROM contacts WHERE status = 'pending'");
$pending_contacts = $res_pending_contacts->fetch_assoc()['total'];

// Count new users this month
$res_new = $conn->query("SELECT (SELECT COUNT(*) FROM providers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) + (SELECT COUNT(*) FROM clients WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS total");
$new_users_month = $res_new->fetch_assoc()['total'];

// Platform earnings
$res_earnings = $conn->query("SELECT SUM(platform_fee) as total FROM payments WHERE status = 'completed'");
$total_earnings = $res_earnings->fetch_assoc()['total'] ?? 0;

// Count completed bookings
$res_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'completed'");
$completed_bookings = $res_bookings->fetch_assoc()['total'];

// Pending payments
$res_pending_pay = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE payment_status = 'pending'");
$pending_payments = $res_pending_pay->fetch_assoc()['total'];

// Recent activity - latest signups
$recent_providers = $conn->query("SELECT name, profession, created_at FROM providers ORDER BY created_at DESC LIMIT 5");
$recent_clients = $conn->query("SELECT name, email, created_at FROM clients ORDER BY created_at DESC LIMIT 5");

include 'includes/admin_header.php';
?>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Professionals</h3>
            <div class="value"><?= $total_providers ?></div>
            <small style="color:#64748b;"><?= $verified_providers ?> verified</small>
        </div>
        <div class="stat-card" style="border-left: 4px solid #10b981;">
            <h3>Platform Earnings</h3>
            <div class="value" style="color: #10b981;"><?= number_format($total_earnings, 0, ',', ' ') ?></div>
            <small style="color:#64748b;">XAF Total</small>
        </div>
        <div class="stat-card">
            <h3>Completed Jobs</h3>
            <div class="value"><?= $completed_bookings ?></div>
            <small style="color:#64748b;">Successful</small>
        </div>
        <div class="stat-card" style="border-left: 4px solid var(--warning);">
            <h3>Pending Approvals</h3>
            <div class="value" style="color: var(--warning);"><?= $total_pending ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid var(--secondary);">
            <h3>New Inquiries</h3>
            <div class="value" style="color: var(--secondary);"><?= $total_new_contacts ?></div>
            <?php if($pending_contacts > 0): ?>
            <small style="color:#991b1b;"><?= $pending_contacts ?> pending</small>
            <?php endif; ?>
        </div>
        <div class="stat-card">
            <h3>Customer Reviews</h3>
            <div class="value"><?= $total_reviews ?></div>
            <small style="color:#64748b; font-weight: 600;">Avg. Rating: <?= $average_rating ?> ★</small>
        </div>
        <div class="stat-card" style="border-left: 4px solid var(--danger);">
            <h3>Suspended</h3>
            <div class="value" style="color: var(--danger);"><?= $suspended_count ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #ef4444;">
            <h3>Reports/Disputes</h3>
            <div class="value" style="color: #ef4444;"><?= $total_reports ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
            <h3>New Users (30d)</h3>
            <div class="value" style="color: #8b5cf6;"><?= $new_users_month ?></div>
        </div>
    </div>

    <div class="table-container" style="padding: 30px; margin-top: 30px;">
        <h3 style="margin-bottom: 20px;">User Growth Dynamics</h3>
        <canvas id="userChart" height="100"></canvas>
    </div>
</div> <!-- End Main Content -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('userChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Clients', 'Professionals'],
        datasets: [{
          label: 'User Distribution',
          data: [<?= $total_clients ?>, <?= $total_providers ?>],
          backgroundColor: ['#007A5E', '#00B894'],
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          title: { display: true, text: 'User Growth Statistics', font: { size: 16 } }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    function openModal(id) { document.getElementById(id).style.display = "flex"; }
    function closeModal(id) { document.getElementById(id).style.display = "none"; }
  </script>
</body>
</html>