<?php
$current_page = 'products';
include 'components/layout_start.php';

$products = [
    ['id' => 1, 'name' => 'Handmade African Cap', 'price' => '45,000 XAF', 'category' => 'Clothing', 'status' => 'In Stock', 'img' => 'https://api.dicebear.com/7.x/initials/svg?seed=AC&backgroundColor=D35400'],
    ['id' => 2, 'name' => 'Beaded Zulu Necklace', 'price' => '12,500 XAF', 'category' => 'Jewelry', 'status' => 'Low Stock', 'img' => 'https://api.dicebear.com/7.x/initials/svg?seed=ZN&backgroundColor=2D1F1A'],
    ['id' => 3, 'name' => 'Woven Sisal Basket', 'price' => '8,000 XAF', 'category' => 'Home Decor', 'status' => 'In Stock', 'img' => 'https://api.dicebear.com/7.x/initials/svg?seed=WB&backgroundColor=F1C40F'],
    ['id' => 4, 'name' => 'Bronze Ife Head Replica', 'price' => '85,000 XAF', 'category' => 'Art', 'status' => 'Out of Stock', 'img' => 'https://api.dicebear.com/7.x/initials/svg?seed=BI&backgroundColor=6D5D57'],
    ['id' => 5, 'name' => 'Kente Cloth Table Runner', 'price' => '15,000 XAF', 'category' => 'Home Decor', 'status' => 'In Stock', 'img' => 'https://api.dicebear.com/7.x/initials/svg?seed=KC&backgroundColor=D35400'],
    ['id' => 6, 'name' => 'Ebony Wood Carving', 'price' => '32,000 XAF', 'category' => 'Art', 'status' => 'In Stock', 'img' => 'https://api.dicebear.com/7.x/initials/svg?seed=EC&backgroundColor=2D1F1A'],
];
?>

<div class="page-header">
    <div class="header-content">
        <h1>Products</h1>
        <p class="subtitle">Manage and showcase your craft</p>
    </div>
    <div class="header-actions">
        <div class="filter-group">
            <select class="form-select">
                <option>All Categories</option>
                <option>Clothing</option>
                <option>Jewelry</option>
                <option>Home Decor</option>
                <option>Art</option>
            </select>
            <select class="form-select">
                <option>Status: All</option>
                <option>In Stock</option>
                <option>Low Stock</option>
                <option>Out of Stock</option>
            </select>
        </div>
    </div>
</div>

<div class="products-grid">
    <?php foreach ($products as $product): ?>
    <div class="product-card">
        <div class="product-img">
            <img src="<?php echo $product['img']; ?>" alt="<?php echo $product['name']; ?>">
            <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $product['status'])); ?>">
                <?php echo $product['status']; ?>
            </span>
        </div>
        <div class="product-details">
            <div class="product-info">
                <span class="category"><?php echo $product['category']; ?></span>
                <h3 class="product-name"><?php echo $product['name']; ?></h3>
            </div>
            <div class="product-footer">
                <span class="price"><?php echo $product['price']; ?></span>
                <div class="actions">
                    <button class="btn-icon-sm"><i data-lucide="edit-3"></i></button>
                    <button class="btn-icon-sm"><i data-lucide="eye"></i></button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <!-- Add New Placeholder -->
    <a href="#" class="add-product-card">
        <i data-lucide="plus-circle" size="48"></i>
        <span>Add New Product</span>
    </a>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.filter-group {
    display: flex;
    gap: 0.75rem;
}

.form-select {
    padding: 0.6rem 1rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: white;
    font-size: 0.85rem;
    outline: none;
    cursor: pointer;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
}

.product-img {
    height: 200px;
    position: relative;
    background: #f9f9f9;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.status-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge.in-stock { background: #E9F7EF; color: #188038; }
.status-badge.low-stock { background: #FFF4E5; color: #B45D00; }
.status-badge.out-of-stock { background: #FDECEC; color: #C62828; }

.product-details {
    padding: 1.25rem;
}

.category {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-name {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    margin: 0.25rem 0 1rem;
    font-weight: 500;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f5f5f5;
    padding-top: 1rem;
}

.price {
    font-weight: 700;
    color: var(--primary-color);
}

.btn-icon-sm {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
}

.add-product-card {
    border: 2px dashed var(--border-color);
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    color: var(--text-muted);
    text-decoration: none;
    min-height: 300px;
    transition: all 0.2s;
}

.add-product-card:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    background: rgba(211, 84, 0, 0.02);
}
</style>

<?php include 'components/layout_end.php'; ?>
