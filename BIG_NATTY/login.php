<?php 
include 'config.php';

$message = '';
$messageType = '';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/index.php');
    } else {
        redirect('dashboard.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = 'All fields are required';
        $messageType = 'error';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                redirect('admin/index.php');
            } else {
                redirect('dashboard.php');
            }
        } else {
            $message = 'Invalid email or password';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IssueSpot</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo"><a href="index.php" style="color: white; text-decoration: none;">IssueSpot</a></div>
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span><span></span><span></span>
            </div>
            <nav id="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <script>
    function toggleMenu() {
        document.getElementById('main-nav').classList.toggle('active');
    }
    </script>

    <main>
        <div class="container">
            <div class="auth-container">
                <div class="card">
                    <h2>Login</h2>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn" style="width: 100%;">Login</button>
                    </form>

                    <p style="margin-top: 1rem; text-align: center;">
                        Don't have an account? <a href="register.php">Register here</a>
                    </p>
                    
                    <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 5px; font-size: 0.9rem;">
                        <strong>Demo Accounts:</strong><br>
                        Admin: admin@admin.com / admin123<br>
                        User: Register a new account
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 IssueSpot System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
