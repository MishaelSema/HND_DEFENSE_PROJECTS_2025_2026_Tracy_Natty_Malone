<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth_check.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$provider_id = $_SESSION['provider_id'];

// Add service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $service_name = clean_input($_POST['service_name']);
    $description = clean_input($_POST['description'] ?? '');
    $price = floatval($_POST['price']);
    
    if (!empty($service_name) && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO provider_services (provider_id, service_name, description, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $provider_id, $service_name, $description, $price);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete service
if (isset($_GET['delete'])) {
    $service_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM provider_services WHERE id = ? AND provider_id = ?");
    $stmt->bind_param("ii", $service_id, $provider_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: provider_profile.php");
exit();
