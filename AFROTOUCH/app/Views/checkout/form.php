<?php
$page_title = 'Checkout';
?>

<div class="checkout-page">
    <h1>Checkout</h1>
    
    <?php if (empty($items)): ?>
    <div class="empty-cart">
        <i data-lucide="shopping-cart"></i>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added anything yet.</p>
        <a href="/AFROTOUCH/public/products" class="btn-primary">Continue Shopping</a>
    </div>
    <?php else: ?>
    <form action="/AFROTOUCH/public/checkout" method="POST" class="checkout-form">
    <div class="checkout-grid">
        <div class="checkout-form-section">
            <h2>Shipping Information</h2>
            
            <div class="form-group">
                <label for="address">Delivery Address</label>
                <textarea id="address" name="address" rows="3" required placeholder="Enter your full delivery address"></textarea>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required placeholder="+237 6XX XXX XXX">
            </div>
            
            <h2>Payment Method</h2>
            
            <div class="payment-options">
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cash" checked>
                    <div class="payment-card">
                        <i data-lucide="banknote"></i>
                        <div>
                            <strong>Cash on Delivery</strong>
                            <span>Pay when you receive your order</span>
                        </div>
                    </div>
                </label>
                
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="card">
                    <div class="payment-card">
                        <i data-lucide="credit-card"></i>
                        <div>
                            <strong>Pay with Card</strong>
                            <span>Visa, Mastercard, Mobile Money</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>
        
        <div class="checkout-summary">
            <h3>Order Summary</h3>
            
            <div class="order-items">
                <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <img src="<?= $item['product']->image ?? 'https://via.placeholder.com/60x60?text=No+Image' ?>" alt="<?= htmlspecialchars($item['product']->name) ?>">
                    <div class="item-info">
                        <h4><?= htmlspecialchars($item['product']->name) ?></h4>
                        <p>Qty: <?= $item['quantity'] ?></p>
                    </div>
                    <div class="item-price"><?= number_format($item['product']->price * $item['quantity'], 0, ',', ' ') ?> XAF</div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span><?= number_format($total, 0, ',', ' ') ?> XAF</span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>Calculated at delivery</span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span><?= number_format($total, 0, ',', ' ') ?> XAF</span>
            </div>
            
            <button type="submit" class="btn-place-order">Place Order</button>
            <a href="/AFROTOUCH/public/cart" class="back-to-cart">Back to Cart</a>
        </div>
    </div>
    </form>
    <?php endif; ?>
</div>

<style>
.checkout-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: 40px 20px;
}

.checkout-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 40px;
}

.checkout-form-section h2 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #241611;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid #E8E2DE;
    border-radius: 10px;
    font-size: 15px;
    font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #D35400;
}

.payment-options {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-option {
    cursor: pointer;
}

.payment-option input {
    display: none;
}

.payment-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border: 2px solid #E8E2DE;
    border-radius: 12px;
    transition: all 0.2s;
}

.payment-option input:checked + .payment-card {
    border-color: #D35400;
    background: #FFF8F3;
}

.payment-card i {
    width: 36px;
    height: 36px;
    color: #D35400;
}

.payment-card strong {
    display: block;
    color: #241611;
    margin-bottom: 4px;
}

.payment-card span {
    font-size: 13px;
    color: #6D5D57;
}

.checkout-summary {
    background: white;
    border-radius: 16px;
    padding: 25px;
    height: fit-content;
    border: 1px solid #E8E2DE;
}

.checkout-summary h3 {
    font-size: 18px;
    margin-bottom: 20px;
}

.order-items {
    margin-bottom: 20px;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #E8E2DE;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.order-item .item-info {
    flex: 1;
}

.order-item h4 {
    font-size: 14px;
    color: #241611;
    margin-bottom: 4px;
}

.order-item p {
    font-size: 13px;
    color: #6D5D57;
    margin: 0;
}

.order-item .item-price {
    font-weight: 600;
    color: #241611;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    color: #6D5D57;
    font-size: 14px;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding: 20px 0;
    font-weight: 700;
    font-size: 18px;
    color: #241611;
    border-top: 2px solid #E8E2DE;
    margin-top: 10px;
}

.btn-place-order {
    width: 100%;
    background: #D35400;
    color: white;
    padding: 16px;
    border: none;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-place-order:hover {
    background: #b94700;
}

.back-to-cart {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #6D5D57;
    font-size: 14px;
}

@media (max-width: 768px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>lucide.createIcons();</script>
