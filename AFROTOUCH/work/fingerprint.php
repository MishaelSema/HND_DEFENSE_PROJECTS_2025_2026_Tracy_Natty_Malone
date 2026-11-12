<?php
// fingerprint.php - Afro Touch Fingerprint Verification
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fingerprint Verification - Afro Touch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {margin:0;font-family:Arial,sans-serif;background:#2e1b0f;color:#fff;}
        header {background:#1a0f08;padding:15px 20px;text-align:center;}
        header h1 {margin:0;color:#f4a300;}
        .container {max-width:400px;margin:40px auto;text-align:center;background:rgba(255,255,255,0.05);padding:30px;border-radius:8px;}
        .container h2 {color:#f4a300;margin-bottom:10px;}
        .fingerprint {font-size:4em;margin:20px 0;}
        .btn {display:inline-block;padding:12px 25px;margin:10px;border:none;border-radius:5px;font-weight:bold;cursor:pointer;text-decoration:none;}
        .btn-simulate {background:#f4a300;color:#2e1b0f;}
        .btn-simulate:hover {background:#d98c00;}
        .btn-continue {background:#e0c9a6;color:#2e1b0f;}
        .btn-continue:hover {background:#c9b08d;}
    </style>
</head>
<body>
    <header><h1>AFRO TOUCH</h1></header>
    <div class="container">
        <h2>Fingerprint Scan</h2>
        <p>Place your finger on the sensor to verify your identity</p>
        <div class="fingerprint">🔒</div>

        <!-- Buttons -->
        <a href="#" class="btn btn-simulate" onclick="simulateScan()">Simulate Scan</a>
        <!-- IMPORTANT: Continue goes to seller.php -->
        <a href="seller_map.php" class="btn btn-continue">Continue →</a>

    
    </div>

    <script>
        function simulateScan() {
            alert("Fingerprint scan simulated successfully!");
        }
    </script>
</body>
</html>
