<?php 
include 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$issue_id = $_GET['id'] ?? null;

if (!$issue_id) {
    redirect('dashboard.php');
}

$user_id = $_SESSION['user_id'];

// Get issue details
$stmt = $pdo->prepare("SELECT i.*, u.username FROM issues i JOIN users u ON i.user_id = u.id WHERE i.id = ?");
$stmt->execute([$issue_id]);
$issue = $stmt->fetch();

if (!$issue) {
    redirect('dashboard.php');
}

// Check access - user must be owner or admin or report must be public
if ($issue['user_id'] != $user_id && !isAdmin() && $issue['is_public'] == 0) {
    die("Access denied");
}

// Handle voting
if (isset($_POST['vote'])) {
    $vote_type = $_POST['vote'];
    $result = vote($issue_id, $vote_type);
    // Refresh the page to show updated votes
    redirect("report_details.php?id=$issue_id");
}

// Toggle public/private
if (isset($_POST['toggle_public'])) {
    $new_value = $issue['is_public'] ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE issues SET is_public = ? WHERE id = ?");
    $stmt->execute([$new_value, $issue_id]);
    redirect("report_details.php?id=$issue_id");
}

$user_vote = getUserVote($issue_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Details - IssueSpot</title>
    <link rel="stylesheet" href="css/style.css?v=3">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo"><a href="index.php" style="color: white; text-decoration: none;">IssueSpot</a></div>
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span><span></span><span></span>
            </div>
            <nav id="main-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="public_reports.php">Public Reports</a></li>
                    <li><a href="report_issue.php">Report Issue</a></li>
                    <li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
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
                <h1>Issue Details</h1>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>

            <div class="card">
                <h2><?php echo htmlspecialchars($issue['title']); ?></h2>
                
                <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                    <span class="status status-<?php echo strtolower(str_replace(' ', '-', $issue['status'])); ?>">
                        <?php echo $issue['status']; ?>
                    </span>
                    <span style="color: #666;">
                        Reported by: <strong><?php echo htmlspecialchars($issue['username']); ?></strong>
                    </span>
                    <span style="color: #666;">
                        Reported: <?php echo date('M d, Y', strtotime($issue['created_at'])); ?>
                    </span>
                    <span style="color: #666;">
                        Updated: <?php echo date('M d, Y', strtotime($issue['updated_at'])); ?>
                    </span>
                    <span style="padding: 0.3rem 0.8rem; background: <?php echo $issue['is_public'] ? '#28a745' : '#6c757d'; ?>; color: white; border-radius: 20px; font-size: 0.85rem;">
                        <?php echo $issue['is_public'] ? 'Public' : 'Private'; ?>
                    </span>
                </div>

                <?php if ($issue['user_id'] == $user_id || isAdmin()): ?>
                    <form method="POST" style="margin-bottom: 1rem;">
                        <button type="submit" name="toggle_public" class="btn btn-small <?php echo $issue['is_public'] ? 'btn-secondary' : 'btn-success'; ?>">
                            <?php echo $issue['is_public'] ? 'Make Private' : 'Make Public'; ?>
                        </button>
                    </form>
                <?php endif; ?>

                <p><strong>Category:</strong> <?php echo htmlspecialchars($issue['category']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($issue['location']); ?></p>
                <p><strong>Description:</strong></p>
                <p style="margin-bottom: 1rem;"><?php echo nl2br(htmlspecialchars($issue['description'])); ?></p>

                <?php 
                $images = getIssueImages($issue['image_path']);
                if (!empty($images)): ?>
                    <p><strong>Images (<?php echo count($images); ?>):</strong></p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                        <?php foreach ($images as $img): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Issue image" style="width: 100%; border-radius: 5px; object-fit: cover;">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div style="display: flex; gap: 1rem; align-items: center; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <strong>Votes:</strong>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="vote" value="upvote" class="btn btn-small <?php echo $user_vote === 'upvote' ? 'btn-success' : 'btn-secondary'; ?>">
                            ▲ Upvote (<?php echo $issue['upvotes']; ?>)
                        </button>
                    </form>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="vote" value="downvote" class="btn btn-small <?php echo $user_vote === 'downvote' ? 'btn-danger' : 'btn-secondary'; ?>">
                            ▼ Downvote (<?php echo $issue['downvotes']; ?>)
                        </button>
                    </form>
                    <span style="color: #666; font-size: 0.9rem;">
                        Total Score: <?php echo $issue['upvotes'] - $issue['downvotes']; ?>
                    </span>
                </div>

                <?php 
                $replies = getIssueReplies($issue_id);
                if (!empty($replies)): ?>
                    <div style="margin-top: 1.5rem;">
                        <h3 style="margin-bottom: 1rem;">Replies Timeline</h3>
                        <div style="border-left: 3px solid #2196F3; padding-left: 1rem;">
                            <?php foreach ($replies as $reply): ?>
                                <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                                    <p style="margin: 0; color: #666; font-size: 0.85rem;">
                                        <strong><?php echo htmlspecialchars($reply['admin_name']); ?></strong> - 
                                        <?php echo date('M d, Y h:i A', strtotime($reply['created_at'])); ?>
                                        <?php if ($reply['updated_at'] != $reply['created_at']): ?>
                                            (edited)
                                        <?php endif; ?>
                                    </p>
                                    <p style="margin: 0.5rem 0;"><?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isAdmin()): ?>
                <div class="card">
                    <h2>Add Reply</h2>
                    <form method="POST" action="admin/manage.php?id=<?php echo $issue_id; ?>">
                        <input type="hidden" name="action" value="add_reply">
                        <div class="form-group">
                            <textarea name="reply_text" rows="4" placeholder="Write your reply..." required></textarea>
                        </div>
                        <button type="submit" class="btn">Add Reply</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 IssueSpot System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
