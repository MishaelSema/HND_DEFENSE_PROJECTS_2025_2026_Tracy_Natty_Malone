<?php
$host = "localhost";      // WAMP default host
$user = "root";           // WAMP default user
$pass = "";               // WAMP default password (empty unless you set one)
$db   = "AFROTOUCH";      // your database name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Successfully connected to the AFROTOUCH database!";
}

$conn->close();
?>


