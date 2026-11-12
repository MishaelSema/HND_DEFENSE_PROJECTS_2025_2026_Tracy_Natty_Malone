<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_SESSION['client_id'] ?? null;
    $provider_id = intval($_POST['provider_id'] ?? 0);
    $reason = clean_input($_POST['reason'] ?? '');
    $description = clean_input($_POST['description'] ?? '');
    
    if (!$client_id) {
        echo json_encode(['success' => false, 'message' => 'Please login to report a professional.']);
        exit;
    }
    
    if ($provider_id <= 0 || empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }
    
    $check = $conn->prepare("SELECT p.provider_id FROM providers p JOIN clients c ON p.email = c.email WHERE p.provider_id = ? AND c.client_id = ?");
    $check->bind_param("ii", $provider_id, $client_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You cannot report yourself.']);
        exit;
    }
    $check->close();
    
    $stmt = $conn->prepare("INSERT INTO reports (client_id, provider_id, reason, description, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->bind_param("isss", $client_id, $provider_id, $reason, $description);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Report submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
exit();
?>
