<?php
include '../db.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: home.php');
    exit;
}

$stmt = $pdo->prepare("SELECT s.*, u.name as owner_name, u.email as owner_email 
    FROM sellers s 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE s.store_slug = ?");
$stmt->execute([$slug]);
$seller = $stmt->fetch();

if (!$seller) {
    http_response_code(404);
    include '../components/store-layout_start.php';
    echo '<div class="container py-5 text-center">
        <h1>404 - Shop Not Found</h1>
        <p>The shop you are looking for does not exist.</p>
        <a href="home.php" class="btn-primary" style="display:inline-block;margin-top:1rem;">Go Home</a>
    </div>';
    include '../components/store-layout_end.php';
    exit;
}

if (!$seller['is_verified']) {
    header('Location: verify.php');
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.seller_id = ? AND p.status = 'active' ORDER BY p.created_at DESC");
    $stmt->execute([$seller['id']]);
    $products = $stmt->fetchAll();

include '../components/store-layout_start.php';
?>

<section class="shop-header">
    <div class="shop-banner">
        <?php if ($seller['banner']): ?>
        <img src="<?php echo htmlspecialchars($seller['banner']); ?>" alt="<?php echo htmlspecialchars($seller['store_name']); ?>">
        <?php else: ?>
        <div class="banner-placeholder"></div>
        <?php endif; ?>
    </div>
    <div class="shop-info-bar">
        <div class="shop-logo">
            <?php if ($seller['logo']): ?>
            <img src="<?php echo htmlspecialchars($seller['logo']); ?>" alt="<?php echo htmlspecialchars($seller['store_name']); ?>">
            <?php else: ?>
            <div class="logo-placeholder"><?php echo substr($seller['store_name'], 0, 2); ?></div>
            <?php endif; ?>
        </div>
        <div class="shop-details">
            <h1>
                <?php echo htmlspecialchars($seller['store_name']); ?>
                <?php if ($seller['is_verified']): ?>
                <span class="verified-icon" title="Verified Seller"><i data-lucide="badge-check"></i></span>
                <?php endif; ?>
            </h1>
            <p class="shop-location">
                <i data-lucide="map-pin"></i>
                <?php echo htmlspecialchars(($seller['city'] ?? '') . ($seller['country'] ? ', ' . $seller['country'] : '')); ?>
            </p>
            <?php if ($seller['description']): ?>
            <p class="shop-desc"><?php echo htmlspecialchars($seller['description']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="container py-5">
    <?php if (empty($products)): ?>
    <div class="empty-shop">
        <i data-lucide="package"></i>
        <h3>No products yet</h3>
        <p>This seller hasn't added any products yet.</p>
    </div>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
        <div class="product-card">
            <a href="../product-detail.php?id=<?php echo $product['id']; ?>" class="product-link">
                <div class="product-img">
                    <?php if ($product['image']): ?>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                    <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo urlencode($product['name']); ?>&backgroundColor=D35400" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php endif; ?>
                    <?php if ($product['stock'] == 0): ?>
                    <span class="out-of-stock">Out of Stock</span>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <span class="category"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></span>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="price-row">
                        <span class="price"><?php echo number_format($product['price'], 0, ',', ' '); ?> FCA</span>
                        <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                        <span class="compare-price"><?php echo number_format($product['compare_price'], 0, ',', ' '); ?> FCA</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.shop-header { margin-bottom: 2rem; }

.shop-banner {
    height: 200px;
    background: #f5f5f5;
    overflow: hidden;
}

.shop-banner img { width: 100%; height: 100%; object-fit: cover; }
.banner-placeholder { width: 100%; height: 100%; background: linear-gradient(135deg, #D35400 0%, #2D1F1A 100%); }

.shop-info-bar {
    max-width: 1200px;
    margin: -40px auto 0;
    padding: 0 1.5rem;
    display: flex;
    gap: 1.5rem;
    position: relative;
}

.shop-logo {
    width: 100px;
    height: 100px;
    border-radius: 16px;
    background: white;
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
    flex-shrink: 0;
}

.shop-logo img { width: 100%; height: 100%; object-fit: cover; }
.logo-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--primary-color); color: white; font-size: 2rem; font-weight: 700; }

.shop-details {
    padding-top: 3rem;
}

.shop-details h1 {
    margin: 0 0 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.verified-icon { color: #188038; }

.shop-location {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: var(--text-muted);
    margin: 0 0 0.5rem;
}

.shop-desc {
    color: var(--text-color);
    margin: 0;
    max-width: 600px;
}

.empty-shop { text-align: center; padding: 4rem 2rem; color: var(--text-muted); }
.empty-shop svg { width: 64px; height: 64px; margin-bottom: 1rem; }

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.product-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.product-link { text-decoration: none; color: inherit; }

.product-img {
    position: relative;
    height: 200px;
    background: #f5f5f5;
}

.product-img img { width: 100%; height: 100%; object-fit: cover; }

.out-of-stock {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 600;
}

.product-info { padding: 1.25rem; }

.product-info .category {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-info h3 {
    margin: 0.5rem 0;
    font-size: 1.1rem;
}

.price-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
}

.compare-price {
    font-size: 0.9rem;
    color: var(--text-muted);
    text-decoration: line-through;
}

@media (max-width: 768px) {
    .shop-info-bar { flex-direction: column; align-items: center; text-align: center; }
    .shop-details { padding-top: 1rem; }
    .shop-details h1 { justify-content: center; }
    .shop-location { justify-content: center; }
}
</style>

<?php include '../components/store-layout_end.php'; ?>