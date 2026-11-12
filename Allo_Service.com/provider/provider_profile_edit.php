<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$provider_id = $_SESSION['provider_id'];

// Auto-migrate locations table
$conn->query("CREATE TABLE IF NOT EXISTS provider_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES providers(provider_id) ON DELETE CASCADE
)");

// Fetch current details
$stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id = ?");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();

if (!$provider) {
    die("Provider not found.");
}

// Fetch extra locations
$current_locs = [];
$res_locs = $conn->query("SELECT address FROM provider_locations WHERE provider_id = $provider_id");
if ($res_locs) {
    while ($row = $res_locs->fetch_assoc()) {
        $current_locs[] = $row['address'];
    }
}

// Handle Profile Update
$msg = '';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $location = clean_input(stripslashes($_POST['location']));
    $desc = clean_input(stripslashes($_POST['description']));
    $skills = clean_input(stripslashes($_POST['competence']));
    $facebook = clean_input(stripslashes($_POST['facebook']));
    $instagram = clean_input(stripslashes($_POST['instagram']));
    $twitter = clean_input(stripslashes($_POST['twitter']));
    $tiktok = clean_input(stripslashes($_POST['tiktok']));
    $whatsapp = clean_input(stripslashes($_POST['whatsapp']));
    $website = clean_input(stripslashes($_POST['website']));
    $phone = clean_input(stripslashes($_POST['phone'] ?? ''));
    $extra_locations = $_POST['extra_locations'] ?? [];
    
    // Handle Profile & Cover Photo Uploads
    $profile_uploaded = false;
    $profile_path = $provider['profile_photo'] ?? '';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $error = "Profile photo must be JPG, PNG, GIF or WEBP";
        } else {
            $filename = 'assets/uploads/prov_p_' . $provider_id . '_' . time() . '.' . $ext;
            $target_path = '../' . $filename;
            
            if (!is_dir('../assets/uploads')) {
                mkdir('../assets/uploads', 0755, true);
            }
            
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
                $profile_path = $filename;
                $profile_uploaded = true;
            } else {
                $error = "Failed to upload profile photo. Error: " . $_FILES['profile_pic']['error'];
            }
        }
    }
    
    $cover_uploaded = false;
    $cover_path = $provider['cover_photo'] ?? '';
    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['cover_photo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $error = "Cover photo must be JPG, PNG, GIF or WEBP";
        } else {
            $filename = 'assets/uploads/prov_c_' . $provider_id . '_' . time() . '.' . $ext;
            $target_path = '../' . $filename;
            
            // Debug - check if directory exists
            if (!is_dir('../assets/uploads')) {
                mkdir('../assets/uploads', 0755, true);
            }
            
            if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], $target_path)) {
                $cover_path = $filename;
                $cover_uploaded = true;
            } else {
                $error = "Failed to upload cover photo. Error: " . $_FILES['cover_photo']['error'];
            }
        }
    }
    
    // Update query
    $sql = "UPDATE providers SET location=?, description=?, skills=?, facebook=?, instagram=?, twitter=?, tiktok=?, whatsapp=?, website=?, phone=?, profile_photo=?, cover_photo=? WHERE provider_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssi", $location, $desc, $skills, $facebook, $instagram, $twitter, $tiktok, $whatsapp, $website, $phone, $profile_path, $cover_path, $provider_id);
    
    if ($stmt->execute()) {
        // Update extra locations
        $conn->query("DELETE FROM provider_locations WHERE provider_id = $provider_id");
        if (!empty($extra_locations)) {
            $loc_stmt = $conn->prepare("INSERT INTO provider_locations (provider_id, address) VALUES (?, ?)");
            foreach ($extra_locations as $loc) {
                $loc = trim($loc);
                if ($loc !== "") {
                    $loc_stmt->bind_param("is", $provider_id, $loc);
                    $loc_stmt->execute();
                }
            }
            $loc_stmt->close();
        }
        $msg = "Profile updated successfully!";
        
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id = ?");
        $stmt->bind_param("i", $provider_id);
        $stmt->execute();
        $provider = $stmt->get_result()->fetch_assoc();
        
        // Refresh locations
        $current_locs = [];
        $res_locs = $conn->query("SELECT address FROM provider_locations WHERE provider_id = $provider_id");
        if ($res_locs) {
            while ($row = $res_locs->fetch_assoc()) {
                $current_locs[] = $row['address'];
            }
        }
    } else {
        $msg = "Error updating profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | ALLO_SERVICE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #007A5E; --light: #f8f9fa; --dark: #212529; --white: #ffffff; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; color: var(--dark); padding: 40px 20px; }
        .edit-container { max-width: 800px; margin: 0 auto; background: var(--white); border-radius: 16px; padding: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h1 { color: var(--primary); margin-bottom: 20px; font-size: 24px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; }
        input[type="text"], input[type="url"], textarea { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 14px; }
        textarea { height: 120px; resize: vertical; }
        .btn-save { background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-family: inherit; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 16px; }
        .btn-save:hover { background: #00604a; }
        .msg { padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
        .section-title { font-size: 18px; margin: 30px 0 15px; color: #334155; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .grid-1 { display: flex; flex-direction: column; gap: 20px; }
        .location-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .btn-remove-loc { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 10px; border-radius: 8px; cursor: pointer; }
        .btn-add-loc { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; margin-top: 5px; }
        .tags-container { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; }
        .tag { background: #eef2ff; color: #4f46e5; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; border: 1px solid #c7d2fe; }
        .tag i { cursor: pointer; color: #94a3b8; transition: 0.2s; }
        .tag i:hover { color: #ef4444; }
        .tag-input-wrapper { display: flex; gap: 10px; }
    </style>
</head>
<body>
    <div class="edit-container">
        <h1><i class="fas fa-user-edit"></i> Edit Your Professional Profile</h1>
        <p style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px;">
            <a href="provider_profile.php" style="color:var(--primary); text-decoration:none; font-weight:600;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            <span style="color: #cbd5e1;">|</span>
            <a href="../index.php" style="color:#64748b; text-decoration:none; font-weight:600;"><i class="fas fa-home"></i> Back to Main Site</a>
        </p>
        
        <?php if ($msg): ?>
            <div class="msg"><?= $msg ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div style="padding: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-weight: 600;"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data" style="margin-top: 25px;">
            <div class="section-title">Basic Information</div>
            
            <div class="form-group">
                <label>Profile Picture (Square recommended)</label>
                <?php if (!empty($provider['profile_photo'])): ?>
                    <img src="../<?= htmlspecialchars($provider['profile_photo']) ?>" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; display: block;">
                <?php endif; ?>
                <input type="file" name="profile_pic" accept="image/*" style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 8px;">
            </div>

            <div class="form-group">
                <label>Profile Cover / Hero Image (Wide banner)</label>
                <?php if (!empty($provider['cover_photo'])): ?>
                    <img src="../<?= htmlspecialchars($provider['cover_photo']) ?>" style="width: 100%; height: 120px; border-radius: 8px; object-fit: cover; margin-bottom: 10px; display: block;">
                <?php endif; ?>
                <input type="file" name="cover_photo" accept="image/*" style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 8px;">
            </div>

            <div class="form-group">
                <label>Primary Phone Number (Calls)</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($provider['phone'] ?? '') ?>" placeholder="e.g. 6XXXXXXXX" required>
            </div>

            <div class="form-group">
                <label>Primary Location (Pinned on Map)</label>
                <input type="text" name="location" value="<?= htmlspecialchars($provider['location'] ?? '') ?>" placeholder="e.g., Douala, Akwa" required>
            </div>

            <div class="form-group">
                <label>Additional Service Locations (Optional)</label>
                <div id="location-container">
                    <?php foreach ($current_locs as $addr): ?>
                        <div class="location-row">
                            <input type="text" name="extra_locations[]" value="<?= htmlspecialchars($addr) ?>" placeholder="Additional city or area">
                            <button type="button" class="btn-remove-loc" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn-add-loc" onclick="addLocation()"><i class="fas fa-plus"></i> Add Another Location</button>
                <p style="font-size: 12px; color: #94a3b8; margin-top: 8px;"><i class="fas fa-info-circle"></i> These locations will be pinned on the interactive map for clients.</p>
            </div>

            <div class="form-group">
                <label>About My Services (Description)</label>
                <textarea name="description" placeholder="Describe your experience and what you offer..."><?= htmlspecialchars($provider['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Key Skills & Competences</label>
                <div id="skills-tags" class="tags-container"></div>
                <div class="tag-input-wrapper">
                    <input type="text" id="skill-input" placeholder="Enter a skill (e.g. Pipe Repair) and press Add">
                    <button type="button" class="btn-add-loc" style="white-space:nowrap; margin-top:0;" onclick="addSkillTag()">Add Skill</button>
                </div>
                <input type="hidden" name="competence" id="competence-hidden" value="<?= htmlspecialchars($provider['skills'] ?? '') ?>">
                <p style="font-size: 12px; color: #94a3b8; margin-top: 8px;"><i class="fas fa-info-circle"></i> Add your skills one by one. They will appear as professional tiles on your public profile.</p>
            </div>

            <div class="section-title">Social & Contact Links</div>
            
            <div class="grid-1">
                <div class="form-group">
                    <label><i class="fab fa-whatsapp" style="color: #25D366;"></i> WhatsApp Number</label>
                    <input type="text" name="whatsapp" value="<?= htmlspecialchars($provider['whatsapp'] ?? '') ?>" placeholder="+237...">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-globe" style="color: #64748b;"></i> Personal Website</label>
                    <input type="url" name="website" value="<?= htmlspecialchars($provider['website'] ?? '') ?>" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-instagram" style="color: #E1306C;"></i> Instagram Username/URL</label>
                    <input type="text" name="instagram" value="<?= htmlspecialchars($provider['instagram'] ?? '') ?>" placeholder="@yourhandle">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-facebook" style="color: #1877F2;"></i> Facebook Profile URL</label>
                    <input type="url" name="facebook" value="<?= htmlspecialchars($provider['facebook'] ?? '') ?>" placeholder="https://facebook.com/...">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-tiktok" style="color: #000000;"></i> TikTok Username</label>
                    <input type="text" name="tiktok" value="<?= htmlspecialchars($provider['tiktok'] ?? '') ?>" placeholder="@yourhandle">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-twitter" style="color: #1DA1F2;"></i> Twitter (X) Username</label>
                    <input type="text" name="twitter" value="<?= htmlspecialchars($provider['twitter'] ?? '') ?>" placeholder="@yourhandle">
                </div>
            </div>

            <button type="submit" name="update_profile" class="btn-save" style="margin-top: 30px; width: 100%;">Save Changes</button>
        </form>
    </div>
    <script>
        function addLocation() {
            const container = document.getElementById('location-container');
            const div = document.createElement('div');
            div.className = 'location-row';
            div.innerHTML = '<input type="text" name="extra_locations[]" placeholder="Additional city or area"><button type="button" class="btn-remove-loc" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>';
            container.appendChild(div);
        }

        let skills = [];
        const skillsHidden = document.getElementById('competence-hidden');
        const skillsContainer = document.getElementById('skills-tags');
        const skillInput = document.getElementById('skill-input');

        if (skillsHidden.value) {
            skills = skillsHidden.value.split(',').map(s => s.trim()).filter(s => s !== "");
            renderSkills();
        }

        function addSkillTag() {
            const val = skillInput.value.trim();
            if (val && !skills.includes(val)) {
                skills.push(val);
                skillInput.value = '';
                updateSkills();
            }
        }

        skillInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addSkillTag();
            }
        });

        function removeSkill(skill) {
            skills = skills.filter(s => s !== skill);
            updateSkills();
        }

        function updateSkills() {
            skillsHidden.value = skills.join(', ');
            renderSkills();
        }

        function renderSkills() {
            skillsContainer.innerHTML = '';
            skills.forEach(skill => {
                const tag = document.createElement('div');
                tag.className = 'tag';
                tag.textContent = skill + ' ';
                
                const closeBtn = document.createElement('i');
                closeBtn.className = 'fas fa-times';
                closeBtn.onclick = function() { removeSkill(skill); };
                
                tag.appendChild(closeBtn);
                skillsContainer.appendChild(tag);
            });
        }
    </script>
</body>
</html>
