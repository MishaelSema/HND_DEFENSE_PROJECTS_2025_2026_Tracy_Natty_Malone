<?php 
include '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Get statistics
$total_issues = $pdo->query("SELECT COUNT(*) FROM issues")->fetchColumn();
$pending_issues = $pdo->query("SELECT COUNT(*) FROM issues WHERE status = 'Pending'")->fetchColumn();
$in_progress_issues = $pdo->query("SELECT COUNT(*) FROM issues WHERE status = 'In Progress'")->fetchColumn();
$resolved_issues = $pdo->query("SELECT COUNT(*) FROM issues WHERE status = 'Resolved'")->fetchColumn();

// Get all issues with user info, ordered by vote score
$stmt = $pdo->query("
    SELECT i.*, u.username, u.email 
    FROM issues i 
    JOIN users u ON i.user_id = u.id 
    ORDER BY (i.upvotes - i.downvotes) DESC, i.created_at DESC
");
$issues = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IssueSpot</title>
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
            <h1 style="margin-bottom: 2rem;">Admin Dashboard</h1>

            <div class="stats">
                <div class="stat-card">
                    <h3><?php echo $total_issues; ?></h3>
                    <p>Total Issues</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $pending_issues; ?></h3>
                    <p>Pending</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $in_progress_issues; ?></h3>
                    <p>In Progress</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $resolved_issues; ?></h3>
                    <p>Resolved</p>
                </div>
            </div>

            <div class="card">
                <h2>All Issues</h2>
                
                <?php if (empty($issues)): ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">
                        No issues reported yet.
                    </p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Votes</th>
                                <th>Status</th>
                                <th>Public</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($issues as $issue): ?>
                                <tr>
                                    <td>#<?php echo $issue['id']; ?></td>
                                    <td><?php echo htmlspecialchars($issue['username']); ?></td>
                                    <td><?php echo htmlspecialchars($issue['title']); ?></td>
                                    <td><?php echo htmlspecialchars($issue['category']); ?></td>
                                    <td>
                                        <span style="color: #28a745;">▲ <?php echo $issue['upvotes']; ?></span><br>
                                        <span style="color: #dc3545;">▼ <?php echo $issue['downvotes']; ?></span>
                                    </td>
                                    <td>
                                        <span class="status status-<?php echo strtolower(str_replace(' ', '-', $issue['status'])); ?>">
                                            <?php echo $issue['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($issue['is_public']): ?>
                                            <span style="color: #28a745;">✓ Yes</span>
                                        <?php else: ?>
                                            <span style="color: #6c757d;">✗ No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($issue['created_at'])); ?></td>
                                    <td>
                                        <a href="manage.php?id=<?php echo $issue['id']; ?>" class="btn btn-small">Manage</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
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
