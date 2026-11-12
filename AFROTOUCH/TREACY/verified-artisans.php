<?php
include 'components/store-layout_start.php';
include 'db.php';

$artisans = getVerifiedSellers($pdo);
?>

<section class="page-hero">
    <h1>Verified Artisans</h1>
    <p>Meet our trusted artisans from across Africa</p>
</section>

<section class="artisans-section">
    <div class="section-header">
        <h2>Our Artisans</h2>
        <span><?php echo count($artisans); ?> verified sellers</span>
    </div>
    
    <?php if (empty($artisans)): ?>
    <div class="no-products">
        <p>No verified artisans yet.</p>
    </div>
    <?php else: ?>
    <div class="artisans-grid">
        <?php foreach ($artisans as $a): ?>
        <a href="shop/index.php?slug=<?php echo htmlspecialchars($a['store_slug']); ?>" class="artisan-card">
            <div class="artisan-img">
                <?php if ($a['logo']): ?>
                <img src="<?php echo htmlspecialchars($a['logo']); ?>" alt="<?php echo htmlspecialchars($a['store_name']); ?>">
                <?php else: ?>
                <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo urlencode($a['store_name']); ?>&backgroundColor=D35400" alt="">
                <?php endif; ?>
                <?php if ($a['is_verified']): ?>
                <span class="verified-badge"><i data-lucide="badge-check"></i> Verified</span>
                <?php endif; ?>
            </div>
            <div class="artisan-info">
                <h3><?php echo htmlspecialchars($a['store_name']); ?></h3>
                <p class="owner"><?php echo htmlspecialchars($a['city'] ?? ''); ?>, <?php echo htmlspecialchars($a['country'] ?? ''); ?></p>
                <span class="products-count"><?php echo $a['product_count']; ?> products</span>
            </div>
        </a>
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

.artisans-section { padding: 4rem 2rem; max-width: 1400px; margin: 0 auto; }

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.section-header h2 { font-family: var(--font-heading); font-size: 2rem; }

.artisans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.artisan-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
    display: flex;
    gap: 1.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.artisan-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.1);
}

.artisan-img {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 16px;
    overflow: hidden;
    flex-shrink: 0;
}

.artisan-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.verified-badge {
    position: absolute;
    bottom: -8px;
    right: -8px;
    background: #188038;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.verified-badge svg { width: 14px; height: 14px; }

.artisan-info h3 {
    margin: 0 0 0.25rem;
    font-size: 1.25rem;
}

.artisan-info .owner {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin: 0 0 0.5rem;
}

.products-count {
    font-size: 0.85rem;
    color: var(--primary-color);
    font-weight: 600;
}

.no-products {
    text-align: center;
    padding: 4rem;
    color: var(--text-muted);
}
</style>

<?php include 'components/store-layout_end.php'; ?>