<header class="top-nav">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <button class="btn-burger" id="burgerToggle">
            <i data-lucide="menu"></i>
        </button>
        <div class="page-title">
            <i data-lucide="layout" class="text-muted"></i>
            <?php
            $role = $_SESSION['role'] ?? 'buyer';
            $titles = [
                'seller' => 'Seller Dashboard',
                'buyer' => 'Buyer Dashboard',
                'admin' => 'Admin Dashboard'
            ];
            ?>
            <span><?php echo $titles[$role] ?? 'Dashboard'; ?></span>
            <?php if ($role === 'seller' && isset($_SESSION['seller_id'])): ?>
                <?php 
                include __DIR__ . '/../db.php';
                $seller = getSellerByUserId($pdo, $_SESSION['user_id']);
                if ($seller && $seller['is_verified']): ?>
                <span class="badge-success ml-2">Verified</span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="top-actions">
        <a href="../home.php" style="text-decoration: none; color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; margin-right: 1rem;">
            <i data-lucide="external-link" size="16"></i> Back to Store
        </a>
        <div class="search-bar">
            <i data-lucide="search" size="18"></i>
            <input type="text" placeholder="Search orders, products...">
        </div>
        
        <div class="icon-group" style="display: flex; gap: 1rem; align-items: center;">
            <button class="btn-icon">
                <i data-lucide="bell"></i>
            </button>
            <div class="user-profile" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;" onclick="toggleUserMenu()">
                <img src="<?php echo $_SESSION['user_avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($_SESSION['user_name'] ?? 'user'); ?>" alt="User" style="width: 32px; height: 32px; border-radius: 50%; background: #eee;">
                <span style="font-size: 0.85rem; font-weight: 500;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                <i data-lucide="chevron-down" size="16"></i>
            </div>
        </div>
    </div>
</header>

<div id="userDropdown" class="user-dropdown" style="display: none; position: absolute; right: 20px; top: 70px; background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 0.5rem; min-width: 180px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 100;">
    <a href="../profile.php" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; text-decoration: none; color: var(--text-color); border-radius: 8px; font-size: 0.9rem;">
        <i data-lucide="user" size="16"></i> My Profile
    </a>
    <?php if ($role === 'seller'): ?>
    <a href="shop.php" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; text-decoration: none; color: var(--text-color); border-radius: 8px; font-size: 0.9rem;">
        <i data-lucide="store" size="16"></i> My Shop
    </a>
    <?php endif; ?>
    <div style="border-top: 1px solid var(--border-color); margin: 0.5rem 0;"></div>
    <a href="../logout.php" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; text-decoration: none; color: #C62828; border-radius: 8px; font-size: 0.9rem;">
        <i data-lucide="log-out" size="16"></i> Logout
    </a>
</div>

<style>
.badge-success {
    background: #E9F7EF;
    color: #188038;
    padding: 2px 8px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 500;
}
.btn-icon {
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ml-2 { margin-left: 0.5rem; }
.user-dropdown a:hover { background: #f5f5f5; }
</style>

<script>
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.user-profile') && !e.target.closest('#userDropdown')) {
        document.getElementById('userDropdown').style.display = 'none';
    }
});
</script>