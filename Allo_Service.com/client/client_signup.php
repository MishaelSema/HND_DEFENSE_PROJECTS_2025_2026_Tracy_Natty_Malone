<?php
// client/client_signup.php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';
redirectIfLoggedIn();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = "Invalid request. Please refresh and try again.";
    } else {
        $name = clean_input($_POST['name']);
        $email = clean_input($_POST['email']);
        $phone = clean_input($_POST['phone']);
        $password = $_POST['password'];

        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            $message = "All fields are required.";
        } elseif (!is_valid_email($email)) {
            $message = "Invalid email address.";
        } elseif (strlen($password) < 8) {
            $message = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $message = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
        } else {
            $stmt = $conn->prepare("SELECT client_id FROM clients WHERE email = ? UNION SELECT provider_id FROM providers WHERE email = ?");
            $stmt->bind_param("ss", $email, $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $message = "This email is already in use by either a client or a professional.";
            } else {
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                
                // Handle ID card uploads
                $id_front_path = '';
                $id_back_path = '';
                $selfie_path = '';
                
                if (isset($_FILES['selfie']) && $_FILES['selfie']['error'] == 0) {
                    $ext = strtolower(pathinfo($_FILES['selfie']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $selfie_path = 'assets/uploads/ids/client_selfie_' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['selfie']['tmp_name'], '../' . $selfie_path);
                    }
                }
                
                if (isset($_FILES['id_card_front']) && $_FILES['id_card_front']['error'] == 0) {
                    $ext = strtolower(pathinfo($_FILES['id_card_front']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $id_front_path = 'assets/uploads/ids/client_front_' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['id_card_front']['tmp_name'], '../' . $id_front_path);
                    }
                }
                
                if (isset($_FILES['id_card_back']) && $_FILES['id_card_back']['error'] == 0) {
                    $ext = strtolower(pathinfo($_FILES['id_card_back']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $id_back_path = 'assets/uploads/ids/client_back_' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['id_card_back']['tmp_name'], '../' . $id_back_path);
                    }
                }
                
                $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, password, selfie, id_card_front, id_card_back) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $name, $email, $phone, $password_hashed, $selfie_path, $id_front_path, $id_back_path);
                
                if ($stmt->execute()) {
                    header("Location: client_login.php?signup=success");
                    exit();
                } else {
                    $message = "An error occurred during registration.";
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
  <meta charset="UTF-8">
  <title>Client Signup | ALLO_SERVICE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/auth.css?v=1.2">
</head>
<body>
  <div class="auth-page">
    <div class="auth-overlay"></div>

    <div class="top-left-return">
      <a href="../choose_role.php">← Back</a>
    </div>

    <div class="auth-container">
      <form class="auth-box" method="POST" action="client_signup.php" enctype="multipart/form-data">
        <h2>Create Account</h2>
        <p class="subtitle">Join our community of satisfied clients</p>

        <?php if ($message != ""): ?>
          <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?= csrf_field() ?>

        <div class="input-group">
          <label>Full Name</label>
          <input type="text" name="name" placeholder="John Doe" required autofocus>
        </div>

        <div class="input-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="john@example.com" required>
        </div>

        <div class="input-group">
          <label>Phone Number</label>
          <input type="tel" name="phone" placeholder="237XXXXXXXXX" required>
        </div>

        <div class="input-group">
          <label>Selfie Photo - Required for verification</label>
          <input type="file" name="selfie" accept="image/*" capture="user" required>
        </div>

        <div class="input-group">
          <label>ID Card (Front) - Required for verification</label>
          <input type="file" name="id_card_front" accept="image/*" required>
        </div>

        <div class="input-group">
          <label>ID Card (Back) - Required for verification</label>
          <input type="file" name="id_card_back" accept="image/*" required>
        </div>

        <div class="input-group">
          <label>Password</label>
          <div class="password-container">
            <input type="password" name="password" id="password" placeholder="Min. 8 characters" required>
            <span class="toggle-password" onclick="togglePassword()">Show</span>
          </div>
          <small style="color: #64748b; font-size: 12px;">At least 8 characters with uppercase, lowercase, and number</small>
        </div>

        <button type="submit" class="auth-btn">Sign Up</button>

        <div class="auth-links">
          <span>Already have an account? <a href="client_login.php" class="highlight">Login here</a></span>
        </div>
      </form>
    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const toggle = document.querySelector('.toggle-password');
      if (input.type === "password") {
        input.type = "text";
        toggle.innerText = "Hide";
      } else {
        input.type = "password";
        toggle.innerText = "Show";
      }
    }
  </script>
</body>
</html>
