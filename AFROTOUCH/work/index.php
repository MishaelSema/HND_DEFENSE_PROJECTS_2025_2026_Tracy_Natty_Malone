<?php
// index.php - Afro Touch Homepage
// Later you can extend this with dynamic PHP (e.g., pulling artisan products from a database)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Afro Touch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Simple styling -->
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #2e1b0f url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }
        header {
            background-color: rgba(46, 27, 15, 0.9);
            padding: 20px;
            text-align: center;
        }
        header h1{
            text-align: left;
        }
        .hero {
            text-align: center;
            padding: 80px 20px;
        }
        .hero h2 {
            font-size: 1.8em;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .btn {
            background-color: #f4a300;
            color: #2e1b0f;
            padding: 15px 30px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background-color: #d98c00;
        }
        .features {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 60px 20px;
        }
        .feature {
            background-color: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
        }
        .feature h3 {
            color: #f4a300;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1><span style="color: white;">AFRO</span> <span style="color:  #f4a300;">TOUCH</span></h1>
    </header>

    <section class="hero">
        <h2>Discover Africa's Finest Artisans</h2>
        <p>Buy and sell authentic handcrafted cultural treasures from artisans across the continent.</p>
        <div class="buttons">
            <a href="sell.php" class="btn">Start Selling</a>
            <a href="explore.php" class="btn">Explore Crafts</a>
        </div>
    </section>

    <section class="features">
        <div class="feature">
            <h3>Verified Artisans</h3>
            <p>Every seller is vetted to ensure authenticity and quality.</p>
        </div>
        <div class="feature">
            <h3>Pan-African</h3>
            <p>Connecting artisans and buyers across Africa and beyond.</p>
        </div>
    </section>
</body>
</html>
   
    <section class="features">
        <div class="feature">
            <!-- You can replace with an icon image -->
            <h3>Global Shipping</h3>
            <p>Delivered worldwide with care.</p>
        </div>
        <div class="feature">
            <h3>Easy Returns</h3>
            <p>30-day return guarantee.</p>
        </div>
    </section>
   <?php
// index.php - Afro Touch Homepage

// ... your homepage content above ...

// Include the footer from the include folder
include 'include/footer.php';
?>




