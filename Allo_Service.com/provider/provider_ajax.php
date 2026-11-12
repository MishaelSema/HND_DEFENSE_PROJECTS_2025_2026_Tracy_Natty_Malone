<?php
// provider_ajax.php
session_start();
require_once dirname(__DIR__) . '/includes/db.php';

$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : (isset($_POST['provider_id']) ? intval($_POST['provider_id']) : 0);

// Handle Review Submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    $reviewer_id = 0;
    $reviewer_type = '';
    
    if (isset($_SESSION['client_id'])) {
        $reviewer_id = $_SESSION['client_id'];
        $reviewer_type = 'client';
    } elseif (isset($_SESSION['provider_id'])) {
        $reviewer_id = $_SESSION['provider_id'];
        $reviewer_type = 'provider';
    } elseif (isset($_SESSION['admin_id'])) {
        $reviewer_id = $_SESSION['admin_id'];
        $reviewer_type = 'admin';
    } else {
        die("Unauthorized");
    }
    
    $provider_id = intval($_POST['provider_id']);
    $rating = intval($_POST['rating']);
    
    // Fix backslash issue: stripslashes then real_escape_string
    $comment_raw = stripslashes($_POST['comment'] ?? ($_POST['commentaire'] ?? ''));
    $comment = $conn->real_escape_string($comment_raw);
    
    if ($provider_id > 0 && $rating > 0) {
        if ($reviewer_type === 'client') {
            $sql = "INSERT INTO reviews (provider_id, client_id, rating, content) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiis", $provider_id, $reviewer_id, $rating, $comment);
        } else {
            $sql = "INSERT INTO reviews (provider_id, rating, content) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $provider_id, $rating, $comment);
        }
        
        if ($stmt && $stmt->execute()) {
            header("Location: ../profile.php?id=$provider_id&msg=Review posted successfully!");
            exit();
        }
    }
    header("Location: ../profile.php?id=$provider_id&error=Failed to post review.");
    exit();
}

if ($id <= 0) {
    echo "<div style='padding:20px; color:red;'>Provider not found.</div>";
    exit;
}

// Get provider info
$stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$p = $result->fetch_assoc();
if (!$p) {
    echo "<div style='padding:20px; color:red;'>Provider not found.</div>";
    exit;
}

// Get average rating
$resStats = $conn->query("SELECT AVG(rating) as average, COUNT(*) as count FROM reviews WHERE provider_id = $id");
$stats = $resStats->fetch_assoc();
$average = $stats['average'] ? round($stats['average'], 1) : 0;
$count = $stats['count'];

// Get latest reviews
$reviews = [];
$resReviews = $conn->query("SELECT r.rating, r.content, r.created_at, c.name 
                            FROM reviews r 
                            LEFT JOIN clients c ON r.client_id = c.client_id 
                            WHERE r.provider_id = $id 
                            ORDER BY r.created_at DESC LIMIT 5");
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

// Ensure extra locations table exists
$conn->query("CREATE TABLE IF NOT EXISTS provider_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Get extra locations
$extraLocs = [];
$resLocs = $conn->query("SELECT address FROM provider_locations WHERE provider_id = $id");
if ($resLocs) {
    while ($row = $resLocs->fetch_assoc()) $extraLocs[] = $row['address'];
}
?>
<div style="text-align:center; padding: 20px;">
    <img src="<?= htmlspecialchars($p['profile'] ?: 'default-avatar.png') ?>" alt="Profile" style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #007a33; margin-bottom:15px;">
    <h2 style="margin:0; color:#007a33;"><?= htmlspecialchars($p['nom']) ?></h2>
    <div style="color:#d71920; font-size:18px; font-weight:600; margin-bottom:5px;"><?= htmlspecialchars(stripslashes($p['profession'])) ?></div>
    <p style="color:#666; margin-bottom:15px; font-size: 14px;"><i class="fas fa-map-marker-alt" style="color:var(--danger)"></i> <?= htmlspecialchars(stripslashes($p['location'])) ?>
        <?php if (count($extraLocs) > 0): ?>
            <span style="color: #94a3b8; font-size: 12px;">(+ <?= count($extraLocs) ?> other locations)</span>
        <?php endif; ?>
    </p>

    <div style="display:flex; justify-content:center; gap:10px; margin-bottom:20px;">
        <?= $average > 0 ? str_repeat('★', round($average)) . str_repeat('☆', 5 - round($average)) : '☆☆☆☆☆' ?>
        <span style="color:#888; font-size:14px;">(<?= $count ?> reviews)</span>
    </div>

    <div style="text-align:left; background:#f9f9f9; padding:20px; border-radius:10px; margin-bottom:20px;">
        <h3 style="font-size:16px; margin-bottom:10px; color:#333;">Description</h3>
        <p style="font-size:14px; color:#555; line-height:1.6;"><?= nl2br(htmlspecialchars(stripslashes($p['description']))) ?></p>
        
        <h3 style="font-size:16px; margin-top:15px; margin-bottom:10px; color:#333;">Skills</h3>
        <p style="font-size:14px; color:#555;"><?= nl2br(htmlspecialchars(stripslashes($p['skills']))) ?></p>
    </div>

    <?php if (isset($_SESSION['client_id'])): ?>
        <h4 style="margin-bottom: 15px; font-size: 14px; color: #64748b; text-transform: uppercase;">Direct Contact</h4>
        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 10px;">
            <?php if (!empty($p['whatsapp'])): ?>
                <a href="https://wa.me/<?= htmlspecialchars($p['whatsapp']) ?>" target="_blank" style="display:inline-block; background:#25D366; color:#fff; padding:10px 20px; border-radius:30px; text-decoration:none; font-weight:bold; font-size:14px; box-shadow: 0 4px 10px rgba(37,211,102,0.3);"><i class="fab fa-whatsapp" style="margin-right: 5px;"></i> WhatsApp</a>
            <?php else: ?>
                <a href="https://wa.me/<?= htmlspecialchars($p['telephone']) ?>" target="_blank" style="display:inline-block; background:#25D366; color:#fff; padding:10px 20px; border-radius:30px; text-decoration:none; font-weight:bold; font-size:14px; box-shadow: 0 4px 10px rgba(37,211,102,0.3);"><i class="fab fa-whatsapp" style="margin-right: 5px;"></i> WhatsApp</a>
            <?php endif; ?>
            <?php if (!empty($p['facebook'])): ?>
                <a href="<?= htmlspecialchars($p['facebook']) ?>" target="_blank" style="display:flex; align-items:center; padding: 10px 15px; background: #1877F2; color: white; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: bold;"><i class="fab fa-facebook"></i></a>
            <?php endif; ?>
            <?php if (!empty($p['instagram'])): ?>
                <a href="https://instagram.com/<?= str_replace('@', '', htmlspecialchars($p['instagram'])) ?>" target="_blank" style="display:flex; align-items:center; padding: 10px 15px; background: #E1306C; color: white; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: bold;"><i class="fab fa-instagram"></i></a>
            <?php endif; ?>
            <?php if (!empty($p['tiktok'])): ?>
                <a href="https://tiktok.com/<?= htmlspecialchars($p['tiktok']) ?>" target="_blank" style="display:flex; align-items:center; padding: 10px 15px; background: #000000; color: white; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: bold;"><i class="fab fa-tiktok"></i></a>
            <?php endif; ?>
            <?php if (!empty($p['twitter'])): ?>
                <a href="https://twitter.com/<?= str_replace('@', '', htmlspecialchars($p['twitter'])) ?>" target="_blank" style="display:flex; align-items:center; padding: 10px 15px; background: #1DA1F2; color: white; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: bold;"><i class="fab fa-twitter"></i></a>
            <?php endif; ?>
            <?php if (!empty($p['website'])): ?>
                <a href="<?= htmlspecialchars($p['website']) ?>" target="_blank" style="display:flex; align-items:center; padding: 10px 15px; background: #64748b; color: white; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: bold;"><i class="fas fa-globe"></i></a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div style="background:#f1f5f9; color:#64748b; padding:12px 25px; border-radius:30px; display:inline-block; font-weight:bold; font-size:14px; border: 1px solid #e2e8f0;">
            <i class="fas fa-lock" style="margin-right: 5px;"></i> Login to Contact Provider
        </div>
    <?php endif; ?>
</div>

<?php if (count($posts) > 0): ?>
<div style="padding: 20px; border-top: 1px solid #eee; text-align: left; background: #f8fafc;">
    <h3 style="font-size:18px; color:#333; margin-bottom:15px;"><i class="fas fa-images"></i> Work Portfolio</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px;">
        <?php foreach ($posts as $pst): ?>
            <div style="border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <img src="<?= htmlspecialchars($pst['image_path']) ?>" style="width: 100%; height: 110px; object-fit: cover; display: block;">
                <div style="padding: 8px;">
                    <p style="font-size: 11px; color: #334155; font-weight: 600; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars(stripslashes($pst['caption'])) ?>"><?= htmlspecialchars(stripslashes($pst['caption'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if (count($reviews)): ?>
<div style="padding: 20px; border-top: 1px solid #eee; text-align: left; background: #fff;">
    <h3 style="font-size:18px; color:#333; margin-bottom:15px;"><i class="fas fa-map-marked-alt"></i> Service Locations</h3>
    <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; height: 200px; margin-bottom: 10px;">
        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 
            src="https://maps.google.com/maps?q=<?= urlencode($p['location'] . (count($extraLocs) > 0 ? " and surrounding areas like " . implode(", ", $extraLocs) : "")) ?>&t=&z=12&ie=UTF8&iwloc=&output=embed">
        </iframe>
    </div>
    <div style="margin-top: 15px;">
        <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #475569;"><i class="fas fa-location-dot"></i> <?= htmlspecialchars($p['location']) ?> (Primary)</span>
        <?php foreach ($extraLocs as $eloc): ?>
            <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #475569;"><i class="fas fa-location-dot"></i> <?= htmlspecialchars($eloc) ?></span>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div style="padding: 20px; border-top: 1px solid #eee;">
    <h3 style="font-size:18px; color:#333; margin-bottom:15px;">Latest Reviews</h3>
    <?php if (count($reviews)): ?>
        <?php foreach ($reviews as $rev): ?>
            <div style="margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid #f0f0f0;">
                <div style="color:#fcd116; font-size:14px;">
                    <?= str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']) ?>
                    <span style="color:#999; font-size:12px; margin-left:10px;"><?= htmlspecialchars($rev['name'] ?: 'Anonymous') ?> - <?= date('M j, Y', strtotime($rev['created_at'])) ?></span>
                </div>
                <p style="margin:5px 0; font-size:14px; color:#444;"><?= htmlspecialchars($rev['content']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color:#999; font-style:italic;">No reviews yet.</p>
    <?php endif; ?>

    <div style="margin-top:30px; background:#fff; padding:20px; border:1px solid #ddd; border-radius:10px;">
        <h4 style="margin-top:0;">Leave a Review</h4>
        <?php if (isset($_SESSION['client_id']) || isset($_SESSION['provider_id'])): 
            $cur_provider_id = $_SESSION['provider_id'] ?? 0;
            if ($cur_provider_id == $id):
        ?>
            <p style="color: #64748b; font-size: 14px;">This is your profile. You cannot review yourself.</p>
        <?php else: ?>
            <form id="reviewForm">
                <input type="hidden" name="provider_id" value="<?= $id ?>">
                <input type="hidden" name="action" value="add_review">
                <input type="hidden" name="rating" id="noteInput" value="0">
                <div id="starRating" style="font-size:25px; margin-bottom:10px; cursor:pointer; color:#ccc;">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <span class="star" data-value="<?= $i ?>">★</span>
                    <?php endfor; ?>
                </div>
                <textarea name="comment" style="width:100%; height:80px; padding:10px; border-radius:8px; border:1px solid #ccc; margin-bottom:10px;" placeholder="Write your experience..." required></textarea>
                <button type="submit" style="background:#007a33; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:bold; width:100%;">Submit Review</button>
            </form>
        <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 20px; background: #f8fafc; border-radius: 8px;">
                <p style="color: #64748b; margin-bottom: 15px;">You must be logged in to leave a review.</p>
                <a href="client/client_login.php" style="background: #007A5E; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;">Log In Now</a>
            </div>
        <?php endif; ?>
    </div>
</div>
