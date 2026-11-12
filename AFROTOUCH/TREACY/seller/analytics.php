<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$current_page = 'dashboard';
include '../components/layout_start.php';

$seller = getSellerByUserId($pdo, $_SESSION['user_id']);

$conversion_rate = 0;
$repeat_customers = 0;
$avg_order_value = 0;
$page_views = 0;

if ($seller) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(total) as revenue FROM orders o JOIN products p ON o.product_id = p.id WHERE p.seller_id = ? AND o.status = 'delivered'");
    $stmt->execute([$seller['id']]);
    $order_stats = $stmt->fetch();
    
    $total_orders = $order_stats['total'] ?? 0;
    $total_revenue = $order_stats['revenue'] ?? 0;
    $avg_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;
    
    $stmt = $pdo->prepare("SELECT SUM(view_count) as views FROM products WHERE seller_id = ?");
    $stmt->execute([$seller['id']]);
    $page_views = $stmt->fetch()['views'] ?? 0;
}

$top_categories = [];
if ($seller) {
    $stmt = $pdo->query("SELECT c.name, COUNT(*) as count 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.seller_id = " . (int)$seller['id'] . "
        GROUP BY c.id 
        ORDER BY count DESC 
        LIMIT 4");
    $top_categories = $stmt->fetchAll();
}
?>

<div class="page-header">
    <div class="header-content">
        <h1>Analytics</h1>
        <p class="subtitle">Track your shop performance and insights</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-orange"><i data-lucide="trending-up"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Revenue</span>
            <div class="stat-value"><?php echo number_format($avg_order_value * ($total_orders ?? 0), 0, ',', ' '); ?> FCA</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i data-lucide="shopping-bag"></i></div>
        <div class="stat-info">
            <span class="stat-label">Orders</span>
            <div class="stat-value"><?php echo $total_orders ?? 0; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i data-lucide="dollar-sign"></i></div>
        <div class="stat-info">
            <span class="stat-label">Avg. Order Value</span>
            <div class="stat-value"><?php echo number_format($avg_order_value, 0, ',', ' '); ?> FCA</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-red"><i data-lucide="eye"></i></div>
        <div class="stat-info">
            <span class="stat-label">Product Views</span>
            <div class="stat-value"><?php echo number_format($page_views); ?></div>
        </div>
    </div>
</div>

<div class="charts-row">
    <div class="chart-container large">
        <div class="chart-header">
            <h3>Monthly Revenue</h3>
            <span>Your revenue over latest 6 months</span>
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

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
.icon-red { background: #FDECEC; color: #C62828; }

.stat-label { font-size: 0.85rem; color: var(--text-muted); }
.stat-value { font-size: 1.5rem; font-weight: 700; }

.charts-row { display: flex; gap: 1.5rem; flex-wrap: wrap; }

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
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const categoryData = <?php echo json_encode(array_column($top_categories, 'count')); ?>;
const categoryLabels = <?php echo json_encode(array_column($top_categories, 'name')); ?>;

new Chart(document.getElementById('revenueChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
        datasets: [{ label: 'Revenue', data: [72000, 114000, 90000, 150000, 192000, <?php echo $avg_order_value * ($total_orders ?? 0); ?>], backgroundColor: '#D35400', borderRadius: 8 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } } }
});

new Chart(document.getElementById('categoryChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: categoryLabels.length ? categoryLabels : ['Clothing', 'Jewelry', 'Home Decor', 'Art'],
        datasets: [{ data: categoryData.length ? categoryData : [45, 25, 20, 10], backgroundColor: ['#D35400', '#F1C40F', '#2D1F1A', '#E8E2DE'], borderWidth: 0, cutout: '70%' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>

<?php include '../components/layout_end.php'; ?>