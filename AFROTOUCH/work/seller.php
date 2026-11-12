<?php
session_start();
include '/../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $country   = $_POST['country'];
    $password  = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Prevent duplicate email registration
    $check = $conn->prepare("SELECT id FROM sellers WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<p style='color:red;'>This email is already registered.</p>";
    } else {
        $sql = "INSERT INTO sellers (full_name, email, phone, country, password) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $full_name, $email, $phone, $country, $password);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Seller account created successfully!</p>";
            echo "<a href='identity.php'>Continue to Verification</a>";
        } else {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
    $check->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller Account - Afro Touch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {margin:0;font-family:Arial,sans-serif;background:#2e1b0f;color:#fff;}
        header {background:#1a0f08;padding:15px 20px;text-align:center;}
        header h1 {margin:0;color:#f4a300;}
        .container {max-width:400px;margin:40px auto;background:rgba(255,255,255,0.05);padding:30px;border-radius:8px;}
        .container h2 {text-align:center;color:#f4a300;margin-bottom:20px;}
        label {display:block;margin-bottom:5px;font-weight:bold;}
        input,select {width:100%;padding:10px;margin-bottom:15px;border:none;border-radius:5px;}
        input[type="submit"] {background:#f4a300;color:#2e1b0f;font-weight:bold;cursor:pointer;}
        input[type="submit"]:hover {background:#d98c00;}
    </style>

</head>
<body>
    <header><h1>AFRO TOUCH</h1></header>
    <div class="container">
        <h2>Create Your Seller Account</h2>
        <!-- IMPORTANT: action points to fingerprint.php -->
        <form action="fingerprint.php" method="get">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="your@email.com" required>

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="+237..." required>

            <label for="country">Country</label>
            <select id="country" name="country" required>
                <option value="">Select your country</option>
                <option value="Cameroon">Cameroon</option>
                <option value="Nigeria">Nigeria</option>
                <option value="Ghana">Ghana</option>
                <option value="Kenya">Kenya</option>
            </select>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>

            <input type="submit" value="Continue to Verification">
        </form>
    </div>
    
</body>
</html>
