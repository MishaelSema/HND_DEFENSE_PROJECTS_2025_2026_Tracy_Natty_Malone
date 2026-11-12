<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../home.php');
    exit;
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    switch ($action) {
        case 'activate':
            $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
            $stmt->execute([$user_id]);
            break;
        case 'deactivate':
            $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
            $stmt->execute([$user_id]);
            break;
        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            break;
    }
    header('Location: users.php');
    exit;
}

$current_page = 'admin-users';
include '../components/layout_start.php';

$sellers = getAllSellers($pdo);
$buyers = getAllBuyers($pdo);
$all_users = getAllUsers($pdo);
?>

<div class="page-header">
    <div class="header-content">
        <h1>User Management</h1>
        <p class="subtitle">Manage all sellers, buyers, and admin accounts</p>
    </div>
</div>

<div class="tabs-container">
    <div class="tabs">
        <button class="tab active" onclick="showTab('all')">All Users (<?php echo count($all_users); ?>)</button>
        <button class="tab" onclick="showTab('sellers')">Sellers (<?php echo count($sellers); ?>)</button>
        <button class="tab" onclick="showTab('buyers')">Buyers (<?php echo count($buyers); ?>)</button>
    </div>
</div>

<div id="all" class="tab-content active">
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_users as $user): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?php echo urlencode($user['name']); ?>" style="width: 36px; height: 36px; border-radius: 50%;">
                            <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="badge badge-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                    <td>
                        <?php if ($user['is_active']): ?>
                        <span class="badge badge-active">Active</span>
                        <?php else: ?>
                        <span class="badge badge-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <?php if ($user['is_active']): ?>
                            <a href="?action=deactivate&id=<?php echo $user['id']; ?>" class="btn-action btn-warning" title="Deactivate">⛔</a>
                            <?php else: ?>
                            <a href="?action=activate&id=<?php echo $user['id']; ?>" class="btn-action btn-success" title="Activate">✓</a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?php echo $user['id']; ?>" class="btn-action btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="sellers" class="tab-content">
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Store</th>
                    <th>Owner</th>
                    <th>Email</th>
                    <th>Products</th>
                    <th>Verification</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $seller): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($seller['store_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($seller['name']); ?></td>
                    <td><?php echo htmlspecialchars($seller['email']); ?></td>
                    <td><?php echo $seller['product_count']; ?></td>
                    <td>
                        <?php if ($seller['is_verified']): ?>
                        <span class="badge badge-verified">Verified</span>
                        <?php elseif (in_array($seller['verification_status'], ['pending', 'processing'])): ?>
                        <span class="badge badge-pending">Processing</span>
                        <?php else: ?>
                        <span class="badge">Unverified</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                        $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
                        $stmt->execute([$seller['user_id']]);
                        $seller_user = $stmt->fetch();
                        ?>
                        <?php if ($seller_user && $seller_user['is_active']): ?>
                        <span class="badge badge-active">Active</span>
                        <?php else: ?>
                        <span class="badge badge-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <?php if ($seller_user && $seller_user['is_active']): ?>
                            <a href="?action=deactivate&id=<?php echo $seller['user_id']; ?>" class="btn-action btn-warning" title="Deactivate">⛔</a>
                            <?php else: ?>
                            <a href="?action=activate&id=<?php echo $seller['user_id']; ?>" class="btn-action btn-success" title="Activate">✓</a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?php echo $seller['user_id']; ?>" class="btn-action btn-danger" title="Delete" onclick="return confirm('Delete this seller and all their products?')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="buyers" class="tab-content">
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Orders</th>
                    <th>Joined</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($buyers as $buyer): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?php echo urlencode($buyer['name']); ?>" style="width: 36px; height: 36px; border-radius: 50%;">
                            <strong><?php echo htmlspecialchars($buyer['name']); ?></strong>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($buyer['email']); ?></td>
                    <td><?php echo $buyer['order_count']; ?></td>
                    <td><?php echo date('M d, Y', strtotime($buyer['created_at'])); ?></td>
                    <td>
                        <?php if ($buyer['is_active']): ?>
                        <span class="badge badge-active">Active</span>
                        <?php else: ?>
                        <span class="badge badge-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <?php if ($buyer['is_active']): ?>
                            <a href="?action=deactivate&id=<?php echo $buyer['id']; ?>" class="btn-action btn-warning" title="Deactivate">⛔</a>
                            <?php else: ?>
                            <a href="?action=activate&id=<?php echo $buyer['id']; ?>" class="btn-action btn-success" title="Activate">✓</a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?php echo $buyer['id']; ?>" class="btn-action btn-danger" title="Delete" onclick="return confirm('Are you sure?')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.tabs-container { margin-bottom: 2rem; }
.tabs { display: flex; gap: 0.5rem; border-bottom: 1px solid var(--border-color); }
.tab {
    padding: 1rem 1.5rem;
    background: none;
    border: none;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-muted);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
}
.tab.active { color: var(--primary-color); border-bottom-color: var(--primary-color); }

.tab-content { display: none; }
.tab-content.active { display: block; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 1rem 1.5rem; text-align: left; }
.data-table th { background: #FAFAFA; font-size: 0.8rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color); }
.data-table td { border-bottom: 1px solid #F5F5F5; }

.badge { padding: 4px 10px; border-radius: 100px; font-size: 0.7rem; font-weight: 600; }
.badge-seller { background: #FFF4E5; color: #B45D00; }
.badge-buyer { background: #EBF5FF; color: #0066CC; }
.badge-admin { background: #E9F7EF; color: #188038; }
.badge-verified { background: #E9F7EF; color: #188038; }
.badge-pending { background: #FFF4E5; color: #B45D00; }
.badge-active { background: #E9F7EF; color: #188038; }
.badge-inactive { background: #FDECEC; color: #C62828; }

.action-buttons { display: flex; gap: 0.5rem; }
.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: white;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}
.btn-action:hover { opacity: 0.8; }
.btn-success { background: #E9F7EF; border-color: #188038; }
.btn-warning { background: #FFF4E5; border-color: #B45D00; }
.btn-danger { background: #FDECEC; border-color: #C62828; }
</style>

<script>
function showTab(tabId) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}
</script>

<?php include '../components/layout_end.php'; ?>