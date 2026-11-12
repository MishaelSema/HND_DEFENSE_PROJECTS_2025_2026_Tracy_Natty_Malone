<?php
$current_page = 'shop';
include 'components/layout_start.php';
?>

<div class="page-header">
    <div class="header-content">
        <h1>My Shop</h1>
        <p class="subtitle">Customize how customers see your brand</p>
    </div>
    <div class="header-actions">
        <button class="btn-primary" onclick="window.open('home.php', '_blank')">
            <i data-lucide="external-link" size="16"></i> View Public Store
        </button>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="shop-banner-setup">
        <div class="banner-preview">
            <div class="overlay-camera">
                <i data-lucide="camera"></i>
                <span>Change Banner</span>
            </div>
        </div>
        <div class="shop-profile-info">
            <div class="shop-logo-setup">
                <img src="https://api.dicebear.com/7.x/initials/svg?seed=Shop&backgroundColor=D35400" alt="Logo">
                <div class="edit-overlay"><i data-lucide="camera"></i></div>
            </div>
            <div class="shop-name-status">
                <h2>AfroTouch Artisan Hub</h2>
                <span class="badge badge-processing">Verified Seller</span>
            </div>
        </div>
    </div>
    
    <div class="p-4">
        <form class="shop-form">
            <div class="form-row mt-4">
                <div class="form-group">
                    <label>Shop Name</label>
                    <input type="text" value="AfroTouch Artisan Hub" placeholder="Enter shop name">
                </div>
                <div class="form-group">
                    <label>Tagline</label>
                    <input type="text" value="Authentic African Craftsmanship" placeholder="Enter tagline">
                </div>
            </div>
            
            <div class="form-group">
                <label>Shop Biography</label>
                <textarea rows="6" placeholder="Tell your story...">Connecting you with the finest authentic African craftsmanship. Each piece in our collection is handmade by master artisans across the continent, preserving ancient techniques while supporting local communities.</textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Primary Category</label>
                    <select>
                        <option>Art & Collectibles</option>
                        <option>Clothing & Fashion</option>
                        <option>Home Decor</option>
                        <option>Jewelry</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" value="Douala, Cameroon">
                </div>
            </div>

            <div class="form-actions-sticky mt-4">
                <button type="submit" class="btn-primary-lg">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<style>
.shop-banner-setup {
    position: relative;
    border-bottom: 1px solid var(--border-color);
}

.banner-preview {
    height: 180px;
    background: linear-gradient(135deg, #2D1F1A 0%, #4A332C 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.overlay-camera {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255,255,255,0.6);
    transition: color 0.2s;
}

.banner-preview:hover .overlay-camera { color: white; }

.shop-profile-info {
    display: flex;
    align-items: flex-end;
    gap: 1.5rem;
    padding: 0 2.5rem 1.5rem;
    margin-top: -50px;
}

.shop-logo-setup {
    width: 120px;
    height: 120px;
    border-radius: 24px;
    border: 4px solid white;
    background: #FAFAFA;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.shop-logo-setup img { width: 100%; height: 100%; object-fit: cover; }

.edit-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
    cursor: pointer;
}

.shop-logo-setup:hover .edit-overlay { opacity: 1; }

.shop-name-status h2 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    font-family: var(--font-heading);
}

.p-4 { padding: 2.5rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

@media (max-width: 600px) {
    .form-row { grid-template-columns: 1fr; }
    .shop-profile-info { flex-direction: column; align-items: flex-start; margin-top: -60px; }
}

.btn-primary-lg {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 1.25rem 3rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    box-shadow: 0 10px 20px rgba(211, 84, 0, 0.15);
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-primary-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(211, 84, 0, 0.2);
}
</style>

<?php include 'components/layout_end.php'; ?>
