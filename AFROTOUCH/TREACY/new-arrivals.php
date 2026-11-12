<?php
include 'components/store-layout_start.php';
include 'db.php';

$newArrivals = getProducts($pdo, 20, null, false, true, false);
?>

<section class="page-hero">
    <h1>New Arrivals</h1>
    <p>Discover the latest additions to our marketplace</p>
</section>

<section class="products-section">
    <div class="section-header">
        <h2>Fresh Finds</h2>
        <span><?php echo count($newArrivals); ?> new items</span>
    </div>
    
    <?php if (empty($newArrivals)): ?>
    <div class="no-products">
        <p>No new arrivals yet. Check back soon!</p>
    </div>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach ($newArrivals as $p): ?>
        <div class="product-item" onclick="location.href='product-detail.php?id=<?php echo $p['id']; ?>'">
            <div class="product-image">
                <?php if ($p['image']): ?>
                <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                <?php else: ?>
                <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo urlencode($p['name']); ?>&backgroundColor=D35400" alt="">
                <?php endif; ?>
                <span class="new-badge">New</span>
            </div>
            <div class="product-details">
                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                <div class="product-meta">
                    <span class="price"><?php echo number_format($p['price'], 0, ',', ' '); ?> FCA</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<style>
.page-hero {
    background: #2D1F1A;
    color: white;
    padding: 5rem 2rem;
    text-align: center;
}

.page-hero h1 {
    font-family: var(--font-heading);
    font-size: 3.5rem;
    margin-bottom: 1rem;
}

.page-hero p { font-size: 1.25rem; opacity: 0.9; }

.products-section { padding: 4rem 2rem; max-width: 1400px; margin: 0 auto; }

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.section-header h2 {
    font-family: var(--font-heading);
    font-size: 2rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
}

.product-item {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.1);
}

.product-image { position: relative; height: 220px; background: #f5f5f5; }

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.new-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: var(--primary-color);
    color: white;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 600;
}

.product-details { padding: 1.25rem; }

.product-details h3 {
    font-size: 1.1rem;
    margin: 0 0 0.75rem;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rating { color: #F1C40F; }

.price {
    font-weight: 700;
    color: var(--primary-color);
    font-size: 1.1rem;
}

.no-products {
    text-align: center;
    padding: 4rem;
    color: var(--text-muted);
}
</style>

<?php include 'components/store-layout_end.php'; ?>