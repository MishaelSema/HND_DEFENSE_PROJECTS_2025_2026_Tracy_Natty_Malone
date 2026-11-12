<?php
session_start();
include 'db.php';

if (!isset($_SESSION['buyer_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyer_id  = $_SESSION['buyer_id'];
    $product_id = $_POST['product_id'];

    // Save purchase into orders table (create this table if not exists)
    $sql = "INSERT INTO orders (buyer_id, product_id, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $buyer_id, $product_id);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Purchase successful!</p>";
        echo "<a href='buyer_home.php'>Back to Products</a>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>
