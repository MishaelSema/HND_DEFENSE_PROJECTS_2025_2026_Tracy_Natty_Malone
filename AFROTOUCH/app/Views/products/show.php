<?php
$product = $product;
$seller = $seller ?? null;
?>

<div class="product-detail-page">
    <div class="product-main">
        <div class="product-gallery">
            <img src="<?= $product->image ?? 'https://via.placeholder.com/600x500?text=No+Image' ?>" alt="<?= htmlspecialchars($product->name) ?>" id="mainImage">
        </div>
        
        <div class="product-info">
            <span class="product-category"><?= htmlspecialchars($product->category) ?></span>
            <h1><?= htmlspecialchars($product->name) ?></h1>
            
            <div class="product-rating">
                <span class="stars">★★★★★</span>
                <span class="rating-text">4.5 (92 reviews)</span>
            </div>
            
            <div class="product-price"><?= number_format($product->price, 0, ',', ' ') ?> XAF</div>
            
            <?php if ($product->stock > 0): ?>
            <span class="stock in-stock"><?= $product->stock ?> in stock</span>
            <?php else: ?>
            <span class="stock out-of-stock">Out of stock</span>
            <?php endif; ?>
            
            <p class="product-description"><?= nl2br(htmlspecialchars($product->description ?? 'No description available.')) ?></p>
            
            <?php if ($product->stock > 0): ?>
            <form method="POST" action="/AFROTOUCH/public/cart/add/<?= $product->id ?>" class="add-to-cart-form">
                <div class="quantity-selector">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product->stock ?>">
                </div>
                <button type="submit" class="btn-add-cart">
                    <i data-lucide="shopping-cart"></i> Add to Cart
                </button>
            </form>
            <?php endif; ?>
            
            <div class="product-badges">
                <div class="badge">
                    <i data-lucide="badge-check"></i> Verified Artisan
                </div>
                <div class="badge">
                    <i data-lucide="globe"></i> Global Shipping
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($seller): ?>
    <div class="seller-card">
        <h3>Sold by</h3>
        <a href="/AFROTOUCH/public/shop/<?= $seller->id ?>" class="seller-info">
            <div class="seller-avatar">
                <?= strtoupper(substr($seller->name ?? 'S', 0, 2)) ?>
            </div>
            <div>
                <span class="seller-name"><?= htmlspecialchars($seller->shop_name ?? $seller->name) ?></span>
                <?php if ($seller->is_verified): ?>
                <span class="verified-badge"><i data-lucide="badge-check"></i> Verified</span>
                <?php endif; ?>
                <?php if ($seller->shop_location): ?>
                <span class="seller-location"><i data-lucide="map-pin"></i> <?= htmlspecialchars($seller->shop_location) ?></span>
                <?php endif; ?>
            </div>
        </a>
        <a href="/AFROTOUCH/public/shop/<?= $seller->id ?>" class="btn-view-shop">View Shop</a>
    </div>
    <?php endif; ?>
    
    <section class="related-products">
        <h2>You might also like</h2>
        <div class="related-grid">
            <?php foreach ($relatedProducts ?? [] as $related): ?>
            <a href="/AFROTOUCH/public/products/<?= $related->id ?>" class="related-item">
                <img src="<?= $related->image ?? 'https://via.placeholder.com/150x150?text=No+Image' ?>" alt="<?= htmlspecialchars($related->name) ?>">
                <h4><?= htmlspecialchars($related->name) ?></h4>
                <p class="price"><?= number_format($related->price, 0, ',', ' ') ?> XAF</p>
            </a>
            <?php endforeach; ?>
            
            <?php if (empty($relatedProducts)): ?>
            <p class="no-related">No related products found</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
.product-detail-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.product-main {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    margin-bottom: 40px;
}

.product-gallery img {
    width: 100%;
    border-radius: 16px;
    object-fit: cover;
}

.product-category {
    color: #D35400;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 13px;
}

.product-info h1 {
    font-size: 32px;
    margin: 10px 0;
}

.product-rating {
    margin: 15px 0;
}

.stars {
    color: #F1C40F;
    font-size: 18px;
}

.rating-text {
    color: #6D5D57;
    font-size: 14px;
    margin-left: 10px;
}

.product-price {
    font-size: 36px;
    font-weight: 700;
    color: #D35400;
    margin: 15px 0;
}

.stock {
    display: inline-block;
    padding: 6px 15px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 20px;
}

.stock.in-stock {
    background: #E9F7EF;
    color: #188038;
}

.stock.out-of-stock {
    background: #FFEBEE;
    color: #C62828;
}

.product-description {
    color: #6D5D57;
    line-height: 1.8;
    margin: 20px 0;
}

.add-to-cart-form {
    display: flex;
    gap: 15px;
    margin: 25px 0;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-selector input {
    width: 70px;
    padding: 12px;
    border: 1px solid #E8E2DE;
    border-radius: 8px;
    font-size: 16px;
    text-align: center;
}

.btn-add-cart {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: #D35400;
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-add-cart:hover {
    background: #b94700;
}

.product-badges {
    display: flex;
    gap: 15px;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid #E8E2DE;
}

.badge {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6D5D57;
    font-size: 14px;
}

.seller-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 40px;
}

.seller-card h3 {
    font-size: 14px;
    color: #6D5D57;
    margin-bottom: 15px;
}

.seller-info {
    display: flex;
    align-items: center;
    gap: 15px;
    text-decoration: none;
    color: inherit;
    margin-bottom: 15px;
}

.seller-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #D35400, #E8A87C);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
}

.seller-name {
    display: block;
    font-weight: 600;
    font-size: 16px;
}

.verified-badge {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #188038;
    font-size: 13px;
}

.seller-location {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #6D5D57;
    font-size: 13px;
}

.btn-view-shop {
    display: inline-block;
    background: #FAF7F5;
    color: #333;
    padding: 10px 25px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
}

.related-products h2 {
    font-size: 24px;
    margin-bottom: 25px;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 20px;
}

.related-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s;
}

.related-item:hover {
    transform: translateY(-5px);
}

.related-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.related-item h4 {
    font-size: 14px;
    margin: 12px 12px 5px;
}

.related-item .price {
    font-weight: 700;
    color: #D35400;
    margin: 0 12px 12px;
}

.no-related {
    grid-column: 1 / -1;
    text-align: center;
    color: #6D5D57;
    padding: 40px;
}

@media (max-width: 768px) {
    .product-main {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
lucide.createIcons();
</script>
