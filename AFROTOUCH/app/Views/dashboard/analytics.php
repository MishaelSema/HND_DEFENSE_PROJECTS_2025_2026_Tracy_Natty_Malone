<div class="dashboard-page-header">
    <h1>Analytics</h1>
</div>

<?php if (($stats['total_orders'] ?? 0) > 0): ?>
<div class="chart-grid">
    <div class="chart-card">
        <h3>Revenue</h3>
        <canvas id="revenueChart"></canvas>
    </div>
    <div class="chart-card">
        <h3>Orders by Status</h3>
        <canvas id="ordersChart"></canvas>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <label>Total Orders</label>
        <div class="value"><?= $stats['total_orders'] ?? 0 ?></div>
    </div>
    <div class="stat-card">
        <label>Total Revenue</label>
        <div class="value"><?= number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') ?> XAF</div>
    </div>
    <div class="stat-card">
        <label>Avg Order</label>
        <div class="value"><?= $stats['total_orders'] > 0 ? number_format(($stats['total_revenue'] / $stats['total_orders']), 0, ',', ' ') : 0 ?> XAF</div>
    </div>
</div>
<?php else: ?>
<div class="empty-state">
    <i data-lucide="bar-chart-2"></i>
    <h3>No sales yet</h3>
    <p>Add products and start selling to see your analytics!</p>
</div>
<?php endif; ?>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
}

.stat-card label {
    font-size: 13px;
    color: #6D5D57;
}

.stat-card .value {
    font-size: 24px;
    font-weight: 700;
    color: #D35400;
    margin-top: 5px;
}

.chart-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.chart-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
}

.chart-card h3 {
    margin: 0 0 15px;
    font-size: 15px;
    color: #2D1F1A;
}

.empty-state {
    text-align: center;
    padding: 50px;
    background: white;
    border-radius: 12px;
}

.empty-state i {
    width: 48px;
    height: 48px;
    color: #ccc;
    margin-bottom: 15px;
}

.empty-state h3 {
    margin: 0 0 8px;
}

.empty-state p {
    color: #6D5D57;
    margin: 0;
}
</style>

<script>
lucide.createIcons();

<?php if (($stats['total_orders'] ?? 0) > 0): ?>
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets: [{
            label: 'Revenue',
            data: [0,0,0,<?= $stats['total_revenue'] ?? 0 ?>,0,0],
            borderColor: '#D35400',
            backgroundColor: 'rgba(211,84,0,0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending','Processing','Shipped','Delivered'],
        datasets: [{
            data: [<?php echo implode(',', [$pendingCount??0, $processingCount??0, $shippedCount??0, $deliveredCount??0]); ?>],
            backgroundColor: ['#FFF3E0','#E3F2FD','#E8F5E9','#C8E6C9']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
<?php endif; ?>
</script>
