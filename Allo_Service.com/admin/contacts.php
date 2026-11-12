<?php
// admin/contacts.php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';
protectAdminPage();
require_once '../includes/mailer.php';

$csrf_error = '';
$success = '';
$error = '';

// Delete a message
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (verify_csrf_token($_GET['token'])) {
        $id = intval($_GET['delete']);
        $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: contacts.php");
        exit();
    } else {
        $csrf_error = 'Invalid request token.';
    }
}

// Handle Reply
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_reply'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $csrf_error = 'Invalid request. Please refresh and try again.';
    } else {
        $id = intval($_POST['message_id']);
        $to_email = clean_input($_POST['to_email']);
        $reply_subject = clean_input($_POST['reply_subject']);
        $reply_message = clean_input($_POST['reply_message']);
        
        if (!is_valid_email($to_email)) {
            $error = 'Invalid email address.';
        } else {
            if (send_mail($to_email, $reply_subject, $reply_message)) {
                $stmt = $conn->prepare("UPDATE contacts SET status = 'replied' WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $success = "Reply sent successfully.";
            } else {
                $error = "Failed to send email. Please try again.";
            }
        }
    }
}

// Mark as read
if (isset($_GET['read']) && isset($_GET['token'])) {
    if (verify_csrf_token($_GET['token'])) {
        $id = intval($_GET['read']);
        $stmt = $conn->prepare("UPDATE contacts SET status = 'read' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: contacts.php");
        exit();
    }
}

// Fetch all messages
$res = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
$messages = [];
while ($row = $res->fetch_assoc()) $messages[] = $row;

include 'includes/admin_header.php';

$csrf_token = generate_csrf_token();
$delete_token = generate_csrf_token();
?>

<?php if (!empty($csrf_error)): ?>
<div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin: 20px;"><?= htmlspecialchars($csrf_error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin: 20px;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div style="background: #fef3c7; color: #92400e; padding: 15px; border-radius: 8px; margin: 20px;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Sender</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($messages)): ?>
                <tr><td colspan="6" style="text-align:center; padding: 20px;">No inquiries found.</td></tr>
            <?php else: ?>
                <?php foreach ($messages as $m): ?>
                    <tr>
                        <td style="font-size: 12px; color: #888;"><?= date('M j, Y H:i', strtotime($m['created_at'])) ?></td>
                        <td><strong><?= htmlspecialchars($m['name']) ?></strong><br><small><?= htmlspecialchars($m['email']) ?></small></td>
                        <td><strong><?= htmlspecialchars($m['subject']) ?></strong></td>
                        <td style="font-size: 13px; color: #555; max-width: 300px;"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
                        <td>
                            <?php if ($m['status'] === 'new'): ?>
                                <span style="background: var(--danger); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">NEW</span>
                            <?php elseif ($m['status'] === 'read'): ?>
                                <span style="background: #64748b; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">READ</span>
                            <?php else: ?>
                                <span style="background: var(--secondary); color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">REPLIED</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($m['status'] !== 'replied'): ?>
                                <a href="#" onclick="openReplyModal(<?= $m['id'] ?>, '<?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($m['subject'], ENT_QUOTES, 'UTF-8') ?>')" style="background: var(--secondary); color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 5px; display: inline-block; margin-bottom: 5px;">Reply</a>
                            <?php endif; ?>
                            <?php if ($m['status'] === 'new'): ?>
                                <a href="contacts.php?read=<?= $m['id'] ?>&token=<?= $csrf_token ?>" style="background: #64748b; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 5px; display: inline-block; margin-bottom: 5px;">Mark Read</a>
                            <?php endif; ?>
                            <a href="contacts.php?delete=<?= $m['id'] ?>&token=<?= $delete_token ?>" style="background: var(--danger); color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold; display: inline-block; margin-bottom: 5px;" onclick="return confirm('Delete this message?');">Delete</a>
                        </td>
                    </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Reply Modal -->
<div id="replyModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
<div style="background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 20px;">Reply to Inquiry</h3>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="message_id" id="modal_message_id">
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">To:</label>
            <input type="email" name="to_email" id="modal_to_email" readonly style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; color: #64748b;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Subject:</label>
            <input type="text" name="reply_subject" id="modal_subject" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
        </div>
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Message:</label>
            <textarea name="reply_message" required rows="5" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; resize: vertical;"></textarea>
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="button" onclick="closeReplyModal()" style="padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; background: #e2e8f0; color: #475569; font-weight: 600;">Cancel</button>
            <button type="submit" name="send_reply" style="padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; background: var(--primary); color: white; font-weight: 600;">Send Reply</button>
        </div>
    </form>
</div>
</div>

<script>
function openReplyModal(id, email, subject) {
    document.getElementById('modal_message_id').value = id;
    document.getElementById('modal_to_email').value = email;
    document.getElementById('modal_subject').value = 'Re: ' + subject;
    document.getElementById('replyModal').style.display = 'flex';
}

function closeReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
}
</script>

</div> <!-- Close main-content -->
</body>
</html>
