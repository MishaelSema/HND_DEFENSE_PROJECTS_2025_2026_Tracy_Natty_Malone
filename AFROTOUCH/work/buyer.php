<?php
// account_register.php - Afro Touch Account Registration
?><?php
      //start-session();
       include '../config/db.php';
       ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account - Afro Touch</title>
    <link rel="stylesheet" href="Buyerhomepage.php">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background-color:#2e1b0f;
            color:#fff;
        }
        header {
            background-color:#1a0f08;
            padding:15px 20px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        header h1 {
            margin:0;
            font-size:1.5em;
            color:#f4a300;
        }
        header .icons {
            display:flex;
            gap:15px;
        }
        header .icons span {
            font-size:1.2em;
            cursor:pointer;
        }
        .container {
            max-width:400px;
            margin:40px auto;
            background-color:rgba(255,255,255,0.05);
            padding:30px;
            border-radius:8px;
        }
        .container h2 {
            text-align:center;
            color:#f4a300;
            margin-bottom:20px;
        }
        label {
            display:block;
            margin-bottom:5px;
            font-weight:bold;
        }
        input, select {
            width:100%;
            padding:10px;
            margin-bottom:15px;
            border:none;
            border-radius:5px;
        }
        input[type="submit"] {
            background-color:#f4a300;
            color:#2e1b0f;
            font-weight:bold;
            cursor:pointer;
        }
        input[type="submit"]:hover {
            background-color:#d98c00;
        }
        .back-link {
            display:inline-block;
            margin-top:10px;
            color:#f4a300;
            text-decoration:none;
        }
        .back-link:hover {
            text-decoration:underline;
        }
        .switch-link {
            text-align:center;
            margin-top:20px;
        }
        .switch-link a {
            color:#fff;
            text-decoration:none;
            font-weight:bold;
        }
        .switch-link a:hover {
            text-decoration:underline;
        }
        footer {
            background-color:#1a0f08;
            padding:30px 20px;
            margin-top:60px;
        }
        footer h3 {
            color:#f4a300;
        }
        footer ul {
            list-style:none;
            padding:0;
        }
        footer ul li {
            margin:5px 0;
        }
        footer ul li a {
            color:#fff;
            text-decoration:none;
        }
        footer ul li a:hover {
            text-decoration:underline;
        }
        .footer-grid {
            display:flex;
            flex-wrap:wrap;
            justify-content:space-between;
            max-width:1200px;
            margin:auto;
        }
        .footer-col {
            flex:1;
            min-width:200px;
            margin:10px;
        }
        .copyright {
            text-align:center;
            margin-top:20px;
            font-size:0.9em;
            color:#ccc;
        }
    </style>
</head>
<body>
    <header>
        <h1>AFRO TOUCH</h1>
        <div class="icons">
            <span>🔔</span>
            <span>🛒</span>
            <span>☰</span>
        </div>
    </header>

    <div class="container">
        <h2>Create Your Account</h2>
        <form action="account_process.php" method="post">
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
                <!-- Add more countries as needed -->
            </select>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>

            <input type="submit" value="Create Account">
        </form>

        <!-- Already have account -->
        <div class="switch-link">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
        </div>

        <!-- Switch to Seller -->
        <div class="switch-link">
            <p>Want to sell instead? <a href="sell.php">Go to Seller Account</a></p>
        </div>

        <!-- Back to Home -->
        <a href="index.php" class="back-link">← Back to Home</a>
    </div>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Shop</h3>
                <ul>
                    <li><a href="categories.php">All Categories</a></li>
                    <li><a href="new_arrivals.php">New Arrivals</a></li>
                    <li><a href="best_sellers.php">Best Sellers</a></li>
                    <li><a href="verified_artisans.php">Verified Artisans</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Afro Touch</h3>
                <p>Africa’s premier marketplace for authentic cultural artisan crafts.</p>
            </div>
        </div>
        <div class="copyright">
            © 2026 Afro Touch. All rights reserved. Celebrating African culture worldwide.
        </div>
    </footer>
</body>
</html>
