<?php
$current_page = 'manage';
include 'components/layout_start.php';
?>

<div class="page-header">
    <div class="header-content">
        <h1>Management Overview</h1>
        <p class="subtitle">Quick access to your shop's core management tasks</p>
    </div>
</div>

<div class="manage-grid">
    <div class="manage-card" onclick="location.href='products.php'">
        <div class="manage-icon"><i data-lucide="package"></i></div>
        <div class="manage-info">
            <h3>Inventory</h3>
            <p>Manage your products, stock levels, and pricing.</p>
        </div>
        <i data-lucide="chevron-right" class="arrow"></i>
    </div>
    
    <div class="manage-card" onclick="location.href='orders.php'">
        <div class="manage-icon"><i data-lucide="shopping-bag"></i></div>
        <div class="manage-info">
            <h3>Order Fulfillment</h3>
            <p>Track, fulfill, and manage customer orders.</p>
        </div>
        <i data-lucide="chevron-right" class="arrow"></i>
    </div>
    
    <div class="manage-card" onclick="location.href='my-shop.php'">
        <div class="manage-icon"><i data-lucide="store"></i></div>
        <div class="manage-info">
            <h3>Shop Profile</h3>
            <p>Customize your storefront and brand identity.</p>
        </div>
        <i data-lucide="chevron-right" class="arrow"></i>
    </div>
    
    <div class="manage-card" onclick="location.href='settings.php'">
        <div class="manage-icon"><i data-lucide="user-cog"></i></div>
        <div class="manage-info">
            <h3>Account Settings</h3>
            <p>Manage your personal info and security.</p>
        </div>
        <i data-lucide="chevron-right" class="arrow"></i>
    </div>
</div>

<style>
.manage-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.manage-card {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.manage-card:hover {
    transform: translateY(-4px);
    border-color: var(--primary-color);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.manage-icon {
    width: 60px;
    height: 60px;
    background: #FAF7F5;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
}

.manage-info h3 {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
}

.manage-info p {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.arrow {
    margin-left: auto;
    color: var(--border-color);
    transition: color 0.2s;
}

.manage-card:hover .arrow {
    color: var(--primary-color);
}
</style>

<?php include 'components/layout_end.php'; ?>
