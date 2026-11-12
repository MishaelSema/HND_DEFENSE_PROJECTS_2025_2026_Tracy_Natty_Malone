<?php
include 'components/store-layout_start.php';
?>

<section class="hero-banner" style="background: #2D1F1A; color: white; padding: 6rem 2rem; text-align: center;">
    <h1 style="font-family: var(--font-heading); font-size: 4rem; margin-bottom: 1.5rem;">Authentic African Craftsmanship</h1>
    <p style="font-size: 1.25rem; color: rgba(255,255,255,0.7); max-width: 700px; margin: 0 auto 3rem;">
        Discover a curated collection of handmade treasures from across the continent. Support local artisans and bring a piece of culture home.
    </p>
    <button class="btn-primary" style="background: var(--accent-orange); border:none; color:white; padding: 1rem 3rem; border-radius: 100px; font-weight:600; cursor:pointer;" onclick="location.href='#explore'">
        Explore Collection
    </button>
</section>

<section id="explore" style="padding: 5rem 2rem; max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem;">
        <div>
            <h2 style="font-family: var(--font-heading); font-size: 2.5rem;">Featured Products</h2>
            <p class="text-muted">Handpicked for you this week</p>
        </div>
        <a href="#" style="color: var(--accent-orange); font-weight: 600; text-decoration: none;">View All →</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
        <?php
        $featured = [
            ['name' => 'Geometric Woven Basket', 'price' => '$52', 'rating' => '4.5', 'img' => 'GB'],
            ['name' => 'Handmade African Cap', 'price' => '$35', 'rating' => '4.8', 'img' => 'AC'],
            ['name' => 'Beaded Zulu Necklace', 'price' => '$12', 'rating' => '4.9', 'img' => 'ZN'],
            ['name' => 'Bronze Ife Head', 'price' => '$85', 'rating' => '4.7', 'img' => 'BI'],
        ];
        foreach ($featured as $p):
        ?>
        <div class="product-item" style="cursor:pointer;" onclick="location.href='product-detail.php'">
            <div style="background: #F8F5F2; border-radius: 20px; height: 300px; margin-bottom: 1.5rem; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden;">
                <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo $p['img']; ?>&backgroundColor=D35400" style="width: 200px;">
                <div style="position:absolute; top:1rem; right:1rem; background:white; padding:4px 10px; border-radius:100px; font-size:0.8rem; font-weight:700;">
                    ★ <?php echo $p['rating']; ?>
                </div>
            </div>
            <h3 style="font-family: var(--font-heading); font-size: 1.5rem; margin-bottom:0.5rem;"><?php echo $p['name']; ?></h3>
            <div style="font-weight:700; color: var(--accent-orange); font-size: 1.2rem;"><?php echo $p['price']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section style="background: #FAF7F5; padding: 5rem 2rem; margin-top: 5rem;">
    <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
        <div>
            <h2 style="font-family: var(--font-heading); font-size: 3rem; margin-bottom: 1.5rem;">Join the Community</h2>
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem; line-height: 1.6;">
                Are you an artisan looking to share your work with the world? Join AfroTouch and start your seller journey today.
            </p>
            <button class="btn-primary" style="background: var(--text-dark); border:none; color:white; padding: 1rem 3rem; border-radius: 100px; font-weight:600; cursor:pointer;" onclick="location.href='dashboard.php'">
                Start Selling
            </button>
        </div>
        <div style="border-radius: 24px; overflow: hidden;">
            <img src="https://api.dicebear.com/7.x/initials/svg?seed=Artisan&backgroundColor=E8A87C" style="width: 100%; height: 300px; object-fit: cover;">
        </div>
    </div>
</section>

<?php include 'components/store-layout_end.php'; ?>
