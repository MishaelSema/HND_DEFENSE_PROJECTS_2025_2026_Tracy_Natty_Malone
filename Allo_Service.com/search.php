<?php
// search.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db.php';
require_once 'includes/csrf.php';

// AJAX search logic...
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['metier'])) {
    header('Content-Type: application/json');
    
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        echo json_encode(['error' => 'Invalid request. Please refresh and try again.']);
        exit;
    }
    
    $job = isset($_POST['metier']) ? clean_input($_POST['metier']) : '';
    $location = isset($_POST['localisation']) ? clean_input($_POST['localisation']) : '';
    $rating = isset($_POST['note']) ? intval($_POST['note']) : '';

    $sql = "SELECT p.provider_id, p.name, p.profile_photo AS photo, p.profession, p.location, p.phone,
                   IFNULL(AVG(a.rating), 0) AS rating, p.description
            FROM providers p
            LEFT JOIN reviews a ON a.provider_id = p.provider_id
            WHERE p.status = 'approved'";
    
    $params = [];
    $types = '';

    if (!empty($job)) {
        $sql .= " AND p.profession LIKE ?";
        $params[] = "%$job%";
        $types .= 's';
    }
    if (!empty($location)) {
        $sql .= " AND p.location = ?";
        $params[] = $location;
        $types .= 's';
    }
    
    $sql .= " GROUP BY p.provider_id";
    
    if (!empty($rating)) {
        $sql .= " HAVING rating >= ?";
        $params[] = (int)$rating;
        $types .= 'i';
    }

    $sql .= " ORDER BY p.name";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    echo json_encode($results);
    exit;
}

// Category search...
$providersByJob = [];
$sql = "SELECT p.provider_id, p.name, p.profile_photo AS photo, p.profession, p.location, p.phone,
               IFNULL(AVG(a.rating), 0) AS rating, p.description
        FROM providers p
        LEFT JOIN reviews a ON a.provider_id = p.provider_id
        WHERE p.status = 'approved'
        GROUP BY p.provider_id
        ORDER BY p.profession, p.name";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $job = $row['profession'];
    if (!isset($providersByJob[$job])) $providersByJob[$job] = [];
    $providersByJob[$job][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Categories | ALLO_SERVICE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=1.2">
    <style>
        :root { --primary: #007A5E; --secondary: #00B894; --bg: #f8fafc; --dark: #1e293b; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; margin: 0; }
        
        .search-hero {
            background: #f8fafc;
            padding: 60px 20px;
            text-align: center;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 80px;
        }

        .category-card {
            background: white;
            padding: 35px 25px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid #f1f5f9;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }

        .category-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .category-card h3 {
            color: var(--primary);
            margin-bottom: 10px;
            font-weight: 800;
        }

        .category-card p {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .results-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .back-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .provider-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 60px;
        }

        .result-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: 0.3s;
        }

        .result-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .result-card h4 {
            color: var(--dark);
            font-size: 1.2rem;
            margin-bottom: 8px;
            font-weight: 800;
        }

        .result-rating {
            color: #fcd116;
            font-weight: 800;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .result-meta {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .wa-btn {
            display: inline-block;
            background: #25D366;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .wa-btn:hover {
            background: #128C7E;
            transform: scale(1.05);
        }

        @media (max-width: 600px) {
            .category-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="search-hero">
    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--dark);">Service Categories</h1>
        <p style="color: #64748b; margin-top: 10px;">Browse our network of professionals by their trade.</p>
    </div>
</section>

<div class="container">
    
    <div id="categoriesSection">
        <div class="category-grid">
            <?php foreach (array_keys($providersByJob) as $job): ?>
                <div class="category-card" onclick="searchByCategory('<?= htmlspecialchars($job, ENT_QUOTES, 'UTF-8') ?>')">
                    <h3><?= htmlspecialchars($job) ?></h3>
                    <p><?= count($providersByJob[$job]) ?> Active Professionals</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="resultsSection" style="display:none;">
        <div class="results-header">
            <div class="back-link" onclick="showCategories()">← All Categories</div>
            <h2 id="categoryTitle" style="color: var(--dark); font-weight: 800;"></h2>
        </div>
        <div class="provider-list" id="resultsDiv"></div>
    </div>
</div>

<script>
const csrfToken = '<?= generate_csrf_token() ?>';

async function searchByCategory(job) {
    document.getElementById('categoryTitle').innerText = job;
    const formData = new FormData();
    formData.append('metier', job);
    formData.append('csrf_token', csrfToken);
    
    const resultsDiv = document.getElementById('resultsDiv');
    resultsDiv.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 40px;">Searching...</div>';
    
    document.getElementById('categoriesSection').style.display = 'none';
    document.getElementById('resultsSection').style.display = 'block';

    try {
        const r = await fetch('search.php', { method: 'POST', body: formData });
        const data = await r.json();
        
        if (data.error) {
            resultsDiv.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 40px; color:red;">' + data.error + '</div>';
            return;
        }
        
        resultsDiv.innerHTML = '';
        if(data.length === 0) {
            resultsDiv.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 40px;">No professionals found in this category yet.</div>';
            return;
        }

        data.forEach(p => {
            const rating = parseFloat(p.rating).toFixed(1);
            resultsDiv.innerHTML += `
                <div class="result-card">
                    <h4>${escapeHtml(p.name)}</h4>
                    <div class="result-rating">★ ${rating > 0 ? rating : 'No ratings'}</div>
                    <div class="result-meta">${escapeHtml(p.profession)} | 📍 ${escapeHtml(p.location)}</div>
                    <a href="https://wa.me/${escapeHtml(p.phone)}" target="_blank" class="wa-btn">Contact on WhatsApp</a>
                </div>
            `;
        });
    } catch (err) {
        resultsDiv.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 40px; color:red;">Error fetching results.</div>';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showCategories() {
    document.getElementById('categoriesSection').style.display = 'block';
    document.getElementById('resultsSection').style.display = 'none';
}
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
