<?php
// admin/admin_login.php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';
require_once '../includes/ratelimit.php';
redirectIfLoggedIn();

$error = "";
$rateLimiter = getRateLimiter();
$adminIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please refresh and try again.";
    } elseif ($lockout = $rateLimiter->isLockedOut($adminIp)) {
        $error = "Too many failed attempts. Please try again in " . $lockout . " minutes.";
    } else {
        $email = clean_input($_POST['email']);
        $password = $_POST['password'];

        if (!is_valid_email($email)) {
            $error = "Please enter a valid email address.";
        } else {
            $stmt = $conn->prepare("SELECT id, password FROM admin WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($user = $result->fetch_assoc()) {
                    if (password_verify($password, $user['password'])) {
                        $rateLimiter->clearAttempts($adminIp);
                        session_regenerate_id(true);
                        $_SESSION['admin_id'] = $user['id'];
                        $_SESSION['admin_email'] = $email;
                        $_SESSION['admin_logged_in'] = true;
                        header("Location: admin_dashboard.php");
                        exit();
                    } else {
                        $lockoutMins = $rateLimiter->recordFailedAttempt($adminIp);
                        if ($lockoutMins > 0) {
                            $error = "Too many failed attempts. Please try again in " . $lockoutMins . " minutes.";
                        } else {
                            $remaining = $rateLimiter->getRemainingAttempts($adminIp);
                            $error = "Invalid password. " . $remaining . " attempts remaining.";
                        }
                    }
                } else {
                    $rateLimiter->recordFailedAttempt($adminIp);
                    $error = "Admin account not found.";
                }
            } else {
                $error = "Database error.";
            }
        }
    }
}

$isLockedOut = $rateLimiter->isLockedOut($adminIp);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | ALLO_SERVICE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.2">
    <style>
        :root { --primary: #007A5E; }
        body { 
            background: linear-gradient(135deg, #007A5E 0%, #00B894 100%); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .auth-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .auth-box h2 { color: var(--primary); margin-bottom: 30px; font-weight: 800; }
        .input-group { margin-bottom: 20px; text-align: left; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #666; font-size: 14px; }
        input { width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px; font-size: 1rem; outline: none; transition: 0.3s; box-sizing: border-box; }
        input:focus { border-color: var(--primary); }
        .btn-submit { width: 100%; padding: 14px; background: var(--primary); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #054334; transform: translateY(-2px); }
        .btn-submit:disabled { background: #ccc; cursor: not-allowed; transform: none; }
        .error-msg { color: #e74c3c; background: #fdeaea; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .back-link { display: block; margin-top: 20px; color: #666; text-decoration: none; font-size: 13px; opacity: 0.8; }
        .back-link:hover { opacity: 1; color: var(--primary); }
    </style>
</head>
<body>
<div class="auth-box">
    <h2>Admin Portal</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger" style="color: #CE1126; background: #fee2e2; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; border: 1px solid #fecaca;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <?= csrf_field() ?>
        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="admin@alloservice.com" <?= $isLockedOut ? 'readonly' : '' ?>>
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter password" <?= $isLockedOut ? 'readonly' : '' ?>>
        </div>
        <button type="submit" class="btn-submit" <?= $isLockedOut ? 'disabled' : '' ?>>Login to Dashboard</button>
    </form>
    <a href="../index.php" class="back-link">← Back to website</a>
</div>
</body>
</html>
