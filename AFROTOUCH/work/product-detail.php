<?php
include 'components/store-layout_start.php';
?>

<div class="product-hero">
    <div class="product-gallery">
        <!-- Using the basket image from the user's latest screenshot context if I could, but I'll use a placeholder -->
        <img src="https://api.dicebear.com/7.x/initials/svg?seed=GB&backgroundColor=D35400" alt="Geometric Woven Basket">
    </div>
    
    <div class="product-info-store">
        <h1 style="font-family: var(--font-heading); font-size: 3rem; margin-bottom: 0.5rem;">Geometric Woven Basket</h1>
        
        <div class="rating">
            <div class="stars">★★★★★</div>
            <span class="text-muted">4.5 (92 reviews)</span>
        </div>
        
        <div class="price-tag">XAF52</div>
        
        <p class="text-muted" style="margin-bottom: 2.5rem; line-height: 1.6;">
            A beautiful, handcrafted geometric basket woven by master artisans in Cameroon. Perfect for home decor or functional storage. Each piece is unique and supports local communities.
        </p>
        
        <button class="btn-add-cart">Add to Cart</button>
        
        <div style="margin-top: 3rem; display: flex; gap: 2rem;">
            <div style="display: flex; align-items:center; gap: 0.5rem; font-size: 0.9rem;">
                <i data-lucide="shield-check" class="text-primary"></i>
                Verified Artisan
            </div>
            <div style="display: flex; align-items:center; gap: 0.5rem; font-size: 0.9rem;">
                <i data-lucide="truck" class="text-primary"></i>
                Global Shipping
            </div>
        </div>
    </div>
</div>

<!-- Similar Items Section (Inspo from modern stores) -->
<section style="padding: 5rem 2rem; max-width: 1200px; margin: 0 auto;">
    <h2 style="font-family: var(--font-heading); font-size: 2rem; margin-bottom: 2rem;">You might also like</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem;">
        <?php for($i=1; $i<=4; $i++): ?>
        <div style="cursor:pointer;" onclick="location.href='product-detail.php'">
            <div style="background: #F8F5F2; border-radius: 16px; height: 250px; margin-bottom: 1rem; display:flex; align-items:center; justify-content:center;">
                <img src="https://api.dicebear.com/7.x/initials/svg?seed=P<?php echo $i; ?>&backgroundColor=2D1F1A" style="width: 150px;">
            </div>
            <h4 style="margin-bottom:0.25rem;">African Craft Item <?php echo $i; ?></h4>
            <div style="font-weight:700;">XAF24.00</div>
        </div>
        <?php endfor; ?>
    </div>
</section>

<?php include 'components/store-layout_end.php'; ?>
