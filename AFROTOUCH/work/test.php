<?php
// Database connection
$host = "localhost";
$user = "root";       // default WAMP/XAMPP user
$pass = "";           // default password (empty unless you set one)
$db   = "AFROTOUCH";  // your database name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Insert test data
$sql = "INSERT INTO admin (username, email, password, role) 
        VALUES ('testadmin', 'test@example.com', 'testpassword', 'admin')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Test admin inserted successfully!";
} else {
    echo "❌ Error inserting record: " . $conn->error;
}

$conn->close();
?>
