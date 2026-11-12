<?php
// forgot_password.php
session_start();
require_once 'includes/db.php';
require_once 'includes/csrf.php';
require_once 'includes/mailer.php';

$message = "";
$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please refresh and try again.";
    } else {
        $email = clean_input($_POST['email']);
        $role = $_POST['role'];

        if (!is_valid_email($email)) {
            $error = "Please enter a valid email address.";
        } else {
            $table = ($role === 'provider') ? 'providers' : 'clients';
            $email_field = 'email';
            
            $stmt = $conn->prepare("SELECT * FROM $table WHERE $email_field = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $stmt_del = $conn->prepare("DELETE FROM password_reset_tokens WHERE email = ? AND user_type = ?");
                $stmt_del->bind_param("ss", $email, $role);
                $stmt_del->execute();
                
                $stmt_ins = $conn->prepare("INSERT INTO password_reset_tokens (email, token, user_type, expires_at) VALUES (?, ?, ?, ?)");
                $stmt_ins->bind_param("ssss", $email, $token, $role, $expires);
                $stmt_ins->execute();
                
                $reset_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
                
                $subject = "Password Reset - ALLO_SERVICE";
                $body = "Hello,\n\n";
                $body .= "You have requested a password reset for your ALLO_SERVICE account.\n\n";
                $body .= "Click the link below to reset your password:\n";
                $body .= $reset_link . "\n\n";
                $body .= "This link will expire in 1 hour.\n\n";
                $body .= "If you did not request this reset, please ignore this email.\n\n";
                $body .= "Regards,\nThe ALLO_SERVICE Team";
                
                if (send_mail($email, $subject, $body)) {
                    $success = true;
                } else {
                    $error = "Failed to send email. Please try again later or contact support.";
                }
            } else {
                $success = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password | ALLO_SERVICE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/auth.css?v=1.2">
</head>
<body>
  <div class="auth-page">
    <div class="auth-overlay"></div>

    <div class="top-left-return">
      <a href="choose_role.php">← Back</a>
    </div>

    <div class="auth-container">
      <?php if ($success): ?>
      <div class="auth-box">
        <h2>Check Your Email</h2>
        <p class="subtitle">We've sent password reset instructions to your email address.</p>
        <div style="background: rgba(40, 167, 69, 0.2); color: #28a745; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
          <p style="margin: 0;">If an account exists with this email, you will receive reset instructions shortly.</p>
        </div>
        <div class="auth-links">
          <a href="choose_role.php" class="highlight">Return to Login</a>
        </div>
      </div>
      <?php else: ?>
      <form class="auth-box" method="POST" action="forgot_password.php">
        <h2>Reset Password</h2>
        <p class="subtitle">Enter your email to receive reset instructions</p>

        <?php if ($error != ""): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?= csrf_field() ?>

        <div class="input-group">
          <label>I am a:</label>
          <select name="role" required>
            <option value="client">Client</option>
            <option value="provider">Service Provider</option>
          </select>
        </div>

        <div class="input-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="Enter your email" required autofocus>
        </div>

        <button type="submit" class="auth-btn">Send Reset Link</button>

        <div class="auth-links">
          <a href="choose_role.php" class="highlight">Remember your password? Login</a>
        </div>
      </form>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
