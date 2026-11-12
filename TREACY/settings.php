<?php
$current_page = 'settings';
include 'components/layout_start.php';
?>

<div class="page-header">
    <div class="header-content">
        <h1>Settings</h1>
        <p class="subtitle">Manage your account, privacy and security</p>
    </div>
</div>

<div class="settings-layout">
    <div class="settings-nav card">
        <ul class="settings-menu">
            <li class="active"><i data-lucide="user"></i> Account Profile</li>
            <li><i data-lucide="lock"></i> Password & Security</li>
            <li><i data-lucide="bell"></i> Notifications</li>
            <li><i data-lucide="banknote"></i> Payout Methods</li>
            <li><i data-lucide="globe"></i> Regional Settings</li>
        </ul>
    </div>
    
    <div class="settings-content-wrapper">
        <div class="card p-4">
            <div class="settings-section-header">
                <h3>Account Profile</h3>
                <p>Publicly shared information across the platform.</p>
            </div>
            
            <form class="settings-form mt-4">
                <div class="avatar-edit mb-4">
                    <img src="https://api.dicebear.com/7.x/initials/svg?seed=Felix&backgroundColor=2D1F1A" alt="Avatar">
                    <div class="avatar-actions">
                        <button type="button" class="btn-primary">Change Photo</button>
                        <button type="button" class="btn-text">Remove</button>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" value="Felix">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" value="Artisan">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" value="felix@afrotouch.com">
                    <small class="text-muted">Verified on March 12, 2024</small>
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" value="+237 600 000 000">
                </div>

                <div class="form-group">
                    <label>Timezone</label>
                    <select>
                        <option>(GMT+01:00) West Central Africa</option>
                        <option>(GMT+00:00) Greenwich Mean Time</option>
                    </select>
                </div>
                
                <div class="mt-4 pt-4 border-top">
                    <button type="submit" class="btn-primary-lg">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.settings-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 1.5rem;
    align-items: start;
}

.settings-nav { padding: 1.5rem; }
.settings-menu { list-style: none; }
.settings-menu li {
    padding: 0.85rem 1.25rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: var(--text-muted);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.settings-menu li:hover { background: #FAF7F5; color: var(--text-dark); }
.settings-menu li.active {
    background: #FAF7F5;
    color: var(--primary-color);
    font-weight: 600;
}

.settings-section-header {
    margin-bottom: 2rem;
}

.settings-section-header h3 { font-size: 1.5rem; margin-bottom: 0.25rem; }

.avatar-edit {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.avatar-edit img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-actions { display: flex; gap: 1rem; align-items: center; }
.btn-text { background: none; border: none; color: #E74C3C; font-weight: 600; cursor: pointer; }

.mb-4 { margin-bottom: 2rem; }
.border-top { border-top: 1px solid var(--border-color); }
.pt-4 { padding-top: 2rem; }

@media (max-width: 900px) {
    .settings-layout { grid-template-columns: 1fr; }
}
</style>

<?php include 'components/layout_end.php'; ?>
