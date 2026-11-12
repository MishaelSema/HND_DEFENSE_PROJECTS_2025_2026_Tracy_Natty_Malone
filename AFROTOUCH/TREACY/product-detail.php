<?php
session_start();
include 'db.php';

$product_id = (int)($_GET['id'] ?? 0);

if (!$product_id) {
    header('Location: home.php');
    exit;
}

$product = getProduct($pdo, $product_id);

if (!$product || $product['status'] !== 'active') {
    echo '<div class="container py-5"><h2>Product not found</h2><a href="home.php">Go back</a></div>';
    exit;
}

$is_owner = false;
if (isset($_SESSION['user_id'])) {
    $seller = getSellerByUserId($pdo, $_SESSION['user_id']);
    if ($seller && $seller['id'] == $product['seller_id']) {
        $is_owner = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php?redirect=product-detail.php?id=' . $product_id);
        exit;
    }
    
    if ($is_owner) {
        echo json_encode(['success' => false, 'message' => "You cannot buy your own product"]);
        exit;
    }
    
    $quantity = (int)($_POST['quantity'] ?? 1);
    $session_id = session_id();
    
    addToCart($pdo, $session_id, $product_id, $quantity);
    
    $cart_count = getCartCount($pdo, $session_id);
    echo json_encode(['success' => true, 'message' => 'Added to cart!', 'cart_count' => $cart_count]);
    exit;
}

include 'components/store-layout_start.php';
?>

<div class="product-hero">
    <div class="product-gallery">
        <?php if ($product['image']): ?>
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php else: ?>
        <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo urlencode($product['name']); ?>&backgroundColor=D35400" alt="">
        <?php endif; ?>
        
        <?php 
        $images = $product['images'] ? json_decode($product['images'], true) : [];
        if (!empty($images)): ?>
        <div class="product-thumbnails">
            <?php foreach ($images as $img): ?>
            <img src="<?php echo htmlspecialchars($img); ?>" alt="">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="product-info-store">
        <span class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></span>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        
        <?php if ($product['is_verified']): ?>
        <div class="verified-badge">
            <i data-lucide="badge-check"></i> Verified Artisan
        </div>
        <?php endif; ?>
        
        <div class="price-tag">
            <?php echo number_format($product['price'], 0, ',', ' '); ?> FCA
            <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
            <span class="compare-price"><?php echo number_format($product['compare_price'], 0, ',', ' '); ?> FCA</span>
            <?php endif; ?>
        </div>
        
        <p class="description"><?php echo htmlspecialchars($product['description'] ?? 'No description available'); ?></p>
        
        <div class="stock-status <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
            <?php echo $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ' available)' : 'Out of Stock'; ?>
        </div>
        
        <?php if ($is_owner): ?>
        <div class="owner-notice">
            <i data-lucide="info"></i> This is your product. You cannot purchase it.
        </div>
        <?php elseif ($product['stock'] > 0): ?>
        <form method="POST" class="add-to-cart-form" onsubmit="return handleAddToCart(event)">
            <input type="hidden" name="add_to_cart" value="1">
            <div class="quantity-selector">
                <label>Quantity</label>
                <div class="qty-input">
                    <button type="button" onclick="this.nextElementSibling.value = Math.max(1, parseInt(this.nextElementSibling.value) - 1)">-</button>
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    <button type="button" onclick="this.previousElementSibling.value = Math.min(<?php echo $product['stock']; ?>, parseInt(this.previousElementSibling.value) + 1)">+</button>
                </div>
            </div>
            <button type="submit" class="btn-add-cart">Add to Cart</button>
        </form>
        <?php else: ?>
        <button class="btn-add-cart" disabled style="opacity: 0.5; cursor: not-allowed;">Out of Stock</button>
        <?php endif; ?>
        
        <div class="product-meta">
            <div class="meta-item">
                <i data-lucide="store"></i>
                <span>Sold by <strong><?php echo htmlspecialchars($product['store_name'] ?? 'Unknown'); ?></strong></span>
            </div>
            <div class="meta-item">
                <i data-lucide="truck"></i>
                <span>Global Shipping Available</span>
            </div>
        </div>
    </div>
</div>

<style>
.product-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    padding: 2rem 0;
}

.product-gallery {
    position: relative;
}

.product-gallery > img {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 24px;
}

.product-thumbnails {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
}

.product-thumbnails img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 12px;
    cursor: pointer;
}

.product-category {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
}

.product-info-store h1 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin: 0.5rem 0;
}

.verified-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    color: #188038;
    font-weight: 600;
    margin-bottom: 1rem;
}

.price-tag {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1.5rem;
}

.compare-price {
    font-size: 1.25rem;
    color: var(--text-muted);
    text-decoration: line-through;
    margin-left: 1rem;
}

.description {
    color: var(--text-color);
    line-height: 1.7;
    margin-bottom: 1.5rem;
}

.stock-status {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: inline-block;
}

.stock-status.in-stock { background: #E9F7EF; color: #188038; }
.stock-status.out-of-stock { background: #FDECEC; color: #C62828; }

.owner-notice {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: #FFF4E5;
    color: #B45D00;
    border-radius: 12px;
    margin-bottom: 1rem;
}

.add-to-cart-form {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    margin-bottom: 2rem;
}

.quantity-selector label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.qty-input {
    display: flex;
    align-items: center;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
}

.qty-input button {
    width: 40px;
    height: 40px;
    border: none;
    background: #fafafa;
    cursor: pointer;
    font-size: 1.25rem;
}

.qty-input input {
    width: 60px;
    height: 40px;
    border: none;
    text-align: center;
    font-size: 1rem;
}

.btn-add-cart {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s;
}

.btn-add-cart:hover { transform: scale(1.02); }

.product-meta {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-muted);
}

.meta-item strong { color: var(--text-color); }

@media (max-width: 768px) {
    .product-hero { grid-template-columns: 1fr; }
    .product-gallery > img { height: 300px; }
}
</style>

<script>
function handleAddToCart(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            showNotification('Added to cart!', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    });
    
    return false;
}

function showNotification(msg, type) {
    const div = document.createElement('div');
    div.className = 'notification ' + type;
    div.textContent = msg;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}
</script>

<?php include 'components/store-layout_end.php'; ?>