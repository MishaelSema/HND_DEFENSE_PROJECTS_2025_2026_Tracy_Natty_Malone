<div class="dashboard-page-header">
    <div>
        <h1>Products</h1>
        <p>Manage your products</p>
    </div>
    <a href="/AFROTOUCH/public/dashboard/add-product" class="btn-add-product" style="display: inline-block; width: auto; margin: 0;">
        + Add Product
    </a>
</div>

<?php if (empty($products)): ?>
    <div style="background: white; padding: 40px; border-radius: 12px; text-align: center;">
        <p style="color: #6D5D57; margin-bottom: 20px;">No products yet. Add your first product!</p>
        <a href="/AFROTOUCH/public/dashboard/add-product" class="btn-add-product" style="display: inline-block; width: auto;">+ Add Product</a>
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <?php foreach ($products as $product): ?>
        <div style="background: white; border-radius: 12px; overflow: hidden; border: 1px solid #E8E2DE;">
            <div style="height: 150px; background: #f5f5f5;">
                <?php if ($product->image): ?>
                    <img src="<?= $product->image ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #6D5D57;">
                        No Image
                    </div>
                <?php endif; ?>
            </div>
            <div style="padding: 15px;">
                <div style="font-size: 12px; color: #6D5D57; text-transform: uppercase;"><?= $product->category ?? 'Uncategorized' ?></div>
                <h3 style="margin: 5px 0; font-size: 16px;"><?= $product->name ?></h3>
                <div style="font-size: 18px; font-weight: bold; color: #D35400;"><?= number_format($product->price, 0, ',', ' ') ?> XAF</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
    lucide.createIcons();
</script>