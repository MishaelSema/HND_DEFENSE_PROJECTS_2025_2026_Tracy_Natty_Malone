<?php
require_once __DIR__ . '/../components/auth_check.php';
$current_page = 'buyer-settings';
include '../components/layout_start.php';

include '../db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    if ($name) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $_SESSION['user_id']]);
        $_SESSION['user_name'] = $name;
        $success = 'Profile updated successfully!';
    } else {
        $error = 'Name is required';
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="page-header">
    <div class="header-content">
        <h1>My Profile</h1>
        <p class="subtitle">Manage your account information</p>
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

<div class="card" style="padding: 2rem;">
    <h3>Profile Information</h3>
    <form method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" style="width: 100%; padding: 0.8rem; border-radius: 12px; border: 1px solid var(--border-color);">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="width: 100%; padding: 0.8rem; border-radius: 12px; border: 1px solid var(--border-color); opacity: 0.6;">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" style="width: 100%; padding: 0.8rem; border-radius: 12px; border: 1px solid var(--border-color);">
        </div>
        <button type="submit" class="btn-primary">Save Changes</button>
    </form>
</div>

<div class="card" style="padding: 2rem; margin-top: 2rem;">
    <h3>Account Security</h3>
    <p style="color: var(--text-muted); margin-bottom: 1rem;">Change your password</p>
    <a href="change-password.php" class="btn-secondary" style="display: inline-block; padding: 0.8rem 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); text-decoration: none;">Change Password</a>
</div>

<style>
.alert { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; }
.alert-success { background: #E9F7EF; color: #188038; }
.alert-error { background: #FDECEC; color: #C62828; }
.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; }
.btn-primary { background: var(--primary-color); color: white; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 600; border: none; cursor: pointer; }
h3 { margin: 0 0 1.5rem; font-size: 1.25rem; }
</style>

<?php include '../components/layout_end.php'; ?>