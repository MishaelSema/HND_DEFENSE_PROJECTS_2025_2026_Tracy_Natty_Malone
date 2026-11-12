<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../home.php');
    exit;
}

$current_page = 'admin-settings';
include '../components/layout_start.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'] ?? 'AfroTouch';
    $support_email = $_POST['support_email'] ?? '';
    $stmt = $pdo->query("UPDATE settings SET value = '$site_name' WHERE key = 'site_name'");
    $success = 'Settings updated!';
}
?>

<div class="page-header">
    <div class="header-content">
        <h1>Settings</h1>
        <p class="subtitle">Platform settings and configuration</p>
    </div>
</div>

<?php if ($success): ?>
<div class="alert alert-success">
    <i data-lucide="check-circle"></i> <?php echo $success; ?>
</div>
<?php endif; ?>

<div class="card" style="padding: 2rem;">
    <h3>General Settings</h3>
    <form method="POST">
        <div class="form-group">
            <label>Site Name</label>
            <input type="text" name="site_name" value="AfroTouch" style="width: 100%; padding: 0.8rem; border-radius: 12px; border: 1px solid var(--border-color);">
        </div>
        <div class="form-group">
            <label>Support Email</label>
            <input type="email" name="support_email" placeholder="support@afrotouch.com" style="width: 100%; padding: 0.8rem; border-radius: 12px; border: 1px solid var(--border-color);">
        </div>
        <button type="submit" class="btn-primary">Save Settings</button>
    </form>
</div>

<style>
.alert { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; }
.alert-success { background: #E9F7EF; color: #188038; }
.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; }
.btn-primary { background: var(--primary-color); color: white; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 600; border: none; cursor: pointer; }
</style>

<?php include '../components/layout_end.php'; ?>