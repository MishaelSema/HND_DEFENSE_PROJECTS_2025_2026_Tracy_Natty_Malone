<div class="explore-header">
    <h1>All Shops</h1>
    <p>Discover sellers from across Africa</p>
</div>

<?php if (!empty($shops)): ?>
<div class="shops-grid">
    <?php foreach ($shops as $shop): ?>
    <div class="shop-card">
        <a href="/AFROTOUCH/public/shop/<?= $shop['user']->id ?>">
            <div class="shop-avatar">
                <?= strtoupper(substr($shop['user']->name, 0, 2)) ?>
            </div>
            <h3><?= htmlspecialchars($shop['user']->name) ?></h3>
            <?php if ($shop['user']->is_verified): ?>
            <span class="verified-badge">
                <i data-lucide="badge-check"></i> Verified
            </span>
            <?php endif; ?>
            <p class="shop-location">
                <i data-lucide="map-pin"></i>
                <?= $shop['user']->address ?? 'Location not set' ?>
            </p>
            <div class="shop-stats">
                <span><?= $shop['product_count'] ?> products</span>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state">
    <i data-lucide="store"></i>
    <h3>No shops yet</h3>
    <p>Be the first to open a shop!</p>
    <a href="/AFROTOUCH/public/register" class="btn-primary">Start Selling</a>
</div>
<?php endif; ?>

<style>
.explore-header {
    text-align: center;
    padding: 40px 20px;
}

.explore-header h1 {
    font-size: 36px;
    margin: 0 0 10px 0;
}

.explore-header p {
    color: #6D5D57;
    margin: 0;
}

.shops-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
    padding: 0 20px 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.shop-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
}

.shop-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.shop-card a {
    text-decoration: none;
    color: inherit;
    display: block;
}

.shop-avatar {
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
    margin: 0 auto 15px;
}

.shop-card h3 {
    margin: 0 0 8px;
    font-size: 18px;
}

.verified-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #188038;
    font-size: 13px;
    margin-bottom: 10px;
}

.shop-location {
    color: #6D5D57;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin: 0 0 15px;
}

.shop-stats {
    padding-top: 15px;
    border-top: 1px solid #E8E2DE;
    color: #D35400;
    font-weight: 600;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    width: 64px;
    height: 64px;
    color: #6D5D57;
    margin-bottom: 15px;
}

.btn-primary {
    display: inline-block;
    padding: 12px 30px;
    background: #D35400;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
}
</style>

<script>
lucide.createIcons();
</script>
