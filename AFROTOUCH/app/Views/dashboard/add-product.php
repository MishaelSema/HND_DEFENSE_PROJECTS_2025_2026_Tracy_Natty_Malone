<div class="dashboard-page-header">
    <h1>Add Product</h1>
    <p>Add a new product to your shop</p>
</div>

<div class="add-product-form">
    <form action="/AFROTOUCH/public/dashboard/store-product" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-section">
                <h3>Product Details</h3>
                
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (XAF) *</label>
                        <input type="number" id="price" name="price" required min="0" step="100">
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" required min="0" value="1">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select category</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Jewelry">Jewelry</option>
                        <option value="Home Decor">Home Decor</option>
                        <option value="Art">Art</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Crafts">Crafts</option>
                        <option value="Food">Food & Spices</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Product Image</h3>
                
                <div class="image-upload-area" id="dropZone">
                    <input type="file" id="productImage" name="image" accept="image/*" hidden>
                    <div class="upload-content">
                        <i data-lucide="upload" style="width: 48px; height: 48px; color: #D35400;"></i>
                        <p>Click or drag image here</p>
                        <span>JPG, PNG, GIF, WebP (max 5MB)</span>
                    </div>
                    <img id="previewImage" class="preview-img" style="display: none;">
                </div>
                
                <div class="or-divider"><span>OR</span></div>
                
                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="url" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="/AFROTOUCH/public/dashboard/products" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Add Product</button>
        </div>
    </form>
</div>

<style>
.add-product-form {
    background: white;
    padding: 30px;
    border-radius: 12px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.form-section h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #2D1F1A;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2D1F1A;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #E8E2DE;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #D35400;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.image-upload-area {
    border: 2px dashed #E8E2DE;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-upload-area:hover {
    border-color: #D35400;
    background: #FFF9F5;
}

.upload-content {
    color: #6D5D57;
}

.upload-content p {
    margin: 10px 0 5px;
    font-weight: 500;
}

.upload-content span {
    font-size: 12px;
}

.preview-img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
}

.or-divider {
    text-align: center;
    margin: 20px 0;
    position: relative;
}

.or-divider::before {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    background: #E8E2DE;
}

.or-divider span {
    background: white;
    padding: 0 15px;
    position: relative;
    color: #6D5D57;
    font-size: 12px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #E8E2DE;
}

.btn-cancel {
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    color: #6D5D57;
    font-weight: 500;
}

.btn-cancel:hover {
    background: #f5f5f5;
}

.btn-submit {
    padding: 12px 32px;
    background: #D35400;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-submit:hover {
    background: #b94700;
}
</style>

<script>
lucide.createIcons();

const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('productImage');
const previewImage = document.getElementById('previewImage');
const uploadContent = document.querySelector('.upload-content');

dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = '#D35400';
    dropZone.style.background = '#FFF9F5';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = '#E8E2DE';
    dropZone.style.background = 'white';
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = '#E8E2DE';
    dropZone.style.background = 'white';
    
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        fileInput.files = e.dataTransfer.files;
        showPreview(file);
    }
});

fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        showPreview(file);
    }
});

function showPreview(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImage.src = e.target.result;
        previewImage.style.display = 'block';
        uploadContent.style.display = 'none';
    };
    reader.readAsDataURL(file);
}
</script>
