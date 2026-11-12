<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$current_page = 'edit-product';

$product_id = (int)($_GET['id'] ?? 0);
$seller = getSellerByUserId($pdo, $_SESSION['user_id']);

if (!$seller) {
    header('Location: products.php');
    exit;
}

$product = null;
if ($product_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
    $stmt->execute([$product_id, $seller['id']]);
    $product = $stmt->fetch();
}

if (!$product) {
    header('Location: products.php');
    exit;
}

// Handle remove image
if (isset($_GET['remove_img'])) {
    $remove_idx = (int)$_GET['remove_img'];
    
    $all_images = [];
    if ($product['image']) {
        $all_images[] = $product['image'];
    }
    $additional_images = $product['images'] ? json_decode($product['images'], true) : [];
    if (!empty($additional_images)) {
        $all_images = array_merge($all_images, $additional_images);
    }
    
    if (isset($all_images[$remove_idx])) {
        unset($all_images[$remove_idx]);
        $all_images = array_values($all_images); // Re-index
        
        $new_main = $all_images[0] ?? null;
        $new_extra = count($all_images) > 1 ? array_slice($all_images, 1) : [];
        
        $stmt = $pdo->prepare("UPDATE products SET image = ?, images = ? WHERE id = ?");
        $stmt->execute([$new_main, json_encode($new_extra), $product_id]);
        
        // Refresh product
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
    }
    
    header('Location: edit-product.php?id=' . $product_id);
    exit;
}

// Handle set main image
if (isset($_GET['set_main'])) {
    $set_main_idx = (int)$_GET['set_main'];
    
    $all_images = [];
    if ($product['image']) {
        $all_images[] = $product['image'];
    }
    $additional_images = $product['images'] ? json_decode($product['images'], true) : [];
    if (!empty($additional_images)) {
        $all_images = array_merge($all_images, $additional_images);
    }
    
    if (isset($all_images[$set_main_idx])) {
        $new_main = $all_images[$set_main_idx];
        unset($all_images[$set_main_idx]);
        array_unshift($all_images, $new_main); // Put main first
        $all_images = array_values($all_images);
        
        $new_main_img = $all_images[0];
        $new_extra_imgs = count($all_images) > 1 ? array_slice($all_images, 1) : [];
        
        $stmt = $pdo->prepare("UPDATE products SET image = ?, images = ? WHERE id = ?");
        $stmt->execute([$new_main_img, json_encode($new_extra_imgs), $product_id]);
        
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
    }
    
    header('Location: edit-product.php?id=' . $product_id);
    exit;
}

include '../components/layout_start.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category_id = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $compare_price = $_POST['compare_price'] ?? null;
    $description = $_POST['description'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $sku = $_POST['sku'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    $uploaded_images = [];
    
    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = __DIR__ . '/../uploads/products/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
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
    
    // Build update query
    $params = [$name, $category_id, $price, $compare_price, $description, $stock, $sku, $status, $product_id, $seller['id']];
    
    // If new images uploaded, add them to existing
    if (!empty($uploaded_images)) {
        // Get existing images
        $existing_images = [];
        if ($product['image']) {
            $existing_images[] = $product['image'];
        }
        if ($product['images']) {
            $existing_extra = json_decode($product['images'], true);
            if (!empty($existing_extra)) {
                $existing_images = array_merge($existing_images, $existing_extra);
            }
        }
        
        // Combine existing + new
        $all_images = array_merge($existing_images, $uploaded_images);
        
        // New main is the first one
        $new_main = $all_images[0];
        $new_extra = count($all_images) > 1 ? array_slice($all_images, 1) : [];
        
        $sql = "UPDATE products SET name = ?, category_id = ?, price = ?, compare_price = ?, description = ?, stock = ?, sku = ?, status = ?, image = ?, images = ? WHERE id = ? AND seller_id = ?";
        $params = [$name, $category_id, $price, $compare_price, $description, $stock, $sku, $status, $new_main, json_encode($new_extra), $product_id, $seller['id']];
    } else {
        $sql = "UPDATE products SET name = ?, category_id = ?, price = ?, compare_price = ?, description = ?, stock = ?, sku = ?, status = ? WHERE id = ? AND seller_id = ?";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Refresh product to get updated data
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    $success = 'Product updated successfully!';
}

$categories = getCategories($pdo);
?>

<div class="page-header">
    <div class="header-content">
        <h1>Edit Product</h1>
        <p class="subtitle">Update your product details</p>
    </div>
</div>

<?php if ($success): ?>
<div class="alert alert-success">
    <i data-lucide="check-circle"></i> <?php echo $success; ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error">
    <i data-lucide="alert-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="form-container card">
    <form action="" method="POST" class="add-product-form" enctype="multipart/form-data">
        <div class="form-section">
            <h3>Basic Information</h3>
            <div class="form-group">
                <label for="product_name">Product Name *</label>
                <input type="text" id="product_name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Price (FCA) *</label>
                    <input type="number" id="price" name="price" value="<?php echo $product['price']; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="draft" <?php echo $product['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                </select>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Product Images</h3>
            <div class="current-images" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <?php 
                $all_images = [];
                if ($product['image']) {
                    $all_images[] = ['url' => $product['image'], 'is_main' => true];
                }
                $additional_images = $product['images'] ? json_decode($product['images'], true) : [];
                if (!empty($additional_images)) {
                    foreach($additional_images as $img) {
                        $all_images[] = ['url' => $img, 'is_main' => false];
                    }
                }
                
                if (!empty($all_images)):
                    foreach($all_images as $idx => $img): 
                    // Get just the filename from the path
                    $filename = basename($img['url']);
                    $img_path = '/AFROTOUCH/TREACY/uploads/products/' . $filename;
                    ?>
                    <div class="img-preview" style="position: relative; width: 100px; height: 120px; border-radius: 8px; overflow: hidden; border: 2px solid <?php echo $img['is_main'] ? '#D35400' : '#e0e0e0'; ?>;">
                        <img src="<?php echo $img_path; ?>" style="width: 100%; height: 80px; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                        <div style="width:100%;height:80px;background:#f5f5f5;display:none;align-items:center;justify-content:center;color:#999;font-size:0.7rem;">No Image</div>
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; font-size: 0.6rem; padding: 2px; text-align: center;">
                            <span><?php echo $img['is_main'] ? '★ Main' : 'Extra'; ?></span>
                            <?php if (!$img['is_main']): ?>
                            <a href="?id=<?php echo $product_id; ?>&set_main=<?php echo $idx; ?>" style="color: #D35400; display: block; margin-top: 2px;">Set Main</a>
                            <?php endif; ?>
                        </div>
                        <a href="?id=<?php echo $product_id; ?>&remove_img=<?php echo $idx; ?>" onclick="return confirm('Remove this image?')" style="position: absolute; top: 2px; right: 2px; background: #C62828; color: white; width: 20px; height: 20px; border-radius: 50%; text-align: center; line-height: 20px; font-size: 12px; text-decoration: none;">×</a>
                    </div>
                    <?php endforeach; 
                endif; ?>
            </div>
            <div class="form-group">
                <label>Upload New Images</label>
                <div class="image-upload-area" id="uploadArea">
                    <input type="file" id="product_images" name="images[]" multiple accept="image/*" onchange="previewNewImages(this)">
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <i data-lucide="image"></i>
                        <p>Click to upload or drag and drop</p>
                    </div>
                </div>
                <div id="newImagePreview" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem;"></div>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">
                    Click × to remove an image. Click "Set Main" to change the main image.
                </p>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Inventory</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" value="<?php echo $product['stock']; ?>" min="0">
                </div>
                <div class="form-group">
                    <label for="sku">SKU</label>
                    <input type="text" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Pricing</h3>
            <div class="form-group">
                <label for="compare_price">Compare At Price (FCA)</label>
                <input type="number" id="compare_price" name="compare_price" value="<?php echo $product['compare_price'] ?? ''; ?>">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
            <a href="products.php" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.alert { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; }
.alert-success { background: #E9F7EF; color: #188038; }
.alert-error { background: #FDECEC; color: #C62828; }

.current-image { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
.current-image span { color: var(--text-muted); font-size: 0.9rem; }

.form-container { padding: 2rem; }
.form-section { margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-color); }
.form-section:last-of-type { border-bottom: none; }
.form-section h3 { font-family: var(--font-heading); font-size: 1.25rem; margin-bottom: 1.5rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.8rem 1rem; border-radius: 12px; border: 1px solid var(--border-color); font-size: 0.95rem; }

.image-upload-area { position: relative; border: 2px dashed var(--border-color); border-radius: 12px; padding: 2rem; text-align: center; cursor: pointer; }
.image-upload-area input { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
.upload-placeholder i { width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 0.5rem; }

.form-actions { display: flex; gap: 1rem; margin-top: 1rem; }
.btn-primary { background: var(--primary-color); color: white; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 600; border: none; cursor: pointer; }
.btn-secondary { background: transparent; color: var(--text-muted); padding: 0.8rem 2rem; border-radius: 12px; font-weight: 600; border: 1px solid var(--border-color); text-decoration: none; }

#newImagePreview img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0; }
#newImagePreview .remove-btn { position: absolute; top: -5px; right: -5px; background: #C62828; color: white; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; }
#newImagePreview > div { position: relative; display: inline-block; }
</style>

<script>
function previewNewImages(input) {
    const preview = document.getElementById('newImagePreview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.innerHTML = '<img src="' + e.target.result + '" alt="Preview"><span class="remove-btn" onclick="this.parentElement.remove()">×</span>';
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>

<?php include '../components/layout_end.php'; ?>