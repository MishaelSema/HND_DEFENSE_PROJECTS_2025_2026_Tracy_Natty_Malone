<?php
$current_page = 'overview';
include 'components/layout_start.php';
?>

<div class="page-header">
    <div class="header-content">
        <h1>Overview</h1>
        <p class="subtitle">Quick summary of your shop activity today</p>
    </div>
</div>

<div class="overview-stats">
    <div class="overview-stat-main card">
        <label>Total Sales</label>
        <div class="value">1,245,000 XAF</div>
        <div class="trend up">+12.5% from yesterday</div>
    </div>
    <div class="overview-stat-main card">
        <label>Active Orders</label>
        <div class="value">24</div>
        <div class="trend">8 ready to ship</div>
    </div>
    <div class="overview-stat-main card">
        <label>Visitor Count</label>
        <div class="value">482</div>
        <div class="trend up">+5% from yesterday</div>
    </div>
</div>

<div class="overview-content mt-4">
    <div class="card p-0 overflow-hidden">
        <div class="card-header-flex">
            <h3>Recent Orders</h3>
            <a href="orders.php" class="text-primary no-underline text-sm font-bold">View All</a>
        </div>
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
                <tr>
                    <td>AFT-9021</td>
                    <td>Adama Traoré</td>
                    <td><span class="badge badge-processing">Processing</span></td>
                    <td>12,000 XAF</td>
                </tr>
                <tr>
                    <td>AFT-9020</td>
                    <td>Sarah Kone</td>
                    <td><span class="badge badge-pending">Pending</span></td>
                    <td>45,000 XAF</td>
                </tr>
                <tr>
                    <td>AFT-9019</td>
                    <td>Kofi Annan</td>
                    <td><span class="badge badge-shipped">Shipped</span></td>
                    <td>32,500 XAF</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.overview-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.overview-stat-main {
    padding: 2rem;
}

.overview-stat-main .value {
    font-size: 2rem;
    font-weight: 700;
    margin: 0.5rem 0;
}

.trend {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.trend.up { color: #27AE60; font-weight: 600; }

.card-header-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: #FAFAFA;
}

.no-underline { text-decoration: none; }
.text-sm { font-size: 0.85rem; }
.mt-4 { margin-top: 2rem; }
.p-0 { padding: 0 !important; }
</style>

<?php include 'components/layout_end.php'; ?>
