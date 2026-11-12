<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
protectAdminPage();

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$provider_id = intval($_GET['id']);
$msg = '';

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_provider'])) {
    $nom = clean_input($_POST['nom']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['telephone']);
    $metier = clean_input($_POST['metier']);
    $location = clean_input($_POST['localisation']);
    $desc = clean_input($_POST['description']);
    $skills = clean_input($_POST['competence']);
    
    // Process profile/cover if uploaded
    $profile_sql = "";
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        $filename = 'assets/uploads/prov_p_' . $provider_id . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], '../' . $filename)) {
            $profile_sql = ", profile = '$filename'";
        }
    }
    
    $cover_sql = "";
    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['cover_photo']['name'], PATHINFO_EXTENSION));
        $filename = 'assets/uploads/prov_c_' . $provider_id . '_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], '../' . $filename)) {
            $cover_sql = ", cover_photo = '$filename'";
        }
    }

    $sql = "UPDATE providers SET name=?, email=?, phone=?, profession=?, location=?, description=?, skills=? $profile_sql $cover_sql WHERE provider_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $nom, $email, $phone, $metier, $location, $desc, $skills, $provider_id);
    
    if ($stmt->execute()) {
        $msg = "Provider updated successfully!";
    } else {
        $msg = "Error updating provider.";
    }
}

// Handle Post Deletion
if (isset($_GET['delete_post'])) {
    $post_id = intval($_GET['delete_post']);
    $res = $conn->query("SELECT image_path FROM provider_posts WHERE id = $post_id");
    if ($res && $img = $res->fetch_assoc()) {
        if (file_exists('../' . $img['image_path'])) unlink('../' . $img['image_path']);
        // Delete extra images
        $extra = $conn->query("SELECT image_path FROM post_images WHERE post_id = $post_id");
        while($e = $extra->fetch_assoc()) {
            if (file_exists('../' . $e['image_path'])) unlink('../' . $e['image_path']);
        }
        $conn->query("DELETE FROM provider_posts WHERE id = $post_id");
        $msg = "Portfolio post deleted.";
    }
}

// Fetch provider details
$stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id = ?");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();

if (!$provider) {
    die("Provider not found.");
}

// Fetch posts
$posts = [];
$res_posts = $conn->query("SELECT * FROM provider_posts WHERE provider_id = $provider_id ORDER BY created_at DESC");
while ($row = $res_posts->fetch_assoc()) $posts[] = $row;

// Fetch categories
$res_cats = $conn->query("SELECT name FROM services ORDER BY name ASC");
$categories = [];
while ($row = $res_cats->fetch_assoc()) $categories[] = $row['name'];

include 'includes/admin_header.php';
?>

<div class="admin-card" style="padding: 40px; background: white; border-radius: 12px; margin: 20px;">
    <h2 style="color: var(--primary); margin-bottom: 30px;"><i class="fas fa-user-edit"></i> Edit Professional: <?= htmlspecialchars($provider['name'] ?? $provider['nom'] ?? '') ?></h2>
    
    <p style="margin-bottom: 20px;">
        <a href="users.php" style="color: var(--primary); text-decoration: none; font-weight: bold;"><i class="fas fa-arrow-left"></i> Back to User Management</a>
    </p>

    <style>
        .tags-container { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; }
        .tag { background: #eef2ff; color: #4f46e5; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; border: 1px solid #c7d2fe; }
        .tag i { cursor: pointer; color: #94a3b8; transition: 0.2s; }
        .tag i:hover { color: #ef4444; }
        .tag-input-wrapper { display: flex; gap: 10px; }
    </style>

    <?php if ($msg): ?><div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 25px; font-weight: bold;"><?= $msg ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div class="left-col">
            <h4 style="margin-bottom: 15px; color: #334155; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">Basic Credentials</h4>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Full Name</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($provider['name'] ?? $provider['nom'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($provider['email'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Telephone</label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($provider['phone'] ?? $provider['telephone'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Job Title (Category)</label>
                <select name="metier" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: white;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat ?>" <?= $cat == ($provider['profession'] ?? $provider['metier'] ?? '') ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Primary Localisation</label>
                <input type="text" name="localisation" value="<?= htmlspecialchars($provider['location'] ?? $provider['localisation'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
            </div>
        </div>

        <div class="right-col">
            <h4 style="margin-bottom: 15px; color: #334155; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">Media & Profile Content</h4>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Profile Picture</label>
                <input type="file" name="profile_pic" style="margin-bottom: 10px;">
                <?php if ($provider['profile_photo'] ?? $provider['profile'] ?? ''): ?><img src="../<?= $provider['profile_photo'] ?? $provider['profile'] ?? '' ?>" style="height: 60px; border-radius: 50%;"><?php endif; ?>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Cover / Hero Photo</label>
                <input type="file" name="cover_photo" style="margin-bottom: 10px;">
                <?php if ($provider['cover_photo']): ?><img src="../<?= $provider['cover_photo'] ?>" style="height: 60px; border-radius: 8px;"><?php endif; ?>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Professional Biography</label>
                <textarea name="description" style="width: 100%; height: 100px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;"><?= htmlspecialchars($provider['description'] ?? '') ?></textarea>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 5px;">Key Skills & Competences</label>
                <div id="skills-tags" class="tags-container" style="margin-bottom: 10px;"></div>
                <div class="tag-input-wrapper">
                    <input type="text" id="skill-input" placeholder="Add a skill..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <button type="button" onclick="addSkillTag()" style="padding: 0 15px; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; white-space: nowrap;">Add</button>
                </div>
                <input type="hidden" name="competence" id="competence-hidden" value="<?= htmlspecialchars($provider['competence'] ?? '') ?>">
            </div>
        </div>

        <div style="grid-column: span 2;">
            <button type="submit" name="update_provider" style="width: 100%; padding: 15px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 16px;">Save Professional Updates</button>
        </div>
    </form>

    <div style="margin-top: 50px;">
        <h3 style="color: #334155; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Portfolio Management</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            <?php foreach ($posts as $pst): ?>
                <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; background: #f8fafc;">
                    <img src="../<?= $pst['image_path'] ?>" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">
                    <p style="font-size: 13px; color: #475569; font-weight: 600; margin-bottom: 10px;"><?= htmlspecialchars($pst['caption']) ?></p>
                    <a href="admin_edit_provider.php?id=<?= $provider_id ?>&delete_post=<?= $pst['id'] ?>" style="color: var(--danger); font-size: 12px; font-weight: bold; text-decoration: none;" onclick="return confirm('Delete this portfolio post?')"><i class="fas fa-trash"></i> Remove from Portfolio</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

    <script>
        // Skill Tags Logic (Admin version)
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

        window.removeSkill = removeSkill;

        function updateSkills() {
            skillsHidden.value = skills.join(', ');
            renderSkills();
        }

        function renderSkills() {
            skillsContainer.innerHTML = '';
            skills.forEach(skill => {
                const tag = document.createElement('div');
                tag.className = 'tag';
                const safeSkill = skill.replace(/'/g, "\\'");
                tag.innerHTML = `${skill} <i class="fas fa-times" onclick="removeSkill('${safeSkill}')"></i>`;
                skillsContainer.appendChild(tag);
            });
        }
    </script>
</body>
</html>
