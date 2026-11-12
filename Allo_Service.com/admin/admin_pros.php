<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
protectAdminPage();
require_once '../includes/csrf.php';

$message = '';

// Handle provider actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['moderate_provider'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request.';
    } else {
        $id = intval($_POST['provider_id']);
        $action = $_POST['action_type'];
        $reason = clean_input($_POST['reason'] ?? '');
        
        if ($action === 'approve') {
            $conn->query("UPDATE providers SET status = 'approved' WHERE provider_id = $id");
        } elseif ($action === 'suspend') {
            $stmt = $conn->prepare("UPDATE providers SET status = 'suspended', suspension_reason = ? WHERE provider_id = ?");
            $stmt->bind_param("si", $reason, $id);
            $stmt->execute();
        } elseif ($action === 'delete') {
            $conn->query("DELETE FROM providers WHERE provider_id = $id");
        }
        header("Location: admin_pros.php");
        exit();
    }
}

// Handle ID verification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_provider'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request.';
    } else {
        $id = intval($_POST['provider_id']);
        $action = $_POST['verify_action'];
        
        if ($action === 'verify') {
            $conn->query("UPDATE providers SET id_verified = 1, id_verified_at = NOW() WHERE provider_id = $id");
        } elseif ($action === 'reject') {
            $conn->query("UPDATE providers SET id_verified = 0, id_card_front = NULL, id_card_back = NULL WHERE provider_id = $id");
        }
        header("Location: admin_pros.php");
        exit();
    }
}

// Fetch providers
$providers = [];
$res = $conn->query("SELECT * FROM providers ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) $providers[] = $row;

$total = count($providers);
$approved = count(array_filter($providers, fn($p) => $p['status'] === 'approved'));
$pending = count(array_filter($providers, fn($p) => $p['status'] === 'pending'));
$suspended = count(array_filter($providers, fn($p) => $p['status'] === 'suspended'));

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Professionals | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css?v=1.1">
</head>
<body>
<?php include 'includes/admin_header.php'; 

if ($message):
?>
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin: 0 30px;"><?= $message ?></div>
<?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Professionals</h3>
            <div class="value"><?= $total ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #22c55e;">
            <h3>Approved</h3>
            <div class="value" style="color: #22c55e;"><?= $approved ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <h3>Pending</h3>
            <div class="value" style="color: #f59e0b;"><?= $pending ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #ef4444;">
            <h3>Suspended</h3>
            <div class="value" style="color: #ef4444;"><?= $suspended ?></div>
        </div>
    </div>

    <div class="table-container" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc;">
                    <th style="padding: 15px; text-align: left;">ID</th>
                    <th>User Information</th>
                    <th>Status</th>
                    <th>Verification</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($providers as $p): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px; font-weight: bold; color: #64748b;"><?= $p['provider_id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                        <small style="color: #64748b;"><?= htmlspecialchars($p['email']) ?></small><br>
                        <small style="color: #007a33;"><?= htmlspecialchars($p['profession']) ?></small>
                    </td>
                    <td>
                        <?php $color = $p['status'] === 'approved' ? '#22c55e' : ($p['status'] === 'pending' ? '#f59e0b' : '#ef4444'); ?>
                        <span style="background: <?= $color ?>22; color: <?= $color ?>; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                            <?= $p['status'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($p['id_card_front'] || $p['id_card_back']): ?>
                            <div style="display: flex; gap: 5px; margin-bottom: 5px;">
                                <?php if ($p['id_card_front']): ?><a href="../<?= $p['id_card_front'] ?>" target="_blank" style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 11px; text-decoration: none; color: #475569;">Front</a><?php endif; ?>
                                <?php if ($p['id_card_back']): ?><a href="../<?= $p['id_card_back'] ?>" target="_blank" style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 11px; text-decoration: none; color: #475569;">Back</a><?php endif; ?>
                            </div>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="verify_provider" value="1">
                                <input type="hidden" name="provider_id" value="<?= $p['provider_id'] ?>">
                                <?php if ($p['id_verified']): ?>
                                    <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 11px;"><i class="fas fa-check"></i> Verified</span>
                                    <button type="submit" name="verify_action" value="reject" style="background: #fee2e2; color: #991b1b; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; cursor: pointer;">Reject</button>
                                <?php else: ?>
                                    <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 11px;">Pending</span>
                                    <button type="submit" name="verify_action" value="verify" style="background: #22c55e; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; cursor: pointer;">Verify</button>
                                <?php endif; ?>
                            </form>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-size: 12px;">No docs</span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space: nowrap;">
                        <a href="admin_edit_provider.php?id=<?= $p['provider_id'] ?>" style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; margin-right: 5px;"><i class="fas fa-edit"></i></a>
                        <a href="../profile.php?id=<?= $p['provider_id'] ?>" target="_blank" style="background: #eef2ff; color: #4f46e5; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; margin-right: 5px;"><i class="fas fa-eye"></i></a>
                        <?php if ($p['status'] !== 'approved'): ?>
                        <a href="#" onclick="openModerateModal(<?= $p['provider_id'] ?>, 'approve')" style="background: #22c55e; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; margin-right: 5px;">Approve</a>
                        <?php endif; ?>
                        <?php if ($p['status'] !== 'suspended'): ?>
                        <a href="#" onclick="openModerateModal(<?= $p['provider_id'] ?>, 'suspend')" style="background: #f59e0b; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; margin-right: 5px;">Suspend</a>
                        <?php endif; ?>
                        <a href="#" onclick="openModerateModal(<?= $p['provider_id'] ?>, 'delete')" style="background: #ef4444; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Moderation Modal -->
<div id="moderateModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
<div style="background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 400px;">
    <h3 id="modalTitle" style="margin-bottom: 20px;">Moderate</h3>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="moderate_provider" value="1">
        <input type="hidden" name="provider_id" id="modal_provider_id">
        <input type="hidden" name="action_type" id="modal_action_type">
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Reason (required for suspend/delete)</label>
            <textarea name="reason" id="modal_reason" rows="3" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;"></textarea>
        </div>
        <button type="submit" style="background: #007a33; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; width: 100%;">Confirm</button>
        <button type="button" onclick="document.getElementById('moderateModal').style.display='none'" style="background: #e2e8f0; color: #475569; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; width: 100%; margin-top: 10px;">Cancel</button>
    </form>
</div>

<script>
function openModerateModal(id, action) {
    document.getElementById('modal_provider_id').value = id;
    document.getElementById('modal_action_type').value = action;
    document.getElementById('modalTitle').innerText = action === 'approve' ? 'Approve Professional' : (action === 'suspend' ? 'Suspend Professional' : 'Delete Professional');
    document.getElementById('modal_reason').required = (action === 'suspend' || action === 'delete');
    document.getElementById('moderateModal').style.display = 'flex';
}
</script>
</body>
</html>
