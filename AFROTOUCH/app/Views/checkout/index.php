<?php
$page_title = 'Shopping Cart';
?>

<div class="cart-page">
    <h1>Shopping Cart</h1>
    
    <?php if (empty($items)): ?>
    <div class="empty-cart">
        <i data-lucide="shopping-cart"></i>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added anything yet.</p>
        <a href="/AFROTOUCH/public/products" class="btn-primary">Continue Shopping</a>
    </div>
    <?php else: ?>
    <div class="cart-grid">
        <div class="cart-items">
            <?php foreach ($items as $item): ?>
            <div class="cart-item">
                <img src="<?= $item['product']->image ?? 'https://via.placeholder.com/100x100?text=No+Image' ?>" alt="<?= htmlspecialchars($item['product']->name) ?>">
                <div class="item-details">
                    <h3><?= htmlspecialchars($item['product']->name) ?></h3>
                    <p class="item-price"><?= number_format($item['product']->price, 0, ',', ' ') ?> XAF</p>
                </div>
                <div class="item-quantity">
                    <button onclick="updateQty(<?= $item['product']->id ?>, -1)">-</button>
                    <span><?= $item['quantity'] ?></span>
                    <button onclick="updateQty(<?= $item['product']->id ?>, 1)">+</button>
                </div>
                <div class="item-total">
                    <?= number_format($item['product']->price * $item['quantity'], 0, ',', ' ') ?> XAF
                </div>
                <button class="remove-btn" onclick="removeItem(<?= $item['product']->id ?>)">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal (<?= count($items) ?> items)</span>
                <span><?= number_format($total, 0, ',', ' ') ?> XAF</span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>Calculated at checkout</span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span><?= number_format($total, 0, ',', ' ') ?> XAF</span>
            </div>
            <a href="/AFROTOUCH/public/checkout" class="btn-checkout">Proceed to Checkout</a>
            <a href="/AFROTOUCH/public/products" class="continue-shopping">Continue Shopping</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.cart-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.cart-page h1 {
    font-size: 28px;
    margin-bottom: 30px;
}

.empty-cart {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 16px;
}

.empty-cart i {
    width: 64px;
    height: 64px;
    color: #ccc;
    margin-bottom: 20px;
}

.empty-cart h3 {
    font-size: 20px;
    margin-bottom: 10px;
}

.empty-cart p {
    color: #6D5D57;
    margin-bottom: 25px;
}

.cart-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
}

.cart-items {
    background: white;
    border-radius: 16px;
    overflow: hidden;
}

.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto auto;
    gap: 15px;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #E8E2DE;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.item-details h3 {
    font-size: 15px;
    margin-bottom: 5px;
}

.item-price {
    color: #6D5D57;
    font-size: 14px;
}

.item-quantity {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #FAF7F5;
    border-radius: 8px;
    padding: 5px;
}

.item-quantity button {
    width: 28px;
    height: 28px;
    border: none;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
}

.item-quantity span {
    min-width: 30px;
    text-align: center;
    font-weight: 600;
}

.item-total {
    font-weight: 700;
    font-size: 16px;
    min-width: 100px;
    text-align: right;
}

.remove-btn {
    background: none;
    border: none;
    color: #ff6b6b;
    cursor: pointer;
    padding: 8px;
}

.cart-summary {
    background: white;
    border-radius: 16px;
    padding: 25px;
    height: fit-content;
}

.cart-summary h3 {
    font-size: 18px;
    margin-bottom: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #E8E2DE;
    color: #6D5D57;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding: 20px 0;
    font-weight: 700;
    font-size: 18px;
}

.btn-checkout {
    display: block;
    background: #D35400;
    color: white;
    text-align: center;
    padding: 15px;
    border-radius: 50px;
    font-weight: 600;
    margin-bottom: 15px;
    transition: background 0.2s;
}

.btn-checkout:hover {
    background: #b94700;
}

.continue-shopping {
    display: block;
    text-align: center;
    color: #6D5D57;
    font-size: 14px;
}

@media (max-width: 768px) {
    .cart-grid {
        grid-template-columns: 1fr;
    }
    
    .cart-item {
        grid-template-columns: 60px 1fr;
        gap: 10px;
    }
    
    .item-quantity, .item-total, .remove-btn {
        grid-column: 2;
    }
}
</style>

<script>
function updateQty(productId, change) {
    fetch('/AFROTOUCH/public/cart/update', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId + '&change=' + change
    }).then(() => location.reload());
}

function removeItem(productId) {
    if (confirm('Remove this item?')) {
        fetch('/AFROTOUCH/public/cart/remove', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'product_id=' + productId
        }).then(() => location.reload());
    }
}

lucide.createIcons();
</script>
