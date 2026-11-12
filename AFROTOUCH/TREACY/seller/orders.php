<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$current_page = 'orders';
include '../components/layout_start.php';

$seller = getSellerByUserId($pdo, $_SESSION['user_id']);
$orders = $seller ? getOrders($pdo, $seller['id']) : [];
?>

<div class="page-header">
    <div class="header-content">
        <h1>Orders</h1>
        <p class="subtitle">Manage and track customer orders</p>
    </div>
</div>

<div class="card">
    <?php if (empty($orders)): ?>
    <div style="padding: 4rem 2rem; text-align: center; color: var(--text-muted);">
        <i data-lucide="shopping-cart" style="width: 64px; height: 64px; margin-bottom: 1rem;"></i>
        <h3>No orders yet</h3>
        <p>Orders from customers will appear here</p>
    </div>
    <?php else: ?>
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Product</th>
                <th>Buyer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                <td><?php echo htmlspecialchars($order['product_name'] ?? 'Unknown'); ?></td>
                <td>
                    <div><?php echo htmlspecialchars($order['buyer_name'] ?? 'Unknown'); ?></div>
                    <small style="color: var(--text-muted);"><?php echo htmlspecialchars($order['buyer_email'] ?? ''); ?></small>
                </td>
                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                <td><strong><?php echo number_format($order['total'], 0, ',', ' '); ?> FCA</strong></td>
                <td><span class="badge badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                <td>
                    <button class="btn-icon-sm" title="View Details"><i data-lucide="eye"></i></button>
                </td>
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

.btn-icon-sm {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: white;
    color: var(--text-muted);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-icon-sm:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}
</style>

<?php include '../components/layout_end.php'; ?>