<?php
// Simulated Payment Gateway
// This simulates mobile money payment (MTN/Momo, Orange Money)

function processPayment($client_id, $provider_id, $amount, $booking_id) {
    global $conn;
    
    // Get platform fee settings
    $settings = $conn->query("SELECT * FROM platform_settings LIMIT 1")->fetch_assoc();
    $client_fee = $settings['client_fee_percent'] ?? 5;
    $provider_fee = $settings['provider_fee_percent'] ?? 10;
    
    // Calculate fees
    $platform_fee = round($amount * ($client_fee / 100), 2);
    $total_amount = $amount + $platform_fee;
    $professional_earning = $amount - round($amount * ($provider_fee / 100), 2);
    
    // Generate transaction reference
    $transaction_ref = 'TXN' . time() . rand(1000, 9999);
    
    // Simulate payment processing (always succeeds for demo)
    $payment_success = true;
    $simulated_delay = 1; // seconds
    
    if ($payment_success) {
        // Insert payment record
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, client_id, provider_id, amount, platform_fee, professional_amount, transaction_ref, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'completed')");
        $stmt->bind_param("iiiddds", $booking_id, $client_id, $provider_id, $amount, $platform_fee, $professional_earning, $transaction_ref);
        $stmt->execute();
        $payment_id = $stmt->insert_id;
        $stmt->close();
        
        // Update booking status (escrow - paid but not released to professional yet)
        $conn->query("UPDATE bookings SET payment_status = 'paid', status = 'confirmed' WHERE booking_id = $booking_id");
        
        return [
            'success' => true,
            'transaction_ref' => $transaction_ref,
            'amount_paid' => $total_amount,
            'service_price' => $amount,
            'platform_fee' => $platform_fee,
            'message' => 'Payment successful! Money held in escrow until service is completed.'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Payment failed. Please try again.'
        ];
    }
}

function releasePayment($booking_id) {
    global $conn;
    
    // Get payment details
    $payment = $conn->query("SELECT * FROM payments WHERE booking_id = $booking_id AND status = 'completed'")->fetch_assoc();
    
    if (!$payment) {
        return ['success' => false, 'message' => 'No payment found'];
    }
    
    // In real system, this would transfer to professional's mobile money
    // For demo, we just mark as completed
    
    // Update booking status
    $conn->query("UPDATE bookings SET status = 'completed' WHERE booking_id = $booking_id");
    
    // Update payment to reflect release
    $conn->query("UPDATE payments SET status = 'completed' WHERE payment_id = " . $payment['payment_id']);
    
    return [
        'success' => true,
        'message' => 'Payment released to professional',
        'amount_released' => $payment['professional_amount']
    ];
}

function refundPayment($booking_id, $reason = '') {
    global $conn;
    
    $payment = $conn->query("SELECT * FROM payments WHERE booking_id = $booking_id AND status = 'completed'")->fetch_assoc();
    
    if (!$payment) {
        return ['success' => false, 'message' => 'No payment found'];
    }
    
    // Update payment status
    $conn->query("UPDATE payments SET status = 'refunded' WHERE payment_id = " . $payment['payment_id']);
    
    // Update booking
    $conn->query("UPDATE bookings SET payment_status = 'refunded', status = 'cancelled' WHERE booking_id = $booking_id");
    
    return [
        'success' => true,
        'message' => 'Refund processed successfully',
        'refunded_amount' => $payment['amount']
    ];
}

function getPlatformEarnings($start_date = null, $end_date = null) {
    global $conn;
    
    $sql = "SELECT SUM(platform_fee) as total_earnings FROM payments WHERE status = 'completed'";
    
    if ($start_date && $end_date) {
        $sql .= " AND created_at BETWEEN '$start_date' AND '$end_date'";
    }
    
    $result = $conn->query($sql)->fetch_assoc();
    return $result['total_earnings'] ?? 0;
}

function getProviderEarnings($provider_id, $start_date = null, $end_date = null) {
    global $conn;
    
    $sql = "SELECT SUM(professional_amount) as total_earnings FROM payments WHERE provider_id = $provider_id AND status = 'completed'";
    
    if ($start_date && $end_date) {
        $sql .= " AND created_at BETWEEN '$start_date' AND '$end_date'";
    }
    
    $result = $conn->query($sql)->fetch_assoc();
    return $result['total_earnings'] ?? 0;
}
