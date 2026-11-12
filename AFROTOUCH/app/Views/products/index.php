<div class="explore-header">
    <h1>Explore</h1>
    <p>Discover unique handcrafted treasures from across Africa</p>
</div>

<div class="explore-filters">
    <form method="GET" action="/AFROTOUCH/public/products">
        <div class="filter-row">
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search ?? '') ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat ?>" <?= $selected_category === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-filter">Search</button>
        </div>
    </form>
</div>

<?php if (!empty($products)): ?>
<div class="products-grid">
    <?php foreach ($products as $product): ?>
    <div class="product-card">
        <a href="/AFROTOUCH/public/products/<?= $product->id ?>">
            <img src="<?= $product->image ?? 'https://via.placeholder.com/300x200?text=No+Image' ?>" alt="<?= htmlspecialchars($product->name) ?>">
            <div class="product-info">
                <span class="category"><?= htmlspecialchars($product->category) ?></span>
                <h3><?= htmlspecialchars($product->name) ?></h3>
                <p class="price"><?= number_format($product->price, 0, ',', ' ') ?> XAF</p>
                <?php if ($product->stock > 0): ?>
                <span class="stock"><?= $product->stock ?> in stock</span>
                <?php else: ?>
                <span class="stock out">Out of stock</span>
                <?php endif; ?>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state">
    <i data-lucide="search-x"></i>
    <h3>No products found</h3>
    <p>Try adjusting your search or browse all products</p>
    <a href="/AFROTOUCH/public/products" class="btn-primary">View All Products</a>
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

.explore-filters {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.filter-row {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.filter-row input,
.filter-row select {
    flex: 1;
    min-width: 200px;
    padding: 12px 16px;
    border: 1px solid #E8E2DE;
    border-radius: 8px;
    font-size: 14px;
}

.filter-row input:focus,
.filter-row select:focus {
    outline: none;
    border-color: #D35400;
}

.btn-filter {
    padding: 12px 30px;
    background: #D35400;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.btn-filter:hover {
    background: #b94700;
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
    display: block;
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
    margin: 10px 0 5px;
}

.product-card .stock {
    font-size: 12px;
    color: #188038;
}

.product-card .stock.out {
    color: #C62828;
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

.empty-state h3 {
    margin: 0 0 10px;
}

.empty-state p {
    color: #6D5D57;
    margin: 0 0 20px;
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
