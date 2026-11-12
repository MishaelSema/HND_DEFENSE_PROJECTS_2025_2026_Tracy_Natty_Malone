<?php 
include 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$message = '';

// Get user's issues
$stmt = $pdo->prepare("SELECT * FROM issues WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$issues = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IssueSpot</title>
    <link rel="stylesheet" href="css/style.css?v=2">
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
                    <li><a href="logout.php">Logout (<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?>)</a></li>
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
                <h1>My Dashboard</h1>
                <a href="report_issue.php" class="btn">Report New Issue</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <h2>My Issues (<?php echo count($issues); ?>)</h2>
                
                <?php if (empty($issues)): ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">
                        You haven't reported any issues yet. 
                        <a href="report_issue.php">Click here to report one</a>
                    </p>
                <?php else: ?>
                    <div class="issue-grid">
                        <?php foreach ($issues as $issue): ?>
                            <div class="issue-card">
                                <h3><?php echo htmlspecialchars($issue['title']); ?></h3>
                                <div class="meta">
                                    <span class="status status-<?php echo strtolower(str_replace(' ', '-', $issue['status'])); ?>">
                                        <?php echo $issue['status']; ?>
                                    </span>
                                    <span style="padding: 0.2rem 0.5rem; background: <?php echo $issue['is_public'] ? '#28a745' : '#6c757d'; ?>; color: white; border-radius: 10px; font-size: 0.75rem;">
                                        <?php echo $issue['is_public'] ? 'Public' : 'Private'; ?>
                                    </span>
                                    <span> | Updated: <?php echo date('M d, Y', strtotime($issue['updated_at'])); ?></span>
                                </div>
                                <p class="description"><?php echo htmlspecialchars($issue['description']); ?></p>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($issue['category']); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($issue['location']); ?></p>
                                <div style="display: flex; gap: 0.5rem; align-items: center; margin: 0.5rem 0;">
                                    <span style="color: #28a745; font-weight: bold;">▲ <?php echo $issue['upvotes']; ?></span>
                                    <span style="color: #dc3545; font-weight: bold;">▼ <?php echo $issue['downvotes']; ?></span>
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
                                <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                                    <a href="report_details.php?id=<?php echo $issue['id']; ?>" class="btn btn-small">View Details</a>
                                    <a href="delete_issue.php?id=<?php echo $issue['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this report?');">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 IssueSpot System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
