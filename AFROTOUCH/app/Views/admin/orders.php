<?php
$page_title = 'Orders';
$current_page = 'orders';
?>

<h1>All Orders</h1>

<div class="table-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Buyer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= htmlspecialchars($order->order_number) ?></td>
                <td><?= htmlspecialchars($order->buyer_name ?? 'Unknown') ?></td>
                <td><?= number_format($order->total, 0, ',', ' ') ?> XAF</td>
                <td>
                    <span class="admin-badge admin-badge-<?= $order->status === 'delivered' ? 'seller' : 'buyer' ?>">
                        <?= ucfirst($order->status) ?>
                    </span>
                </td>
                <td><?= ucfirst($order->payment_status) ?></td>
                <td><?= date('M d, Y', strtotime($order->created_at)) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.table-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
</style>
