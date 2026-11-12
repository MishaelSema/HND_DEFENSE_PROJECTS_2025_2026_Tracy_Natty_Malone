<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$current_page = 'overview';
include '../components/layout_start.php';

$seller = getSellerByUserId($pdo, $_SESSION['user_id']);
$stats = $seller ? getSellerStats($pdo, $seller['id']) : ['products' => 0, 'orders' => 0, 'revenue' => 0];

$recent_orders = [];
if ($seller) {
    $recent_orders = getOrders($pdo, $seller['id'], null, null);
    $recent_orders = array_slice($recent_orders, 0, 5);
}
?>

<div class="page-header">
    <div class="header-content">
        <h1>Overview</h1>
        <p class="subtitle">Quick summary of your shop activity today</p>
    </div>
</div>

<?php if (!$seller): ?>
<div class="alert-box alert-warning">
    <i data-lucide="alert-triangle"></i>
    <div>
        <strong>Shop not set up yet</strong>
        <p>Complete your shop setup to start selling on AfroTouch.</p>
        <a href="settings.php" class="btn-primary" style="margin-top: 0.5rem; display: inline-block;">Set Up Shop</a>
    </div>
</div>
<?php elseif (in_array($seller['verification_status'], ['pending', 'processing'])): ?>
<div class="alert-box alert-info">
    <i data-lucide="clock"></i>
    <div>
        <strong>Verification Processing</strong>
        <p>Your shop is pending verification. Complete verification to be visible on the main site.</p>
        <a href="../verify.php" class="btn-primary" style="margin-top: 0.5rem; display: inline-block;">Complete Verification</a>
    </div>
</div>
<?php elseif (!$seller['is_verified']): ?>
<div class="alert-box alert-warning">
    <i data-lucide="shield"></i>
    <div>
        <strong>Verification required</strong>
        <p>Get verified to increase trust and visibility on AfroTouch.</p>
        <a href="../verify.php" class="btn-primary" style="margin-top: 0.5rem; display: inline-block;">Get Verified</a>
    </div>
</div>
<?php endif; ?>

<div class="overview-stats">
    <div class="overview-stat-main card">
        <label>Total Sales</label>
        <div class="value"><?php echo number_format($stats['revenue'], 0, ',', ' '); ?> FCA</div>
        <div class="trend">From delivered orders</div>
    </div>
    <div class="overview-stat-main card">
        <label>Active Orders</label>
        <div class="value"><?php echo $stats['orders']; ?></div>
        <div class="trend">Total orders received</div>
    </div>
    <div class="overview-stat-main card">
        <label>Products</label>
        <div class="value"><?php echo $stats['products']; ?></div>
        <div class="trend">Active listings</div>
    </div>
</div>

<div class="charts-row">
    <div class="chart-container large">
        <div class="chart-header">
            <h3>Monthly Revenue</h3>
            <span>Insights over latest 6 months</span>
        </div>
        <canvas id="revenueChart"></canvas>
    </div>
    <div class="chart-container small">
        <div class="chart-header">
            <h3>Sales by Category</h3>
            <span>Most popular categories</span>
        </div>
        <canvas id="categoryChart"></canvas>
    </div>
</div>

<div class="overview-content mt-4">
    <div class="card p-0 overflow-hidden">
        <div class="card-header-flex">
            <h3>Recent Orders</h3>
            <a href="orders.php" class="text-primary no-underline text-sm font-bold">View All</a>
        </div>
        <?php if (empty($recent_orders)): ?>
        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
            <i data-lucide="shopping-cart" style="width: 48px; height: 48px; margin-bottom: 1rem;"></i>
            <p>No orders yet</p>
        </div>
        <?php else: ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Buyer</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td><?php echo htmlspecialchars($order['buyer_name'] ?? 'Unknown'); ?></td>
                    <td><span class="badge badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                    <td><?php echo number_format($order['total'], 0, ',', ' '); ?> FCA</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<style>
.alert { padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start; }
.alert-warning { background: #FFF4E5; color: #B45D00; }
.alert i { flex-shrink: 0; }
.alert strong { display: block; margin-bottom: 0.25rem; }
.alert p { margin: 0; font-size: 0.9rem; opacity: 0.9; }

.overview-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.overview-stat-main {
    padding: 1.5rem;
}

.overview-stat-main label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.overview-stat-main .value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.trend {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.trend.up { color: #188038; }

.card-header-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.card-header-flex h3 {
    font-family: var(--font-heading);
    font-size: 1.25rem;
}

.no-underline { text-decoration: none; }
.text-sm { font-size: 0.85rem; }
.font-bold { font-weight: 600; }
.text-primary { color: var(--primary-color); }
.mt-4 { margin-top: 2rem; }
.p-0 { padding: 0; }
.overflow-hidden { overflow: hidden; }

.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th,
.orders-table td {
    padding: 1rem 1.5rem;
    text-align: left;
}

.orders-table th {
    background: #FAFAFA;
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: 500;
    border-bottom: 1px solid var(--border-color);
}

.orders-table td {
    border-bottom: 1px solid #F5F5F5;
}

.badge {
    padding: 4px 10px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-pending { background: #F5F5F5; color: #666; }
.badge-processing { background: #FFF4E5; color: #B45D00; }
.badge-shipped { background: #EBF5FF; color: #0066CC; }
.badge-delivered { background: #E9F7EF; color: #188038; }
.badge-canceled { background: #FDECEC; color: #C62828; }

.charts-row { display: flex; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 2rem; }

.chart-container {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    flex: 1;
    min-width: 300px;
}

.chart-container.large { flex: 2; }
.chart-header { margin-bottom: 1.5rem; }
.chart-header h3 { font-size: 1.1rem; margin-bottom: 2px; }
.chart-header span { font-size: 0.85rem; color: var(--text-muted); }

@media (max-width: 768px) {
    .charts-row { flex-direction: column; }
}

.alert-box {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 16px;
    margin-bottom: 2rem;
}

.alert-box svg { flex-shrink: 0; margin-top: 0.25rem; }

.alert-warning {
    background: #FFF4E5;
    color: #B45D00;
}

.alert-info {
    background: #EBF5FF;
    color: #0066CC;
}

.alert-box strong { display: block; margin-bottom: 0.25rem; }
.alert-box p { margin: 0; font-size: 0.9rem; opacity: 0.9; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('revenueChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
        datasets: [{ label: 'Revenue', data: [72000, 114000, 90000, 150000, 192000, <?php echo $stats['revenue']; ?>], backgroundColor: '#D35400', borderRadius: 8 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } } }
});

new Chart(document.getElementById('categoryChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Clothing', 'Jewelry', 'Home Decor', 'Art'],
        datasets: [{ data: [45, 25, 20, 10], backgroundColor: ['#D35400', '#F1C40F', '#2D1F1A', '#E8E2DE'], borderWidth: 0, cutout: '70%' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>

<?php include '../components/layout_end.php'; ?>