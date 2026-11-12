<?php
// all_services.php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth_check.php';
restrictAdminFromStorefront();

// Category Filter Logic
$whereClause = "status = 'approved'";
$active_cats = [];

// Handle multiple categories (e.g., from all_services page)
if (isset($_GET['cats']) && !empty($_GET['cats'])) {
    $slugs = explode(',', $_GET['cats']);
    $names = [];
    foreach ($slugs as $slug) {
        $slug = $conn->real_escape_string(trim($slug));
        if (empty($slug)) continue;
        $active_cats[] = $slug;
        $res_slug = $conn->query("SELECT name FROM services WHERE slug = '$slug'");
        if ($res_slug && $res_slug->num_rows > 0) {
            $names[] = "'" . $conn->real_escape_string($res_slug->fetch_assoc()['name']) . "'";
        }
    }
    if (!empty($names)) {
        $whereClause .= " AND profession IN (" . implode(',', $names) . ")";
    }
} 
// Handle single category fallback (e.g., direct link from homepage)
elseif (isset($_GET['cat']) && !empty($_GET['cat'])) {
    $slug = $conn->real_escape_string(trim($_GET['cat']));
    $active_cats[] = $slug;
    $res_slug = $conn->query("SELECT name FROM services WHERE slug = '$slug'");
    if ($res_slug && $res_slug->num_rows > 0) {
        $whereClause .= " AND profession = '" . $conn->real_escape_string($res_slug->fetch_assoc()['name']) . "'";
    }
}

// Fetch all matching providers - show all (online and offline)
$sql = "SELECT * FROM providers WHERE $whereClause ORDER BY provider_id DESC";
$res = $conn->query($sql);
$providers = [];
while ($row = $res->fetch_assoc()) {
    $providers[] = $row;
}

// Fetch all categories for filter pills
$cat_res = $conn->query("SELECT name, slug FROM services ORDER BY name ASC");
$all_categories = [];
if ($cat_res) {
    while ($c = $cat_res->fetch_assoc()) $all_categories[] = $c;
}

// Fetch reviews for each provider
$reviewsData = [];
$resReviews = $conn->query("SELECT provider_id, AVG(rating) as average, COUNT(*) as count FROM reviews GROUP BY provider_id");
if ($resReviews) {
    while ($row = $resReviews->fetch_assoc()) {
        $reviewsData[$row['provider_id']] = [
            'average' => round($row['average'], 1),
            'count' => $row['count']
        ];
    }
}

// Fetch Feeds (Portfolio Posts) - show all providers
$feeds = [];
$resFeeds = $conn->query("SELECT pp.*, pr.name, pr.profession, pr.profile_photo as provider_pic, pr.location, pr.is_online
                          FROM provider_posts pp 
                          JOIN providers pr ON pp.provider_id = pr.provider_id 
                          WHERE pr.status = 'approved'
                          ORDER BY pp.created_at DESC LIMIT 20");
if ($resFeeds) {
    while ($f = $resFeeds->fetch_assoc()) $feeds[] = $f;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Local Experts | ALLO_SERVICE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=1.1">
    <style>
        :root { --primary: #007A5E; --secondary: #00B894; --accent: #fcd116; --dark: #1e293b; --light: #f8fafc; }
        
        .services-hero {
            background: #f8fafc;
            padding: 60px 20px;
            text-align: center;
        }

        .search-container {
            max-width: 600px;
            margin: -30px auto 40px;
            position: relative;
            z-index: 10;
        }

        #searchInput {
            width: 100%;
            padding: 20px 30px;
            border-radius: 50px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            font-size: 1.1rem;
            outline: none;
            transition: 0.3s;
        }

        #searchInput:focus {
            box-shadow: 0 10px 35px rgba(0, 122, 94, 0.2);
            transform: translateY(-2px);
        }

        .provider-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 80px;
        }

        .provider-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .provider-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            border-color: var(--primary);
        }

        .provider-img {
            height: 200px;
            background: #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        .provider-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* List View Overrides */
        .provider-grid.list-view {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .provider-grid.list-view .provider-card {
            flex-direction: row;
            height: auto;
            align-items: center;
            padding: 20px;
            min-height: 200px;
            gap: 25px;
        }
        .provider-grid.list-view .provider-img {
            width: 150px;
            height: 150px;
            border-radius: 12px;
            margin-left: 10px;
            flex-shrink: 0;
        }
        .provider-grid.list-view .provider-info {
            padding: 0;
            flex-grow: 1;
        }
        .provider-grid.list-view .provider-name {
            font-size: 1.6rem;
            margin-bottom: 8px;
        }
        .provider-desc {
            display: none; /* Hidden in Card View */
            font-size: 14px;
            color: #475569;
            margin-bottom: 12px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .provider-skills-preview {
            display: flex; gap: 8px; flex-wrap: wrap; margin-top: 15px; 
        }
        .list-view .provider-skills-preview {
            display: none !important;
        }
        .skill-chip { 
            background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; 
        }

        .list-view .provider-desc {
            display: flex;
        }

        @media (max-width: 768px) {
            .hero-section { padding: 30px 15px; }
            .hero-section h1 { font-size: 1.6rem; margin-bottom: 5px; }
            .hero-section p { font-size: 0.85rem; }
            
            .search-container { margin-top: -20px; margin-bottom: 25px; }
            #searchInput { padding: 15px 25px; font-size: 0.95rem; }
            
            .category-filters { margin-bottom: 25px; gap: 8px; }
            .category-filters a { padding: 6px 14px; font-size: 12px; }
            
            .view-controls { margin-bottom: 20px; gap: 10px; }
            .toggle-btn { padding: 6px 12px; font-size: 13px; }
            
            .provider-grid { gap: 12px; }
            
            /* Card view on mobile */
            .provider-card {
                padding: 10px;
                border-radius: 12px;
            }
            .provider-img { height: 140px; border-radius: 10px; }
            .provider-info { padding: 10px; }
            .provider-name { font-size: 1rem; margin-bottom: 2px; }
            .provider-job { font-size: 0.7rem; }
            .provider-loc { font-size: 0.75rem; margin-top: 2px; }
            .provider-desc { font-size: 0.75rem; margin-top: 6px; -webkit-line-clamp: 2; }
            .provider-rating { margin-top: 6px; font-size: 0.75rem; gap: 8px; }
            
            /* List view on mobile */
            .list-view .provider-card {
                padding: 10px;
                gap: 5px;
            }
            .list-view .provider-img {
                display: none !important;
            }
            .list-view .provider-info {
                width: 100%;
            }
            .list-view .provider-name {
                font-size: 0.9rem;
                font-weight: 800;
            }
            .list-view .provider-desc {
                -webkit-line-clamp: 2;
                font-size: 0.7rem;
                margin-top: 2px;
            }
        }

        /* View Toggles */
        .view-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 15px;
        }
        .toggle-group {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
        }
        .toggle-btn {
            padding: 8px 16px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            transition: 0.2s;
            color: #64748b;
            background: transparent;
        }
        .toggle-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        /* Feed Post Styles */
        .feed-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            margin-bottom: 20px;
            transition: 0.3s;
        }
        .feed-header {
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }
        .feed-header img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }
        .feed-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            cursor: pointer;
        }
        .feed-content {
            padding: 15px 20px;
        }
        .feed-caption {
            font-size: 1rem;
            color: var(--dark);
            line-height: 1.5;
            margin-bottom: 5px;
        }
        .feed-meta {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .category-filters {
            margin-bottom: 40px !important; /* Move search up, filters down */
        }
        .search-container {
            margin: -30px auto 30px !important;
        }

        .provider-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .provider-info {
            padding: 25px;
            flex-grow: 1;
        }

        .provider-job {
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .provider-name {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .provider-loc {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .provider-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
        }

        .stars { color: #fcd116; font-size: 1.1rem; }
        .rev-count { color: #94a3b8; font-size: 0.85rem; }

        /* Modal Styles */
        #providerModal {
            display:none; 
            position:fixed; 
            z-index:1100; 
            left:0; top:0; 
            width:100%; height:100%; 
            background: rgba(15, 23, 42, 0.8); 
            backdrop-filter: blur(8px); 
            justify-content:center; 
            align-items:center;
            padding: 20px;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 650px;
            border-radius: 24px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalIn 0.3s ease-out;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 10;
        }

        #modalBody { padding: 40px; }

        @media (max-width: 768px) {
            .provider-grid.list-view .provider-card {
                flex-direction: column;
            }
            .provider-grid.list-view .provider-img {
                width: 100%;
                height: 200px;
            }
        }
        @media (max-width: 640px) {
            .provider-grid { grid-template-columns: 1fr; }
            #modalBody { padding: 25px; }
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="services-hero">
    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--dark);">Our Professional Partners</h1>
        <p style="color: #64748b; margin-top: 10px;">Connect directly with verified local experts across Cameroon.</p>
    </div>
</section>

<div class="container">
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search for experts (e.g. Plumber, Douala)...">
    </div>

    <div class="category-filters" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 40px; justify-content: center;">
        <a href="all_services.php" style="padding: 8px 18px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: bold; transition: 0.3s; <?= empty($active_cats) ? 'background: var(--primary); color: white;' : 'background: white; color: var(--dark); border: 1px solid #e2e8f0;' ?>">All</a>
        <?php foreach ($all_categories as $c): 
            $slug = $c['slug'];
            $is_active = in_array($slug, $active_cats);
            $new_cats = $is_active ? array_diff($active_cats, [$slug]) : array_merge($active_cats, [$slug]);
            $href = empty($new_cats) ? "all_services.php" : "all_services.php?cats=" . urlencode(implode(',', $new_cats));
        ?>
            <a href="<?= $href ?>" style="padding: 8px 18px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: bold; transition: 0.3s; <?= $is_active ? 'background: var(--primary); color: white;' : 'background: white; color: var(--dark); border: 1px solid #e2e8f0;' ?>"><?= htmlspecialchars($c['name']) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="view-controls">
        <div class="toggle-group" id="contentTypeToggle">
            <button class="toggle-btn active" data-type="accounts"><i class="fas fa-users"></i> Accounts</button>
            <button class="toggle-btn" data-type="feeds"><i class="fas fa-rss"></i> Feeds</button>
        </div>

        <div class="toggle-group" id="viewModeToggle" style="margin-left: auto;">
            <button class="toggle-btn active" data-mode="card"><i class="fas fa-th-large"></i> Card</button>
            <button class="toggle-btn" data-mode="list"><i class="fas fa-list"></i> List</button>
        </div>
    </div>

    <div id="accountsWrapper">
        <div class="provider-grid" id="providerList">
            <?php foreach ($providers as $p):
                $id = $p['provider_id'];
                $review = isset($reviewsData[$id]) ? $reviewsData[$id] : ['average' => 0, 'count' => 0];
                $photo = !empty($p['profile_photo']) ? $p['profile_photo'] : 'assets/img/default-avatar.png';
                $is_online = !empty($p['is_online']);
            ?>
                <div class="provider-card" onclick="window.location.href='profile.php?id=<?= $id ?>'" <?= !$is_online ? 'style="opacity: 0.7;"' : '' ?>>
                    <div class="provider-img">
                        <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars(stripslashes($p['name'])) ?>" onerror="this.src='assets/img/default-avatar.png'">
                        <?php if($p['status'] === 'approved'): ?>
                            <div class="provider-badge" title="Verified Professional"><i class="fas fa-check-circle"></i></div>
                        <?php endif; ?>
                        <?php if(!$is_online): ?>
                            <div class="provider-badge" style="background: #94a3b8;" title="Offline"><i class="fas fa-circle" style="font-size: 10px;"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="provider-info">
                        <div class="provider-job"><?= htmlspecialchars(stripslashes($p['profession'])) ?></div>
                        <div class="provider-name"><?= htmlspecialchars(stripslashes($p['name'])) ?></div>
                        <?php if(!$is_online): ?>
                        <div style="color: #94a3b8; font-size: 12px; margin-top: 2px;"><i class="fas fa-clock"></i> Offline</div>
                        <?php endif; ?>
                        <div class="provider-loc">📍 <span class="location-text"><?= htmlspecialchars(stripslashes($p['location'])) ?></span></div>
                        <div class="provider-rating">
                            <div class="stars">
                                <?= $review['average'] > 0 ? str_repeat('★', floor($review['average'])) . ($review['average'] - floor($review['average']) >= 0.5 ? '½' : '') : '☆☆☆☆☆' ?>
                            </div>
                            <div class="rev-count">(<?= $review['count'] ?> Verified Reviews)</div>
                        </div>
                        <?php if (!empty($p['description'])): ?>
                            <div class="provider-desc"><?= htmlspecialchars(stripslashes($p['description'])) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($p['skills'])): ?>
                            <div class="provider-skills-preview">
                                <?php 
                                $skills = explode(',', $p['skills']);
                                $count = 0;
                                foreach($skills as $sk): if($count++ >= 3) break; if(empty(trim($sk))) continue; ?>
                                    <span class="skill-badge"><?= htmlspecialchars(trim($sk)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Feeds View (Hidden by default) -->
    <div id="feedsWrapper" style="display: none; max-width: 600px; margin: 0 auto;">
        <?php if (empty($feeds)): ?>
            <p style="text-align: center; color: #64748b; padding: 40px;">No work posts found yet. Portfolio feeds will appear here as professionals upload their work.</p>
        <?php else: ?>
            <?php foreach ($feeds as $f): ?>
                <div class="feed-card">
                    <div class="feed-header" onclick="window.location.href='profile.php?id=<?= $f['provider_id'] ?>'">
                        <img src="<?= htmlspecialchars($f['provider_pic'] ?: 'assets/img/default-avatar.png') ?>" alt="Pro">
                        <div>
                            <div style="font-weight: 800; color: var(--dark);"><?= htmlspecialchars(stripslashes($f['name'])) ?></div>
                            <div style="font-size: 0.8rem; color: var(--primary); text-transform: uppercase; font-weight: 700;"><?= htmlspecialchars(stripslashes($f['profession'])) ?> • <span class="location-text"><?= htmlspecialchars(stripslashes($f['location'])) ?></span></div>
                        </div>
                    </div>
                    <img src="<?= htmlspecialchars($f['image_path']) ?>" class="feed-img" onclick="window.location.href='profile.php?id=<?= $f['provider_id'] ?>'">
                    <div class="feed-content">
                        <p class="feed-caption"><?= htmlspecialchars(stripslashes($f['caption'])) ?></p>
                        <div class="feed-meta"><?= date('M j, Y', strtotime($f['created_at'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div id="providerModal">
    <div class="modal-content">
        <div class="modal-close" onclick="closeModal()">&times;</div>
        <div id="modalBody">
            <div style="text-align:center; padding: 40px;">
                <div class="loading-spinner"></div>
                <p>Loading profile...</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle View Mode (Card vs Tile)
    document.querySelectorAll('#viewModeToggle .toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#viewModeToggle .toggle-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const mode = this.dataset.mode;
            const grid = document.getElementById('providerList');
            if (mode === 'list') {
                grid.classList.add('list-view');
            } else {
                grid.classList.remove('list-view');
            }
        });
    });

    // Toggle Content Type (Accounts vs Feeds)
    document.querySelectorAll('#contentTypeToggle .toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#contentTypeToggle .toggle-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const type = this.dataset.type;
            const accounts = document.getElementById('accountsWrapper');
            const feeds = document.getElementById('feedsWrapper');
            const viewControls = document.getElementById('viewModeToggle');

            if (type === 'feeds') {
                accounts.style.display = 'none';
                feeds.style.display = 'block';
                viewControls.style.opacity = '0.3';
                viewControls.style.pointerEvents = 'none';
            } else {
                accounts.style.display = 'block';
                feeds.style.display = 'none';
                viewControls.style.opacity = '1';
                viewControls.style.pointerEvents = 'all';
            }
        });
    });

    // Live Search Logic
    document.getElementById('searchInput').addEventListener('input', function() {
        const val = this.value.toLowerCase();
        const cards = document.querySelectorAll('.provider-card');
        const feedCards = document.querySelectorAll('.feed-card');
        
        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(val) ? 'flex' : 'none';
        });

        feedCards.forEach(card => {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(val) ? 'block' : 'none';
        });
    });

    function showProvider(id) {
        const modal = document.getElementById('providerModal');
        const modalBody = document.getElementById('modalBody');
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent scroll
        
        // Show loading state
        modalBody.innerHTML = '<div style="text-align:center; padding: 60px;"><p>Fetching professional details...</p></div>';

        fetch('provider/provider_ajax.php?id=' + id)
            .then(r => r.text())
            .then(html => {
                modalBody.innerHTML = html;
            })
            .catch(err => {
                modalBody.innerHTML = '<div style="text-align:center; padding: 60px; color: red;"><p>Error loading profile. Please try again.</p></div>';
            });
    }

    function closeModal() {
        document.getElementById('providerModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    window.onclick = function(e) {
        if(e.target == document.getElementById('providerModal')) closeModal();
    };

    // Event Delegation for Dynamic Modal Content (Stars & Review Form)
    document.addEventListener('click', function(e) {
        // Star Rating Toggle
        if (e.target.classList.contains('star')) {
            const val = e.target.dataset.value;
            const parent = e.target.parentElement;
            const input = document.getElementById('noteInput');
            if (input) input.value = val;
            
            // UI Update
            const stars = parent.querySelectorAll('.star');
            stars.forEach(s => {
                if (parseInt(s.dataset.value) <= parseInt(val)) {
                    s.style.color = '#fcd116';
                } else {
                    s.style.color = '#ccc';
                }
            });
        }
    });

    document.addEventListener('submit', function(e) {
        if (e.target.id === 'reviewForm') {
            e.preventDefault();
            const formData = new FormData(e.target);
            const btn = e.target.querySelector('button');
            const originalText = btn.innerText;
            
            btn.disabled = true;
            btn.innerText = 'Submitting...';

            fetch('save_review.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Review submitted successfully!');
                    closeModal();
                    location.reload(); // Refresh to show new review & rating
                } else {
                    alert(data.message || 'Error submitting review.');
                    btn.disabled = false;
                    btn.innerText = originalText;
                }
            })
            .catch(err => {
                alert('Network error. Please try again.');
                btn.disabled = false;
                btn.innerText = originalText;
            });
        }
    });

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

    // Add fadeIn animation
    const style = document.createElement('style');
    style.innerHTML = `@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }`;
    document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
