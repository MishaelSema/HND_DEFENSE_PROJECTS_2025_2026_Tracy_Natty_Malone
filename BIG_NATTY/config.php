<?php
session_start();

$host = 'localhost';
$dbname = 'issue_reporting';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ============================================
// EMAIL CONFIGURATION (PHPMailer)
// ============================================
// Fill in your SMTP details below

$smtp_host = 'smtp.gmail.com';           // SMTP server (e.g., smtp.gmail.com, smtp.mailtrap.io)
$smtp_username = 'your-email@gmail.com'; // Your email address
$smtp_password = 'your-app-password';     // Your app password (not your login password)
$smtp_port = 587;                         // SMTP port (587 for TLS, 465 for SSL)
$smtp_from_email = 'your-email@gmail.com'; // Sender email
$smtp_from_name = 'IssueSpot'; // Sender name

// ============================================
// EMAIL FUNCTION (Simple - without PHPMailer for now)
// ============================================
function sendEmail($to, $subject, $body) {
    global $smtp_host, $smtp_username, $smtp_password, $smtp_port, $smtp_from_email, $smtp_from_name;
    
    // For now, just log the email (remove this in production)
    // To enable emails, configure SMTP in config.php and uncomment PHPMailer code below
    
    return true; // Email sent successfully (stub)
    
    /* 
    // To enable emails later, download PHPMailer via Composer and use:
    // composer require phpmailer/phpmailer
    
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
    require_once 'PHPMailer/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $smtp_port;
        $mail->setFrom($smtp_from_email, $smtp_from_name);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
    */
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Vote function
function vote($issue_id, $vote_type) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login to vote'];
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Check if user has already voted
    $stmt = $pdo->prepare("SELECT * FROM user_votes WHERE user_id = ? AND issue_id = ?");
    $stmt->execute([$user_id, $issue_id]);
    $existing_vote = $stmt->fetch();
    
    if ($existing_vote) {
        if ($existing_vote['vote_type'] === $vote_type) {
            // Remove vote (toggle off)
            $stmt = $pdo->prepare("DELETE FROM user_votes WHERE user_id = ? AND issue_id = ?");
            $stmt->execute([$user_id, $issue_id]);
            
            // Update count
            if ($vote_type === 'upvote') {
                $pdo->exec("UPDATE issues SET upvotes = GREATEST(upvotes - 1, 0) WHERE id = $issue_id");
            } else {
                $pdo->exec("UPDATE issues SET downvotes = GREATEST(downvotes - 1, 0) WHERE id = $issue_id");
            }
            return ['success' => true, 'message' => 'Vote removed'];
        } else {
            // Change vote
            $stmt = $pdo->prepare("UPDATE user_votes SET vote_type = ? WHERE user_id = ? AND issue_id = ?");
            $stmt->execute([$vote_type, $user_id, $issue_id]);
            
            if ($vote_type === 'upvote') {
                $pdo->exec("UPDATE issues SET upvotes = upvotes + 1, downvotes = GREATEST(downvotes - 1, 0) WHERE id = $issue_id");
            } else {
                $pdo->exec("UPDATE issues SET downvotes = downvotes + 1, upvotes = GREATEST(upvotes - 1, 0) WHERE id = $issue_id");
            }
            return ['success' => true, 'message' => 'Vote changed'];
        }
    } else {
        // New vote
        $stmt = $pdo->prepare("INSERT INTO user_votes (user_id, issue_id, vote_type) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $issue_id, $vote_type]);
        
        if ($vote_type === 'upvote') {
            $pdo->exec("UPDATE issues SET upvotes = upvotes + 1 WHERE id = $issue_id");
        } else {
            $pdo->exec("UPDATE issues SET downvotes = downvotes + 1 WHERE id = $issue_id");
        }
        return ['success' => true, 'message' => 'Voted'];
    }
}

// Get user's vote on an issue
function getUserVote($issue_id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT vote_type FROM user_votes WHERE user_id = ? AND issue_id = ?");
    $stmt->execute([$_SESSION['user_id'], $issue_id]);
    $result = $stmt->fetch();
    return $result ? $result['vote_type'] : null;
}

// Get images from issue (handles both single and multiple images)
function getIssueImages($image_path) {
    if (empty($image_path)) {
        return [];
    }
    
    // Try to decode as JSON (multiple images)
    $images = json_decode($image_path, true);
    
    if (json_last_error() === JSON_ERROR_NONE && is_array($images)) {
        return $images;
    }
    
    // Single image (legacy format)
    return [$image_path];
}

// Get all replies for an issue
function getIssueReplies($issue_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT r.*, u.username as admin_name 
        FROM issue_replies r 
        JOIN users u ON r.admin_id = u.id 
        WHERE r.issue_id = ? 
        ORDER BY r.created_at ASC
    ");
    $stmt->execute([$issue_id]);
    return $stmt->fetchAll();
}

// Add a new reply
function addReply($issue_id, $admin_id, $reply_text) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO issue_replies (issue_id, admin_id, reply_text) VALUES (?, ?, ?)");
    return $stmt->execute([$issue_id, $admin_id, $reply_text]);
}

// Update a reply
function updateReply($reply_id, $reply_text) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE issue_replies SET reply_text = ? WHERE id = ?");
    return $stmt->execute([$reply_text, $reply_id]);
}

// Delete a reply
function deleteReply($reply_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM issue_replies WHERE id = ?");
    return $stmt->execute([$reply_id]);
}
