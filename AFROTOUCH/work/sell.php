<?php
// join.php - Afro Touch Join Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join Afro Touch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background-color:#2e1b0f; /* Dark brown background */
            color:#fff;
        }
        header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:15px 20px;
            background-color:#1a0f08;
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
        .hero {
            text-align:center;
            padding:60px 20px;
        }
        .hero h2 {
            font-size:1.8em;
            margin-bottom:10px;
            color:#f4a300;
        }
        .hero p {
            font-size:1.1em;
            margin-bottom:40px;
        }
        .options {
            display:flex;
            justify-content:center;
            gap:30px;
            flex-wrap:wrap;
        }
        .option {
            background-color:#f4a300;
            color:#2e1b0f;
            padding:30px;
            border-radius:8px;
            width:220px;
            text-align:center;
            transition:transform 0.3s;
        }
        .option:hover {
            transform:scale(1.05);
        }
        .option h3 {
            margin-bottom:10px;
        }
        .option p {
            margin-bottom:20px;
        }
        .option a {
            display:inline-block;
            background-color:#2e1b0f;
            color:#fff;
            padding:10px 20px;
            text-decoration:none;
            border-radius:5px;
            font-weight:bold;
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
        .home-link {
            display:inline-block;
            margin-top:30px;
            background-color:#f4a300;
            color:#2e1b0f;
            padding:10px 20px;
            text-decoration:none;
            border-radius:5px;
            font-weight:bold;
        }
        .home-link:hover {
            background-color:#d98c00;
        }
    </style>
</head>
<body>
    <header>
        <h1>AFRO TOUCH</h1>
        <div class="icons">
            <span>🛒</span>
            <span>☰</span>
        </div>
    </header>

    <section class="hero">
        <h2>Join Afro Touch</h2>
        <p>How would you like to use Afro Touch?</p>
        <div class="options">
            <div class="option">
                <h3>I'm a Buyer</h3>
                <p>Browse and purchase artisan crafts</p>
                <a href="buyer.php">Continue</a>
            </div>
            <div class="option">
                <h3>I'm a Seller</h3>
                <p>Sell your artisan crafts to the world</p>
                <a href="seller.php">Continue</a>
            </div>
        </div>
        <!-- Link back to homepage -->
        <a href="index.php" class="home-link">Back to Home</a>
    </section>

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



