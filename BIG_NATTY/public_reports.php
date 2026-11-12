<?php 
include 'config.php';

// Get all public issues ordered by upvotes (most popular first)
$stmt = $pdo->query("SELECT i.*, u.username FROM issues i JOIN users u ON i.user_id = u.id WHERE i.is_public = 1 ORDER BY (i.upvotes - i.downvotes) DESC, i.created_at DESC");
$issues = $stmt->fetchAll();

// Handle voting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vote']) && isset($_POST['issue_id'])) {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
    $issue_id = $_POST['issue_id'];
    $vote_type = $_POST['vote'];
    vote($issue_id, $vote_type);
    redirect('public_reports.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Reports - IssueSpot</title>
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="public_reports.php">Public Reports</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="report_issue.php">Report Issue</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php" class="btn">Register</a></li>
                    <?php endif; ?>
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
            <h1 style="margin-bottom: 1rem;">Public Reports</h1>
            <p style="color: #666; margin-bottom: 2rem;">View community issues reported by others. Vote to help prioritize the most important issues.</p>

            <?php if (empty($issues)): ?>
                <div class="card">
                    <p style="text-align: center; color: #666; padding: 2rem;">
                        No public reports yet. Be the first to report an issue!
                    </p>
                </div>
            <?php else: ?>
                <div class="issue-grid">
                    <?php foreach ($issues as $issue): ?>
                        <div class="issue-card">
                            <h3><?php echo htmlspecialchars($issue['title']); ?></h3>
                            <div class="meta">
                                <span class="status status-<?php echo strtolower(str_replace(' ', '-', $issue['status'])); ?>">
                                    <?php echo $issue['status']; ?>
                                </span>
                                <span> by <?php echo htmlspecialchars($issue['username']); ?></span>
                                <span> | Updated: <?php echo date('M d, Y', strtotime($issue['updated_at'])); ?></span>
                            </div>
                            <p class="description"><?php echo htmlspecialchars($issue['description']); ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($issue['category']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($issue['location']); ?></p>
                            
                            <div style="display: flex; gap: 0.5rem; align-items: center; margin: 1rem 0;">
                                <?php if (isLoggedIn()): ?>
                                    <form method="POST">
                                        <input type="hidden" name="issue_id" value="<?php echo $issue['id']; ?>">
                                        <button type="submit" name="vote" value="upvote" class="btn btn-small btn-success">▲ <?php echo $issue['upvotes']; ?></button>
                                    </form>
                                    <form method="POST">
                                        <input type="hidden" name="issue_id" value="<?php echo $issue['id']; ?>">
                                        <button type="submit" name="vote" value="downvote" class="btn btn-small btn-danger">▼ <?php echo $issue['downvotes']; ?></button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #28a745; font-weight: bold;">▲ <?php echo $issue['upvotes']; ?></span>
                                    <span style="color: #dc3545; font-weight: bold;">▼ <?php echo $issue['downvotes']; ?></span>
                                <?php endif; ?>
                                <span style="color: #666; font-size: 0.9rem; margin-left: 0.5rem;">
                                    Score: <?php echo $issue['upvotes'] - $issue['downvotes']; ?>
                                </span>
                            </div>

                            <?php 
                            $images = getIssueImages($issue['image_path']);
                            if (!empty($images)): ?>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.5rem; margin-top: 0.5rem;">
                                    <?php foreach ($images as $img): ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Issue image" style="width: 100%; border-radius: 5px; object-fit: cover;">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php 
                            $issue_replies = getIssueReplies($issue['id']);
                            if (!empty($issue_replies)): ?>
                                <div style="margin-top: 1rem;">
                                    <strong>Replies (<?php echo count($issue_replies); ?>):</strong>
                                    <div style="border-left: 2px solid #2196F3; padding-left: 0.5rem; margin-top: 0.5rem;">
                                        <?php foreach ($issue_replies as $reply): ?>
                                            <div style="background: #f8f9fa; padding: 0.5rem; border-radius: 3px; margin-bottom: 0.5rem; font-size: 0.9rem;">
                                                <p style="margin: 0; color: #666; font-size: 0.8rem;">
                                                    <strong><?php echo htmlspecialchars($reply['admin_name']); ?></strong> - <?php echo date('M d, Y', strtotime($reply['created_at'])); ?>
                                                </p>
                                                <p style="margin: 0.2rem 0 0;"><?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
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
