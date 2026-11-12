<?php 
include '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$issue_id = $_GET['id'] ?? null;

if (!$issue_id) {
    redirect('index.php');
}

// Get issue details with user info
$stmt = $pdo->prepare("
    SELECT i.*, u.username, u.email 
    FROM issues i 
    JOIN users u ON i.user_id = u.id 
    WHERE i.id = ?
");
$stmt->execute([$issue_id]);
$issue = $stmt->fetch();

if (!$issue) {
    redirect('index.php');
}

// Get all replies for this issue
$replies = getIssueReplies($issue_id);

$message = '';
$messageType = '';

// Handle reply actions
if (isset($_POST['action'])) {
    $admin_id = $_SESSION['user_id'];
    
    if ($_POST['action'] === 'add_reply' && !empty($_POST['reply_text'])) {
        addReply($issue_id, $admin_id, trim($_POST['reply_text']));
        $message = 'Reply added successfully!';
        $messageType = 'success';
    } elseif ($_POST['action'] === 'edit_reply' && !empty($_POST['reply_text']) && !empty($_POST['reply_id'])) {
        updateReply($_POST['reply_id'], trim($_POST['reply_text']));
        $message = 'Reply updated successfully!';
        $messageType = 'success';
    } elseif ($_POST['action'] === 'delete_reply' && !empty($_POST['reply_id'])) {
        deleteReply($_POST['reply_id']);
        $message = 'Reply deleted successfully!';
        $messageType = 'success';
    }
    
    // Refresh replies
    $replies = getIssueReplies($issue_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    // Handle public/private toggle
    if (isset($_POST['toggle_public'])) {
        $new_value = $issue['is_public'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE issues SET is_public = ? WHERE id = ?");
        $stmt->execute([$new_value, $issue_id]);
        
        $stmt = $pdo->prepare("SELECT i.*, u.username, u.email FROM issues i JOIN users u ON i.user_id = u.id WHERE i.id = ?");
        $stmt->execute([$issue_id]);
        $issue = $stmt->fetch();
        $message = $new_value ? 'Report is now public.' : 'Report is now private.';
        $messageType = 'success';
    } else {
        $status = $_POST['status'];
        $admin_note = trim($_POST['admin_note']);

        $stmt = $pdo->prepare("UPDATE issues SET status = ?, admin_note = ? WHERE id = ?");
        
        if ($stmt->execute([$status, $admin_note, $issue_id])) {
            $email_subject = "Issue Status Update - #$issue_id";
            $email_body = "
                <h2>Issue Status Updated</h2>
                <p>Dear {$issue['username']},</p>
                <p>The status of your reported issue has been updated:</p>
                <ul>
                    <li><strong>Issue ID:</strong> #$issue_id</li>
                    <li><strong>Title:</strong> {$issue['title']}</li>
                    <li><strong>New Status:</strong> $status</li>
                </ul>
            ";
            
            if (!empty($admin_note)) {
                $email_body .= "
                    <p><strong>Admin Response:</strong> $admin_note</p>
                ";
            }
            
            $email_body .= "
                <p>Thank you for using our IssueSpot.</p>
                <p>Best regards,<br>Admin Team</p>
            ";
            
            sendEmail($issue['email'], $email_subject, $email_body);
            
            $message = 'Issue updated successfully! User has been notified via email.';
            $messageType = 'success';
            
            $stmt = $pdo->prepare("SELECT i.*, u.username, u.email FROM issues i JOIN users u ON i.user_id = u.id WHERE i.id = ?");
            $stmt->execute([$issue_id]);
            $issue = $stmt->fetch();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Issue - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css?v=2">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo"><a href="../index.php" style="color: white; text-decoration: none;">Admin Panel</a></div>
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span><span></span><span></span>
            </div>
            <nav id="main-nav">
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="../logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <script>
    function toggleMenu() {
        document.getElementById('main-nav').classList.toggle('active');
    }
    </script>

    <main>
        <div class="container">
            <div class="dashboard-header">
                <h1>Manage Issue #<?php echo $issue_id; ?></h1>
                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="card">
                    <h2>Issue Details</h2>
                    <p><strong>Title:</strong> <?php echo htmlspecialchars($issue['title']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($issue['category']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($issue['location']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($issue['description']); ?></p>
                    <p><strong>Reported by:</strong> <?php echo htmlspecialchars($issue['username']); ?> (<?php echo htmlspecialchars($issue['email']); ?>)</p>
                    <p><strong>Date Reported:</strong> <?php echo date('M d, Y H:i', strtotime($issue['created_at'])); ?></p>
                    <p><strong>Current Status:</strong> 
                        <span class="status status-<?php echo strtolower(str_replace(' ', '-', $issue['status'])); ?>">
                            <?php echo $issue['status']; ?>
                        </span>
                    </p>
                    <p><strong>Visibility:</strong> 
                        <span style="padding: 0.3rem 0.8rem; background: <?php echo $issue['is_public'] ? '#28a745' : '#6c757d'; ?>; color: white; border-radius: 20px; font-size: 0.85rem;">
                            <?php echo $issue['is_public'] ? 'Public' : 'Private'; ?>
                        </span>
                    </p>
                    <div style="margin: 1rem 0; padding: 0.5rem; background: #f8f9fa; border-radius: 5px;">
                        <strong>Votes:</strong> 
                        <span style="color: #28a745; margin-left: 1rem;">▲ Upvotes: <?php echo $issue['upvotes']; ?></span>
                        <span style="color: #dc3545; margin-left: 1rem;">▼ Downvotes: <?php echo $issue['downvotes']; ?></span>
                        <span style="margin-left: 1rem; font-weight: bold;">Score: <?php echo $issue['upvotes'] - $issue['downvotes']; ?></span>
                    </div>
                    <form method="POST" style="margin-bottom: 1rem;">
                        <button type="submit" name="toggle_public" class="btn btn-small <?php echo $issue['is_public'] ? 'btn-secondary' : 'btn-success'; ?>">
                            <?php echo $issue['is_public'] ? 'Make Private' : 'Make Public'; ?>
                        </button>
                    </form>
                    <?php 
                    $images = getIssueImages($issue['image_path']);
                    if (!empty($images)): ?>
                        <p><strong>Images (<?php echo count($images); ?>):</strong></p>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-top: 0.5rem;">
                            <?php foreach ($images as $img): ?>
                                <img src="../<?php echo htmlspecialchars($img); ?>" alt="Issue image" style="width: 100%; border-radius: 5px; object-fit: cover;">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h2>Update Status</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="Pending" <?php echo $issue['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="In Progress" <?php echo $issue['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Resolved" <?php echo $issue['status'] == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                <option value="Rejected" <?php echo $issue['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="admin_note">Admin Note</label>
                            <textarea id="admin_note" name="admin_note" placeholder="Enter admin note..."><?php echo htmlspecialchars($issue['admin_note'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn">Update Status</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <h2>Replies Timeline</h2>
                
                <?php if (!empty($replies)): ?>
                    <div style="border-left: 3px solid #2196F3; padding-left: 1rem; margin-bottom: 1.5rem;">
                        <?php foreach ($replies as $reply): ?>
                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; position: relative;">
                                <p style="margin: 0; color: #666; font-size: 0.85rem;">
                                    <strong><?php echo htmlspecialchars($reply['admin_name']); ?></strong> - 
                                    <?php echo date('M d, Y h:i A', strtotime($reply['created_at'])); ?>
                                    <?php if ($reply['updated_at'] != $reply['created_at']): ?>
                                        (edited)
                                    <?php endif; ?>
                                </p>
                                <p style="margin: 0.5rem 0;"><?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?></p>
                                <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                    <button type="button" class="btn btn-small btn-secondary" onclick="document.getElementById('edit-form-<?php echo $reply['id']; ?>').style.display = 'block'">Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this reply?');">
                                        <input type="hidden" name="action" value="delete_reply">
                                        <input type="hidden" name="reply_id" value="<?php echo $reply['id']; ?>">
                                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                    </form>
                                </div>
                                <div id="edit-form-<?php echo $reply['id']; ?>" style="display: none; margin-top: 1rem;">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="edit_reply">
                                        <input type="hidden" name="reply_id" value="<?php echo $reply['id']; ?>">
                                        <textarea name="reply_text" rows="3" style="width: 100%;"><?php echo htmlspecialchars($reply['reply_text']); ?></textarea>
                                        <button type="submit" class="btn btn-small" style="margin-top: 0.5rem;">Save</button>
                                        <button type="button" class="btn btn-small btn-secondary" onclick="document.getElementById('edit-form-<?php echo $reply['id']; ?>').style.display = 'none'">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h3>Add New Reply</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_reply">
                    <div class="form-group">
                        <textarea name="reply_text" rows="4" placeholder="Write your reply..." required></textarea>
                    </div>
                    <button type="submit" class="btn">Add Reply</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 IssueSpot. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
