<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'components/store-layout_start.php';

$store = $_GET['store'] ?? 'nigerian-crafts';

$sellers = [
    'felix-crafts' => [
        'name' => 'Felix Crafts',
        'owner' => 'Felix Artisan',
        'location' => 'Douala, Cameroon',
        'verified' => true,
        'description' => 'Handmade African crafts from Cameroon. Each piece is uniquely crafted by master artisans.',
        'products' => 24,
        'rating' => 4.8
    ],
    'nigerian-crafts' => [
        'name' => 'Nigerian Crafts',
        'owner' => 'Chioma Obi',
        'location' => 'Lagos, Nigeria',
        'verified' => true,
        'description' => 'Authentic Nigerian crafts and artworks.',
        'products' => 18,
        'rating' => 4.9
    ]
];

$seller = $sellers[$store] ?? $sellers['felix-crafts'];

$products = [
    ['name' => 'Handmade African Cap', 'price' => '27,000 FCA', 'img' => 'AC'],
    ['name' => 'Beaded Zulu Necklace', 'price' => '7,500 FCA', 'img' => 'ZN'],
    ['name' => 'Geometric Woven Basket', 'price' => '31,200 FCA', 'img' => 'GB'],
    ['name' => 'Bronze Ife Head', 'price' => '51,000 FCA', 'img' => 'BI'],
];
?>

<section class="seller-profile">
    <div class="seller-header">
        <div class="seller-info">
            <div class="seller-avatar">
                <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo substr($seller['name'], 0, 2); ?>&backgroundColor=D35400" alt="<?php echo $seller['name']; ?>">
                <?php if ($seller['verified']): ?>
                <span class="verified-badge"><i data-lucide="badge-check"></i></span>
                <?php endif; ?>
            </div>
            <div class="seller-details">
                <h1><?php echo $seller['name']; ?></h1>
                <p class="owner">by <?php echo $seller['owner']; ?></p>
                <p class="location"><i data-lucide="map-pin"></i> <?php echo $seller['location']; ?></p>
                <div class="seller-stats">
                    <span>★ <?php echo $seller['rating']; ?></span>
                    <span>•</span>
                    <span><?php echo $seller['products']; ?> products</span>
                </div>
            </div>
        </div>
        <p class="description"><?php echo $seller['description']; ?></p>
    </div>
    
    <h2>Products</h2>
    <div class="products-grid">
        <?php foreach ($products as $p): ?>
        <div class="product-card" onclick="location.href='product-detail.php'">
            <div class="product-img">
                <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo $p['img']; ?>&backgroundColor=D35400" alt="<?php echo $p['name']; ?>">
            </div>
            <div class="product-info">
                <h3><?php echo $p['name']; ?></h3>
                <span class="price"><?php echo $p['price']; ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
.seller-profile {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.seller-header {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    margin-bottom: 2rem;
}

.seller-info {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.seller-avatar {
    position: relative;
}

.seller-avatar img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
}

.verified-badge {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #188038;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.seller-details h1 {
    font-family: var(--font-heading);
    font-size: 2rem;
    margin-bottom: 0.25rem;
}

.seller-details .owner {
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.seller-details .location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-bottom: 0.75rem;
}

.seller-stats {
    display: flex;
    gap: 0.75rem;
    font-size: 0.9rem;
    font-weight: 500;
}

.description {
    color: var(--text-muted);
    line-height: 1.6;
}

.seller-profile h2 {
    font-family: var(--font-heading);
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: all 0.2s;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.product-img {
    height: 200px;
    background: #F8F5F2;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-img img {
    width: 120px;
    height: 120px;
}

.product-info {
    padding: 1.25rem;
}

.product-info h3 {
    font-family: var(--font-heading);
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.price {
    font-weight: 700;
    color: var(--accent-orange);
}

@media (max-width: 768px) {
    .seller-info {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<?php include 'components/store-layout_end.php'; ?>
