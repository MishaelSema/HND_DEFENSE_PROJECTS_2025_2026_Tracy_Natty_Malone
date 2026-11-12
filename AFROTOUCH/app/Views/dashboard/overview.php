<div class="dashboard-page-header">
    <h1>Welcome back, <?= $seller_name ?? 'Seller' ?>!</h1>
    <p>Here's your shop summary</p>
</div>

<div class="overview-stats">
    <div class="overview-stat card">
        <label>Total Revenue</label>
        <div class="value"><?= number_format($totalRevenue ?? 0, 0, ',', ' ') ?> XAF</div>
        <?php if (($totalRevenue ?? 0) > 0): ?>
        <div class="trend">From <?= $totalOrders ?? 0 ?> orders</div>
        <?php else: ?>
        <div class="trend">No sales yet</div>
        <?php endif; ?>
    </div>
    <div class="overview-stat card">
        <label>Orders</label>
        <div class="value"><?= $totalOrders ?? 0 ?></div>
        <?php 
        $pending = 0;
        foreach ($recentOrders as $o) { if ($o->status === 'pending') $pending++; }
        ?>
        <?php if ($pending > 0): ?>
        <div class="trend" style="color: #E65100;"><?= $pending ?> pending</div>
        <?php else: ?>
        <div class="trend">All fulfilled</div>
        <?php endif; ?>
    </div>
    <div class="overview-stat card">
        <label>Products</label>
        <div class="value"><?= $totalProducts ?? 0 ?></div>
        <?php 
        $lowStock = 0;
        foreach ($products ?? [] as $p) { if ($p->stock < 5) $lowStock++; }
        ?>
        <?php if ($lowStock > 0): ?>
        <div class="trend" style="color: #E65100;"><?= $lowStock ?> low stock</div>
        <?php else: ?>
        <div class="trend">Stock healthy</div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($recentOrders)): ?>
<h3 style="margin-bottom: 15px;">Recent Orders</h3>
<div class="card p-0">
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Buyer</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $order): ?>
            <tr>
                <td style="font-weight: 600; color: #D35400;"><?= $order->order_number ?></td>
                <td><?= $order->buyer_name ?? 'Unknown' ?></td>
                <td><?= date('M d, Y', strtotime($order->created_at ?? 'now')) ?></td>
                <td><span class="badge badge-<?= $order->status ?>"><?= ucfirst($order->status) ?></span></td>
                <td style="font-weight: 600;"><?= number_format($order->total, 0, ',', ' ') ?> XAF</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<style>
.dashboard-page-header {
    margin-bottom: 30px;
}

.dashboard-page-header h1 {
    font-size: 28px;
    margin: 0 0 5px 0;
}

.dashboard-page-header p {
    color: #6D5D57;
    margin: 0;
}

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
    margin: 8px 0;
    color: #2D1F1A;
}

.trend {
    font-size: 13px;
    color: #6D5D57;
}

.card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

.p-0 { padding: 0 !important; }

.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th {
    padding: 15px 20px;
    text-align: left;
    font-size: 13px;
    color: #6D5D57;
    font-weight: 500;
    background: #fafafa;
    border-bottom: 1px solid #E8E2DE;
}

.orders-table td {
    padding: 15px 20px;
    border-bottom: 1px solid #E8E2DE;
}

.orders-table tr:last-child td {
    border-bottom: none;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-pending { background: #FFF3E0; color: #E65100; }
.badge-processing { background: #E3F2FD; color: #1565C0; }
.badge-shipped { background: #E8F5E9; color: #2E7D32; }
.badge-delivered { background: #E9F7EF; color: #188038; }
.badge-cancelled { background: #FFEBEE; color: #C62828; }
</style>

<script>
lucide.createIcons();
</script>
