<?php
// provider/provider_signup.php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';

$error = "";

redirectIfLoggedIn();

$services_list = [];
$res = $conn->query("SELECT name FROM services ORDER BY name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $services_list[] = $row['name'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please refresh and try again.";
    } else {
        $name = clean_input($_POST["name"] ?? "");
        $email = clean_input($_POST["email"] ?? "");
        $phone = clean_input($_POST["phone"] ?? "");
        $job = clean_input($_POST["job"] ?? "");
        $description = clean_input($_POST["description"] ?? "");
        $location = clean_input($_POST["location"] ?? "");
        $availability = clean_input($_POST["availability"] ?? "");
        $password = $_POST["password"] ?? "";

        if (empty($name) || empty($email) || empty($phone) || empty($job) || empty($location) || empty($availability) || empty($password)) {
            $error = "Please fill in all required fields.";
        } elseif (!is_valid_email($email)) {
            $error = "Invalid email address.";
        } elseif (!is_valid_phone($phone)) {
            $error = "Please enter a valid Cameroon phone number (e.g., 6XXXXXXXX).";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long.";
        } else {
            $stmt = $conn->prepare("SELECT provider_id FROM providers WHERE email = ? UNION SELECT client_id FROM clients WHERE email = ?");
            $stmt->bind_param("ss", $email, $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $error = "This email is already in use by either a professional or a client.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $profile_pic = "";
                
                if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $max_size = 5 * 1024 * 1024;
                    
                    if (!in_array($_FILES['profile']['type'], $allowed_types)) {
                        $error = "Only JPEG, PNG, GIF, and WEBP images are allowed.";
                    } elseif ($_FILES['profile']['size'] > $max_size) {
                        $error = "Profile photo must be less than 5MB.";
                    } else {
                        $target_dir = "uploads/";
                        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
                        $ext = strtolower(pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION));
                        $filename = "photo_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
                        $target_file = $target_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['profile']['tmp_name'], $target_file)) {
                            $profile_pic = $target_file;
                        } else {
                            $error = "Failed to upload profile photo.";
                        }
                    }
                }

                if (empty($error)) {
                    $id_front_path = "";
                    $id_back_path = "";
                    $selfie_path = "";
                    
                    // Handle selfie upload
                    if (isset($_FILES['selfie']) && $_FILES['selfie']['error'] == 0) {
                        $ext = strtolower(pathinfo($_FILES['selfie']['name'], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $filename = 'assets/uploads/ids/selfie_' . time() . '.' . $ext;
                            if (move_uploaded_file($_FILES['selfie']['tmp_name'], '../' . $filename)) {
                                $selfie_path = $filename;
                            }
                        }
                    }
                    
                    // Handle ID card front upload
                    if (isset($_FILES['id_card_front']) && $_FILES['id_card_front']['error'] == 0) {
                        $ext = strtolower(pathinfo($_FILES['id_card_front']['name'], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $filename = 'assets/uploads/ids/id_front_' . time() . '.' . $ext;
                            if (move_uploaded_file($_FILES['id_card_front']['tmp_name'], '../' . $filename)) {
                                $id_front_path = $filename;
                            }
                        }
                    }
                    
                    // Handle ID card back upload
                    if (isset($_FILES['id_card_back']) && $_FILES['id_card_back']['error'] == 0) {
                        $ext = strtolower(pathinfo($_FILES['id_card_back']['name'], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $filename = 'assets/uploads/ids/id_back_' . time() . '.' . $ext;
                            if (move_uploaded_file($_FILES['id_card_back']['tmp_name'], '../' . $filename)) {
                                $id_back_path = $filename;
                            }
                        }
                    }
                    
                    if (empty($id_front_path) || empty($id_back_path) || empty($selfie_path)) {
                        $error = "Please upload your selfie and both sides of your ID card.";
                    } else {
                        $stmt = $conn->prepare("INSERT INTO providers (name, email, password, phone, selfie, profession, profile_photo, description, skills, location, availability, id_card_front, id_card_back, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
                        $skills = "";
                        $stmt->bind_param("sssssssssssss", $name, $email, $hash, $phone, $selfie_path, $job, $profile_pic, $description, $skills, $location, $availability, $id_front_path, $id_back_path);
                        
                        if ($stmt->execute()) {
                            header("Location: provider_login.php?signup=success");
                            exit();
                        } else {
                            $error = "An error occurred during registration.";
                        }
                    }
                }
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Provider Signup | ALLO_SERVICE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/auth.css?v=1.2">
  <style>
    .auth-box {
      max-width: 500px;
      padding: 40px 30px;
    }
    .auth-container {
      max-width: 550px;
    }
    .input-group textarea {
      width: 100%;
      padding: 12px;
      border-radius: 12px;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.2);
      color: white;
      resize: vertical;
      font-family: inherit;
    }
    .input-group select {
      cursor: pointer;
    }
    .input-group select option {
      background: #1e293b;
      color: white;
    }
    .file-info {
      font-size: 12px;
      color: rgba(255,255,255,0.6);
      margin-top: 5px;
    }
  </style>
</head>
<body>
  <div class="auth-page">
    <div class="auth-overlay"></div>

    <div class="top-left-return">
      <a href="../choose_role.php">← Back</a>
    </div>

    <div class="auth-container">
      <form class="auth-box" method="POST" action="provider_signup.php" enctype="multipart/form-data">
        <h2>Professional Registration</h2>
        <p class="subtitle">Join our network of verified professionals</p>

        <?php if ($error): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?= csrf_field() ?>

        <div class="input-group">
          <label for="name">Full Name *</label>
          <input type="text" name="name" id="name" placeholder="John Doe" required>
        </div>

        <div class="input-group">
          <label for="email">Email Address *</label>
          <input type="email" name="email" id="email" placeholder="pro@example.com" required>
        </div>

        <div class="input-group">
          <label for="phone">Phone Number *</label>
          <input type="text" name="phone" id="phone" placeholder="6XXXXXXXX" required>
        </div>

        <div class="input-group">
          <label for="job">Occupation / Profession *</label>
          <select name="job" id="job" required>
            <option value="">-- Select Your Profession --</option>
            <?php foreach($services_list as $service): ?>
                <option value="<?= htmlspecialchars($service) ?>"><?= htmlspecialchars($service) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group">
          <label for="profile">Profile Photo</label>
          <input type="file" name="profile" id="profile" accept="image/*" style="padding: 10px; background: rgba(255,255,255,0.05);">
          <span class="file-info">Max 5MB. Accepted: JPEG, PNG, GIF, WEBP</span>
        </div>

        <div class="input-group">
          <label for="selfie">Selfie Photo - Required for verification</label>
          <input type="file" name="selfie" id="selfie" accept="image/*" capture="user" required style="padding: 10px; background: rgba(255,255,255,0.05);">
          <span class="file-info">Take a clear selfie for identity verification</span>
        </div>

        <div class="input-group" style="border: 1px solid rgba(255,255,255,0.2); padding: 15px; border-radius: 12px; margin: 15px 0;">
          <label style="color: #fbbf24; font-weight: 700; margin-bottom: 10px; display: block;">
            <i class="fas fa-id-card"></i> Identity Verification (KYC) - Required
          </label>
          <p style="font-size: 12px; color: rgba(255,255,255,0.6); margin-bottom: 15px;">
            Upload your national ID card (CNI) or passport. This helps build trust with clients.
          </p>
          <label for="id_card_front" style="font-size: 13px; color: rgba(255,255,255,0.8);">ID Card Front *</label>
          <input type="file" name="id_card_front" id="id_card_front" accept="image/*" required style="padding: 10px; background: rgba(255,255,255,0.05); margin-bottom: 10px;">
          
          <label for="id_card_back" style="font-size: 13px; color: rgba(255,255,255,0.8);">ID Card Back *</label>
          <input type="file" name="id_card_back" id="id_card_back" accept="image/*" required style="padding: 10px; background: rgba(255,255,255,0.05);">
        </div>

        <div class="input-group">
          <label for="description">About Your Services</label>
          <textarea name="description" id="description" rows="3" placeholder="Briefly describe what you do..."></textarea>
        </div>

        <div class="input-group">
          <label for="location">Location *</label>
          <input type="text" name="location" id="location" placeholder="City or Region" required>
        </div>

        <div class="input-group">
          <label for="availability">Availability *</label>
          <select name="availability" id="availability" required>
            <option value="">-- Select Availability --</option>
            <option value="Full Time">Full Time</option>
            <option value="Part Time">Part Time</option>
            <option value="On Demand">On Demand</option>
          </select>
        </div>

        <div class="input-group">
          <label for="password">Password *</label>
          <input type="password" name="password" id="password" placeholder="Min 8 characters" required minlength="8">
        </div>

        <button type="submit" class="auth-btn">Register as Partner</button>

        <div class="auth-links">
          <span>Already have a partner account? <a href="provider_login.php" class="highlight">Login here</a></span>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
