<div class="my-orders-page">
    <h1>My Orders</h1>
    
    <?php if (empty($orders)): ?>
    <div class="empty-orders">
        <i data-lucide="package"></i>
        <h3>No orders yet</h3>
        <p>When you place orders, they'll appear here.</p>
        <a href="/AFROTOUCH/public/products" class="btn-primary">Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="orders-list">
        <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <div class="order-header">
                <div class="order-info">
                    <span class="order-number">Order #<?= htmlspecialchars($order->order_number) ?></span>
                    <span class="order-date"><?= date('M d, Y', strtotime($order->created_at)) ?></span>
                </div>
                <div class="order-status status-<?= $order->status ?>">
                    <?= ucfirst($order->status) ?>
                </div>
            </div>
            
            <div class="order-details">
                <div class="order-summary">
                    <span class="item-count"><?= count($order->items()) ?> item(s)</span>
                    <span class="order-total"><?= number_format($order->total, 0, ',', ' ') ?> XAF</span>
                </div>
                <a href="/AFROTOUCH/public/orders/<?= $order->id ?>" class="btn-view">View Details</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.my-orders-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
}

.my-orders-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
    color: #241611;
}

.empty-orders {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
}

.empty-orders i {
    width: 64px;
    height: 64px;
    color: #ccc;
    margin-bottom: 20px;
}

.empty-orders h3 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #241611;
}

.empty-orders p {
    color: #6D5D57;
    margin-bottom: 25px;
}

.btn-primary {
    display: inline-block;
    background: #D35400;
    color: white;
    padding: 12px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.order-card {
    background: white;
    border: 1px solid #E8E2DE;
    border-radius: 16px;
    padding: 20px;
    transition: all 0.2s;
}

.order-card:hover {
    border-color: #D35400;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.order-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.order-number {
    font-weight: 600;
    color: #241611;
}

.order-date {
    font-size: 13px;
    color: #6D5D57;
}

.order-status {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.order-status.status-pending {
    background: #FEF3C7;
    color: #92400E;
}

.order-status.status-processing {
    background: #DBEAFE;
    color: #1E40AF;
}

.order-status.status-shipped {
    background: #D1FAE5;
    color: #065F46;
}

.order-status.status-delivered {
    background: #D1FAE5;
    color: #065F46;
}

.order-status.status-cancelled {
    background: #FEE2E2;
    color: #991B1B;
}

.order-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #E8E2DE;
}

.order-summary {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.item-count {
    font-size: 14px;
    color: #6D5D57;
}

.order-total {
    font-weight: 700;
    color: #241611;
    font-size: 18px;
}

.btn-view {
    background: #FAF7F5;
    color: #241611;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: background 0.2s;
}

.btn-view:hover {
    background: #E8E2DE;
}
</style>

<script>lucide.createIcons();</script>
