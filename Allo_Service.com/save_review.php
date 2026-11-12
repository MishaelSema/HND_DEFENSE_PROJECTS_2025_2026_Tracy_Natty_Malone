<?php
// save_review.php
session_start();
header('Content-Type: application/json');
require_once 'includes/db.php';
require_once 'includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    
    if (!verify_csrf_token($token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request. Please refresh and try again.']);
        exit;
    }
    
    $client_id = $_SESSION['client_id'] ?? null;
    $provider_id = intval($_POST['provider_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $content = isset($_POST['content']) ? clean_input(trim($_POST['content'])) : '';

    if (!$client_id) {
        echo json_encode(['success' => false, 'message' => 'Please login to leave a review.']);
        exit;
    }

    if ($provider_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Provider ID is missing or invalid.']);
        exit;
    }
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5.']);
        exit;
    }
    
    if (strlen($content) > 1000) {
        echo json_encode(['success' => false, 'message' => 'Comment is too long (max 1000 characters).']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO reviews (provider_id, client_id, rating, content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $provider_id, $client_id, $rating, $content);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
exit();
?>
