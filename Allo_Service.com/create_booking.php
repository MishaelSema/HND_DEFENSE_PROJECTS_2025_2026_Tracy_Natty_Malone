<?php
// Force session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    require_once 'includes/db.php';
    require_once 'includes/auth_check.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $client_id = $_SESSION['client_id'] ?? null;
        $provider_id = intval($_POST['provider_id'] ?? 0);
        $service_type = clean_input($_POST['service_type'] ?? '');
        $booking_date = clean_input($_POST['booking_date'] ?? '');
        $booking_time = clean_input($_POST['booking_time'] ?? '');
        $address = clean_input($_POST['address'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $selected_service_id = intval($_POST['service_id'] ?? 0);
        
        if (!$client_id) {
            echo json_encode(['success' => false, 'message' => 'Please login to book a service.']);
            exit;
        }
        
        if ($provider_id <= 0 || empty($service_type) || empty($booking_date) || empty($address)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
            exit;
        }
        
        // Get platform fee settings
        $settings = $conn->query("SELECT * FROM platform_settings LIMIT 1")->fetch_assoc();
        if (!$settings) {
            $conn->query("INSERT INTO platform_settings (client_fee_percent, provider_fee_percent) VALUES (5.00, 10.00)");
            $settings = ['client_fee_percent' => 5.00, 'provider_fee_percent' => 10.00];
        }
        $client_fee = $settings['client_fee_percent'] ?? 5;
        $provider_fee = $settings['provider_fee_percent'] ?? 10;
        
        // Get price from provider's service list, or use default
        $price = 5000;
        if ($selected_service_id > 0) {
            $svc = $conn->query("SELECT price FROM provider_services WHERE id = $selected_service_id AND provider_id = $provider_id")->fetch_assoc();
            if ($svc) {
                $price = $svc['price'];
            }
        }
        
        $platform_fee = round($price * ($client_fee / 100), 2);
        $professional_earning = $price - round($price * ($provider_fee / 100), 2);
        
        // Create booking
        $stmt = $conn->prepare("INSERT INTO bookings (client_id, provider_id, service_type, description, booking_date, booking_time, address, price, platform_fee, professional_earning) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssssdd", $client_id, $provider_id, $service_type, $description, $booking_date, $booking_time, $address, $price, $platform_fee, $professional_earning);
        
        if ($stmt->execute()) {
            $booking_id = $stmt->insert_id;
            $stmt->close();
            
            // Create notification for provider
            $notif_msg = "New booking for " . $service_type . " - " . number_format($price, 0, ',', ' ') . " XAF";
            $conn->query("INSERT INTO provider_notifications (provider_id, type, message, booking_id) VALUES ($provider_id, 'booking', '$notif_msg', $booking_id)");
            
            echo json_encode([
                'success' => true, 
                'message' => 'Booking created successfully',
                'booking_id' => $booking_id,
                'payment_required' => true,
                'amount' => $price,
                'platform_fee' => $platform_fee,
                'total' => $price + $platform_fee
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating booking. Please try again.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    error_log("create_booking.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
exit();
?>
