<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$seller = getSellerByUserId($pdo, $_SESSION['user_id']);

if (!$seller) {
    header('Location: settings.php?setup=required');
    exit;
}

if (isset($_GET['just_created'])) {
    if (!$seller['is_verified'] && !in_array($seller['verification_status'], ['pending', 'processing'])) {
        header('Location: ../verify.php');
        exit;
    }
}

$current_page = 'shop-view';
include '../components/layout_start.php';

$products = getSellerProducts($pdo, $seller['id']);
?>

<div class="page-header">
    <div class="header-content">
        <h1>My Shop</h1>
        <p class="subtitle">Preview and manage your public shop</p>
    </div>
    <div class="header-actions">
        <a href="../shop/index.php?slug=<?php echo htmlspecialchars($seller['store_slug']); ?>" target="_blank" class="btn-primary">
            <i data-lucide="external-link"></i> View Live Shop
        </a>
    </div>
</div>

<?php if (in_array($seller['verification_status'], ['pending', 'processing'])): ?>
<div class="alert alert-warning">
    <i data-lucide="clock"></i>
    <div>
        <strong>Verification Processing</strong>
        <p>Your verification is being reviewed. You can still add products but they won't be visible until verified.</p>
    </div>
</div>
<?php elseif (!$seller['is_verified']): ?>
<div class="alert alert-info">
    <i data-lucide="shield"></i>
    <div>
        <strong>Get Verified to Increase Sales</strong>
        <p>Verified sellers get more visibility and customer trust.</p>
        <a href="../verify.php" class="btn-sm">Complete Verification →</a>
    </div>
</div>
<?php endif; ?>

<div class="shop-preview-card">
    <div class="preview-header">
        <div class="shop-logo">
            <?php if ($seller['logo']): ?>
            <img src="<?php echo htmlspecialchars($seller['logo']); ?>" alt="Logo">
            <?php else: ?>
            <div class="logo-placeholder"><?php echo substr($seller['store_name'], 0, 2); ?></div>
            <?php endif; ?>
        </div>
        <div class="shop-info">
            <h2>
                <?php echo htmlspecialchars($seller['store_name']); ?>
                <?php if ($seller['is_verified']): ?>
                <span class="verified-badge"><i data-lucide="badge-check"></i> Verified</span>
                <?php endif; ?>
            </h2>
            <p class="location"><i data-lucide="map-pin"></i> <?php echo htmlspecialchars(($seller['city'] ?? '') . ($seller['country'] ? ', ' . $seller['country'] : '')); ?></p>
            <p class="desc"><?php echo htmlspecialchars($seller['description'] ?? 'No description yet'); ?></p>
        </div>
    </div>
    
    <div class="shop-stats">
        <div class="stat-box">
            <span class="stat-num"><?php echo count($products); ?></span>
            <span class="stat-label">Products</span>
        </div>
        <div class="stat-box">
            <span class="stat-num"><?php echo $seller['is_verified'] ? 'Yes' : 'No'; ?></span>
            <span class="stat-label">Verified</span>
        </div>
        <div class="stat-box">
            <span class="stat-num"><?php echo ucfirst($seller['verification_status']); ?></span>
            <span class="stat-label">Status</span>
        </div>
    </div>
</div>

<div class="products-section">
    <div class="section-header">
        <h2>Your Products</h2>
        <a href="add-product.php" class="btn-add">
            <i data-lucide="plus"></i> Add Product
        </a>
    </div>
    
    <?php if (empty($products)): ?>
    <div class="empty-state">
        <i data-lucide="package"></i>
        <h3>No products yet</h3>
        <p>Start adding products to your shop</p>
        <a href="add-product.php" class="btn-primary">Add Your First Product</a>
    </div>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
        <div class="product-card">
            <div class="product-img">
                <?php if ($product['image']): ?>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo urlencode($product['name']); ?>&backgroundColor=D35400" alt="">
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
                    <span class="compare"><?php echo number_format($product['compare_price'], 0, ',', ' '); ?></span>
                    <?php endif; ?>
                </div>
                <div class="product-actions">
                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn-edit">Edit</a>
                    <a href="../product-detail.php?id=<?php echo $product['id']; ?>" class="btn-view">View</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
.header-actions { display: flex; gap: 1rem; }
.btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; background: var(--primary-color); color: white; padding: 0.75rem 1.5rem; border-radius: 12px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; }
.alert { display: flex; align-items: center; gap: 1rem; padding: 1.25rem; border-radius: 16px; margin-bottom: 2rem; }
.alert-warning { background: #FFF4E5; color: #B45D00; }
.alert-info { background: #EBF5FF; color: #0066CC; }
.alert strong { display: block; margin-bottom: 0.25rem; }
.alert p { margin: 0; font-size: 0.9rem; }
.btn-sm { display: inline-block; margin-top: 0.5rem; color: #0066CC; text-decoration: none; font-weight: 600; }

.shop-preview-card { background: white; border-radius: 24px; padding: 2rem; margin-bottom: 2rem; border: 1px solid var(--border-color); }
.preview-header { display: flex; gap: 2rem; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-color); }
.shop-logo { width: 100px; height: 100px; border-radius: 20px; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.shop-logo img { width: 100%; height: 100%; object-fit: cover; }
.logo-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #D35400, #2D1F1A); color: white; font-size: 2rem; font-weight: 700; }
.shop-info { flex: 1; }
.shop-info h2 { display: flex; align-items: center; gap: 0.75rem; margin: 0 0 0.5rem; font-size: 1.75rem; }
.verified-badge { display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.85rem; color: #188038; }
.location { display: flex; align-items: center; gap: 0.25rem; color: var(--text-muted); margin: 0 0 0.75rem; }
.desc { color: var(--text-color); margin: 0; line-height: 1.5; }
.shop-stats { display: flex; gap: 2rem; }
.stat-box { display: flex; flex-direction: column; align-items: center; padding: 1rem 2rem; background: #fafafa; border-radius: 16px; }
.stat-num { font-size: 1.5rem; font-weight: 700; color: var(--primary-color); }
.stat-label { font-size: 0.85rem; color: var(--text-muted); }

.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.section-header h2 { font-size: 1.5rem; margin: 0; }
.btn-add { display: inline-flex; align-items: center; gap: 0.5rem; background: white; color: var(--primary-color); padding: 0.75rem 1.5rem; border-radius: 12px; text-decoration: none; font-weight: 600; border: 1px solid var(--primary-color); }
.empty-state { text-align: center; padding: 4rem 2rem; background: #fafafa; border-radius: 24px; }
.empty-state svg { width: 64px; height: 64px; color: var(--text-muted); margin-bottom: 1rem; }
.empty-state h3 { margin: 0 0 0.5rem; }
.empty-state p { color: var(--text-muted); margin-bottom: 1.5rem; }

.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; }
.product-card { background: white; border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
.product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
.product-img { position: relative; height: 200px; background: #f5f5f5; }
.product-img img { width: 100%; height: 100%; object-fit: cover; }
.out-of-stock { position: absolute; top: 12px; left: 12px; background: rgba(0,0,0,0.7); color: white; padding: 4px 12px; border-radius: 100px; font-size: 0.75rem; font-weight: 600; }
.product-info { padding: 1.25rem; }
.product-info .category { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
.product-info h3 { margin: 0.5rem 0; font-size: 1.1rem; }
.price-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
.price { font-size: 1.25rem; font-weight: 700; color: var(--primary-color); }
.compare { font-size: 0.9rem; color: var(--text-muted); text-decoration: line-through; }
.product-actions { display: flex; gap: 0.5rem; }
.btn-edit, .btn-view { flex: 1; padding: 0.5rem; border-radius: 8px; text-align: center; text-decoration: none; font-size: 0.85rem; font-weight: 500; }
.btn-edit { background: #fafafa; color: var(--text-color); border: 1px solid var(--border-color); }
.btn-view { background: var(--primary-color); color: white; }

@media (max-width: 768px) {
    .preview-header { flex-direction: column; }
    .shop-stats { flex-wrap: wrap; }
    .products-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
}
</style>

<?php include '../components/layout_end.php'; ?>