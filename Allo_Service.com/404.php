<?php
// 404.php - Page Not Found
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | ALLO_SERVICE</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #007A5E 0%, #00B894 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: white;
            padding: 60px 40px;
            border-radius: 24px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #007A5E;
            line-height: 1;
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
        }
        .error-message {
            color: #64748b;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-home {
            display: inline-block;
            background: #007A5E;
            color: white;
            padding: 14px 35px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-home:hover {
            background: #00B894;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        @media (max-width: 500px) {
            .error-code { font-size: 5rem; }
            .error-title { font-size: 1.4rem; }
            .error-container { padding: 40px 25px; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">
            Sorry, the page you're looking for doesn't exist or has been moved.
        </p>
        <a href="index.php" class="btn-home">Go to Homepage</a>
    </div>
</body>
</html>
