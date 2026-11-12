<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['provider_id'])) { header("Location: provider_login.php"); exit(); }
$id = $_SESSION['provider_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reviews | ALLO_SERVICE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; background: #f1f5f9; padding: 40px; text-align: center; } h1 { color: #007A5E; }</style>
</head>
<body>
    <div style="max-width: 900px; margin: 40px auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: left;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="margin:0; font-size: 24px; color: #007A5E;"><i class="fas fa-star" style="color: #FCD116;"></i> My Customer Reviews</h1>
            <a href="provider_profile.php" style="background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>

        <?php
        $res = $conn->query("SELECT r.*, c.name as client_name FROM reviews r JOIN clients c ON r.client_id = c.client_id WHERE r.provider_id = $id ORDER BY r.created_at DESC");
        if ($res && $res->num_rows > 0):
            while ($rev = $res->fetch_assoc()):
        ?>
            <div style="padding: 20px; border: 1px solid #f1f5f9; border-radius: 12px; margin-bottom: 15px; position: relative;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <strong style="color: #334155;"><?= htmlspecialchars($rev['client_name'] ?? 'Client') ?></strong>
                    <span style="color: #94a3b8; font-size: 12px;"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                </div>
                <div style="margin-bottom: 10px; color: #FCD116;">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <i class="fas fa-star<?= $i <= $rev['rating'] ? '' : '-o' ?>" style="<?= $i <= $rev['rating'] ? '' : 'color: #e2e8f0;' ?>"></i>
                    <?php endfor; ?>
                </div>
                <p style="color: #475569; margin: 0; line-height: 1.6;"><?= nl2br(htmlspecialchars($rev['content'] ?? '')) ?></p>
            </div>
        <?php 
            endwhile;
        else:
        ?>
            <div style="text-align: center; padding: 50px; color: #94a3b8;">
                <i class="fas fa-comment-slash" style="font-size: 50px; margin-bottom: 20px; display: block;"></i>
                <p>No reviews yet. Complete more services to get feedback!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
