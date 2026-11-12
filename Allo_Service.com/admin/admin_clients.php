<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
protectAdminPage();
require_once '../includes/csrf.php';

$message = '';

// Handle client suspension/activation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['client_action'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request.';
    } else {
        $id = intval($_POST['client_id']);
        $action = $_POST['client_action'];
        
        if ($action === 'suspend' && isset($_POST['suspend_client'])) {
            $reason = clean_input($_POST['reason'] ?? '');
            $stmt = $conn->prepare("UPDATE clients SET status = 'suspended', suspension_reason = ? WHERE client_id = ?");
            $stmt->bind_param("si", $reason, $id);
            $stmt->execute();
        } elseif ($action === 'activate') {
            $conn->query("UPDATE clients SET status = 'active', suspension_reason = NULL WHERE client_id = $id");
        }
        header("Location: admin_clients.php");
        exit();
    }
}

// Handle ID verification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_client'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request.';
    } else {
        $id = intval($_POST['client_id']);
        $action = $_POST['verify_action'];
        
        if ($action === 'verify') {
            $conn->query("UPDATE clients SET id_verified = 1, id_verified_at = NOW() WHERE client_id = $id");
        } elseif ($action === 'reject') {
            $conn->query("UPDATE clients SET id_verified = 0, id_card_front = NULL, id_card_back = NULL WHERE client_id = $id");
        }
        header("Location: admin_clients.php");
        exit();
    }
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (verify_csrf_token($_GET['token'])) {
        $id = intval($_GET['delete']);
        $conn->query("DELETE FROM clients WHERE client_id = $id");
        header("Location: admin_clients.php");
        exit();
    }
}

// Fetch clients
$clients = [];
$res = $conn->query("SELECT * FROM clients ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) $clients[] = $row;

$total = count($clients);
$verified = count(array_filter($clients, fn($c) => $c['id_verified'] == 1));
$pending = count(array_filter($clients, fn($c) => $c['id_verified'] == 0 && !empty($c['id_card_front'])));
$suspended = count(array_filter($clients, fn($c) => $c['status'] === 'suspended'));

$csrf_token = generate_csrf_token();
$delete_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clients | Admin</title>
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
            <h3>Total Clients</h3>
            <div class="value"><?= $total ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #22c55e;">
            <h3>Verified</h3>
            <div class="value" style="color: #22c55e;"><?= $verified ?></div>
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
                    <th>ID Verification</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $c): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px; font-weight: bold; color: #64748b;"><?= $c['client_id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($c['name']) ?></strong><br>
                        <small style="color: #64748b;"><?= htmlspecialchars($c['email']) ?></small><br>
                        <small style="color: #64748b;"><?= htmlspecialchars($c['phone'] ?? 'No phone') ?></small>
                    </td>
                    <td>
                        <?php $color = $c['status'] === 'active' ? '#22c55e' : '#ef4444'; ?>
                        <span style="background: <?= $color ?>22; color: <?= $color ?>; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                            <?= $c['status'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($c['id_card_front'] || $c['id_card_back']): ?>
                            <div style="display: flex; gap: 5px; margin-bottom: 5px;">
                                <?php if ($c['id_card_front']): ?><a href="../<?= $c['id_card_front'] ?>" target="_blank" style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 11px; text-decoration: none; color: #475569;">Front</a><?php endif; ?>
                                <?php if ($c['id_card_back']): ?><a href="../<?= $c['id_card_back'] ?>" target="_blank" style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 11px; text-decoration: none; color: #475569;">Back</a><?php endif; ?>
                            </div>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="verify_client" value="1">
                                <input type="hidden" name="client_id" value="<?= $c['client_id'] ?>">
                                <?php if ($c['id_verified']): ?>
                                    <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 11px;"><i class="fas fa-check"></i> Verified</span>
                                    <button type="submit" name="verify_action" value="reject" style="background: #fee2e2; color: #991b1b; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; cursor: pointer;">Reject</button>
                                <?php else: ?>
                                    <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 11px;">Pending</span>
                                    <button type="submit" name="verify_action" value="verify" style="background: #22c55e; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; cursor: pointer;">Verify</button>
                                    <button type="submit" name="verify_action" value="reject" style="background: #fee2e2; color: #991b1b; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; cursor: pointer;">Reject</button>
                                <?php endif; ?>
                            </form>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-size: 12px;">No documents</span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space: nowrap;">
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="client_id" value="<?= $c['client_id'] ?>">
                            <?php if ($c['status'] === 'suspended'): ?>
                            <button type="submit" name="client_action" value="activate" style="background: #22c55e; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">Activate</button>
                            <?php else: ?>
                            <button type="button" onclick="openSuspendModal(<?= $c['client_id'] ?>)" style="background: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">Suspend</button>
                            <?php endif; ?>
                            <a href="admin_clients.php?delete=<?= $c['client_id'] ?>&token=<?= $delete_token ?>" style="background: #ef4444; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;" onclick="return confirm('Delete this client?');">Remove</a>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
<div style="background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 400px;">
    <h3 style="margin-bottom: 20px;">Suspend Client</h3>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="suspend_client" value="1">
        <input type="hidden" name="client_id" id="suspend_client_id">
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Reason for suspension</label>
            <textarea name="reason" id="suspend_reason" rows="3" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;" required></textarea>
        </div>
        <button type="submit" name="client_action" value="suspend" style="background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; width: 100%;">Suspend Client</button>
        <button type="button" onclick="document.getElementById('suspendModal').style.display='none'" style="background: #e2e8f0; color: #475569; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; width: 100%; margin-top: 10px;">Cancel</button>
    </form>
</div>

<script>
function openSuspendModal(id) {
    document.getElementById('suspend_client_id').value = id;
    document.getElementById('suspendModal').style.display = 'flex';
}
</script>
</body>
</html>
