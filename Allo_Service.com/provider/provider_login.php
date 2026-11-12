<?php
// provider/provider_login.php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';
require_once '../includes/ratelimit.php';
redirectIfLoggedIn();

$error = "";
$rateLimiter = getRateLimiter();
$providerIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please refresh and try again.";
    } elseif ($lockout = $rateLimiter->isLockedOut($providerIp)) {
        $error = "Too many failed attempts. Please try again in " . $lockout . " minutes.";
    } else {
        $email = clean_input($_POST['email']);
        $password = $_POST['password'];

        if (!is_valid_email($email)) {
            $error = "Please enter a valid email address.";
        } else {
            $stmt = $conn->prepare("SELECT provider_id, name, password, status FROM providers WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                if (password_verify($password, $user['password'])) {
                    if ($user['status'] === 'pending') {
                        $error = "Your account is pending approval. Please wait for admin verification.";
                    } elseif ($user['status'] === 'suspended') {
                        $suspension_msg = !empty($user['suspension_reason']) 
                            ? "Reason: " . htmlspecialchars($user['suspension_reason']) 
                            : "Please contact support.";
                        $error = "Your account has been suspended. " . $suspension_msg;
                    } else {
                        $rateLimiter->clearAttempts($providerIp);
                        session_regenerate_id(true);
                        $_SESSION['provider_id'] = $user['provider_id'];
                        $_SESSION['provider_name'] = $user['name'];
                        $_SESSION['provider_logged_in'] = true;
                        header("Location: provider_profile.php");
                        exit();
                    }
                } else {
                    $lockoutMins = $rateLimiter->recordFailedAttempt($providerIp);
                    $remaining = $rateLimiter->getRemainingAttempts($providerIp);
                    if ($lockoutMins > 0) {
                        $error = "Too many failed attempts. Please try again in " . $lockoutMins . " minutes.";
                    } else {
                        $error = "Invalid password. " . $remaining . " attempts remaining.";
                    }
                }
            } else {
                $rateLimiter->recordFailedAttempt($providerIp);
                $error = "No account found with this email.";
            }
            $stmt->close();
        }
    }
}

$isLockedOut = $rateLimiter->isLockedOut($providerIp);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Provider Login | ALLO_SERVICE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/auth.css?v=1.3">
</head>
<body>
  <div class="auth-page">
    <div class="auth-overlay"></div>

    <div class="top-left-return">
      <a href="../choose_role.php">← Back</a>
    </div>

    <div class="auth-container">
      <form class="auth-box" method="POST" action="provider_login.php">
        <h2>Professional Login</h2>
        <p class="subtitle">Access your service provider account</p>

        <?php if ($error != ""): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?= csrf_field() ?>

        <div class="input-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="pro@alloservice.com" required autofocus <?= $isLockedOut ? 'readonly' : '' ?>>
        </div>

        <div class="input-group">
          <label>Password</label>
          <div class="password-container">
            <input type="password" name="password" id="password" placeholder="••••••••" required <?= $isLockedOut ? 'readonly' : '' ?>>
            <span class="toggle-password" onclick="togglePassword()">Show</span>
          </div>
        </div>

        <button type="submit" class="auth-btn" <?= $isLockedOut ? 'disabled' : '' ?>>Login</button>

        <div class="auth-links">
          <a href="../forgot_password.php">Forgot password?</a>
          <a href="provider_signup.php" class="highlight">Become a partner? Register here</a>
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
