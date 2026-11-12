<?php
$page_title = 'Products';
$current_page = 'products';
?>

<h1>All Products</h1>

<div class="table-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Seller</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td>#<?= $product->id ?></td>
                <td>
                    <div class="product-cell">
                        <img src="<?= $product->image ?? 'https://via.placeholder.com/40x40?text=No+Image' ?>" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                        <span><?= htmlspecialchars($product->name) ?></span>
                    </div>
                </td>
                <td><?= htmlspecialchars($product->seller_name ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($product->category ?? 'N/A') ?></td>
                <td><?= number_format($product->price, 0, ',', ' ') ?> XAF</td>
                <td><?= $product->stock ?></td>
                <td>
                    <span class="admin-badge admin-badge-<?= $product->status === 'active' ? 'seller' : 'buyer' ?>">
                        <?= ucfirst($product->status) ?>
                    </span>
                </td>
                <td>
                    <form method="POST" action="/AFROTOUCH/public/admin/products/<?= $product->id ?>/delete" onsubmit="return confirm('Delete this product?')">
                        <button type="submit" class="btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.product-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}
.table-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
</style>
