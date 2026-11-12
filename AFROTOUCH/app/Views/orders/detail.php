<div class="order-detail-page">
    <a href="/AFROTOUCH/public/orders" class="back-link">
        <i data-lucide="arrow-left"></i> Back to Orders
    </a>
    
    <div class="order-header">
        <div class="order-title">
            <h1>Order #<?= htmlspecialchars($order->order_number) ?></h1>
            <div class="order-status status-<?= $order->status ?>">
                <?= ucfirst($order->status) ?>
            </div>
        </div>
        <div class="order-date">Placed on <?= date('F d, Y', strtotime($order->created_at)) ?></div>
    </div>
    
    <div class="order-grid">
        <div class="order-items-section">
            <h2>Order Items</h2>
            <div class="items-list">
                <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <img src="<?= $item->product()->image ?? 'https://via.placeholder.com/80x80?text=No+Image' ?>" alt="<?= htmlspecialchars($item->product()->name ?? 'Product') ?>">
                    <div class="item-details">
                        <h3><?= htmlspecialchars($item->product()->name ?? 'Product') ?></h3>
                        <p>Qty: <?= $item->quantity ?> × <?= number_format($item->price, 0, ',', ' ') ?> XAF</p>
                    </div>
                    <div class="item-total">
                        <?= number_format($item->price * $item->quantity, 0, ',', ' ') ?> XAF
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="order-sidebar">
            <div class="info-card">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span><?= number_format($order->total, 0, ',', ' ') ?> XAF</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>To be calculated</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span><?= number_format($order->total, 0, ',', ' ') ?> XAF</span>
                </div>
            </div>
            
            <div class="info-card">
                <h3>Shipping Address</h3>
                <p><?= nl2br(htmlspecialchars($order->shipping_address ?? 'Not provided')) ?></p>
            </div>
            
            <div class="info-card">
                <h3>Payment</h3>
                <p><strong>Method:</strong> <?= ucfirst(str_replace('_', ' ', $order->payment_method ?? 'Not specified')) ?></p>
                <p><strong>Status:</strong> <span class="payment-status <?= $order->payment_status ?>"><?= ucfirst($order->payment_status) ?></span></p>
            </div>
        </div>
    </div>
</div>

<style>
.order-detail-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 40px 20px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #6D5D57;
    text-decoration: none;
    margin-bottom: 25px;
    font-size: 14px;
}

.back-link:hover {
    color: #D35400;
}

.back-link i {
    width: 18px;
    height: 18px;
}

.order-header {
    margin-bottom: 30px;
}

.order-title {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
}

.order-title h1 {
    font-size: 28px;
    color: #241611;
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

.order-date {
    color: #6D5D57;
    font-size: 14px;
}

.order-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
}

.order-items-section h2 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #241611;
}

.items-list {
    background: white;
    border-radius: 16px;
    border: 1px solid #E8E2DE;
    overflow: hidden;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border-bottom: 1px solid #E8E2DE;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
}

.item-details {
    flex: 1;
}

.item-details h3 {
    font-size: 16px;
    color: #241611;
    margin-bottom: 5px;
}

.item-details p {
    color: #6D5D57;
    font-size: 14px;
    margin: 0;
}

.item-total {
    font-weight: 700;
    font-size: 16px;
    color: #241611;
}

.order-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.info-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #E8E2DE;
    padding: 20px;
}

.info-card h3 {
    font-size: 16px;
    color: #241611;
    margin-bottom: 15px;
}

.info-card p {
    color: #6D5D57;
    font-size: 14px;
    margin: 0;
    line-height: 1.6;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    color: #6D5D57;
    font-size: 14px;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    font-weight: 700;
    font-size: 18px;
    color: #241611;
    border-top: 1px solid #E8E2DE;
    margin-top: 10px;
}

.payment-status {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.payment-status.paid {
    background: #D1FAE5;
    color: #065F46;
}

.payment-status.pending {
    background: #FEF3C7;
    color: #92400E;
}

@media (max-width: 768px) {
    .order-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>lucide.createIcons();</script>
