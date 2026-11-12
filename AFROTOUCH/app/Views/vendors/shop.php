<div class="vendor-shop-header">
    <div class="vendor-info">
        <div class="vendor-avatar">
            <?= strtoupper(substr($vendor->name, 0, 2)) ?>
        </div>
        <div>
            <h1><?= $vendor->name ?></h1>
            <p class="vendor-location">
                <i data-lucide="map-pin"></i>
                <?= $vendor->address ?? 'Location not set' ?>
            </p>
        </div>
    </div>
    <div class="vendor-stats">
        <div class="stat">
            <span class="stat-value"><?= count($products) ?></span>
            <span class="stat-label">Products</span>
        </div>
    </div>
</div>

<?php if ($vendor->is_verified): ?>
<div class="verified-badge">
    <i data-lucide="badge-check"></i> Verified Seller
</div>
<?php endif; ?>

<div class="products-grid">
    <?php foreach ($products as $product): ?>
    <div class="product-card">
        <a href="/AFROTOUCH/public/products/<?= $product->id ?>">
            <img src="<?= $product->image ?? 'https://via.placeholder.com/300x200?text=No+Image' ?>" alt="<?= $product->name ?>">
            <div class="product-info">
                <span class="category"><?= $product->category ?></span>
                <h3><?= $product->name ?></h3>
                <p class="price"><?= number_format($product->price, 0, ',', ' ') ?> XAF</p>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($products)): ?>
<div class="empty-state">
    <i data-lucide="package-open"></i>
    <p>This shop has no products yet.</p>
</div>
<?php endif; ?>

<style>
.vendor-shop-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 30px;
    background: white;
    border-radius: 12px;
    margin-bottom: 30px;
}

.vendor-info {
    display: flex;
    align-items: center;
    gap: 20px;
}

.vendor-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #D35400, #E8A87C);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: 700;
}

.vendor-info h1 {
    margin: 0 0 5px 0;
    font-size: 28px;
}

.vendor-location {
    color: #6D5D57;
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 0;
}

.vendor-stats {
    display: flex;
    gap: 30px;
}

.stat {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 28px;
    font-weight: 700;
    color: #D35400;
}

.stat-label {
    font-size: 14px;
    color: #6D5D57;
}

.verified-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #E9F7EF;
    color: #188038;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 20px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.product-card a {
    text-decoration: none;
    color: inherit;
}

.product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.product-card .product-info {
    padding: 20px;
}

.product-card .category {
    font-size: 12px;
    color: #D35400;
    font-weight: 600;
    text-transform: uppercase;
}

.product-card h3 {
    margin: 8px 0;
    font-size: 16px;
    color: #2D1F1A;
}

.product-card .price {
    font-size: 18px;
    font-weight: 700;
    color: #2D1F1A;
    margin: 0;
}

.empty-state {
    text-align: center;
    padding: 60px;
    color: #6D5D57;
}

.empty-state i {
    width: 64px;
    height: 64px;
    margin-bottom: 15px;
    opacity: 0.5;
}
</style>

<script>
lucide.createIcons();
</script>
