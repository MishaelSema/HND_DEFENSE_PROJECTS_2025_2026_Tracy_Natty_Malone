<div class="dashboard-page-header">
    <h1>Admin Dashboard</h1>
    <p>Overview of the AfroTouch marketplace</p>
</div>

<div class="overview-stats">
    <div class="overview-stat card">
        <label>Total Users</label>
        <div class="value"><?= $stats['total_users'] ?? 0 ?></div>
    </div>
    <div class="overview-stat card">
        <label>Sellers</label>
        <div class="value"><?= $stats['total_sellers'] ?? 0 ?></div>
    </div>
    <div class="overview-stat card">
        <label>Buyers</label>
        <div class="value"><?= $stats['total_buyers'] ?? 0 ?></div>
    </div>
    <div class="overview-stat card">
        <label>Products</label>
        <div class="value"><?= $stats['total_products'] ?? 0 ?></div>
    </div>
    <div class="overview-stat card">
        <label>Orders</label>
        <div class="value"><?= $stats['total_orders'] ?? 0 ?></div>
    </div>
    <div class="overview-stat card">
        <label>Total Revenue</label>
        <div class="value"><?= number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') ?> XAF</div>
    </div>
</div>

<style>
.overview-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.overview-stat {
    padding: 24px;
    background: white;
    border-radius: 12px;
}

.overview-stat label {
    font-size: 14px;
    color: #6D5D57;
}

.overview-stat .value {
    font-size: 28px;
    font-weight: 700;
    margin: 8px 0 0;
    color: #2D1F1A;
}
</style>

<script>
lucide.createIcons();
</script>
