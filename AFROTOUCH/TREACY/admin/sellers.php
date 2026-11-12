<?php
require_once __DIR__ . '/../components/auth_check.php';
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../home.php');
    exit;
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $seller_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE sellers SET verification_status = 'verified', is_verified = 1 WHERE id = ?");
        $stmt->execute([$seller_id]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE sellers SET verification_status = 'rejected', is_verified = 0 WHERE id = ?");
        $stmt->execute([$seller_id]);
    }
    header('Location: sellers.php');
    exit;
}

$current_page = 'admin-sellers';
include '../components/layout_start.php';

$stmt = $pdo->query("SELECT s.*, u.name as owner_name, u.email as owner_email, u.is_active as user_active,
    (SELECT COUNT(*) FROM products WHERE seller_id = s.id) as product_count
    FROM sellers s 
    LEFT JOIN users u ON s.user_id = u.id 
    ORDER BY s.created_at DESC");
$sellers = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="header-content">
        <h1>All Sellers</h1>
        <p class="subtitle">Manage seller accounts and verification</p>
    </div>
</div>

<div class="card">
    <?php if (empty($sellers)): ?>
    <div style="padding: 4rem 2rem; text-align: center; color: var(--text-muted);">
        <i data-lucide="store" style="width: 64px; height: 64px; margin-bottom: 1rem;"></i>
        <h3>No sellers yet</h3>
    </div>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Store</th>
                <th>Owner</th>
                <th>Products</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sellers as $seller): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($seller['store_name']); ?></strong>
                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $seller['city'] . ($seller['country'] ? ', ' . $seller['country'] : ''); ?></div>
                </td>
                <td>
                    <div><?php echo htmlspecialchars($seller['owner_name']); ?></div>
                    <small><?php echo htmlspecialchars($seller['owner_email']); ?></small>
                </td>
                <td><?php echo $seller['product_count']; ?></td>
                <td>
                    <?php if ($seller['is_verified']): ?>
                    <span class="badge badge-verified">Verified</span>
                    <?php elseif (in_array($seller['verification_status'], ['pending', 'processing'])): ?>
                    <span class="badge badge-pending">Processing</span>
                    <?php elseif ($seller['verification_status'] === 'rejected'): ?>
                    <span class="badge badge-rejected">Rejected</span>
                    <?php elseif (empty($seller['verification_status'])): ?>
                    <span class="badge" style="background: #F5F5F5; color: #666;">Not Started</span>
                    <?php else: ?>
                    <span class="badge">Unverified</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($seller['is_verified']): ?>
                    <span style="color: var(--text-muted); font-size: 0.85rem;">✓ Verified</span>
                    <?php elseif ($seller['verification_status'] === 'rejected'): ?>
                    <span style="color: #C62828; font-size: 0.85rem;">✗ Rejected</span>
                    <?php else: ?>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <?php if (!empty($seller['verification_data'])): ?>
                        <button onclick="viewDocuments(<?php echo htmlspecialchars(json_encode($seller['verification_data'])); ?>)" class="btn-sm" style="background: #0066CC; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; border: none; font-size: 0.8rem; cursor: pointer;">
                            <i data-lucide="file-text" size="14"></i> Documents
                        </button>
                        <?php endif; ?>
                        <a href="?action=approve&id=<?php echo $seller['id']; ?>" class="btn-sm" style="background: #188038; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; text-decoration: none; font-size: 0.8rem;">
                            <i data-lucide="check" size="14"></i> Approve
                        </a>
                        <a href="?action=reject&id=<?php echo $seller['id']; ?>" class="btn-sm" style="background: #C62828; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; text-decoration: none; font-size: 0.8rem;" onclick="return confirm('Reject this seller?');">
                            <i data-lucide="x" size="14"></i> Reject
                        </a>
                    </div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 1rem; text-align: left; }
.data-table th { background: #FAFAFA; font-size: 0.8rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color); }
.data-table td { border-bottom: 1px solid #F5F5F5; }
.data-table td:first-child { font-weight: 600; }
.data-table small { display: block; color: var(--text-muted); font-size: 0.8rem; }

.badge { padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 600; }
.badge-verified { background: #E9F7EF; color: #188038; }
.badge-pending { background: #FFF4E5; color: #B45D00; }
.badge-rejected { background: #FDECEC; color: #C62828; }
</style>

<script>
lucide.createIcons();

function viewDocuments(dataStr) {
    const data = JSON.parse(dataStr);
    let content = '<div style="text-align:left; max-height: 400px; overflow-y: auto; padding: 1rem;">';
    
    content += '<h3 style="margin-bottom:1rem; color: #2D1F1A;">📋 Verification Documents</h3>';
    
    if (data.doc_type) {
        content += '<p style="margin: 0.5rem 0;"><strong>Document Type:</strong> ' + data.doc_type + '</p>';
    }
    if (data.doc_number) {
        content += '<p style="margin: 0.5rem 0;"><strong>Document Number:</strong> ' + data.doc_number + '</p>';
    }
    if (data.address) {
        content += '<p style="margin: 0.5rem 0;"><strong>Address:</strong> ' + data.address + '</p>';
    }
    if (data.city) {
        content += '<p style="margin: 0.5rem 0;"><strong>City:</strong> ' + data.city + '</p>';
    }
    if (data.country) {
        content += '<p style="margin: 0.5rem 0;"><strong>Country:</strong> ' + data.country + '</p>';
    }
    if (data.plan) {
        content += '<p style="margin: 0.5rem 0;"><strong>Plan:</strong> ' + data.plan + '</p>';
    }
    if (data.submitted_at) {
        content += '<p style="margin: 0.5rem 0;"><strong>Submitted:</strong> ' + data.submitted_at + '</p>';
    }
    
    content += '<h4 style="margin-top:1.5rem; color: #2D1F1A;">🗂️ Uploaded Files:</h4>';
    
    if (data.id_front) {
        content += '<p><a href="../uploads/' + data.id_front + '" target="_blank" style="display:inline-flex;align-items:center;gap:0.5rem;background:#D35400;color:white;padding:0.5rem 1rem;border-radius:8px;text-decoration:none;margin:0.25rem;"><i data-lucide="file-text"></i> View ID Front</a></p>';
    }
    if (data.id_back) {
        content += '<p><a href="../uploads/' + data.id_back + '" target="_blank" style="display:inline-flex;align-items:center;gap:0.5rem;background:#D35400;color:white;padding:0.5rem 1rem;border-radius:8px;text-decoration:none;margin:0.25rem;"><i data-lucide="file-text"></i> View ID Back</a></p>';
    }
    
    if (!data.id_front && !data.id_back) {
        content += '<p style="color: #666;">No files uploaded yet.</p>';
    }
    
    content += '</div>';
    
    // Create modal
    const modal = document.createElement('div');
    modal.id = 'docModal';
    modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999;';
    modal.onclick = function(e) { if(e.target===modal) modal.remove(); };
    
    modal.innerHTML = '<div style="background:white;border-radius:16px;max-width:500px;width:90%;max-height:80vh;overflow:hidden;">' +
        '<div style="padding:1.5rem;border-bottom:1px solid #e0e0e0;display:flex;justify-content:space-between;align-items:center;">' +
        '<h2 style="margin:0;font-size:1.25rem;">Verification Details</h2>' +
        '<button onclick="document.getElementById(\'docModal\').remove()" style="background:none;border:none;font-size:1.5rem;cursor:pointer;padding:0.5rem;">✕</button>' +
        '</div>' +
        '<div>' + content + '</div>' +
        '</div>';
    
    document.body.appendChild(modal);
    lucide.createIcons();
}
</script>

<?php include '../components/layout_end.php'; ?>