<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$current_page = 'buyer-dashboard';
include '../components/layout_start.php';

$buyer_id = $_SESSION['user_id'];
$orders = getBuyerOrders($pdo, $buyer_id);

$total_orders = count($orders);
$delivered = count(array_filter($orders, fn($o) => $o['status'] === 'delivered'));
$in_transit = count(array_filter($orders, fn($o) => in_array($o['status'], ['processing', 'shipped'])));
?>

<div class="page-header">
    <div class="header-content">
        <h1>My Dashboard</h1>
        <p class="subtitle">Welcome back! Track your orders and manage your account</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-orange"><i data-lucide="shopping-bag"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Orders</span>
            <div class="stat-value"><?php echo $total_orders; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i data-lucide="package"></i></div>
        <div class="stat-info">
            <span class="stat-label">Delivered</span>
            <div class="stat-value"><?php echo $delivered; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i data-lucide="clock"></i></div>
        <div class="stat-info">
            <span class="stat-label">In Transit</span>
            <div class="stat-value"><?php echo $in_transit; ?></div>
        </div>
    </div>
</div>

<div class="charts-row mt-4">
    <div class="chart-container large">
        <div class="chart-header">
            <h3>Spending History</h3>
            <span>Your spending over last 6 months</span>
        </div>
        <canvas id="spendingChart"></canvas>
    </div>
    <div class="chart-container small">
        <div class="chart-header">
            <h3>Purchases by Category</h3>
            <span>Most ordered categories</span>
        </div>
        <canvas id="categoryPieChart"></canvas>
    </div>
</div>

<div class="recent-orders-section">
    <div class="section-header">
        <h2>Recent Orders</h2>
        <a href="orders.php" class="view-all-link">View All Orders →</a>
    </div>
    
    <?php if (empty($orders)): ?>
    <div class="card" style="padding: 4rem 2rem; text-align: center; color: var(--text-muted);">
        <i data-lucide="shopping-cart" style="width: 64px; height: 64px; margin-bottom: 1rem;"></i>
        <h3>No orders yet</h3>
        <p>Start shopping to see your orders here</p>
        <a href="../products-store.php" class="btn-primary" style="margin-top: 1rem; display: inline-block;">Browse Products</a>
    </div>
    <?php else: ?>
    <div class="card">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Product</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($order['product_name'] ?? 'Unknown'); ?></td>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td><strong><?php echo number_format($order['total'], 0, ',', ' '); ?> FCA</strong></td>
                    <td><span class="badge badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-orange { background: #FFF4E5; color: #D35400; }
.icon-blue { background: #EBF5FF; color: #0066CC; }
.icon-green { background: #E9F7EF; color: #188038; }

.stat-label { font-size: 0.85rem; color: var(--text-muted); }
.stat-value { font-size: 1.5rem; font-weight: 700; }

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header h2 { font-family: var(--font-heading); font-size: 1.5rem; }

.view-all-link { color: var(--primary-color); text-decoration: none; font-weight: 600; }

.card { background: white; border-radius: 16px; border: 1px solid var(--border-color); }

.orders-table { width: 100%; border-collapse: collapse; }
.orders-table th { padding: 1rem; background: #FAFAFA; font-size: 0.85rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color); text-align: left; }
.orders-table td { padding: 1rem; border-bottom: 1px solid #F5F5F5; }

.badge { padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 600; }
.badge-pending { background: #F5F5F5; color: #666; }
.badge-processing { background: #FFF4E5; color: #B45D00; }
.badge-shipped { background: #EBF5FF; color: #0066CC; }
.badge-delivered { background: #E9F7EF; color: #188038; }
.badge-canceled { background: #FDECEC; color: #C62828; }

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
}

.charts-row { display: flex; gap: 1.5rem; flex-wrap: wrap; }
.chart-container { background: white; padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border-color); flex: 1; min-width: 300px; }
.chart-container.large { flex: 2; }
.chart-header { margin-bottom: 1.5rem; }
.chart-header h3 { font-size: 1.1rem; margin-bottom: 2px; }
.chart-header span { font-size: 0.85rem; color: var(--text-muted); }
.mt-4 { margin-top: 2rem; }

@media (max-width: 768px) { .charts-row { flex-direction: column; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('spendingChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
        datasets: [{ label: 'Spending', data: [45000, 72000, 38000, 90000, 54000, 27000], borderColor: '#D35400', backgroundColor: 'rgba(211, 84, 0, 0.1)', fill: true, tension: 0.4 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } }
});

new Chart(document.getElementById('categoryPieChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Clothing', 'Jewelry', 'Home Decor', 'Art'],
        datasets: [{ data: [35, 30, 25, 10], backgroundColor: ['#D35500', '#F1C40F', '#2D1F1A', '#E8E2DE'], borderWidth: 0, cutout: '70%' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>

<?php include '../components/layout_end.php'; ?>