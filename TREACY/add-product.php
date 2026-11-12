<?php
$current_page = 'add-product';
include 'components/layout_start.php';
?>

<div class="page-header">
    <div class="header-content">
        <h1>Add New Product</h1>
        <p class="subtitle">Create a new listing for your shop</p>
    </div>
</div>

<div class="form-container card">
    <form action="products.php" method="POST" class="add-product-form">
        <div class="form-section">
            <h3>Basic Information</h3>
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="name" placeholder="e.g. Handmade African Cap" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option>Clothing</option>
                        <option>Jewelry</option>
                        <option>Home Decor</option>
                        <option>Art</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Price (XAF)</label>
                    <input type="text" id="price" name="price" placeholder="e.g. 45,000" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Description & Media</h3>
            <div class="form-group">
                <label for="description">Product Description</label>
                <textarea id="description" name="description" rows="5" placeholder="Tell the story of your craft..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Product Images</label>
                <div class="upload-zone" id="uploadZone">
                    <i data-lucide="upload-cloud" size="40"></i>
                    <p>Drag and drop images here, or <span>browse</span></p>
                    <input type="file" id="fileInput" hidden multiple>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="location.href='products.php'">Cancel</button>
            <button type="submit" class="btn-primary">Publish Product</button>
        </div>
    </form>
</div>

<style>
.form-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2.5rem;
}

.add-product-form {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

.form-section h3 {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-color);
}

.form-group {
    margin-bottom: 1.5rem;
}

/* Form actions and specific layout for add product */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.8rem 2.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.btn-secondary {
    background: #F5F5F5;
    color: var(--text-dark);
    border: none;
    padding: 0.8rem 2.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

@media (max-width: 600px) {
    .form-row { grid-template-columns: 1fr; }
}
</style>

<script>
    document.getElementById('uploadZone').addEventListener('click', () => {
        document.getElementById('fileInput').click();
    });
</script>

<?php include 'components/layout_end.php'; ?>
