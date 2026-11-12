<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/csrf.php';
require_once 'includes/auth_check.php';
require_once 'includes/ratelimit.php';

// Auto-deactivate providers who were online yesterday
$conn->query("UPDATE providers SET is_online = 0, online_since = NULL WHERE is_online = 1 AND DATE(online_since) < CURDATE()");

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: all_services.php");
    exit();
}

// Fetch Provider - show all approved providers
$stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id = ? AND status = 'approved'");
$stmt->bind_param("i", $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$p) {
    die("Provider not found or not yet approved.");
}

$is_online = !empty($p['is_online']);
$coverImg = $p['cover_photo'] ?? '';

// Check if client is verified
$client_verified = false;
$client_pending = false;
if (isset($_SESSION['client_id'])) {
    $check_verified = $conn->query("SELECT id_verified, id_card_front FROM clients WHERE client_id = " . $_SESSION['client_id']);
    $client_data = $check_verified->fetch_assoc();
    $client_verified = $client_data && $client_data['id_verified'] == 1;
    $client_pending = $client_data && $client_data['id_verified'] == 0 && !empty($client_data['id_card_front']);
}

// Get provider services
$resServices = $conn->query("SELECT * FROM provider_services WHERE provider_id = $id ORDER BY service_name");
$provider_services = [];
if ($resServices) {
    while ($s = $resServices->fetch_assoc()) {
        $provider_services[] = $s;
    }
}

// Get platform fee for pricing display
$settings = $conn->query("SELECT * FROM platform_settings LIMIT 1")->fetch_assoc();
$client_fee = $settings['client_fee_percent'] ?? 5;

// Get average rating
$resStats = $conn->query("SELECT AVG(rating) as average, COUNT(*) as count FROM reviews WHERE provider_id = $id");
$stats = $resStats->fetch_assoc();
$average = $stats['average'] ? round($stats['average'], 1) : 0;
$count = $stats['count'];

// Get all portfolio posts
$posts = [];
$resPosts = $conn->query("SELECT * FROM provider_posts WHERE provider_id = $id ORDER BY created_at DESC");
if ($resPosts) {
    while ($pst = $resPosts->fetch_assoc()) {
        $pst_id = $pst['id'];
        $pst['extra_images'] = [];
        $resExtra = $conn->query("SELECT image_path FROM post_images WHERE post_id = $pst_id");
        if ($resExtra) {
            while ($img = $resExtra->fetch_assoc()) {
                $pst['extra_images'][] = $img['image_path'];
            }
        }
        $posts[] = $pst;
    }
}

// Get reviews with client names
$reviews = [];
$resReviews = $conn->query("SELECT r.*, c.name as client_name 
                            FROM reviews r 
                            LEFT JOIN clients c ON r.client_id = c.client_id 
                            WHERE r.provider_id = $id 
                            ORDER BY r.created_at DESC");
if ($resReviews) {
    while ($row = $resReviews->fetch_assoc()) $reviews[] = $row;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($p['name']) ?> | Shop Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --p-green: #007a33; --p-red: #d71920; }
        body { background: #f4f7f6; }
        .profile-header { 
            background: linear-gradient(45deg, var(--p-green), #00b894);
            padding: 60px 20px; 
            text-align: center; 
            border-bottom: 1px solid #eee; 
            position: relative; 
        }
        .profile-header .avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.15); margin-top: -80px; }
        .cover-photo { display: none; }
        
        .profile-nav { background: white; sticky: top; top: 0; z-index: 100; border-bottom: 1px solid #eee; display: flex; justify-content: center; gap: 30px; padding: 15px; }
        .profile-nav a { text-decoration: none; color: #666; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .profile-nav a.active { color: var(--p-green); border-bottom: 2px solid var(--p-green); }

        .shop-container { width: 100%; max-width: 1200px; margin: 40px auto; display: grid; grid-template-columns: 1fr 350px; gap: 30px; padding: 0 20px; box-sizing: border-box; }
        .main-content { background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); min-width: 0; }
        .sidebar { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); height: fit-content; position: sticky; top: 80px; min-width: 350px; }

        .section-title { font-size: 22px; font-weight: 800; margin-bottom: 25px; color: #1e293b; display: flex; align-items: center; gap: 10px; }
        .section-title i { color: var(--p-green); }

        .portfolio-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .post-card { border-radius: 12px; overflow: hidden; border: 1px solid #eee; transition: 0.3s; background: white; }
        .post-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .post-card img { width: 100%; height: 200px; object-fit: cover; }
        .post-card .info { padding: 15px; }
        .post-card .info h4 { margin: 0 0 5px; font-size: 15px; }
        .post-card .info p { margin: 0; font-size: 12px; color: #888; }

        .contact-btn { display: block; width: 100%; padding: 15px; border-radius: 12px; text-align: center; text-decoration: none; font-weight: 700; margin-bottom: 10px; transition: 0.3s; box-sizing: border-box; }
        .btn-whatsapp { background: #25D366; color: white; }
        .btn-call { background: var(--p-green); color: white; }
        .contact-btn:hover { opacity: 0.9; transform: translateY(-2px); }

        /* Review Form Styles */
        .review-form { background: #f8fafc; padding: 25px; border-radius: 12px; margin-top: 30px; border: 1px solid #e2e8f0; }
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 5px; margin-bottom: 15px; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 24px; color: #cbd5e1; cursor: pointer; transition: 0.2s; }
        .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #fcd116; }
        
        @media (max-width: 991px) {
            .shop-container { grid-template-columns: 1fr; margin: 20px auto; }
            .sidebar { position: static; min-width: 0; padding: 20px; }
            .main-content { padding: 25px; }
            .profile-header { padding: 40px 15px; }
            .profile-header h1 { font-size: 1.8rem; }
            .cover-photo { height: 200px; }
        }
        .portfolio-carousel { position: relative; overflow: hidden; border-radius: 12px; height: 300px; width: 100%; }
        .portfolio-track { display: flex; transition: transform 0.3s ease-out; height: 100%; width: 100%; }
        .portfolio-slide { min-width: 100%; flex-shrink: 0; height: 100%; object-fit: cover; }
        .carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.8); border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center; font-size: 12px; }
        .carousel-btn.prev { left: 10px; }
        .carousel-btn.next { right: 10px; }
        .carousel-dots { position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); display: flex; gap: 5px; z-index: 10; }
        .dot { width: 8px; height: 8px; background: rgba(255,255,255,0.5); border-radius: 50%; cursor: pointer; }
        .dot.active { background: white; width: 20px; border-radius: 10px; }
    </style>
</head>
<body>

<?php include 'includes/header.php'; 
?>

<div class="profile-header" <?= !empty($coverImg) ? "style=\"background: url('" . htmlspecialchars($coverImg) . "') no-repeat center center; background-size: cover;\"" : "" ?>>
    <div class="container" style="position: relative; z-index: 1;">
        <div style="background: rgba(255,255,255,0.85); border-radius: 16px; padding: 25px; display: inline-block;">
        <img src="<?= htmlspecialchars($p['profile_photo'] ?: 'assets/img/default-avatar.png') ?>" class="avatar" alt="Profile">
        <h1 style="margin: 15px 0 5px;"><?= htmlspecialchars(stripslashes($p['name'])) ?> <i class="fas fa-check-circle" style="color: #38bdf8; font-size: 18px;"></i></h1>
        <p style="color: var(--p-green); font-weight: 800; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 1.5px;"><?= htmlspecialchars(stripslashes($p['profession'])) ?></p>
        
        <div style="display:flex; justify-content:center; gap:20px; margin-bottom: 20px;">
            <div style="text-align: center;">
                <div style="font-weight: 800; font-size: 20px;"><?= $average ?> <i class="fas fa-star" style="color:#fcd116;"></i></div>
                <div style="font-size: 12px; color: #888; text-transform: uppercase;">Rating</div>
            </div>
            <div style="text-align: center;">
                <div style="font-weight: 800; font-size: 20px;"><?= count($posts) ?></div>
                <div style="font-size: 12px; color: #888; text-transform: uppercase;">Works</div>
            </div>
            <div style="text-align: center;">
                <div style="font-weight: 800; font-size: 20px;"><?= $count ?></div>
                <div style="font-size: 12px; color: #888; text-transform: uppercase;">Reviews</div>
            </div>
        </div>
        </div>
    </div>
</div>

<div class="profile-nav">
    <a href="#about" class="active">About</a>
    <a href="#portfolio">Shop Feed</a>
    <a href="#reviews">Testimonials</a>
    <a href="#location">Service Area</a>
</div>

<div class="shop-container" style="width: 100%; max-width: 1200px; margin: 40px auto; display: grid; grid-template-columns: 1fr 350px; gap: 30px; padding: 0 20px; box-sizing: border-box;">
    <div class="main-content" style="background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <section id="about" style="margin-bottom: 50px;">
            <h3 class="section-title"><i class="fas fa-user"></i> Professional Summary</h3>
            <p style="color: #475569; line-height: 1.7; font-size: 16px;">
                <?= nl2br(htmlspecialchars(stripslashes($p['description'] ?: 'This professional has not provided a detailed description yet.'))) ?>
            </p>
            <?php if(!empty($p['skills'])): ?>
            <?php 
                    $skills = explode(',', $p['skills']);
                    foreach($skills as $s): if(empty(trim($s))) continue; ?>
                        <span style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 6px 15px; border-radius: 50px; font-size: 13px; font-weight: 600;"><?= htmlspecialchars(stripslashes(trim($s))) ?></span>
                    <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section id="portfolio" style="margin-bottom: 50px;">
            <h3 class="section-title"><i class="fas fa-th-large"></i> Work Portfolio</h3>
            <?php if (empty($posts)): ?>
                <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 12px; color: #94a3b8;">
                    No portfolio posts available yet.
                </div>
            <?php else: ?>
                <div class="portfolio-grid">
                    <?php foreach ($posts as $pst): 
                        $pst_id = $pst['id'];
                        $all_images = array_merge([$pst['image_path']], $pst['extra_images']);
                    ?>
                        <div class="post-card" style="position: relative;">
                            <?php if (count($all_images) > 1): ?>
                                <div class="portfolio-carousel" id="carousel-<?= $pst_id ?>">
                                    <div class="portfolio-track">
                                        <?php foreach ($all_images as $img_path): ?>
                                            <img src="<?= htmlspecialchars($img_path) ?>" class="portfolio-slide" onclick="openGallery(<?= htmlspecialchars(json_encode($all_images)) ?>)">
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-btn prev" onclick="event.stopPropagation(); moveCarousel(<?= $pst_id ?>, -1)"><i class="fas fa-chevron-left"></i></button>
                                    <button class="carousel-btn next" onclick="event.stopPropagation(); moveCarousel(<?= $pst_id ?>, 1)"><i class="fas fa-chevron-right"></i></button>
                                    <div class="carousel-dots">
                                        <?php for ($i=0; $i<count($all_images); $i++): ?>
                                            <div class="dot <?= $i==0?'active':'' ?>" onclick="event.stopPropagation(); setCarousel(<?= $pst_id ?>, <?= $i ?>)"></div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($pst['image_path']) ?>" alt="Work image" style="cursor: pointer;" onclick="openGallery(['<?= htmlspecialchars($pst['image_path']) ?>'])">
                            <?php endif; ?>

                            <div class="info">
                                <h4><?= htmlspecialchars(stripslashes($pst['caption'])) ?></h4>
                                <p><?= date('M d, Y', strtotime($pst['created_at'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section id="reviews">
            <h3 class="section-title"><i class="fas fa-comments"></i> Customer Feedback</h3>
            
            <?php if (isset($_SESSION['provider_id']) && $_SESSION['provider_id'] == $id): ?>
                <div style="padding: 15px; background: #fffbeb; color: #92400e; border-radius: 12px; border: 1px solid #fef3c7; margin-bottom: 25px; font-size: 14px;">
                    <i class="fas fa-info-circle"></i> This is your public profile. You cannot leave reviews for yourself.
                </div>
            <?php elseif (isset($_SESSION['client_id']) || isset($_SESSION['provider_id'])): ?>
                <?php if (!isset($_SESSION['provider_id']) || $_SESSION['provider_id'] != $id): ?>
                <?php if ($client_verified): ?>
                <div class="review-form">
                    <h4 style="margin: 0 0 15px; font-size: 16px;">Leave a Review</h4>
                    <form action="provider/provider_ajax.php" method="post">
                        <input type="hidden" name="action" value="add_review">
                        <input type="hidden" name="provider_id" value="<?= $id ?>">
                        <div class="star-rating">
                            <input type="radio" id="5-stars" name="rating" value="5" required /><label for="5-stars" class="fas fa-star"></label>
                            <input type="radio" id="4-stars" name="rating" value="4" /><label for="4-stars" class="fas fa-star"></label>
                            <input type="radio" id="3-stars" name="rating" value="3" /><label for="3-stars" class="fas fa-star"></label>
                            <input type="radio" id="2-stars" name="rating" value="2" /><label for="2-stars" class="fas fa-star"></label>
                            <input type="radio" id="1-star" name="rating" value="1" /><label for="1-star" class="fas fa-star"></label>
                        </div>
                        <textarea name="comment" placeholder="Describe your experience with this professional..." required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; font-family: inherit; resize: vertical; min-height: 80px;"></textarea>
                        <button type="submit" style="background: var(--p-green); color: white; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 700; cursor: pointer;">Post My Review</button>
                    </form>
                </div>
                <div style="margin-bottom: 30px;"></div>
                <?php else: ?>
                <?php if ($client_pending): ?>
                <div style="padding: 20px; background: #dbeafe; border: 1px solid #93c5fd; border-radius: 12px; margin-bottom: 25px; text-align: center;">
                    <p style="margin: 0 0 10px; font-size: 14px; color: #1e40af;">
                        <i class="fas fa-clock"></i> <strong>Verification Pending</strong>
                    </p>
                    <p style="margin: 0; font-size: 13px; color: #1e3a8a;">
                        Your verification is being reviewed. Please wait for approval.
                    </p>
                </div>
                <?php else: ?>
                <div style="padding: 20px; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 12px; margin-bottom: 25px; text-align: center;">
                    <p style="margin: 0 0 10px; font-size: 14px; color: #92400e;">
                        <i class="fas fa-id-card"></i> <strong>Verification Required</strong>
                    </p>
                    <p style="margin: 0 0 15px; font-size: 13px; color: #78350f;">
                        You must verify your ID to leave reviews.
                    </p>
                    <a href="client/client_kyc.php" style="background: #92400e; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">Verify Now</a>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                <?php endif; ?>
            <?php elseif (!isset($_SESSION['provider_id']) && !isset($_SESSION['client_id'])): ?>
                <div style="padding: 20px; text-align: center; background: #f1f5f9; border-radius: 12px; margin-bottom: 25px;">
                    <p style="margin: 0 0 10px; font-size: 14px; color: #64748b;">Want to leave a review?</p>
                    <a href="client/client_login.php" style="color: var(--p-green); font-weight: 700; text-decoration: none;">Login as a Client</a>
                </div>
            <?php endif; ?>

            <?php if (empty($reviews)): ?>
                <p style="color: #94a3b8;">No reviews yet. Be the first to leave one!</p>
            <?php else: ?>
                <?php foreach ($reviews as $rev): 
                    $reviewer_name = htmlspecialchars($rev['client_name'] ?: ($rev['provider_reviewer_name'] ?: 'Verified User'));
                ?>
                    <div style="padding: 20px; border-radius: 12px; background: #f8fafc; margin-bottom: 15px; border-left: 4px solid var(--p-green);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-weight: 700; color: #1e293b;"><?= $reviewer_name ?></span>
                            <span style="color: #fcd116; font-size: 14px;">
                                <?= str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']) ?>
                            </span>
                        </div>
                        <p style="color: #475569; font-size: 14px; margin: 0;"><?= nl2br(htmlspecialchars(stripslashes($rev['content'] ?? ''))) ?></p>
                        <small style="color: #94a3b8; margin-top: 10px; display: block;"><?= date('M j, Y', strtotime($rev['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>

    <div class="sidebar" style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); height: fit-content;">
        <?php if (!$is_online): ?>
        <div style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 12px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
            <span style="color: #64748b; font-size: 13px;"><i class="fas fa-circle" style="color: #94a3b8; font-size: 10px;"></i> Currently Offline</span>
        </div>
        <?php endif; ?>
        
        <h3 style="font-size: 18px; margin-bottom: 20px;">Contact & Booking</h3>
        
        <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
            <?php if (!$is_online): ?>
                This professional is currently offline. Booking will be available when they go online.
            <?php elseif (isset($_SESSION['client_id']) && $client_verified): ?>
                Book a service to unlock direct contact with this professional.
            <?php elseif (isset($_SESSION['client_id']) && $client_pending): ?>
                Your verification is being reviewed. You can book once approved.
            <?php else: ?>
                Book a service to unlock direct contact with this professional.
            <?php endif; ?>
        </p>

        
        <?php if (isset($_SESSION['client_id']) && $client_verified && $is_online): ?>
            <a href="#" onclick="openBookingModal()" class="contact-btn" style="background: var(--p-green); color: white; margin-top: 10px;">
                <i class="fas fa-calendar-check"></i> Book Now
            </a>
        <?php elseif (isset($_SESSION['client_id']) && $client_pending): ?>
            <div style="background: #dbeafe; border: 1px solid #93c5fd; padding: 15px; border-radius: 12px; margin-bottom: 15px;">
                <p style="margin: 0; font-size: 13px; color: #1e40af;">
                    <i class="fas fa-clock"></i> <strong>Verification Pending</strong><br>
                    Your verification is being reviewed.
                </p>
            </div>
        <?php elseif (isset($_SESSION['client_id'])): ?>
            <div style="background: #fef3c7; border: 1px solid #fcd34d; padding: 15px; border-radius: 12px; margin-bottom: 15px;">
                <p style="margin: 0; font-size: 13px; color: #92400e;">
                    <i class="fas fa-lock"></i> <strong>Contact locked</strong><br>
                    Complete ID verification to book and contact providers.
                </p>
            </div>
            <a href="client/client_kyc.php" class="contact-btn" style="background: #92400e; color: white; margin-top: 10px;">
                <i class="fas fa-id-card"></i> Verify Now
            </a>
        <?php elseif (!isset($_SESSION['provider_id']) || $_SESSION['provider_id'] != $id): ?>
            <div style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; margin-bottom: 15px;">
                <p style="margin: 0; font-size: 13px; color: #64748b;">
                    <i class="fas fa-info-circle"></i> Login and verify to book services.
                </p>
            </div>
            <?php if ($is_online): ?>
            <a href="client/client_login.php" class="contact-btn" style="background: var(--p-green); color: white; margin-top: 10px;">
                <i class="fas fa-calendar-check"></i> Login to Book
            </a>
            <?php else: ?>
            <button disabled class="contact-btn" style="background: #cbd5e1; color: #94a3b8; margin-top: 10px; cursor: not-allowed;">
                <i class="fas fa-calendar-check"></i> Offline
            </button>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!(isset($_SESSION['provider_id']) && $_SESSION['provider_id'] == $id)): ?>
        <?php if (isset($_SESSION['client_id']) && $client_verified): ?>
        <a href="#" onclick="openReportModal()" class="contact-btn" style="background: #fee2e2; color: #991b1b; margin-top: 10px;">
            <i class="fas fa-flag"></i> Report Professional
        </a>
        <?php elseif (isset($_SESSION['client_id']) && $client_pending): ?>
        <div style="background: #dbeafe; border: 1px solid #93c5fd; padding: 12px; border-radius: 12px; margin-top: 10px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #1e40af;"><i class="fas fa-clock"></i> Verification pending</p>
        </div>
        <?php elseif (isset($_SESSION['client_id'])): ?>
        <a href="client/client_kyc.php" class="contact-btn" style="background: #fee2e2; color: #991b1b; margin-top: 10px;">
            <i class="fas fa-flag"></i> Verify to Report
        </a>
        <?php else: ?>
        <a href="client/client_login.php" class="contact-btn" style="background: #fee2e2; color: #991b1b; margin-top: 10px;">
            <i class="fas fa-flag"></i> Report Professional
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee;">
            <h4 style="font-size: 13px; text-transform: uppercase; color: #94a3b8; margin-bottom: 15px;">Shop Details</h4>
            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px; color: #334155;">
                <div id="primary-location"><i class="fas fa-map-marker-alt" style="width: 20px; color: var(--p-red);"></i> <span class="location-text"><?= htmlspecialchars(stripslashes($p['location'])) ?></span></div>
                <div><i class="fas fa-clock" style="width: 20px; color: #3b82f6;"></i> <?= htmlspecialchars(stripslashes($p['availability'])) ?></div>
                <div><i class="fas fa-calendar-check" style="width: 20px; color: #10b981;"></i> Member since <?= date('Y', strtotime($p['created_at'])) ?></div>
            </div>
        </div>

        <?php if (!empty($p['facebook']) || !empty($p['instagram']) || !empty($p['tiktok']) || !empty($p['twitter']) || !empty($p['whatsapp']) || !empty($p['website'])): ?>
        <div style="margin-top: 30px;">
            <h4 style="font-size: 13px; text-transform: uppercase; color: #94a3b8; margin-bottom: 15px;">Connect with Pro</h4>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <?php if(!empty($p['website'])): ?><a href="<?= htmlspecialchars($p['website']) ?>" target="_blank" title="Website" style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #475569;"><i class="fas fa-globe"></i></a><?php endif; ?>
                <?php if(!empty($p['facebook'])): ?><a href="<?= htmlspecialchars($p['facebook']) ?>" target="_blank" title="Facebook" style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1877F2;"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                <?php if(!empty($p['instagram'])): ?><a href="<?= htmlspecialchars($p['instagram']) ?>" target="_blank" title="Instagram" style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #E1306C;"><i class="fab fa-instagram"></i></a><?php endif; ?>
                <?php if(!empty($p['tiktok'])): ?><a href="<?= htmlspecialchars($p['tiktok']) ?>" target="_blank" title="TikTok" style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #000000; transition: 0.3s;"><i class="fab fa-tiktok"></i></a><?php endif; ?>
                <?php if(!empty($p['twitter'])): ?><a href="<?= htmlspecialchars($p['twitter']) ?>" target="_blank" title="Twitter/X" style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1DA1F2; transition: 0.3s;"><i class="fab fa-twitter"></i></a><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($provider_services)): ?>
<section class="container" style="margin-bottom: 80px;">
    <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="padding: 25px; border-bottom: 1px solid #eee;">
            <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-tags" style="color: var(--p-green);"></i> Services & Pricing</h3>
        </div>
        <div style="padding: 25px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                <?php 
                foreach ($provider_services as $svc):
                    $total_price = $svc['price'] + ($svc['price'] * $client_fee / 100);
                ?>
                <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; transition: 0.3s; cursor: pointer;" onclick="selectService(<?= $svc['id'] ?>, '<?= htmlspecialchars(addslashes($svc['service_name'])) ?>')">
                    <h4 style="margin: 0 0 10px; font-size: 16px; color: #1e293b;"><?= htmlspecialchars($svc['service_name']) ?></h4>
                    <?php if (!empty($svc['description'])): ?>
                    <p style="margin: 0 0 15px; font-size: 13px; color: #64748b;"><?= htmlspecialchars($svc['description']) ?></p>
                    <?php endif; ?>
                    <div style="font-size: 20px; font-weight: 700; color: var(--p-green);"><?= number_format($total_price, 0, ',', ' ') ?> XAF</div>
                    <div style="font-size: 11px; color: #94a3b8;">Includes platform fee</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function selectService(serviceId, serviceName) {
    const select = document.getElementById('serviceType');
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value === serviceName) {
            select.selectedIndex = i;
            updatePrice();
            document.getElementById('bookingModal').style.scrollIntoView({behavior: 'smooth'});
            openBookingModal();
            break;
        }
    }
}
</script>

<section id="location" class="container" style="margin-bottom: 80px;">
    <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div style="padding: 25px; border-bottom: 1px solid #eee;">
            <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-map-marked-alt" style="color: var(--p-green);"></i> Service Coverage</h3>
        </div>
        <div style="height: 400px;">
            <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 
                src="https://maps.google.com/maps?q=<?= urlencode($p['location'] . (count($extraLocs) > 0 ? " and " . implode(", ", $extraLocs) : "")) ?>&t=&z=12&ie=UTF8&iwloc=&output=embed">
            </iframe>
        </div>
    </div>
</section>

<!-- Gallery Modal -->
<div id="galleryModal" style="display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.9); justify-content:center; align-items:center; flex-direction:column; padding: 20px;">
    <span style="position:absolute; top:30px; right:30px; color:white; font-size:40px; cursor:pointer;" onclick="closeGallery()">&times;</span>
    <div id="galleryContent" style="display:flex; gap:15px; overflow-x:auto; width:100%; max-width: 1000px; padding: 20px;"></div>
</div>

<script>
function openGallery(images) {
    const modal = document.getElementById('galleryModal');
    const content = document.getElementById('galleryContent');
    content.innerHTML = '';
    
    images.forEach(src => {
        const img = document.createElement('img');
        img.src = src;
        img.style.maxHeight = '80vh';
        img.style.maxWidth = '90%';
        img.style.objectFit = 'contain';
        img.style.borderRadius = '12px';
        content.appendChild(img);
    });
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeGallery() {
    document.getElementById('galleryModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Carousel Logic
const carouselStates = {};

function moveCarousel(postId, direction) {
    if (!carouselStates[postId]) carouselStates[postId] = 0;
    const carousel = document.getElementById(`carousel-${postId}`);
    const track = carousel.querySelector('.portfolio-track');
    const slides = track.querySelectorAll('.portfolio-slide');
    const dots = carousel.querySelectorAll('.dot');
    
    carouselStates[postId] = (carouselStates[postId] + direction + slides.length) % slides.length;
    
    track.style.transform = `translateX(-${carouselStates[postId] * 100}%)`;
    
    dots.forEach((dot, idx) => {
        dot.classList.toggle('active', idx === carouselStates[postId]);
    });
}

function setCarousel(postId, index) {
    carouselStates[postId] = index;
    const carousel = document.getElementById(`carousel-${postId}`);
    const track = carousel.querySelector('.portfolio-track');
    const dots = carousel.querySelectorAll('.dot');
    
    track.style.transform = `translateX(-${index * 100}%)`;
    
    dots.forEach((dot, idx) => {
        dot.classList.toggle('active', idx === index);
    });
}

// Close on background click
document.getElementById('galleryModal').onclick = function(e) {
    if (e.target.id === 'galleryModal') closeGallery();
}

// Geocoding Logic
async function reverseGeocode() {
    const locElements = document.querySelectorAll('.location-text');
    for (let el of locElements) {
        const text = el.textContent.trim();
        const coordRegex = /^(-?\d+(\.\d+)?),\s*(-?\d+(\.\d+)?)$/;
        const match = text.match(coordRegex);
        if (match) {
            const lat = match[1];
            const lon = match[3];
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`, {
                    headers: { 'User-Agent': 'AlloService/1.0' }
                });
                const data = await response.json();
                if (data && data.display_name) {
                    const addr = data.address;
                    const friendly = (addr.suburb || addr.neighbourhood || addr.city_district || addr.town || addr.city || "") 
                                    + (addr.city && addr.town ? "" : (addr.city || addr.town ? ", " : "")) 
                                    + (addr.city || addr.town || "");
                                    
                    el.textContent = friendly || data.display_name;
                    el.title = data.display_name;
                }
            } catch (err) {
                console.error("Geocoding failed:", err);
            }
        }
    }
}
document.addEventListener('DOMContentLoaded', reverseGeocode);

function openReportModal() {
    document.getElementById('reportModal').style.display = 'flex';
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
}

function submitReport() {
    const reason = document.querySelector('input[name="reason"]:checked');
    const description = document.getElementById('reportDescription').value;
    
    if (!reason) {
        alert('Please select a reason');
        return;
    }
    
    const formData = new FormData();
    formData.append('provider_id', <?= $id ?>);
    formData.append('reason', reason.value);
    formData.append('description', description);
    
    fetch('../submit_report.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Report submitted. We will review it shortly.');
            closeReportModal();
        } else {
            alert(data.message || 'Error submitting report');
        }
    });
}
</script>

<!-- Report Modal -->
<div id="reportModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 16px; max-width: 500px; width: 90%;">
        <h3 style="margin-top: 0;"><i class="fas fa-flag" style="color: #991b1b;"></i> Report Professional</h3>
        <p style="color: #64748b; margin-bottom: 20px;">Why are you reporting this professional?</p>
        
        <form onsubmit="event.preventDefault(); submitReport();">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px;"><input type="radio" name="reason" value="Fraud/Scam"> Fraud/Scam</label>
                <label style="display: block; margin-bottom: 8px;"><input type="radio" name="reason" value="Poor Service"> Poor Service</label>
                <label style="display: block; margin-bottom: 8px;"><input type="radio" name="reason" value="No Show"> Did not show up</label>
                <label style="display: block; margin-bottom: 8px;"><input type="radio" name="reason" value="Wrong Pricing"> Wrong Pricing</label>
                <label style="display: block; margin-bottom: 8px;"><input type="radio" name="reason" value="Other"> Other</label>
            </div>
            <textarea id="reportDescription" placeholder="Describe what happened..." style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px;" rows="4"></textarea>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: #991b1b; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; flex: 1;">Submit Report</button>
                <button type="button" onclick="closeReportModal()" style="background: #e2e8f0; color: #475569; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; padding: 25px; border-radius: 16px; max-width: 500px; width: 100%; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-top: 0; font-size: 20px;"><i class="fas fa-calendar-check" style="color: var(--p-green);"></i> Book Service</h3>
        <p style="color: #64748b; margin-bottom: 20px; font-size: 14px;">Fill in your booking details</p>
        
        <form onsubmit="event.preventDefault(); submitBooking();">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Select Service</label>
                <select id="serviceType" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;" onchange="updatePrice()">
                    <option value="">Select a service</option>
                    <?php 
                    $settings = $conn->query("SELECT * FROM platform_settings LIMIT 1")->fetch_assoc();
                    $client_fee = $settings['client_fee_percent'] ?? 5;
                    foreach ($provider_services as $svc): 
                        $total_price = $svc['price'] + ($svc['price'] * $client_fee / 100);
                    ?>
                    <option value="<?= htmlspecialchars($svc['service_name']) ?>" data-price="<?= $svc['price'] ?>" data-service-id="<?= $svc['id'] ?>">
                        <?= htmlspecialchars($svc['service_name']) ?> - <?= number_format($total_price, 0, ',', ' ') ?> XAF
                    </option>
                    <?php endforeach; ?>
                    <option value="Other">Other (Custom Quote)</option>
                </select>
                <input type="hidden" id="selectedServiceId" value="">
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Preferred Date</label>
                    <input type="date" id="bookingDate" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Preferred Time</label>
                    <input type="time" id="bookingTime" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Your Address</label>
                <input type="text" id="bookingAddress" placeholder="Enter your address" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Describe the Service Needed</label>
                <textarea id="bookingDescription" placeholder="Describe what you need..." required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;" rows="3"></textarea>
            </div>
            <div style="margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 8px;">
                <p style="margin: 0 0 10px; font-weight: 600; color: #334155; font-size: 14px;">Payment Summary</p>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px;">
                    <span>Service Price:</span>
                    <span id="servicePrice">To be quoted</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Platform Fee (5%):</span>
                    <span id="platformFee">-</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: 700; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                    <span>Total:</span>
                    <span id="totalAmount">-</span>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: var(--p-green); color: white; border: none; padding: 14px 24px; border-radius: 8px; cursor: pointer; flex: 1; font-weight: 600;">Proceed to Payment</button>
                <button type="button" onclick="closeBookingModal()" style="background: #e2e8f0; color: #475569; border: none; padding: 14px 24px; border-radius: 8px; cursor: pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
const clientFeePercent = <?= $client_fee ?>;

function openBookingModal() {
    document.getElementById('bookingModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeBookingModal() {
    document.getElementById('bookingModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeBookingModal();
});

// Close modal on clicking outside
document.getElementById('bookingModal').addEventListener('click', function(e) {
    if (e.target === this) closeBookingModal();
});

function updatePrice() {
    const select = document.getElementById('serviceType');
    const selectedOption = select.options[select.selectedIndex];
    const price = parseFloat(selectedOption.dataset.price) || 0;
    const serviceId = selectedOption.dataset.serviceId || 0;
    
    document.getElementById('selectedServiceId').value = serviceId;
    
    if (price > 0) {
        const platformFee = Math.round(price * clientFeePercent / 100);
        const total = price + platformFee;
        document.getElementById('servicePrice').textContent = price.toLocaleString() + ' XAF';
        document.getElementById('platformFee').textContent = platformFee.toLocaleString() + ' XAF';
        document.getElementById('totalAmount').textContent = total.toLocaleString() + ' XAF';
    } else {
        document.getElementById('servicePrice').textContent = 'To be quoted';
        document.getElementById('platformFee').textContent = '-';
        document.getElementById('totalAmount').textContent = '-';
    }
}

function submitBooking() {
    const formData = new FormData();
    formData.append('provider_id', <?= $id ?>);
    formData.append('service_id', document.getElementById('selectedServiceId').value);
    formData.append('service_type', document.getElementById('serviceType').value);
    formData.append('booking_date', document.getElementById('bookingDate').value);
    formData.append('booking_time', document.getElementById('bookingTime').value);
    formData.append('address', document.getElementById('bookingAddress').value);
    formData.append('description', document.getElementById('bookingDescription').value);

    fetch('create_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Booking created! Redirecting to payment...');
            if (data.payment_required) {
                window.location.href = 'payment.php?booking_id=' + data.booking_id;
            } else {
                alert('Booking created successfully!');
                closeBookingModal();
            }
        } else {
            alert(data.message || 'Error creating booking');
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}
</script>

<?php include 'includes/footer.php'; ?>

</body>
</html>
