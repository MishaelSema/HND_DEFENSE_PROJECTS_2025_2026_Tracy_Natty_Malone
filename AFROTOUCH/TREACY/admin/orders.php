<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../home.php');
    exit;
}

$current_page = 'admin-orders';
include '../components/layout_start.php';

$stmt = $pdo->query("SELECT o.*, u.name as buyer_name, u.email as buyer_email, p.name as product_name, s.store_name
    FROM orders o
    LEFT JOIN users u ON o.buyer_id = u.id
    LEFT JOIN products p ON o.product_id = p.id
    LEFT JOIN sellers s ON p.seller_id = s.id
    ORDER BY o.created_at DESC");
$orders = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="header-content">
        <h1>All Orders</h1>
        <p class="subtitle">Manage all platform orders</p>
    </div>
</div>

<div class="card">
    <?php if (empty($orders)): ?>
    <div style="padding: 3rem; text-align: center; color: var(--text-muted);">
        No orders yet
    </div>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Buyer</th>
                <th>Product</th>
                <th>Seller</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                <td>
                    <div><?php echo htmlspecialchars($order['buyer_name'] ?? 'Unknown'); ?></div>
                    <small><?php echo htmlspecialchars($order['buyer_email'] ?? ''); ?></small>
                </td>
                <td><?php echo htmlspecialchars($order['product_name'] ?? 'Unknown'); ?></td>
                <td><?php echo htmlspecialchars($order['store_name'] ?? 'Unknown'); ?></td>
                <td><strong><?php echo number_format($order['total'], 0, ',', ' '); ?> FCA</strong></td>
                <td><span class="badge badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 1rem; text-align: left; }
.data-table th { background: #FAFAFA; font-size: 0.8rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color); }
.data-table td { border-bottom: 1px solid #F5F5F5; }

.badge { padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 600; }
.badge-pending { background: #F5F5F5; color: #666; }
.badge-processing { background: #FFF4E5; color: #B45D00; }
.badge-shipped { background: #EBF5FF; color: #0066CC; }
.badge-delivered { background: #E9F7EF; color: #188038; }
.badge-canceled { background: #FDECEC; color: #C62828; }
</style>

<?php include '../components/layout_end.php'; ?>