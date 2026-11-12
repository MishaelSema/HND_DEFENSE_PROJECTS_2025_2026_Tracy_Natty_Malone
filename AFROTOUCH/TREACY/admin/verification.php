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
    header('Location: verification.php');
    exit;
}

$current_page = 'admin-verification';
include '../components/layout_start.php';

$stmt = $pdo->query("SELECT s.*, u.name, u.email FROM sellers s LEFT JOIN users u ON s.user_id = u.id WHERE s.verification_status IN ('pending', 'processing') ORDER BY s.created_at DESC");
$pending_verifications = $stmt->fetchAll();

$stmt = $pdo->query("SELECT s.*, u.name, u.email FROM sellers s LEFT JOIN users u ON s.user_id = u.id WHERE s.verification_status IN ('verified', 'rejected') ORDER BY s.updated_at DESC LIMIT 20");
$past_verifications = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="header-content">
        <h1>Verification Requests</h1>
        <p class="subtitle">Review seller verification documents</p>
    </div>
</div>

<?php if (empty($pending_verifications)): ?>
<div class="empty-state card">
    <i data-lucide="shield-check" size="48"></i>
    <h3>No Pending Requests</h3>
    <p>There are no verification requests to review</p>
</div>
<?php else: ?>

<div class="verification-grid">
    <?php foreach ($pending_verifications as $seller): ?>
    <?php 
    $verification_data = $seller['verification_data'] ? json_decode($seller['verification_data'], true) : [];
    ?>
    <div class="verify-card">
        <div class="verify-header">
            <div class="store-info">
                <h3><?php echo htmlspecialchars($seller['store_name']); ?></h3>
                <p><?php echo htmlspecialchars($seller['name']); ?> • <?php echo htmlspecialchars($seller['email']); ?></p>
                <span class="submitted-date">Submitted: <?php echo date('M d, Y', strtotime($verification_data['submitted_at'] ?? $seller['created_at'])); ?></span>
            </div>
        </div>
        
        <div class="documents-grid">
            <?php if (!empty($verification_data['id_front'])): ?>
            <div class="doc-card">
                <div class="doc-icon"><i data-lucide="id-card"></i></div>
                <div class="doc-info">
                    <span class="doc-label">ID Front</span>
                    <a href="../<?php echo htmlspecialchars($verification_data['id_front']); ?>" target="_blank" class="doc-view">
                        <i data-lucide="external-link"></i> View
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($verification_data['id_back'])): ?>
            <div class="doc-card">
                <div class="doc-icon"><i data-lucide="id-card"></i></div>
                <div class="doc-info">
                    <span class="doc-label">ID Back</span>
                    <a href="../<?php echo htmlspecialchars($verification_data['id_back']); ?>" target="_blank" class="doc-view">
                        <i data-lucide="external-link"></i> View
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($verification_data['business_doc'])): ?>
            <div class="doc-card">
                <div class="doc-icon"><i data-lucide="briefcase"></i></div>
                <div class="doc-info">
                    <span class="doc-label">Business Doc</span>
                    <a href="../<?php echo htmlspecialchars($verification_data['business_doc']); ?>" target="_blank" class="doc-view">
                        <i data-lucide="external-link"></i> View
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="verify-actions">
            <a href="?action=approve&id=<?php echo $seller['id']; ?>" class="btn-approve">
                <i data-lucide="check-circle"></i> Approve
            </a>
            <a href="?action=reject&id=<?php echo $seller['id']; ?>" class="btn-reject" onclick="return confirm('Reject this verification request?')">
                <i data-lucide="x-circle"></i> Reject
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="past-verifications mt-4">
    <h2>Recent Decisions</h2>
    <?php if (empty($past_verifications)): ?>
    <div class="card" style="padding: 2rem; text-align: center; color: var(--text-muted);">
        No past verifications yet
    </div>
    <?php else: ?>
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Store</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($past_verifications as $v): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($v['store_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($v['name']); ?></td>
                    <td>
                        <?php if ($v['verification_status'] === 'verified'): ?>
                        <span class="badge badge-verified"><i data-lucide="check"></i> Verified</span>
                        <?php else: ?>
                        <span class="badge badge-rejected"><i data-lucide="x"></i> Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($v['updated_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<style>
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state svg { color: var(--primary-color); margin-bottom: 1rem; }
.empty-state h3 { margin: 0 0 0.5rem; }
.empty-state p { color: var(--text-muted); margin: 0; }

.verification-grid {
    display: grid;
    gap: 1.5rem;
}

.verify-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    padding: 1.5rem;
}

.verify-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.store-info h3 { margin: 0 0 0.25rem; }
.store-info p { margin: 0; color: var(--text-muted); font-size: 0.9rem; }
.submitted-date { font-size: 0.8rem; color: var(--text-muted); }

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.doc-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #fafafa;
    border-radius: 12px;
}

.doc-icon {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
}

.doc-info { flex: 1; }
.doc-label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.25rem; }
.doc-view {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.85rem;
    color: var(--primary-color);
    text-decoration: none;
}

.verify-actions {
    display: flex;
    gap: 1rem;
}

.btn-approve, .btn-reject {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: transform 0.2s;
}

.btn-approve { background: #E9F7EF; color: #188038; }
.btn-approve:hover { transform: scale(1.02); }

.btn-reject { background: #FDECEC; color: #C62828; }
.btn-reject:hover { transform: scale(1.02); }

.past-verifications h2 { margin-bottom: 1.5rem; font-size: 1.25rem; }
.mt-4 { margin-top: 3rem; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 1rem; text-align: left; }
.data-table th { background: #FAFAFA; font-size: 0.85rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color); }
.data-table td { border-bottom: 1px solid #F5F5F5; }

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge-verified { background: #E9F7EF; color: #188038; }
.badge-rejected { background: #FDECEC; color: #C62828; }
</style>

<?php include '../components/layout_end.php'; ?>