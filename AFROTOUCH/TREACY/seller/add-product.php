<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$current_page = 'add-product';

$error = '';
$success = '';
$redirect = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category_id = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $compare_price = $_POST['compare_price'] ?? null;
    $description = $_POST['description'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $sku = $_POST['sku'] ?? '';
    
    if (empty($name) || empty($category_id) || empty($price)) {
        $error = 'Please fill in all required fields';
    } else {
        $uploaded_images = [];
        
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = __DIR__ . '/../uploads/products/';
            
            foreach ($_FILES['images']['name'] as $key => $filename) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid('prod_') . '.' . $ext;
                        $target = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target)) {
                            $uploaded_images[] = 'uploads/products/' . $new_filename;
                        }
                    }
                }
            }
        }
        
        $seller = getSellerByUserId($pdo, $_SESSION['user_id']);
        
        if ($seller) {
            $result = createProduct($pdo, $seller['id'], [
                'name' => $name,
                'category_id' => $category_id,
                'price' => $price,
                'compare_price' => $compare_price,
                'description' => $description,
                'stock' => $stock,
                'sku' => $sku
            ], $uploaded_images);
            
            if ($result) {
                $redirect = 'products.php?success=1';
            } else {
                $error = 'Failed to create product';
            }
        } else {
            $error = 'You need to set up your shop first';
        }
    }
}

include '../components/layout_start.php';

if ($redirect) {
    echo '<script>window.location.href = "' . $redirect . '";</script>';
}

$categories = getCategories($pdo);
?>

<div class="page-header">
    <div class="header-content">
        <h1>Add New Product</h1>
        <p class="subtitle">Create a new listing for your shop</p>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="form-container card">
    <form action="" method="POST" class="add-product-form" enctype="multipart/form-data">
        <div class="form-section">
            <h3>Basic Information</h3>
            <div class="form-group">
                <label for="product_name">Product Name *</label>
                <input type="text" id="product_name" name="name" placeholder="e.g. Handmade African Cap" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Price (FCA) *</label>
                    <input type="number" id="price" name="price" placeholder="e.g. 27000" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Describe your product..."></textarea>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Product Images</h3>
            <div class="form-group">
                <label>Upload Images (max 5)</label>
                <div class="image-upload-area">
                    <input type="file" id="product_images" name="images[]" multiple accept="image/*" onchange="previewImages(this)">
                    <div class="upload-placeholder" id="upload_placeholder">
                        <i data-lucide="image"></i>
                        <p>Click to upload or drag and drop</p>
                        <span>JPG, PNG, GIF, WEBP</span>
                    </div>
                    <div class="image-preview-grid" id="image_preview_grid"></div>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Inventory</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" value="0" min="0">
                </div>
                <div class="form-group">
                    <label for="sku">SKU (optional)</label>
                    <input type="text" id="sku" name="sku" placeholder="e.g. AC-001">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Pricing</h3>
            <div class="form-group">
                <label for="compare_price">Compare At Price (FCA)</label>
                <input type="number" id="compare_price" name="compare_price" placeholder="Original price for display">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Publish Product</button>
            <button type="button" class="btn-secondary" onclick="location.href='products.php'">Cancel</button>
        </div>
    </form>
</div>

<style>
.alert { padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; }
.alert-error { background: #FDECEC; color: #C62828; }

.form-container { padding: 2rem; }

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.form-section:last-of-type {
    border-bottom: none;
    margin-bottom: 0;
}

.form-section h3 {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group { margin-bottom: 1rem; }

.form-group label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
    font-family: var(--font-body);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--primary-color);
    outline: none;
}

.image-upload-area {
    position: relative;
    border: 2px dashed var(--border-color);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s;
}

.image-upload-area:hover {
    border-color: var(--primary-color);
}

.image-upload-area input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.upload-placeholder i {
    width: 48px;
    height: 48px;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.upload-placeholder p {
    color: var(--text-color);
    margin: 0.5rem 0;
}

.upload-placeholder span {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.image-preview-grid {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.image-preview-item {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
}

.image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-preview-item .remove-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    background: rgba(0,0,0,0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    font-size: 14px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
}

.btn-secondary {
    background: transparent;
    color: var(--text-muted);
    border: 1px solid var(--border-color);
    padding: 0.8rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
}

@media (max-width: 768px) {
    .form-row { grid-template-columns: 1fr; }
}
</style>

<script>
function previewImages(input) {
    const grid = document.getElementById('image_preview_grid');
    const placeholder = document.getElementById('upload_placeholder');
    grid.innerHTML = '';
    
    if (input.files.length > 0) {
        placeholder.style.display = 'none';
    } else {
        placeholder.style.display = 'block';
        return;
    }
    
    Array.from(input.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'image-preview-item';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-btn" onclick="removeImage(${index})">×</button>
            `;
            grid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>

<?php include '../components/layout_end.php'; ?>