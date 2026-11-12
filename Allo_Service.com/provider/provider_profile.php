<?php
// provider/provider_profile.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

// Auto-migrate social media fields & posts table (failsafe)
$res_cols = $conn->query("SHOW COLUMNS FROM providers LIKE 'instagram'");
if ($res_cols && $res_cols->num_rows == 0) {
    $conn->query("ALTER TABLE providers ADD COLUMN instagram VARCHAR(255), ADD COLUMN facebook VARCHAR(255), ADD COLUMN twitter VARCHAR(255), ADD COLUMN website VARCHAR(255), ADD COLUMN tiktok VARCHAR(255), ADD COLUMN whatsapp VARCHAR(255)");
}

$conn->query("CREATE TABLE IF NOT EXISTS provider_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS post_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (post_id) REFERENCES provider_posts(id) ON DELETE CASCADE
)");

$provider_id = $_SESSION['provider_id'];
$stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id = ?");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();
$stmt->close();

$is_pending = ($provider['status'] === 'pending');
$is_online = $provider['is_online'] ?? 0;
$needs_kyc = empty($provider['selfie']) || !isset($provider['selfie']) || empty($provider['id_card_front']) || empty($provider['id_card_back']);

// Auto-deactivate providers who were online yesterday
$conn->query("UPDATE providers SET is_online = 0, online_since = NULL WHERE is_online = 1 AND DATE(online_since) < CURDATE()");

// Get wallet balance
$wallet_balance = $provider['wallet_balance'] ?? 0;

// Get earnings and bookings stats
$res_earn = $conn->query("SELECT SUM(professional_amount) as total FROM payments WHERE provider_id = $provider_id AND status = 'completed'");
$total_earnings = $res_earn->fetch_assoc()['total'] ?? 0;

$res_book = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE provider_id = $provider_id AND status = 'completed'");
$completed_jobs = $res_book->fetch_assoc()['total'];

$res_pending = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE provider_id = $provider_id AND status = 'confirmed'");
$pending_jobs = $res_pending->fetch_assoc()['total'];

// Get provider services
$res_services = $conn->query("SELECT * FROM provider_services WHERE provider_id = $provider_id ORDER BY service_name");
$provider_services = [];
if ($res_services) {
    while ($s = $res_services->fetch_assoc()) {
        $provider_services[] = $s;
    }
}

if ($provider['status'] === 'rejected') {
    die("Your application was rejected. Please contact support.");
}

// Extract provider details for display
$name = $provider['name'];
$email = $provider['email'];
$phone = $provider['phone'];
$job = $provider['profession'];
$availability = $provider['availability'];
$description = $provider['description'];
$skills = $provider['skills'];
$profile_pic = $provider['profile_photo'];
$location = $provider['location'];

// Fetch reviews count or average rating if you have it (optional for now)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Dashboard | ALLO_SERVICE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #007A5E;
            --accent: #FCD116;
            --danger: #CE1126;
            --light: #f8f9fa;
            --dark: #212529;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        .sidebar {
            width: 260px;
            background: var(--primary);
            color: var(--white);
            height: 100vh;
            position: fixed;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 40px;
            text-align: center;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .nav-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-links a:hover, .nav-links a.active {
            background: rgba(255,255,255,0.15);
            color: var(--white);
            transform: translateX(5px);
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 1.8rem;
            color: var(--primary);
        }

        .profile-card {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 15px;
            object-fit: cover;
            border: 4px solid var(--primary);
        }

        .profile-info h2 {
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .info-tag {
            display: inline-block;
            background: #e9ecef;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--primary);
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }

        .detail-item span {
            display: block;
            font-size: 0.8rem;
            color: #777;
            text-transform: uppercase;
            font-weight: 700;
        }

        .detail-item p {
            font-weight: 500;
        }

        .services-section {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .services-section h3 {
            margin-bottom: 20px;
            border-bottom: 2px solid var(--light);
            padding-bottom: 10px;
            color: var(--primary);
        }

        .edit-btn {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s;
        }

        .edit-btn:hover {
            opacity: 0.9;
        }

        @media (max-width: 900px) {
            .sidebar { width: 80px; padding: 20px 10px; }
            .sidebar .logo, .sidebar a span { display: none; }
            .main-content { margin-left: 80px; }
            .profile-card { flex-direction: column; align-items: center; text-align: center; }
            .details-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo">ALLO_SERVICE</div>
            <nav class="nav-links">
            <a href="provider_profile.php" class="active">Dashboard</a>
            <a href="provider_profile_edit.php">Manage Profile</a>
            <a href="provider_bookings.php">My Bookings</a>
            <a href="payout_settings.php">Wallet & Payout</a>
            <a href="provider_reviews.php">My Reviews</a>
            <a href="provider_notifications.php">Notifications</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <?php if ($needs_kyc): ?>
        <div style="background: #fef3c7; border: 1px solid #fcd34d; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; border-left: 5px solid #f59e0b; display: flex; align-items: center; gap: 15px;">
            <i class="fas fa-id-card" style="font-size: 1.5rem; color: #f59e0b;"></i>
            <div>
                <strong style="display: block; margin-bottom: 3px;">Verification Required</strong>
                <p style="font-size: 0.9rem; margin: 0;">Please upload your selfie and ID documents to get verified and start receiving bookings.</p>
            </div>
            <a href="provider_kyc.php" target="_blank" style="background: #f59e0b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-left: auto;">Complete Verification</a>
        </div>
        <?php endif; ?>
        
        <?php if ($is_pending): ?>
        <div style="background: #fff3cd; color: #856404; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; border-left: 5px solid #ffc107; display: flex; align-items: center; gap: 15px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
            <div>
                <strong style="display: block; margin-bottom: 3px;">Account Pending Approval</strong>
                <p style="font-size: 0.9rem; margin: 0;">Your profile is being reviewed by our team. Some features like "View Shop" and "Portfolio Posting" are currently disabled.</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="header" style="flex-wrap: wrap; gap: 15px;">
            <h1>Provider Dashboard</h1>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <?php if (!$is_pending && !$needs_kyc): ?>
                <div style="display: flex; align-items: center; gap: 10px; background: <?= $is_online ? '#dcfce7' : '#f1f5f9' ?>; padding: 8px 15px; border-radius: 8px;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: <?= $is_online ? '#10b981' : '#94a3b8' ?>;"></span>
                    <span style="font-size: 13px; font-weight: 600; color: <?= $is_online ? '#166534' : '#64748b' ?>;"><?= $is_online ? 'Online' : 'Offline' ?></span>
                    <form method="post" action="toggle_online.php" style="margin: 0;">
                        <?php if ($is_online): ?>
                        <button type="submit" name="action" value="go_offline" style="background: #64748b; color: white; border: none; padding: 5px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">Go Offline</button>
                        <?php else: ?>
                        <button type="submit" name="action" value="go_online" style="background: #10b981; color: white; border: none; padding: 5px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">Go Online</button>
                        <?php endif; ?>
                    </form>
                </div>
                <?php endif; ?>
                <a href="../index.php" style="background:var(--light); color:var(--dark); padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:600; border:1px solid #e2e8f0; transition:0.3s;"><i class="fas fa-home"></i> Main Site</a>
                <?php if (!$is_pending): ?>
                    <a href="../profile.php?id=<?= $provider_id ?>" target="_blank" style="background:#4f46e5; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:600; border:1px solid #3730a3; transition:0.3s;"><i class="fas fa-eye"></i> View Shop Profile</a>
                <?php else: ?>
                    <button disabled style="background:#94a3b8; color:white; padding:10px 20px; border-radius:8px; border:none; font-weight:600; cursor:not-allowed;"><i class="fas fa-eye"></i> View Profil (Pending)</button>
                <?php endif; ?>
                <a href="provider_profile_edit.php" class="edit-btn">Edit Profile</a>
            </div>
        </div>

        <?php if (!$is_pending): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 4px solid #10b981;">
                <h4 style="color: #64748b; font-size: 0.9rem; margin-bottom: 10px;">Total Earnings</h4>
                <div style="font-size: 1.8rem; font-weight: 700; color: #10b981;"><?= number_format($total_earnings, 0, ',', ' ') ?> XAF</div>
            </div>
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 4px solid #007a33;">
                <h4 style="color: #64748b; font-size: 0.9rem; margin-bottom: 10px;">Completed Jobs</h4>
                <div style="font-size: 1.8rem; font-weight: 700; color: #007a33;"><?= $completed_jobs ?></div>
            </div>
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 4px solid #f59e0b;">
                <h4 style="color: #64748b; font-size: 0.9rem; margin-bottom: 10px;">Pending Jobs</h4>
                <div style="font-size: 1.8rem; font-weight: 700; color: #f59e0b;"><?= $pending_jobs ?></div>
            </div>
        </div>
        <?php endif; ?>

        <div class="profile-card">
            <img src="<?php echo $profile_pic ? '../' . htmlspecialchars($profile_pic) : '../assets/img/default-avatar.png'; ?>" alt="Profile Picture" class="profile-pic">
            <div class="profile-info">
                <span class="info-tag"><?php echo htmlspecialchars($job); ?></span>
                <h2><?php echo htmlspecialchars($name); ?></h2>
                <p style="color: #666; margin-bottom: 15px;"><?php echo htmlspecialchars($location); ?></p>
                
                <div class="details-grid">
                    <div class="detail-item">
                        <span>Email Address</span>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                    <div class="detail-item">
                        <span>Regular Phone Line</span>
                        <p><?php echo htmlspecialchars($phone); ?></p>
                    </div>
                    <div class="detail-item">
                        <span>Availability</span>
                        <p><?php echo htmlspecialchars($availability); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="services-section">
            <h3>Description & Skills</h3>
            <div style="margin-bottom: 20px;">
                <h4 style="font-size: 0.9rem; color: #777; text-transform: uppercase;">About My Services</h4>
                <p><?php echo nl2br(htmlspecialchars($description ?? '')); ?></p>
            </div>
            <div>
                <h4 style="font-size: 0.9rem; color: #777; text-transform: uppercase;">Key Skills</h4>
                <p><?php echo nl2br(htmlspecialchars($skills ?? '')); ?></p>
            </div>
        </div>

        <div class="services-section" style="margin-top: 30px;">
            <h3>Social & Online Presence</h3>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <?php if (!empty($provider['whatsapp'])): ?>
                    <a href="https://wa.me/<?= htmlspecialchars($provider['whatsapp']) ?>" target="_blank" style="padding: 8px 15px; background: #25D366; color: white; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: bold;"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                <?php endif; ?>
                <?php if (!empty($provider['facebook'])): ?>
                    <a href="<?= htmlspecialchars($provider['facebook']) ?>" target="_blank" style="padding: 8px 15px; background: #1877F2; color: white; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: bold;"><i class="fab fa-facebook"></i> Facebook</a>
                <?php endif; ?>
                <?php if (!empty($provider['instagram'])): ?>
                    <a href="https://instagram.com/<?= str_replace('@', '', htmlspecialchars($provider['instagram'])) ?>" target="_blank" style="padding: 8px 15px; background: #E1306C; color: white; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: bold;"><i class="fab fa-instagram"></i> Instagram</a>
                <?php endif; ?>
                <?php if (!empty($provider['tiktok'])): ?>
                    <a href="https://tiktok.com/<?= htmlspecialchars($provider['tiktok']) ?>" target="_blank" style="padding: 8px 15px; background: #000000; color: white; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: bold;"><i class="fab fa-tiktok"></i> TikTok</a>
                <?php endif; ?>
                <?php if (!empty($provider['twitter'])): ?>
                    <a href="https://twitter.com/<?= str_replace('@', '', htmlspecialchars($provider['twitter'])) ?>" target="_blank" style="padding: 8px 15px; background: #1DA1F2; color: white; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: bold;"><i class="fab fa-twitter"></i> Twitter</a>
                <?php endif; ?>
                <?php if (!empty($provider['website'])): ?>
                    <a href="<?= htmlspecialchars($provider['website']) ?>" target="_blank" style="padding: 8px 15px; background: #64748b; color: white; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: bold;"><i class="fas fa-globe"></i> Website</a>
                <?php endif; ?>
            </div>
            <p style="margin-top: 15px; font-size: 13px; color: #94a3b8;"><a href="provider_profile_edit.php" style="color: var(--primary);">Update your social links here.</a></p>
        </div>

        <div class="services-section" style="margin-top: 30px;">
            <h3>My Portfolio Posts</h3>
            <p style="color: #64748b; margin-bottom: 20px;">Upload photos of your recent work to attract more clients.</p>
            
            <?php if (!$is_pending): ?>
            <form method="post" enctype="multipart/form-data" action="provider_portfolio_upload.php" style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #e2e8f0;">
                <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Upload Work Photo(s) - Up to 8</label>
                        <div id="postPreviewContainer" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;"></div>
                        <input type="file" id="postImageInput" name="post_images[]" accept="image/*" multiple required style="width: 100%; border: 1px dashed #cbd5e1; padding: 10px; border-radius: 8px; background: white;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Caption / Description</label>
                        <input type="text" name="caption" placeholder="E.g., Installed a new sink..." required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>
                    <button type="submit" name="upload_post" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; justify-self: start;"><i class="fas fa-upload"></i> Post to Portfolio</button>
                </div>
            </form>
            <?php else: ?>
            <div style="background: #f1f5f9; padding: 20px; border-radius: 12px; border: 1px dashed #cbd5e1; text-align: center; color: #64748b; margin-bottom: 30px;">
                <i class="fas fa-lock" style="margin-bottom: 10px; font-size: 1.2rem;"></i>
                <p>Portfolio posting is disabled until your account is approved.</p>
            </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                <?php
                $res_posts = $conn->query("SELECT * FROM provider_posts WHERE provider_id = $provider_id ORDER BY created_at DESC");
                if ($res_posts && $res_posts->num_rows > 0):
                    while ($post = $res_posts->fetch_assoc()):
                ?>
                    <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; position: relative;">
                        <img src="../<?= htmlspecialchars($post['image_path']) ?>" style="width: 100%; height: 180px; object-fit: cover; display: block;">
                        <div style="padding: 12px; background: white;">
                            <p style="font-size: 13px; color: #334155; font-weight: 600; margin-bottom: 5px;"><?= htmlspecialchars($post['caption']) ?></p>
                            <span style="font-size: 11px; color: #94a3b8;"><?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                            
                            <a href="#" onclick="editPost(<?= $post['id'] ?>, '<?= htmlspecialchars(addslashes($post['caption'])) ?>')" style="position: absolute; top: 10px; right: 40px; background: #f59e0b; color: white; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 11px;" title="Edit Post Caption"><i class="fas fa-edit"></i></a>
                            <a href="provider_portfolio_upload.php?delete=<?= $post['id'] ?>" style="position: absolute; top: 10px; right: 10px; background: red; color: white; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 11px;" onclick="return confirm('Delete this post?');" title="Delete Post"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else: 
                ?>
                    <p style="color: #94a3b8; font-style: italic; grid-column: 1 / -1;">You haven't uploaded any portfolio photos yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="services-section" style="margin-top: 30px;">
            <h3><i class="fas fa-tags"></i> My Service Prices</h3>
            <p style="color: #64748b; margin-bottom: 20px;">Set your baseline prices for different services you offer. Platform and provider fees will be added automatically.</p>
            
            <?php if (!$is_pending): ?>
            <form method="post" action="provider_services.php" style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #e2e8f0;">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Service Name</label>
                        <input type="text" name="service_name" placeholder="e.g., Sink Installation" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Description</label>
                        <input type="text" name="description" placeholder="Brief description" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 5px;">Price (XAF)</label>
                        <input type="number" name="price" placeholder="5000" required min="0" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                    </div>
                    <button type="submit" name="add_service" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;"><i class="fas fa-plus"></i> Add</button>
                </div>
            </form>
            <?php endif; ?>

            <div style="background: white; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 15px; text-align: left; font-size: 13px; color: #475569;">Service</th>
                            <th style="padding: 15px; text-align: left; font-size: 13px; color: #475569;">Description</th>
                            <th style="padding: 15px; text-align: right; font-size: 13px; color: #475569;">Base Price</th>
                            <th style="padding: 15px; text-align: right; font-size: 13px; color: #475569;">Total (inc. fees)</th>
                            <?php if (!$is_pending): ?>
                            <th style="padding: 15px; text-align: center; font-size: 13px; color: #475569;">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $settings = $conn->query("SELECT * FROM platform_settings LIMIT 1")->fetch_assoc();
                        $client_fee = $settings['client_fee_percent'] ?? 5;
                        $provider_fee = $settings['provider_fee_percent'] ?? 10;
                        
                        if (empty($provider_services)): ?>
                        <tr>
                            <td colspan="<?= $is_pending ? 4 : 5 ?>" style="padding: 30px; text-align: center; color: #94a3b8;">
                                <i class="fas fa-tags" style="font-size: 24px; margin-bottom: 10px;"></i>
                                <p>No services added yet. Add your baseline prices above.</p>
                            </td>
                        </tr>
                        <?php else: 
                            foreach ($provider_services as $svc):
                                $total_price = $svc['price'] + ($svc['price'] * $client_fee / 100);
                        ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 15px; font-weight: 600; color: #334155;"><?= htmlspecialchars($svc['service_name']) ?></td>
                            <td style="padding: 15px; color: #64748b; font-size: 14px;"><?= htmlspecialchars($svc['description'] ?? '-') ?></td>
                            <td style="padding: 15px; text-align: right; font-weight: 600;"><?= number_format($svc['price'], 0, ',', ' ') ?> XAF</td>
                            <td style="padding: 15px; text-align: right; color: var(--primary); font-weight: 700;"><?= number_format($total_price, 0, ',', ' ') ?> XAF</td>
                            <?php if (!$is_pending): ?>
                            <td style="padding: 15px; text-align: center;">
                                <a href="provider_services.php?delete=<?= $svc['id'] ?>" style="color: #ef4444; font-size: 13px;" onclick="return confirm('Delete this service?');"><i class="fas fa-trash"></i></a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p style="font-size: 12px; color: #94a3b8; margin-top: 10px;">
                <i class="fas fa-info-circle"></i> Base price is what you receive. Client pays base + <?= $client_fee ?>% platform fee. You pay <?= $provider_fee ?>% commission on completion.
            </p>
        </div>
    </main>
    
    <!-- Hidden form for editing posts -->
    <form id="editPostForm" method="post" action="provider_portfolio_upload.php" style="display:none;">
        <input type="hidden" name="edit_post" value="1">
        <input type="hidden" name="post_id" id="editPostId">
        <input type="hidden" name="caption" id="editPostCaption">
    </form>

    <script>
        // Real-time local image preview reader (Multiple)
        document.getElementById('postImageInput').addEventListener('change', function(e) {
            const container = document.getElementById('postPreviewContainer');
            container.innerHTML = ''; // Clear previous
            const files = Array.from(e.target.files).slice(0, 8); // Limit to 8 for preview
            
            files.forEach(file => {
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '80px';
                        img.style.height = '80px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '8px';
                        container.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
        
        // Editable prompt for post caption
        function editPost(id, oldCaption) {
            const newCaption = prompt("Edit your post description:", oldCaption);
            if (newCaption !== null && newCaption.trim() !== "") {
                document.getElementById('editPostId').value = id;
                document.getElementById('editPostCaption').value = newCaption.trim();
                document.getElementById('editPostForm').submit();
            }
        }
    </script>
</body>
</html>