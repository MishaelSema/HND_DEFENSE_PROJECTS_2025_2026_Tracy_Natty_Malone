<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = 'Shopping Cart';
?>

<?php include 'components/store-layout_start.php'; ?>

<section class="cart-section">
    <h1>Shopping Cart</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
    <div class="empty-cart">
        <i data-lucide="shopping-cart"></i>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added anything to your cart yet.</p>
        <a href="home.php" class="btn-shop">Start Shopping</a>
    </div>
    <?php else: ?>
    
    <div class="cart-grid">
        <div class="cart-items-list">
            <?php
            include 'db.php';
            
            $ids = array_keys($_SESSION['cart']);
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare("SELECT p.*, s.store_name, s.is_verified FROM products p LEFT JOIN sellers s ON p.seller_id = s.id WHERE p.id IN ($placeholders) AND p.status = 'active'");
                $stmt->execute($ids);
                $products = $stmt->fetchAll();
            }
            
            $cart_total = 0;
            ?>
            
            <?php foreach ($products as $product): 
                $quantity = $_SESSION['cart'][$product['id']];
                $subtotal = $product['price'] * $quantity;
                $cart_total += $subtotal;
            ?>
            <div class="cart-item" data-product-id="<?php echo $product['id']; ?>">
                <div class="item-image">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                
                <div class="item-info">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="store-name">
                        <?php if ($product['is_verified']): ?><i data-lucide="badge-check"></i><?php endif; ?>
                        <?php echo htmlspecialchars($product['store_name']); ?>
                    </p>
                    <p class="category"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
                </div>
                
                <div class="item-quantity">
                    <button class="qty-btn" onclick="updateQuantity(<?php echo $product['id']; ?>, -1)">-</button>
                    <span><?php echo $quantity; ?></span>
                    <button class="qty-btn" onclick="updateQuantity(<?php echo $product['id']; ?>, 1)">+</button>
                </div>
                
                <div class="item-price">
                    <?php echo number_format($subtotal, 0); ?> FCA
                </div>
                
                <button class="btn-remove" onclick="removeFromCart(<?php echo $product['id']; ?>)">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary">
            <h3>Order Summary</h3>
            
            <div class="summary-details">
                <div class="summary-row">
                    <span>Subtotal (<?php echo array_sum($_SESSION['cart']); ?> items)</span>
                    <span><?php echo number_format($cart_total, 0); ?> FCA</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>Calculated at checkout</span>
                </div>
            </div>
            
            <div class="summary-total">
                <span>Total</span>
                <span><?php echo number_format($cart_total, 0); ?> FCA</span>
            </div>
            
            <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
            <a href="home.php" class="btn-continue">Continue Shopping</a>
        </div>
    </div>
    <?php endif; ?>
</section>

<style>
.cart-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.cart-section h1 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin-bottom: 2rem;
}

.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 20px;
    border: 1px solid var(--border-color);
}

.empty-cart i {
    color: var(--text-muted);
    margin-bottom: 1.5rem;
}

.empty-cart h2 {
    font-family: var(--font-heading);
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}

.empty-cart p {
    color: var(--text-muted);
    margin-bottom: 2rem;
}

.btn-shop {
    display: inline-block;
    background: var(--accent-orange);
    color: white;
    padding: 1rem 2rem;
    border-radius: 100px;
    text-decoration: none;
    font-weight: 600;
}

.cart-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 2rem;
    align-items: start;
}

.cart-items-list {
    background: white;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.cart-item {
    display: grid;
    grid-template-columns: 100px 1fr auto auto auto;
    gap: 1.5rem;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.cart-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 100px;
    height: 100px;
    background: #f5f5f5;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.item-image img {
    width: 80px;
    height: 80px;
    object-fit: contain;
}

.item-info h3 {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.store-name {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 0.25rem;
}

.store-name i {
    color: #188038;
}

.category {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.item-quantity {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #f5f5f5;
    border-radius: 8px;
    padding: 0.25rem;
}

.qty-btn {
    width: 32px;
    height: 32px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    cursor: pointer;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.qty-btn:hover {
    background: var(--accent-orange);
    color: white;
    border-color: var(--accent-orange);
}

.item-quantity span {
    min-width: 30px;
    text-align: center;
    font-weight: 600;
}

.item-price {
    font-weight: 700;
    font-size: 1.1rem;
    min-width: 100px;
    text-align: right;
}

.btn-remove {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 0.5rem;
    transition: color 0.2s;
}

.btn-remove:hover {
    color: #C62828;
}

.cart-summary {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    position: sticky;
    top: 100px;
}

.cart-summary h3 {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
}

.summary-details {
    margin-bottom: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    color: var(--text-muted);
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-top: 1px solid var(--border-color);
    font-size: 1.25rem;
    font-weight: 700;
}

.btn-checkout {
    display: block;
    width: 100%;
    background: var(--accent-orange);
    color: white;
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 1rem;
    transition: transform 0.2s;
}

.btn-checkout:hover {
    transform: scale(1.02);
}

.btn-continue {
    display: block;
    width: 100%;
    background: transparent;
    color: var(--text-dark);
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    font-weight: 600;
    border: 2px solid var(--border-color);
}

.btn-continue:hover {
    border-color: var(--accent-orange);
    color: var(--accent-orange);
}

@media (max-width: 1024px) {
    .cart-grid {
        grid-template-columns: 1fr;
    }
    .cart-summary {
        position: static;
    }
}

@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 80px 1fr;
        gap: 1rem;
    }
    .item-quantity, .item-price, .btn-remove {
        grid-column: 2;
    }
    .item-image {
        width: 80px;
        height: 80px;
    }
    .item-image img {
        width: 60px;
        height: 60px;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<script>
function updateQuantity(productId, change) {
    fetch('api/update-cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId + '&change=' + change
    }).then(() => location.reload());
}

function removeFromCart(productId) {
    if (confirm('Remove this item from cart?')) {
        fetch('api/remove-from-cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'product_id=' + productId
        }).then(() => location.reload());
    }
}
</script>

<?php include 'components/store-layout_end.php'; ?>
