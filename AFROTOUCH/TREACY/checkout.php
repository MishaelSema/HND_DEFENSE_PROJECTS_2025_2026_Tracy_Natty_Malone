<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

$cart_items = [];
$cart_total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $pdo->prepare("SELECT p.*, s.store_name FROM products p LEFT JOIN sellers s ON p.seller_id = s.id WHERE p.id IN ($placeholders) AND p.status = 'active'");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $product['quantity'] = $_SESSION['cart'][$product['id']];
        $product['subtotal'] = $product['price'] * $product['quantity'];
        $cart_items[] = $product;
        $cart_total += $product['subtotal'];
    }
}

$buyer_id = $_SESSION['user_id'] ?? null;
$buyer_name = $_SESSION['user_name'] ?? '';
$buyer_email = $_SESSION['user_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cart_items)) {
    if (!$buyer_id) {
        header('Location: login.php?redirect=checkout.php');
        exit;
    }
    
    $errors = [];
    $shipping_address = $_POST['address'] . ', ' . $_POST['city'] . ', ' . $_POST['country'];
    $payment_method = $_POST['payment_method'] ?? 'card';
    $phone = $_POST['phone'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (empty($name) || empty($email) || empty($phone) || empty($_POST['address']) || empty($_POST['city']) || empty($_POST['country'])) {
        $errors[] = 'Please fill in all required fields';
    }
    
    if (empty($errors)) {
        $pdo->beginTransaction();
        
        try {
            foreach ($cart_items as $item) {
                $order_number = 'AFT-' . strtoupper(uniqid());
                $total = $item['price'] * $item['quantity'];
                
                $stmt = $pdo->prepare("INSERT INTO orders (order_number, buyer_id, product_id, quantity, unit_price, total, shipping_address, shipping_city, shipping_country, payment_method, name, email, phone, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
                $stmt->execute([
                    $order_number,
                    $buyer_id,
                    $item['id'],
                    $item['quantity'],
                    $item['price'],
                    $total,
                    $_POST['address'],
                    $_POST['city'],
                    $_POST['country'],
                    $payment_method,
                    $name,
                    $email,
                    $phone
                ]);
                
                $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['id']]);
            }
            
            $pdo->commit();
            unset($_SESSION['cart']);
            
            header('Location: buyer/orders.php?success=1');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Failed to place order. Please try again.';
        }
    }
}
?>

<?php include 'components/store-layout_start.php'; ?>

<section class="checkout-section">
    <h1>Checkout</h1>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-error" style="margin-bottom: 2rem;">
        <?php foreach ($errors as $error): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <?php if (empty($cart_items)): ?>
    <div class="alert alert-error">
        <p>Your cart is empty. <a href="products-store.php">Continue shopping</a></p>
    </div>
    <?php else: ?>
    
    <div class="checkout-grid">
        <div class="checkout-form-section">
            <form class="checkout-form" id="checkoutForm" method="POST">
                <div class="form-section">
                    <h3>Contact Information</h3>
                    
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($buyer_name); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($buyer_email); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" required>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Shipping Address</h3>
                    
                    <div class="form-group">
                        <label>Address *</label>
                        <input type="text" name="address" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" required>
                        </div>
                        <div class="form-group">
                            <label>Country *</label>
                            <select name="country" required>
                                <option value="">Select Country</option>
                                <option value="cameroon">Cameroon</option>
                                <option value="ghana">Ghana</option>
                                <option value="nigeria">Nigeria</option>
                                <option value="kenya">Kenya</option>
                                <option value="south-africa">South Africa</option>
                                <option value="usa">United States</option>
                                <option value="uk">United Kingdom</option>
                                <option value="france">France</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Payment Method</h3>
                    
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="card" checked>
                            <span class="option-content">
                                <i data-lucide="credit-card"></i>
                                Credit/Debit Card
                            </span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="mobile-money">
                            <span class="option-content">
                                <i data-lucide="smartphone"></i>
                                Mobile Money
                            </span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="paypal">
                            <span class="option-content">
                                <i data-lucide="wallet"></i>
                                PayPal
                            </span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-place-order">Place Order - <?php echo number_format($cart_total, 0); ?> FCA</button>
            </form>
        </div>
        
        <div class="order-summary">
            <h3>Order Summary</h3>
            
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="item-img">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <span class="item-qty"><?php echo $item['quantity']; ?></span>
                    </div>
                    <div class="item-details">
                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p><?php echo htmlspecialchars($item['store_name']); ?></p>
                    </div>
                    <div class="item-price"><?php echo number_format($item['subtotal'], 0); ?> FCA</div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="summary-totals">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span><?php echo number_format($cart_total, 0); ?> FCA</span>
                </div>
                <div class="total-row">
                    <span>Shipping</span>
                    <span>Calculated at next step</span>
                </div>
                <div class="total-row total-final">
                    <span>Total</span>
                    <span><?php echo number_format($cart_total, 0); ?> FCA</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<style>
.checkout-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.checkout-section h1 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin-bottom: 2rem;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 3rem;
}

.checkout-form {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    border: 1px solid var(--border-color);
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
}

.payment-options {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.payment-option {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.payment-option:has(input:checked) {
    border-color: var(--accent-orange);
    background: rgba(211, 84, 0, 0.05);
}

.payment-option input {
    margin-right: 1rem;
}

.option-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 500;
}

.btn-place-order {
    width: 100%;
    background: var(--accent-orange);
    color: white;
    border: none;
    padding: 1.25rem;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s;
}

.btn-place-order:hover {
    transform: scale(1.02);
}

.order-summary {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.order-summary h3 {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
}

.cart-items {
    margin-bottom: 1.5rem;
}

.cart-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f5f5f5;
}

.item-img {
    position: relative;
    width: 60px;
    height: 60px;
    background: #f5f5f5;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.item-img img {
    width: 40px;
    height: 40px;
    object-fit: contain;
}

.item-qty {
    position: absolute;
    top: -8px;
    right: -8px;
    background: var(--accent-orange);
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 0.7rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}

.item-details h4 {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.item-details p {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.item-price {
    margin-left: auto;
    font-weight: 600;
}

.summary-totals {
    border-top: 1px solid var(--border-color);
    padding-top: 1rem;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    color: var(--text-muted);
}

.total-final {
    border-top: 1px solid var(--border-color);
    margin-top: 0.5rem;
    padding-top: 1rem;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-dark);
}

@media (max-width: 1024px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }
    .order-summary {
        position: static;
    }
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<?php include 'components/store-layout_end.php'; ?>
