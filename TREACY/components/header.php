<header class="top-nav">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <button class="btn-burger" id="burgerToggle">
            <i data-lucide="menu"></i>
        </button>
        <div class="page-title">
            <i data-lucide="layout" class="text-muted"></i>
            <span>Seller Dashboard</span>
            <span class="badge-success ml-2">Verified</span>
        </div>
    </div>

    
    <div class="top-actions">
        <a href="home.php" style="text-decoration: none; color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; margin-right: 1rem;">
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
            <div class="user-profile" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" alt="User" style="width: 32px; height: 32px; border-radius: 50%; background: #eee;">
                <i data-lucide="chevron-down" size="16"></i>
            </div>
        </div>
    </div>
</header>
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
</style>
