<?php
// includes/db.php
// Centralized database connection using MySQLi

$host = "localhost";
$user = "root";
$pass = "";
$db   = "allo_service_database";

// Enable error reporting for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Database Connection Failed: " . $e->getMessage());
    die("Database Connection Failed. Please try again later.");
}

// Function to clean and sanitize input data
if (!function_exists('clean_input')) {
    function clean_input($data) {
        if (is_array($data)) {
            return array_map('clean_input', $data);
        }
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

// Function to sanitize for SQL (use prepared statements instead)
if (!function_exists('escape_sql')) {
    function escape_sql($conn, $data) {
        return $conn->real_escape_string($data);
    }
}

// Function to validate email
if (!function_exists('is_valid_email')) {
    function is_valid_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

// Function to validate phone (Cameroon format)
if (!function_exists('is_valid_phone')) {
    function is_valid_phone($phone) {
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        return preg_match('/^(\+?237)?[0-9]{9}$/', $phone) === 1;
    }
}
?>
