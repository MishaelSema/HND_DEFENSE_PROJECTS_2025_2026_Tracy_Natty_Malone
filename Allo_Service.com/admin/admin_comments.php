<?php
// admin/admin_comments.php
require_once '../includes/db.php';

// Delete a comment (unifed to reviews)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM reviews WHERE id = $id");
    header("Location: admin_comments.php");
    exit();
}

// Fetch all reviews as comments
$res = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
$comments = [];
while ($row = $res->fetch_assoc()) $comments[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Comments Management | ALLO_SERVICE</title>
    <style>
        :root { --primary: #007A5E; --danger: #ff7675; }
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .container { 
            max-width: 1000px; 
            margin: 40px auto; 
            background: #fff; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
            padding: 40px; 
        }
        h1 { color: #333; margin-bottom: 30px; font-weight: 800; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: var(--primary); color: #fff; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
        tr:hover { background: #fcfcfc; }
        .btn-delete { background: var(--danger); color: #fff; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; }
        .back-link { display: inline-block; margin-bottom: 20px; color: var(--primary); text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
<div class="container">
    <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
    <h1>Comments Management</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Content</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $com): ?>
                <tr>
                    <td><?= $com['id']; ?></td>
                    <td><?= htmlspecialchars($com['client_id']); ?></td>
                    <td style="font-size: 14px; color: #555;"><?= htmlspecialchars($com['content']); ?></td>
                    <td style="font-size: 12px; color: #888;"><?= $com['created_at']; ?></td>
                    <td>
                        <a href="admin_comments.php?delete=<?= $com['id']; ?>" class="btn-delete" onclick="return confirm('Delete this comment?');">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>