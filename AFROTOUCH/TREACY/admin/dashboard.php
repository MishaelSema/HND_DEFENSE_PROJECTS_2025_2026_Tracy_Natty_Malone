<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../home.php');
    exit;
}

$current_page = 'admin-dashboard';
include '../components/layout_start.php';

$stats = getAdminStats($pdo);
$recent_sellers = getAllSellers($pdo);
$recent_sellers = array_slice($recent_sellers, 0, 5);

$stmt = $pdo->query("SELECT o.*, u.name as buyer_name, p.name as product_name 
    FROM orders o 
    LEFT JOIN users u ON o.buyer_id = u.id 
    LEFT JOIN products p ON o.product_id = p.id 
    ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="header-content">
        <h1>Admin Dashboard</h1>
        <p class="subtitle">Manage the AfroTouch marketplace</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-orange"><i data-lucide="users"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Sellers</span>
            <div class="stat-value"><?php echo $stats['total_sellers']; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i data-lucide="shopping-cart"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Buyers</span>
            <div class="stat-value"><?php echo $stats['total_buyers']; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i data-lucide="package"></i></div>
        <div class="stat-info">
            <span class="stat-label">Active Products</span>
            <div class="stat-value"><?php echo $stats['total_products']; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-red"><i data-lucide="banknote"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Revenue</span>
            <div class="stat-value"><?php echo number_format($stats['total_revenue'], 0, ',', ' '); ?> FCA</div>
        </div>
    </div>
</div>

<div class="charts-row mt-4">
    <div class="chart-container large">
        <div class="chart-header">
            <h3>Revenue Overview</h3>
            <span>Platform revenue over last 6 months</span>
        </div>
        <canvas id="adminRevenueChart"></canvas>
    </div>
    <div class="chart-container small">
        <div class="chart-header">
            <h3>User Growth</h3>
            <span>Sellers vs Buyers</span>
        </div>
        <canvas id="userGrowthChart"></canvas>
    </div>
</div>

<div class="dashboard-tables">
    <div class="table-section">
        <div class="section-header">
            <h3>Recent Sellers</h3>
            <a href="sellers.php" class="view-all-link">View All →</a>
        </div>
        <?php if (empty($recent_sellers)): ?>
        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
            <p>No sellers yet</p>
        </div>
        <?php else: ?>
        <div class="card">
            <table class="data-table">
                <thead><tr><th>Store</th><th>Owner</th><th>Products</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($recent_sellers as $seller): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($seller['store_name']); ?></td>
                        <td><?php echo htmlspecialchars($seller['name']); ?></td>
                        <td><?php echo $seller['product_count']; ?></td>
                        <td>
                            <?php if ($seller['is_verified']): ?>
                            <span class="badge badge-verified">Verified</span>
                            <?php elseif (in_array($seller['verification_status'], ['pending', 'processing'])): ?>
                            <span class="badge badge-pending">Pending</span>
                            <?php else: ?>
                            <span class="badge" style="background: #F5F5F5; color: #666;">Unverified</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="table-section">
        <div class="section-header">
            <h3>Recent Orders</h3>
            <a href="orders.php" class="view-all-link">View All →</a>
        </div>
        <?php if (empty($recent_orders)): ?>
        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
            <p>No orders yet</p>
        </div>
        <?php else: ?>
        <div class="card">
            <table class="data-table">
                <thead><tr><th>Order</th><th>Buyer</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($order['buyer_name'] ?? 'Unknown'); ?></td>
                        <td><?php echo number_format($order['total'], 0, ',', ' '); ?> FCA</td>
                        <td><span class="badge badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: white; padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1rem; }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.icon-orange { background: #FFF4E5; color: #D35400; }
.icon-blue { background: #EBF5FF; color: #0066CC; }
.icon-green { background: #E9F7EF; color: #188038; }
.icon-red { background: #FDECEC; color: #C62828; }
.stat-label { font-size: 0.85rem; color: var(--text-muted); }
.stat-value { font-size: 1.25rem; font-weight: 700; }

.dashboard-tables { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.table-section { background: white; border-radius: 16px; border: 1px solid var(--border-color); padding: 1.5rem; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.section-header h3 { font-size: 1.1rem; }
.view-all-link { color: var(--primary-color); text-decoration: none; font-weight: 600; font-size: 0.85rem; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 0.75rem; background: #FAFAFA; font-size: 0.8rem; color: var(--text-muted); text-align: left; border-bottom: 1px solid var(--border-color); }
.data-table td { padding: 0.75rem; border-bottom: 1px solid #F5F5F5; font-size: 0.85rem; }

.badge { padding: 4px 10px; border-radius: 100px; font-size: 0.7rem; font-weight: 600; }
.badge-verified { background: #E9F7EF; color: #188038; }
.badge-pending { background: #FFF4E5; color: #B45D00; }
.badge-shipped { background: #EBF5FF; color: #0066CC; }
.badge-processing { background: #FFF4E5; color: #B45D00; }
.badge-delivered { background: #E9F7EF; color: #188038; }

.mt-4 { margin-top: 2rem; }

.charts-row { display: flex; gap: 1.5rem; flex-wrap: wrap; }
.chart-container { background: white; padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border-color); flex: 1; min-width: 300px; }
.chart-container.large { flex: 2; }
.chart-header { margin-bottom: 1.5rem; }
.chart-header h3 { font-size: 1.1rem; margin-bottom: 2px; }
.chart-header span { font-size: 0.85rem; color: var(--text-muted); }

@media (max-width: 1024px) { .dashboard-tables { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .charts-row { flex-direction: column; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('adminRevenueChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
        datasets: [{ label: 'Revenue', data: [1800000, 2400000, 2100000, 3200000, 2800000, 3500000], backgroundColor: '#D35400', borderRadius: 8 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } } }
});

new Chart(document.getElementById('userGrowthChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Sellers', 'Buyers'],
        datasets: [{ data: [<?php echo $stats['total_sellers']; ?>, <?php echo $stats['total_buyers']; ?>], backgroundColor: ['#D35400', '#F1C40F'], borderWidth: 0, cutout: '70%' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>

<?php include '../components/layout_end.php'; ?>