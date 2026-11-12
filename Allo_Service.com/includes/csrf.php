<?php
// includes/csrf.php
// CSRF Protection functions

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function validate_csrf() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }
    
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        die("Invalid CSRF token. Please refresh the page and try again.");
    }
    
    return true;
}
?>
