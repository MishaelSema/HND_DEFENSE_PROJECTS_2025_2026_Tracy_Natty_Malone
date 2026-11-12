<?php
// contact.php
session_start();
require_once 'includes/auth_check.php';
require_once 'includes/db.php';
require_once 'includes/csrf.php';
require_once 'includes/captcha.php';
require_once 'includes/ratelimit.php';
restrictAdminFromStorefront();
require_once 'includes/mailer.php';

$message_sent = false;
$error = "";
$visitorIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimiter = getRateLimiter();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please refresh and try again.";
    } elseif ($rateLimiter->isLockedOut($visitorIp)) {
        $error = "Too many submissions. Please try again later.";
    } else {
        $captchaAnswer = $_POST['captcha_answer'] ?? '';
        $captchaToken = $_POST['captcha_token'] ?? '';
        
        if (!verify_captcha($captchaAnswer, $captchaToken)) {
            $error = "Incorrect answer to the security check. Please try again.";
        } else {
            $name = isset($_POST['name']) ? clean_input($_POST['name']) : '';
            $email = isset($_POST['email']) ? clean_input($_POST['email']) : '';
            $subject = isset($_POST['subject']) ? clean_input($_POST['subject']) : '';
            $msg = isset($_POST['message']) ? clean_input($_POST['message']) : '';

            if (empty($name) || empty($email) || empty($subject) || empty($msg)) {
                $error = "Please fill in all fields.";
            } elseif (!is_valid_email($email)) {
                $error = "Please enter a valid email address.";
            } elseif (strlen($name) < 2 || strlen($name) > 100) {
                $error = "Name must be between 2 and 100 characters.";
            } elseif (strlen($msg) < 10 || strlen($msg) > 2000) {
                $error = "Message must be between 10 and 2000 characters.";
            } else {
                $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $subject, $msg);
                
                if ($stmt->execute()) {
                    $to = "alloservice@Cameroon.com";
                    $body = "New message from $name ($email):\n\nSubject: $subject\n\n$msg";
                    if (send_mail($to, "New Contact Message: $subject", $body)) {
                        $message_sent = true;
                        $rateLimiter->clearAttempts($visitorIp);
                    } else {
                        $error = "Message saved, but email notification failed. We will check it soon!";
                        $message_sent = true;
                    }
                } else {
                    $error = "Failed to save message. Please try again later.";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | ALLO_SERVICE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=1.2">
    <style>
        :root { --primary: #007A5E; --secondary: #00B894; --bg: #f8fafc; --dark: #1e293b; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--dark); margin:0; }
        
        .contact-hero { 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/img/photo.jpg') center/cover;
            color: white; 
            padding: 80px 20px; 
            text-align: center; 
        }
        
        .contact-container { 
            width: 95%; 
            max-width: 800px; 
            margin: -60px auto 100px; 
            background: white; 
            padding: 50px; 
            border-radius: 20px; 
            box-shadow: 0 15px 50px rgba(0,0,0,0.1); 
            position: relative;
            z-index: 2;
        }
        
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 10px; font-weight: 700; color: var(--dark); }
        input, textarea { 
            width: 100%; 
            padding: 14px; 
            border: 2px solid #e2e8f0; 
            border-radius: 12px; 
            font-family: inherit; 
            font-size: 1rem; 
            transition: 0.3s;
            box-sizing: border-box;
        }
        input:focus, textarea:focus { 
            border-color: var(--primary); 
            outline: none; 
            box-shadow: 0 0 0 4px rgba(0, 122, 94, 0.1);
        }
        
        .alert { padding: 20px; border-radius: 12px; margin-bottom: 30px; text-align: center; font-weight: 600; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        @media (max-width: 600px) {
            .contact-container { padding: 30px 20px; margin-top: -40px; }
            .contact-hero h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="contact-hero">
    <h1>Get in Touch</h1>
    <p>Have questions? We're here to help you connect with the right talent.</p>
</div>

<div class="contact-container">
    <?php if ($message_sent): ?>
        <div class="alert alert-success">
            <h2 style="margin-top:0;">Thank you!</h2>
            <p>Your message has been received. We'll get back to you within 24 hours.</p>
            <?php if ($error) echo "<p><small>" . htmlspecialchars($error) . "</small></p>"; ?>
        </div>
        <div style="text-align:center;">
            <a href="index.php" class="btn btn-primary">Return Home</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="John Doe" maxlength="100">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="john@example.com">
            </div>
            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" required placeholder="How can we help?" maxlength="200">
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="6" required placeholder="Write your message here..." maxlength="2000"></textarea>
            </div>
            <?= captcha_field() ?>
            <button type="submit" class="btn btn-primary" style="width:100%; padding: 16px; font-size: 1.1rem;">Send Message</button>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
