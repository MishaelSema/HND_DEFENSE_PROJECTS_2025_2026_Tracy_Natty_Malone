<?php
// Run this once to create admin user, then delete this file

$host = 'localhost';
$dbname = 'issue_reporting';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Hash password for admin123
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Update or insert admin user
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@admin.com'");
    $stmt->execute([$password]);
    
    if ($stmt->rowCount() > 0) {
        echo "Admin password updated successfully!<br>";
        echo "Email: admin@admin.com<br>";
        echo "Password: admin123";
    } else {
        // Insert if doesn't exist
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin', 'admin@admin.com', $password, 'admin']);
        echo "Admin user created successfully!<br>";
        echo "Email: admin@admin.com<br>";
        echo "Password: admin123";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
