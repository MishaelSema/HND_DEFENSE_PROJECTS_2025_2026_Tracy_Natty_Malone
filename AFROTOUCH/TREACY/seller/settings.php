<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

$seller = getSellerByUserId($pdo, $_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store_name = $_POST['store_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $country = $_POST['country'] ?? '';
    $city = $_POST['city'] ?? '';
    $address = $_POST['address'] ?? '';
    $store_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $store_name)));
    
    if (empty($store_name)) {
        $error = 'Store name is required';
    } else {
        if ($seller) {
            $stmt = $pdo->prepare("UPDATE sellers SET store_name = ?, description = ?, country = ?, city = ?, address = ?, store_slug = ? WHERE id = ?");
            $stmt->execute([$store_name, $description, $country, $city, $address, $store_slug, $seller['id']]);
            $success = 'Shop updated successfully!';
            $seller = getSellerByUserId($pdo, $_SESSION['user_id']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO sellers (user_id, store_name, store_slug, description, country, city, address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $store_name, $store_slug, $description, $country, $city, $address]);
            $seller = getSellerByUserId($pdo, $_SESSION['user_id']);
            $_SESSION['seller_id'] = $seller['id'];
            header('Location: shop.php?just_created=1');
            exit;
        }
    }
}

$current_page = 'shop-setup';
include '../components/layout_start.php';
?>

<div class="page-header">
    <div class="header-content">
        <h1>Shop Settings</h1>
        <p class="subtitle">Set up and manage your public shop</p>
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

<?php if ($seller): ?>
<div class="shop-preview card">
    <div class="preview-header">
        <h3><i data-lucide="eye"></i> Your Shop Preview</h3>
        <a href="../shop/index.php?slug=<?php echo htmlspecialchars($seller['store_slug']); ?>" target="_blank" class="btn-secondary">
            <i data-lucide="external-link"></i> View Public Shop
        </a>
    </div>
    <div class="shop-url">
        <strong>Shop URL:</strong> 
        <span>afrotouch.com/shop/index.php?slug=<?php echo htmlspecialchars($seller['store_slug']); ?></span>
    </div>
    <?php if ($seller['is_verified']): ?>
    <div class="verified-badge">
        <i data-lucide="badge-check"></i> Verified Seller
    </div>
    <?php elseif (in_array($seller['verification_status'], ['pending', 'processing'])): ?>
    <div class="pending-badge">
        <i data-lucide="clock"></i> Verification Processing
    </div>
    <?php else: ?>
    <div class="verify-prompt">
        <p>Get verified to increase trust and visibility!</p>
        <a href="../verify.php" class="btn-primary">Get Verified</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="form-container card">
    <form method="POST" class="shop-form">
        <div class="form-section">
            <h3>Basic Information</h3>
            <div class="form-group">
                <label for="store_name">Store Name *</label>
                <input type="text" id="store_name" name="store_name" value="<?php echo htmlspecialchars($seller['store_name'] ?? ''); ?>" placeholder="Your artisan shop name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Tell customers about your shop and what you sell..."><?php echo htmlspecialchars($seller['description'] ?? ''); ?></textarea>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Location</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($seller['country'] ?? ''); ?>" placeholder="e.g. Ghana">
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($seller['city'] ?? ''); ?>" placeholder="e.g. Accra">
                </div>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($seller['address'] ?? ''); ?>" placeholder="Full address">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<style>
.alert { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; }
.alert-success { background: #E9F7EF; color: #188038; }
.alert-error { background: #FDECEC; color: #C62828; }

.shop-preview { padding: 1.5rem; margin-bottom: 2rem; }

.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.preview-header h3 { display: flex; align-items: center; gap: 0.5rem; margin: 0; font-size: 1.1rem; }

.shop-url {
    padding: 1rem;
    background: #fafafa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.shop-url span { color: var(--primary-color); font-weight: 500; }

.verified-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #E9F7EF;
    color: #188038;
    padding: 0.5rem 1rem;
    border-radius: 100px;
    font-weight: 600;
}

.pending-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #FFF4E5;
    color: #B45D00;
    padding: 0.5rem 1rem;
    border-radius: 100px;
    font-weight: 600;
}

.verify-prompt {
    padding: 1rem;
    background: #fafafa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.verify-prompt p { margin: 0; color: var(--text-muted); }

.form-container { padding: 2rem; }

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.form-section:last-of-type { border-bottom: none; }
.form-section h3 { margin-bottom: 1.5rem; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; }
.form-group input, .form-group textarea {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 0.8rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    color: var(--text-color);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    border: 1px solid var(--border-color);
    font-size: 0.85rem;
}

@media (max-width: 768px) {
    .form-row { grid-template-columns: 1fr; }
    .preview-header { flex-direction: column; gap: 1rem; align-items: flex-start; }
}
</style>

<?php include '../components/layout_end.php'; ?>