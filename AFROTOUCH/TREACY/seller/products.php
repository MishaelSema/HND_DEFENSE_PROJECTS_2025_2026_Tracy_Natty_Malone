<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$current_page = 'products';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $seller = getSellerByUserId($pdo, $_SESSION['user_id']);
    if ($seller) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
        $stmt->execute([$delete_id, $seller['id']]);
    }
    header('Location: products.php');
    exit;
}

include '../components/layout_start.php';

$seller = getSellerByUserId($pdo, $_SESSION['user_id']);
$products = $seller ? getSellerProducts($pdo, $seller['id']) : [];
$categories = getCategories($pdo);
?>

<div class="page-header">
    <div class="header-content">
        <h1>Products</h1>
        <p class="subtitle">Manage and showcase your craft</p>
    </div>
    <div class="header-actions">
        <a href="add-product.php" class="btn-primary">
            <i data-lucide="plus"></i> Add Product
        </a>
    </div>
</div>

<div class="products-grid">
    <?php if (empty($products)): ?>
    <div class="empty-state">
        <i data-lucide="package"></i>
        <h3>No products yet</h3>
        <p>Start adding products to your shop</p>
        <a href="add-product.php" class="btn-primary">Add Your First Product</a>
    </div>
    <?php else: ?>
    <?php foreach ($products as $product): ?>
    <div class="product-card">
        <div class="product-img">
            <?php if ($product['image']): 
                $filename = basename($product['image']);
                $img_path = '/AFROTOUCH/TREACY/uploads/products/' . $filename;
            ?>
            <img src="<?php echo $img_path; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php else: ?>
            <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?php echo urlencode($product['name']); ?>&backgroundColor=D35400" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php endif; ?>
            <span class="status-badge <?php echo $product['stock'] > 5 ? 'in-stock' : ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock'); ?>">
                <?php echo $product['stock'] > 5 ? 'In Stock' : ($product['stock'] > 0 ? 'Low Stock' : 'Out of Stock'); ?>
            </span>
        </div>
        <div class="product-details">
            <div class="product-info">
                <span class="category"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span>
                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
            </div>
            <div class="product-footer">
                <span class="price"><?php echo number_format($product['price'], 0, ',', ' '); ?> FCA</span>
                <div class="actions">
                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn-icon-sm"><i data-lucide="edit-3"></i></a>
                    <a href="?delete=<?php echo $product['id']; ?>" class="btn-icon-sm" onclick="return confirm('Delete this product?');" style="color: #C62828;"><i data-lucide="trash-2"></i></a>
                    <a href="../product-detail.php?id=<?php echo $product['id']; ?>" class="btn-icon-sm"><i data-lucide="eye"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    
    <a href="add-product.php" class="add-product-card">
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

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--primary-color);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    border: none;
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
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.product-img {
    position: relative;
    height: 200px;
    background: #f5f5f5;
}

.product-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.status-badge {
    position: absolute;
    top: 12px;
    right: 12px;
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

.product-info { margin-bottom: 1rem; }

.product-info .category {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-name {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0.25rem 0 0;
    color: var(--text-color);
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.product-footer .price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon-sm {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: white;
    color: var(--text-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.btn-icon-sm:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.add-product-card {
    background: #fafafa;
    border: 2px dashed var(--border-color);
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    text-decoration: none;
    color: var(--text-muted);
    transition: border-color 0.2s, color 0.2s;
}

.add-product-card:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.add-product-card span {
    margin-top: 1rem;
    font-weight: 600;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: #fafafa;
    border-radius: 16px;
}

.empty-state i {
    width: 64px;
    height: 64px;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.empty-state h3 { margin: 0 0 0.5rem; }
.empty-state p { color: var(--text-muted); margin-bottom: 1.5rem; }
</style>

<?php include '../components/layout_end.php'; ?>