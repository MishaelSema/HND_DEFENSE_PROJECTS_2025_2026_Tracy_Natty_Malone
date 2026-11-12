<?php

use App\Models\Product;

$featuredProducts = Product::findActive();
$featuredProducts = array_slice($featuredProducts, 0, 4);
?>

<section class="hero-banner">
    <h1>Authentic African Craftsmanship</h1>
    <p>Discover a curated collection of handmade treasures from across the continent. Support local artisans and bring a piece of culture home.</p>
    <div class="hero-buttons">
        <a href="/AFROTOUCH/public/products" class="btn btn-primary">Explore Collection</a>
        <a href="/AFROTOUCH/public/register" class="btn btn-secondary">Start Selling</a>
    </div>
</section>

<section class="featured-section">
    <div class="section-header">
        <div>
            <h2>Featured Products</h2>
            <p>Handpicked for you this week</p>
        </div>
        <a href="/AFROTOUCH/public/products" class="view-all">View All →</a>
    </div>
    
    <div class="products-grid">
        <?php foreach ($featuredProducts as $product): ?>
        <a href="/AFROTOUCH/public/products/<?= $product->id ?>" class="product-card">
            <div class="product-image">
                <img src="<?= $product->image ?? 'https://via.placeholder.com/300x200?text=No+Image' ?>" alt="<?= htmlspecialchars($product->name) ?>">
            </div>
            <div class="product-details">
                <span class="category"><?= htmlspecialchars($product->category) ?></span>
                <h3><?= htmlspecialchars($product->name) ?></h3>
                <p class="price"><?= number_format($product->price, 0, ',', ' ') ?> XAF</p>
            </div>
        </a>
        <?php endforeach; ?>
        
        <?php if (empty($featuredProducts)): ?>
        <div class="empty-featured">
            <p>No products yet. Be the first to sell!</p>
            <a href="/AFROTOUCH/public/register" class="btn btn-primary">Become a Seller</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="community-section">
    <div class="community-content">
        <div class="community-text">
            <h2>Join the Community</h2>
            <p>Are you an artisan looking to share your work with the world? Join AfroTouch and start your seller journey today.</p>
            <a href="/AFROTOUCH/public/register" class="btn btn-primary">Start Selling</a>
        </div>
        <div class="community-image">
            <img src="/AFROTOUCH/public/assets/images/community.jpg" alt="Artisan Community" onerror="this.style.display='none'">
            <div class="placeholder-img">AFRO TOUCH</div>
        </div>
    </div>
</section>

<style>
.hero-banner {
    background: #241611;
    color: white;
    padding: 80px 30px;
    text-align: center;
}

.hero-banner h1 {
    font-family: 'Instrument Serif', serif;
    font-size: 48px;
    margin-bottom: 20px;
}

.hero-banner p {
    font-size: 18px;
    color: rgba(255,255,255,0.7);
    max-width: 700px;
    margin: 0 auto 30px;
    line-height: 1.6;
}

.hero-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-primary {
    background: #D35400;
    color: white;
    padding: 14px 35px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s;
}

.btn-primary:hover {
    background: #b94700;
}

.btn-secondary {
    background: transparent;
    color: white;
    border: 2px solid rgba(255,255,255,0.3);
    padding: 12px 33px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-secondary:hover {
    border-color: white;
}

.featured-section {
    padding: 60px 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 30px;
}

.section-header h2 {
    font-size: 28px;
    margin: 0 0 5px;
}

.section-header p {
    color: #6D5D57;
    margin: 0;
}

.view-all {
    color: #D35400;
    font-weight: 600;
    text-decoration: none;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
}

.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.product-image {
    height: 220px;
    overflow: hidden;
    background: #FAF7F5;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-details {
    padding: 20px;
}

.category {
    font-size: 12px;
    color: #D35400;
    font-weight: 600;
    text-transform: uppercase;
}

.product-details h3 {
    margin: 8px 0;
    font-size: 16px;
}

.price {
    font-size: 18px;
    font-weight: 700;
    color: #2D1F1A;
    margin: 0;
}

.community-section {
    background: #FAF7F5;
    padding: 80px 30px;
}

.community-content {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

.community-text h2 {
    font-size: 36px;
    margin-bottom: 20px;
}

.community-text p {
    font-size: 18px;
    color: #6D5D57;
    line-height: 1.6;
    margin-bottom: 30px;
}

.community-image {
    border-radius: 24px;
    overflow: hidden;
    min-height: 300px;
    background: #241611;
}

.community-image img {
    width: 100%;
    height: 300px;
    object-fit: cover;
}

.placeholder-img {
    width: 100%;
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Instrument Serif', serif;
    font-size: 24px;
    color: #E8A87C;
}

.empty-featured {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    background: white;
    border-radius: 16px;
}

.empty-featured p {
    color: #6D5D57;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .community-content {
        grid-template-columns: 1fr;
    }
    .hero-banner h1 {
        font-size: 32px;
    }
}
</style>
