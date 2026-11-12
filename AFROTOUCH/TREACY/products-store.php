<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'components/store-layout_start.php';
include 'db.php';

$category_slug = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$categories = getCategories($pdo);

$sql = "SELECT p.*, c.name as category_name, s.store_name, s.is_verified 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN sellers s ON p.seller_id = s.id 
        WHERE p.status = 'active'";

$params = [];

if ($category_slug) {
    $sql .= " AND c.slug = ?";
    $params[] = $category_slug;
}

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$page_title = $category_slug ? ucfirst($category_slug) : 'All Products';
?>

<section class="products-page">
    <div class="products-header">
        <h1><?php echo $page_title; ?></h1>
        <p><?php echo count($products); ?> products found</p>
    </div>
    
    <div class="products-layout">
        <aside class="filters-sidebar">
            <div class="filter-section">
                <h3>Search</h3>
                <form method="GET" class="search-form">
                    <?php if ($category_slug): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_slug); ?>">
                    <?php endif; ?>
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i data-lucide="search"></i></button>
                </form>
            </div>
            
            <div class="filter-section">
                <h3>Categories</h3>
                <ul class="category-list">
                    <li><a href="products-store.php" class="<?php echo !$category_slug ? 'active' : ''; ?>">All Products</a></li>
                    <?php foreach ($categories as $cat): ?>
                    <li><a href="products-store.php?category=<?php echo $cat['slug']; ?>" class="<?php echo $category_slug === $cat['slug'] ? 'active' : ''; ?>"><?php echo $cat['name']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
        
        <div class="products-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card" onclick="location.href='product-detail.php?id=<?php echo $p['id']; ?>'">
                <div class="product-img">
                    <?php if ($p['image']): ?>
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                    <?php else: ?>
                    <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo urlencode($p['name']); ?>&backgroundColor=D35400" alt="">
                    <?php endif; ?>
                    <span class="category-badge"><?php echo $p['category_name'] ?? 'Uncategorized'; ?></span>
                </div>
                <div class="product-info">
                    <span class="seller-name"><?php echo $p['store_name'] ?? 'AfroTouch'; ?></span>
                    <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                    <div class="product-meta">
                        <span class="price"><?php echo number_format($p['price'], 0, ',', ' '); ?> FCA</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($products)): ?>
            <div class="no-products">
                <p>No products found</p>
                <a href="products-store.php">Clear filters</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.products-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.products-header {
    margin-bottom: 2rem;
}

.products-header h1 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.products-header p {
    color: var(--text-muted);
}

.products-layout {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
}

.filters-sidebar {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.filter-section {
    margin-bottom: 2rem;
}

.filter-section:last-child {
    margin-bottom: 0;
}

.filter-section h3 {
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1rem;
}

.search-form {
    display: flex;
    gap: 0.5rem;
}

.search-form input {
    flex: 1;
    padding: 0.6rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
}

.search-form button {
    background: var(--accent-orange);
    border: none;
    color: white;
    width: 40px;
    border-radius: 8px;
    cursor: pointer;
}

.category-list {
    list-style: none;
}

.category-list li {
    margin-bottom: 0.5rem;
}

.category-list a {
    display: block;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    color: var(--text-dark);
    text-decoration: none;
    transition: all 0.2s;
}

.category-list a:hover,
.category-list a.active {
    background: #FAF7F5;
    color: var(--accent-orange);
    font-weight: 500;
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
    position: relative;
}

.product-img img {
    width: 120px;
    height: 120px;
    object-fit: contain;
}

.category-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: white;
    padding: 4px 10px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 500;
}

.product-info {
    padding: 1.25rem;
}

.seller-name {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.product-info h3 {
    font-family: var(--font-heading);
    font-size: 1.1rem;
    margin: 0.25rem 0 0.75rem;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rating {
    color: #FFB800;
    font-weight: 600;
}

.price {
    font-weight: 700;
    color: var(--accent-orange);
}

.no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem;
    background: white;
    border-radius: 16px;
}

.no-products a {
    color: var(--accent-orange);
}

@media (max-width: 768px) {
    .products-layout {
        grid-template-columns: 1fr;
    }
    .filters-sidebar {
        position: static;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<?php include 'components/store-layout_end.php'; ?>
