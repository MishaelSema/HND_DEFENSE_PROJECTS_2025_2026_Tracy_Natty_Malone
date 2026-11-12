<?php
session_start();
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
?>

<?php include 'components/store-layout_start.php'; ?>

<section class="settings-section">
    <h1>My Profile</h1>
    
    <div class="settings-grid">
        <div class="settings-nav">
            <a href="buyer-dashboard.php">← Back to Dashboard</a>
        </div>
        
        <div class="settings-content">
            <form class="settings-form">
                <div class="form-section">
                    <h3>Personal Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="<?php echo $_SESSION['user_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" value="<?php echo $_SESSION['user_email'] ?? ''; ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" placeholder="+237 600 000 000">
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Shipping Addresses</h3>
                    
                    <div class="address-card">
                        <div class="address-header">
                            <strong>Default Address</strong>
                            <button type="button" class="btn-edit">Edit</button>
                        </div>
                        <p>No address added yet</p>
                        <button type="button" class="btn-add-address">+ Add Address</button>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Change Password</h3>
                    
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" placeholder="Enter current password">
                    </div>
                    
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" placeholder="Enter new password">
                    </div>
                    
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" placeholder="Confirm new password">
                    </div>
                </div>
                
                <button type="submit" class="btn-save">Save Changes</button>
            </form>
        </div>
    </div>
</section>

<style>
.settings-section {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

.settings-section h1 {
    font-family: var(--font-heading);
    font-size: 2.5rem;
    margin-bottom: 2rem;
}

.settings-grid {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 2rem;
}

.settings-nav a {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 0.9rem;
}

.settings-nav a:hover {
    color: var(--accent-orange);
}

.settings-content {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    border: 1px solid var(--border-color);
}

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

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-group input {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
}

.form-group input:focus {
    border-color: var(--accent-orange);
    outline: none;
}

.form-group input:disabled {
    background: #f5f5f5;
    color: var(--text-muted);
}

.address-card {
    background: #f9f9f9;
    padding: 1.5rem;
    border-radius: 12px;
}

.address-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.btn-edit {
    background: none;
    border: none;
    color: var(--accent-orange);
    cursor: pointer;
    font-weight: 600;
}

.btn-add-address {
    background: none;
    border: 2px dashed var(--border-color);
    width: 100%;
    padding: 1rem;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 1rem;
    color: var(--text-muted);
}

.btn-add-address:hover {
    border-color: var(--accent-orange);
    color: var(--accent-orange);
}

.btn-save {
    background: var(--accent-orange);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
}

@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'components/store-layout_end.php'; ?>
