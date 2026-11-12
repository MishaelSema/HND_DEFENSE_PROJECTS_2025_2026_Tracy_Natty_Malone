<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$current_page = 'buyer-orders';
include '../components/layout_start.php';

$buyer_id = $_SESSION['user_id'];
$orders = getBuyerOrders($pdo, $buyer_id);
?>

<div class="page-header">
    <div class="header-content">
        <h1>My Orders</h1>
        <p class="subtitle">Track your orders and view history</p>
    </div>
</div>

<div class="card">
    <?php if (empty($orders)): ?>
    <div style="padding: 4rem 2rem; text-align: center; color: var(--text-muted);">
        <i data-lucide="package" style="width: 64px; height: 64px; margin-bottom: 1rem;"></i>
        <h3>No orders yet</h3>
        <p>Your order history will appear here</p>
        <a href="../products-store.php" class="btn-primary" style="margin-top: 1rem; display: inline-block;">Start Shopping</a>
    </div>
    <?php else: ?>
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Product</th>
                <th>Seller</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                <td><?php echo htmlspecialchars($order['product_name'] ?? 'Unknown'); ?></td>
                <td><?php echo htmlspecialchars($order['store_name'] ?? 'Unknown'); ?></td>
                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                <td><strong><?php echo number_format($order['total'], 0, ',', ' '); ?> FCA</strong></td>
                <td><span class="badge badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<style>
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

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
}
</style>

<?php include '../components/layout_end.php'; ?>