<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$id = $_SESSION['provider_id'];

// Borrow logic from provider_ajax.php to show the exact same view as clients
$stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$p) {
    die("Provider not found.");
}

// Get average rating
$resNote = $conn->query("SELECT AVG(rating) as avg FROM reviews WHERE provider_id = $id");
$avg = 0;
if ($resNote && $row = $resNote->fetch_assoc()) {
    $avg = round($row['avg'], 1);
}

// Get latest reviews
$reviews = [];
$resReviews = $conn->query("SELECT rating, content, created_at FROM reviews WHERE provider_id = $id ORDER BY created_at DESC LIMIT 5");
if ($resReviews) {
    while ($row = $resReviews->fetch_assoc()) {
        $reviews[] = $row;
    }
}

// Get portfolio posts
$posts = [];
$resPosts = $conn->query("SELECT * FROM provider_posts WHERE provider_id = $id ORDER BY created_at DESC LIMIT 6");
if ($resPosts) {
    while ($row = $resPosts->fetch_assoc()) $posts[] = $row;
}

// Get extra locations
$extraLocs = [];
$resLocs = $conn->query("SELECT address FROM provider_locations WHERE provider_id = $id");
if ($resLocs) {
    while ($row = $resLocs->fetch_assoc()) $extraLocs[] = $row['address'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Preview | <?= htmlspecialchars($p['name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; padding: 40px 20px; color: #1e293b; }
        .preview-container { max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); }
        .header-msg { text-align: center; margin-bottom: 20px; font-weight: 600; color: #64748b; }
        .back-btn { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007A5E; font-weight: 700; }
    </style>
</head>
<body>
    <div class="header-msg">
        <i class="fas fa-eye"></i> This is a LIVE preview of how your profile looks to clients.
    </div>
    <div style="text-align: center;">
        <a href="provider_profile.php" class="back-btn"><i class="fas fa-arrow-left"></i> Return to Dashboard</a>
    </div>

    <div class="preview-container">
        <!-- Re-using provider_ajax.php structure for consistency -->
        <div style="text-align:center; padding: 30px; border-bottom: 1px solid #f1f5f9;">
            <img src="../<?= htmlspecialchars($p['profile'] ?: 'assets/img/default-avatar.png') ?>" alt="Profile" style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #007a33; margin-bottom:15px;">
            <h2 style="margin:0; color:#333; font-size: 24px;"><?= htmlspecialchars($p['nom']) ?></h2>
            <p style="color:#007a33; font-weight:bold; margin:5px 0 15px; text-transform: uppercase; letter-spacing: 1px; font-size: 14px;"><?= htmlspecialchars($p['profession']) ?></p>
            
            <div style="display:flex; justify-content:center; gap:10px; margin-bottom:20px;">
                <span style="background:#fcd11622; color:#b45309; padding:5px 12px; border-radius:20px; font-size:13px; font-weight:bold;"><i class="fas fa-star"></i> <?= $avg ?> Rating</span>
                <span style="background:#007a3322; color:#007a33; padding:5px 12px; border-radius:20px; font-size:13px; font-weight:bold;"><i class="fas fa-check-circle"></i> Verified</span>
            </div>

            <div style="color:#666; font-size:14px; line-height:1.6; margin-bottom: 25px;">
                <?= nl2br(htmlspecialchars($p['description'] ?: 'No description provided yet.')) ?>
            </div>

            <h4 style="margin-bottom: 15px; font-size: 14px; color: #64748b; text-transform: uppercase;">Direct Contact</h4>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 10px;">
                <a href="#" class="btn-preview" style="display:inline-block; background:#25D366; color:#fff; padding:10px 20px; border-radius:30px; text-decoration:none; font-weight:bold; font-size:14px; opacity: 0.7; cursor: not-allowed;"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                <?php if (!empty($p['facebook'])): ?><a href="#" style="background:#1877F2; color:white; padding:10px; border-radius:8px; opacity:0.7;"><i class="fab fa-facebook"></i></a><?php endif; ?>
                <?php if (!empty($p['instagram'])): ?><a href="#" style="background:#E1306C; color:white; padding:10px; border-radius:8px; opacity:0.7;"><i class="fab fa-instagram"></i></a><?php endif; ?>
            </div>
        </div>

        <?php if (count($posts) > 0): ?>
        <div style="padding: 30px; border-bottom: 1px solid #f1f5f9; text-align: left; background: #f8fafc;">
            <h3 style="font-size:18px; color:#333; margin-bottom:15px;"><i class="fas fa-images"></i> Work Portfolio</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px;">
                <?php foreach ($posts as $pst): ?>
                    <div style="border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; background: white;">
                        <img src="../<?= htmlspecialchars($pst['image_path']) ?>" style="width: 100%; height: 110px; object-fit: cover; display: block;">
                        <div style="padding: 8px;">
                            <p style="font-size: 11px; color: #334155; font-weight: 600; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($pst['caption']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- MAP PREVIEW -->
        <div style="padding: 30px;">
            <h3 style="font-size:18px; color:#333; margin-bottom:15px;"><i class="fas fa-map-marked-alt"></i> Service Area</h3>
            <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; height: 250px; margin-bottom: 15px;">
                <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 
                src="https://maps.google.com/maps?q=<?= urlencode($p['location'] . (count($extraLocs) > 0 ? " and " . implode(", ", $extraLocs) : "")) ?>&t=&z=11&ie=UTF8&iwloc=&output=embed">
            </iframe>
        </div>
        <div style="margin-top: 15px;">
            <span style="background: #f1f5f9; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #475569;"><i class="fas fa-location-dot" style="color:var(--danger)"></i> <?= htmlspecialchars($p['location']) ?> (Main)</span>
                <?php foreach ($extraLocs as $eloc): ?>
                    <span style="background: #f1f5f9; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #475569;"><i class="fas fa-location-dot"></i> <?= htmlspecialchars($eloc) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
