<?php
session_start();
// admin/users.php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';
protectAdminPage();

require_once '../includes/mailer.php';

$csrf_error = '';

// Handle Moderation Action (Suspend / Delete / Approve)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['moderate_provider'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $csrf_error = 'Invalid request. Please refresh and try again.';
    } else {
        $id = intval($_POST['provider_id']);
        $action = $_POST['action_type'];
        $reason = clean_input($_POST['reason'] ?? '');
        
        $stmt = $conn->prepare("SELECT email, name FROM providers WHERE provider_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res && $res->num_rows > 0) {
            $provider = $res->fetch_assoc();
            $to_email = $provider['email'];
            $name = $provider['name'];
            
            $subject = "Update regarding your Allo_Service account";
            $message = "Hello $name,\n\n";
            
            if ($action === 'approve') {
                $stmt2 = $conn->prepare("UPDATE providers SET status = 'approved' WHERE provider_id = ?");
                $stmt2->bind_param("i", $id);
                $stmt2->execute();
                $message .= "Great news! Your professional account has been APPROVED. You can now offer your services on our platform.\n\n";
            } elseif ($action === 'suspend') {
                $stmt2 = $conn->prepare("UPDATE providers SET status = 'suspended', suspension_reason = ? WHERE provider_id = ?");
                $stmt2->bind_param("si", $reason, $id);
                $stmt2->execute();
                $message .= "Your professional account has been SUSPENDED by our moderation team.\n\nReason: $reason\n\n";
            } elseif ($action === 'delete') {
                $stmt2 = $conn->prepare("DELETE FROM providers WHERE provider_id = ?");
                $stmt2->bind_param("i", $id);
                $stmt2->execute();
                $message .= "Your professional account has been DELETED by our moderation team.\n\nReason: $reason\n\n";
            }
            
            $message .= "Regards,\nThe Allo_Service Team";
            send_mail($to_email, $subject, $message);
        }
        
        header("Location: users.php?msg=ActionCompleted");
        exit();
    }
}

// Delete a client
if (isset($_GET['delete']) && isset($_GET['type']) && $_GET['type'] === 'client' && isset($_GET['token'])) {
    if (verify_csrf_token($_GET['token'])) {
        $id = intval($_GET['delete']);
        $stmt = $conn->prepare("DELETE FROM clients WHERE client_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: users.php");
        exit();
    }
}

// Handle Client Suspension
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['suspend_client'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $csrf_error = 'Invalid request.';
    } else {
        $id = intval($_POST['client_id']);
        $action = $_POST['client_action'];
        $reason = clean_input($_POST['reason'] ?? '');
        
        if ($action === 'suspend') {
            $stmt = $conn->prepare("UPDATE clients SET status = 'suspended', suspension_reason = ? WHERE client_id = ?");
            $stmt->bind_param("si", $reason, $id);
            $stmt->execute();
        } elseif ($action === 'activate') {
            $stmt = $conn->prepare("UPDATE clients SET status = 'active', suspension_reason = NULL WHERE client_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        header("Location: users.php");
        exit();
    }
}

// Handle Client ID Verification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_client'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $csrf_error = 'Invalid request.';
    } else {
        $id = intval($_POST['client_id']);
        $action = $_POST['verify_action'];
        
        if ($action === 'verify') {
            $stmt = $conn->prepare("UPDATE clients SET id_verified = 1, id_verified_at = NOW() WHERE client_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("UPDATE clients SET id_verified = 0, id_card_front = NULL, id_card_back = NULL WHERE client_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        header("Location: users.php");
        exit();
    }
}

// Add a provider
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $csrf_error = 'Invalid request. Please refresh and try again.';
    } else {
        $name = clean_input($_POST['nom']);
        $email = clean_input($_POST['email']);
        $phone = clean_input($_POST['telephone']);
        $profession = clean_input($_POST['metier']);
        $location = clean_input($_POST['localisation'] ?? 'TBD');
        $availability = clean_input($_POST['disponibilite'] ?? 'Full-Time');
        
        if (!is_valid_email($email)) {
            $csrf_error = 'Invalid email address.';
        } elseif (!is_valid_phone($phone)) {
            $csrf_error = 'Invalid phone number.';
        } else {
            $password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO providers (name, email, password, phone, profession, location, availability, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'approved')");
            $stmt->bind_param("sssssss", $name, $email, $password, $phone, $profession, $location, $availability);
            $stmt->execute();
            header("Location: users.php?msg=ProviderAdded");
            exit();
        }
    }
}

// Fetch all providers
$res_presta = $conn->query("SELECT provider_id AS id, name, email, phone, profession, status, id_card_front, id_card_back, id_verified, 'provider' AS type FROM providers");
$providers = [];
while ($row = $res_presta->fetch_assoc()) $providers[] = $row;

// Fetch all clients with all needed columns
$res_clients = $conn->query("SELECT client_id AS id, name, email, phone, status, suspension_reason, id_card_front, id_card_back, id_verified, 'client' AS type FROM clients");
$clients = [];
while ($row = $res_clients->fetch_assoc()) $clients[] = $row;

// Handle ID Verification Action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_action'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $csrf_error = 'Invalid request.';
    } else {
        $id = intval($_POST['provider_id']);
        $action = $_POST['verify_action']; // 'verify' or 'reject'
        
        if ($action === 'verify') {
            $conn->query("UPDATE providers SET id_verified = 1, id_verified_at = NOW() WHERE provider_id = $id");
        } elseif ($action === 'reject') {
            $conn->query("UPDATE providers SET id_verified = 0, id_verified_at = NULL WHERE provider_id = $id");
        }
        
        header("Location: users.php");
        exit();
    }
}

// Fetch all clients
$res_clients = $conn->query("SELECT client_id AS id, name, email, phone, status, id_card_front, id_card_back, id_verified, 'client' AS type FROM clients");
$clients = [];
while ($row = $res_clients->fetch_assoc()) $clients[] = $row;

// Merge lists
$users = array_merge($providers, $clients);

// Fetch all categories for the dropdown
$res_categories = $conn->query("SELECT name FROM services ORDER BY name ASC");
$all_categories = [];
if ($res_categories) {
    while ($row = $res_categories->fetch_assoc()) $all_categories[] = $row['name'];
}

include 'includes/admin_header.php';

$csrf_token = generate_csrf_token();
$delete_token = generate_csrf_token();
?>

<?php if (!empty($csrf_error)): ?>
<div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin: 20px;"><?= htmlspecialchars($csrf_error) ?></div>
<?php endif; ?>

<div class="table-container" style="padding: 30px; margin-bottom: 40px;">
    <h3 style="margin-bottom:20px; color: var(--dark);">Add New Professional</h3>
    <form class="add-form" method="post" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: end;">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div><label style="font-size: 13px; font-weight: 600; color: #64748b;">Name</label><input type="text" name="nom" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;"></div>
        <div><label style="font-size: 13px; font-weight: 600; color: #64748b;">Email</label><input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;"></div>
        <div><label style="font-size: 13px; font-weight: 600; color: #64748b;">Phone</label><input type="text" name="telephone" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;"></div>
        <div><label style="font-size: 13px; font-weight: 600; color: #64748b;">Job Title (Service)</label>
            <select name="metier" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: white;">
                <option value="">Select a service...</option>
                <?php foreach ($all_categories as $cat_name): ?>
                    <option value="<?= htmlspecialchars($cat_name) ?>"><?= htmlspecialchars($cat_name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><label style="font-size: 13px; font-weight: 600; color: #64748b;">Location</label><input type="text" name="localisation" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;"></div>
        <div><label style="font-size: 13px; font-weight: 600; color: #64748b;">Availability</label>
            <select name="disponibilite" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: white;">
                <option value="Full-Time">Full-Time</option>
                <option value="Part-Time">Part-Time</option>
                <option value="Weekends Only">Weekends Only</option>
            </select>
        </div>
        <div><label style="font-size: 13px; font-weight: 600; color: #64748b;">Password</label><input type="password" name="mot_de_passe" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;"></div>
        <button type="submit" name="add" style="background: var(--primary); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Add Provider</button>
    </form>
</div>

<div class="table-container">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; text-align: left;">
                <th style="padding: 15px; border-bottom: 2px solid #e2e8f0; color: #475569;">ID</th>
                <th style="padding: 15px; border-bottom: 2px solid #e2e8f0; color: #475569;">User Information</th>
                <th style="padding: 15px; border-bottom: 2px solid #e2e8f0; color: #475569;">Role & Status</th>
                <th style="padding: 15px; border-bottom: 2px solid #e2e8f0; color: #475569;">Verification Docs</th>
                <th style="padding: 15px; border-bottom: 2px solid #e2e8f0; color: #475569;">Management</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.3s;">
                    <td style="padding: 15px; vertical-align: middle; color: #64748b; font-weight: bold;"><?= $user['id']; ?></td>
                    
                    <td style="padding: 15px; vertical-align: middle;">
                        <strong style="color: var(--dark); font-size: 15px;"><?= htmlspecialchars($user['name'] ?? ''); ?></strong><br>
                        <span style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;"><?= htmlspecialchars($user['email']); ?></span>
                        <?php if ($user['type'] === 'provider'): ?>
                            <div style="font-size: 12px; color: #007A5E; font-weight: 600;"><i class="fas fa-briefcase" style="margin-right: 4px;"></i> <?= htmlspecialchars($user['profession'] ?? '') ?></div>
                        <?php endif; ?>
                    </td>
                    
                    <td style="padding: 15px; vertical-align: middle;">
                        <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 6px; background: <?= $user['type'] === 'provider' ? '#e3f9f1' : '#eef2ff'; ?>; color: <?= $user['type'] === 'provider' ? '#007A5E' : '#4f46e5'; ?>;"><?= $user['type']; ?></span>
                        <br>
                        <?php if ($user['type'] === 'provider'): 
                            $badge_bg = $user['status'] == 'approved' ? '#dcfce7' : ($user['status'] == 'suspended' ? '#fef3c7' : '#fee2e2');
                            $badge_color = $user['status'] == 'approved' ? '#166534' : ($user['status'] == 'suspended' ? '#92400e' : '#991b1b');
                        ?>
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; text-transform: uppercase; background: <?= $badge_bg ?>; color: <?= $badge_color ?>;"><?= $user['status'] ?></span>
                        <?php else: 
                            $badge_bg = $user['status'] == 'active' ? '#dcfce7' : '#fef3c7';
                            $badge_color = $user['status'] == 'active' ? '#166534' : '#92400e';
                        ?>
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; text-transform: uppercase; background: <?= $badge_bg ?>; color: <?= $badge_color ?>;"><?= $user['status'] ?></span>
                        <?php endif; ?>
                    </td>
                    
                    <td style="padding: 15px; vertical-align: middle;">
                        <?php if ($user['type'] === 'provider'): ?>
                            <?php if ($user['id_card_front'] || $user['id_card_back']): ?>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
                                    <?php if ($user['id_card_front']): ?>
                                        <a href="../<?= htmlspecialchars($user['id_card_front']) ?>" target="_blank" style="font-size: 11px; background: #f8fafc; padding: 6px 12px; border-radius: 6px; text-decoration: none; color: #334155; font-weight: 600; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s;"><i class="fas fa-id-card"></i> Front</a>
                                    <?php endif; ?>
                                    <?php if ($user['id_card_back']): ?>
                                        <a href="../<?= htmlspecialchars($user['id_card_back']) ?>" target="_blank" style="font-size: 11px; background: #f8fafc; padding: 6px 12px; border-radius: 6px; text-decoration: none; color: #334155; font-weight: 600; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 5px; transition: 0.2s;"><i class="fas fa-id-card"></i> Back</a>
                                    <?php endif; ?>
                                </div>
                                <!-- KYC Verification Controls -->
                                <form method="post" style="display: inline-flex; gap: 5px; align-items: center;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="provider_id" value="<?= $user['id'] ?>">
                                    <?php if (!empty($user['id_verified'])): ?>
                                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold;"><i class="fas fa-check-circle"></i> Verified</span>
                                        <button type="submit" name="verify_action" value="reject" style="background: #fee2e2; color: #991b1b; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; cursor: pointer;">Reject</button>
                                    <?php else: ?>
                                        <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold;">Pending</span>
                                        <button type="submit" name="verify_action" value="verify" style="background: #22c55e; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; cursor: pointer;">Verify ID</button>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <span style="font-size: 12px; color: #94a3b8; font-style: italic;">No Documents Uploaded</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if ($user['id_card_front'] || $user['id_card_back']): ?>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px;">
                                    <?php if ($user['id_card_front']): ?>
                                        <a href="../<?= htmlspecialchars($user['id_card_front']) ?>" target="_blank" style="font-size: 11px; background: #f8fafc; padding: 6px 12px; border-radius: 6px; text-decoration: none; color: #334155; font-weight: 600; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 5px;"><i class="fas fa-id-card"></i> Front</a>
                                    <?php endif; ?>
                                    <?php if ($user['id_card_back']): ?>
                                        <a href="../<?= htmlspecialchars($user['id_card_back']) ?>" target="_blank" style="font-size: 11px; background: #f8fafc; padding: 6px 12px; border-radius: 6px; text-decoration: none; color: #334155; font-weight: 600; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 5px;"><i class="fas fa-id-card"></i> Back</a>
                                    <?php endif; ?>
                                </div>
                                <form method="post" style="display: inline-flex; gap: 5px; align-items: center;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="client_id" value="<?= $user['id'] ?>">
                                    <?php if (!empty($user['id_verified'])): ?>
                                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold;"><i class="fas fa-check-circle"></i> Verified</span>
                                    <?php else: ?>
                                        <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold;">Pending</span>
                                        <button type="submit" name="verify_action" value="verify" style="background: #22c55e; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; cursor: pointer;">Verify</button>
                                        <button type="submit" name="verify_action" value="reject" style="background: #fee2e2; color: #991b1b; border: none; padding: 4px 8px; border-radius: 4px; font-size: 10px; cursor: pointer;">Reject</button>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <span style="font-size: 12px; color: #94a3b8; font-style: italic;">No Documents</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    
                    <td style="padding: 15px; vertical-align: middle; white-space: nowrap;">
                        <?php if ($user['type'] === 'provider'): ?>
                            <a href="admin_edit_provider.php?id=<?= $user['id'] ?>" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; text-decoration: none; margin-right: 5px; display: inline-block; margin-bottom: 5px;"><i class="fas fa-edit"></i> Edit</a>
                            <a href="../profile.php?id=<?= $user['id'] ?>" target="_blank" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; text-decoration: none; margin-right: 5px; display: inline-block; margin-bottom: 5px;" title="View public profile"><i class="fas fa-external-link-alt"></i> Preview</a>
                            
                            <?php if ($user['status'] === 'pending' || $user['status'] === 'suspended'): ?>
                                <a href="#" onclick="openModerateModal(<?= $user['id'] ?>, 'approve')" style="background: var(--secondary); color: #fff; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; text-decoration: none; margin-right: 5px; display: inline-block; margin-bottom: 5px;">Approve</a>
                            <?php endif; ?>
                            <?php if ($user['status'] === 'pending' || $user['status'] === 'approved'): ?>
                                <a href="#" onclick="openModerateModal(<?= $user['id'] ?>, 'suspend')" style="background: #f59e0b; color: #fff; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; text-decoration: none; margin-right: 5px; display: inline-block; margin-bottom: 5px;">Suspend</a>
                            <?php endif; ?>
                            <a href="#" onclick="openModerateModal(<?= $user['id'] ?>, 'delete')" style="background: var(--danger); color: #fff; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; text-decoration: none; display: inline-block; margin-bottom: 5px;">Remove</a>
                        <?php else: ?>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <input type="hidden" name="client_id" value="<?= $user['id'] ?>">
                                <?php if ($user['status'] === 'suspended'): ?>
                                    <button type="submit" name="client_action" value="activate" style="background: #22c55e; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">Activate</button>
                                <?php else: ?>
                                    <button type="submit" name="client_action" value="suspend" style="background: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;" onclick="return confirm('Suspend this client?');">Suspend</button>
                                <?php endif; ?>
                            </form>
                            <a href="users.php?delete=<?= $user['id']; ?>&type=<?= $user['type']; ?>&token=<?= $delete_token ?>" style="background: var(--danger); color: #fff; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; text-decoration: none; display: inline-block; margin-bottom: 5px; margin-left: 5px;" onclick="return confirm('Delete this <?= $user['type'] ?>?');">Remove</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Moderation Modal -->
<div id="moderateModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
<div style="background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
    <h3 id="modalTitle" style="margin-bottom: 20px;">Moderate Professional</h3>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="provider_id" id="modal_provider_id">
        <input type="hidden" name="action_type" id="modal_action_type">
        
        <p id="modalDesc" style="font-size: 14px; color: #475569; margin-bottom: 15px;">Please provide a reason. This will be automatically emailed to the professional.</p>
        
        <div style="margin-bottom: 25px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Reason (Optional for Approvals):</label>
            <textarea name="reason" id="modal_reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; resize: vertical;"></textarea>
        </div>
        
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="button" onclick="closeModerateModal()" style="padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; background: #e2e8f0; color: #475569; font-weight: 600;">Cancel</button>
            <button type="submit" name="moderate_provider" id="modalSubmitBtn" style="padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; color: white; font-weight: 600;">Confirm</button>
        </div>
    </form>
</div>
</div>

<script>
function openModerateModal(id, action) {
    document.getElementById('modal_provider_id').value = id;
    document.getElementById('modal_action_type').value = action;
    
    let title = '';
    let btnColor = '';
    let btnText = '';
    let isRequired = true;
    
    if (action === 'approve') {
        title = 'Approve Professional';
        btnColor = 'var(--secondary)';
        btnText = 'Approve Account';
        isRequired = false;
        document.getElementById('modalDesc').innerText = "An automated email will be sent welcoming them to the platform. You may optionally include a message.";
    } else if (action === 'suspend') {
        title = 'Suspend Professional';
        btnColor = '#f59e0b';
        btnText = 'Suspend Account';
        document.getElementById('modalDesc').innerText = "A mandatory email will be sent explaining why they are suspended.";
    } else if (action === 'delete') {
        title = 'Delete Professional';
        btnColor = 'var(--danger)';
        btnText = 'Delete Account Forever';
        document.getElementById('modalDesc').innerText = "A mandatory email will be sent. THIS ACTION IS IRREVERSIBLE.";
    }
    
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalSubmitBtn').style.background = btnColor;
    document.getElementById('modalSubmitBtn').innerText = btnText;
    document.getElementById('modal_reason').required = isRequired;
    
    document.getElementById('moderateModal').style.display = 'flex';
}

function closeModerateModal() {
    document.getElementById('moderateModal').style.display = 'none';
}
</script>

</div> <!-- Close main-content -->
</body>
</html>