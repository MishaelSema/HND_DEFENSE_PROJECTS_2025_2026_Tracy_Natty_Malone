<?php
// client/client_login.php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';
require_once '../includes/ratelimit.php';
redirectIfLoggedIn();

$error = "";
$rateLimiter = getRateLimiter();
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please refresh and try again.";
    } elseif ($lockout = $rateLimiter->isLockedOut($clientIp)) {
        $error = "Too many failed attempts. Please try again in " . $lockout . " minutes.";
    } else {
        $email = clean_input($_POST['email']);
        $password = $_POST['password'];

        if (!is_valid_email($email)) {
            $error = "Please enter a valid email address.";
        } else {
            $stmt = $conn->prepare("SELECT client_id, name, password, status, suspension_reason FROM clients WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                if ($user['status'] === 'suspended') {
                    $suspension_msg = !empty($user['suspension_reason']) 
                        ? "Reason: " . htmlspecialchars($user['suspension_reason']) 
                        : "Please contact support.";
                    $error = "Your account has been suspended. " . $suspension_msg;
                } elseif (password_verify($password, $user['password'])) {
                    $rateLimiter->clearAttempts($clientIp);
                    session_regenerate_id(true);
                    $_SESSION['client_id'] = $user['client_id'];
                    $_SESSION['client_name'] = $user['name'];
                    $_SESSION['client_logged_in'] = true;
                    header("Location: client_dashboard.php");
                    exit();
                } else {
                    $lockoutMins = $rateLimiter->recordFailedAttempt($clientIp);
                    $remaining = $rateLimiter->getRemainingAttempts($clientIp);
                    if ($lockoutMins > 0) {
                        $error = "Too many failed attempts. Please try again in " . $lockoutMins . " minutes.";
                    } else {
                        $error = "Invalid password. " . $remaining . " attempts remaining.";
                    }
                }
            } else {
                $rateLimiter->recordFailedAttempt($clientIp);
                $error = "No account found with this email.";
            }
            $stmt->close();
        }
    }
}

$remainingAttempts = $rateLimiter->getRemainingAttempts($clientIp);
$isLockedOut = $rateLimiter->isLockedOut($clientIp);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Client Login | ALLO_SERVICE</title>
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
      <form class="auth-box" method="POST" action="client_login.php">
        <h2>Client Login</h2>
        <p class="subtitle">Access your personal dashboard</p>

        <?php if ($error != ""): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
          <div class="message" style="background: rgba(40, 167, 69, 0.2); color: #28a745; padding: 12px; border-radius: 10px; margin-bottom: 25px; border: 1px solid rgba(40, 167, 69, 0.3);">
            Account created successfully! Please login.
          </div>
        <?php endif; ?>

        <?= csrf_field() ?>

        <div class="input-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="email@example.com" required autofocus <?= $isLockedOut ? 'readonly' : '' ?>>
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
          <a href="client_signup.php" class="highlight">Don't have an account? Sign up</a>
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
