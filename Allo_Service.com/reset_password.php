<?php
// reset_password.php
session_start();
require_once 'includes/db.php';
require_once 'includes/csrf.php';

$error = "";
$success = false;
$token_valid = false;

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = "Invalid reset link. Please request a new password reset.";
} else {
    $stmt = $conn->prepare("SELECT email, user_type FROM password_reset_tokens WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $token_data = $result->fetch_assoc();
        $token_valid = true;
        $user_email = $token_data['email'];
        $user_type = $token_data['user_type'];
    } else {
        $error = "This reset link has expired or is invalid. Please request a new password reset.";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valid) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please refresh and try again.";
    } else {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $error = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            if ($user_type === 'provider') {
                $stmt = $conn->prepare("UPDATE providers SET password = ? WHERE email = ?");
            } else {
                $stmt = $conn->prepare("UPDATE clients SET password = ? WHERE email = ?");
            }
            $stmt->bind_param("ss", $hash, $user_email);
            $stmt->execute();
            
            $stmt_del = $conn->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
            $stmt_del->bind_param("s", $token);
            $stmt_del->execute();
            
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password | ALLO_SERVICE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/auth.css?v=1.2">
</head>
<body>
  <div class="auth-page">
    <div class="auth-overlay"></div>

    <div class="auth-container">
      <?php if ($success): ?>
      <div class="auth-box">
        <h2>Password Reset Complete</h2>
        <p class="subtitle">Your password has been successfully reset.</p>
        <div style="background: rgba(40, 167, 69, 0.2); color: #28a745; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
          <p style="margin: 0;">You can now login with your new password.</p>
        </div>
        <a href="choose_role.php" class="auth-btn" style="display: block; text-decoration: none;">Go to Login</a>
      </div>
      <?php elseif (!$token_valid): ?>
      <div class="auth-box">
        <h2>Invalid Reset Link</h2>
        <p class="subtitle"><?= htmlspecialchars($error) ?></p>
        <a href="forgot_password.php" class="auth-btn" style="display: block; text-decoration: none; margin-top: 20px;">Request New Reset</a>
        <div class="auth-links" style="margin-top: 20px;">
          <a href="choose_role.php" class="highlight">Return to Login</a>
        </div>
      </div>
      <?php else: ?>
      <form class="auth-box" method="POST" action="reset_password.php?token=<?= htmlspecialchars($token) ?>">
        <h2>Set New Password</h2>
        <p class="subtitle">Enter your new password below</p>

        <?php if ($error != ""): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?= csrf_field() ?>

        <div class="input-group">
          <label>New Password</label>
          <div class="password-container">
            <input type="password" name="password" id="password" placeholder="Min 8 characters" required minlength="8">
          </div>
          <small style="color: #64748b; font-size: 12px;">At least 8 characters with uppercase, lowercase, and number</small>
        </div>

        <div class="input-group">
          <label>Confirm Password</label>
          <div class="password-container">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
          </div>
        </div>

        <button type="submit" class="auth-btn">Reset Password</button>

        <div class="auth-links">
          <a href="choose_role.php" class="highlight">Cancel and return to login</a>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
