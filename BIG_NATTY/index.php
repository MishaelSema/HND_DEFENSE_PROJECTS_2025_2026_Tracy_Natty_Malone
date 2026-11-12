<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IssueSpot</title>
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
                    <li><a href="public_reports.php">Public Reports</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php" class="btn">Register</a></li>
                    <?php endif; ?>
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
            <div class="card" style="text-align: center; padding: 3rem;">
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem; color: #333;">IssueSpot</h1>
                <p style="font-size: 1.2rem; color: #666; margin-bottom: 2rem;">
                    Report community issues easily and track their resolution
                </p>
                
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn">Get Started</a>
                        <a href="login.php" class="btn btn-secondary">Login</a>
                    <?php else: ?>
                        <a href="dashboard.php" class="btn">Go to Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <h3>Easy Reporting</h3>
                    <p>Submit issues in seconds</p>
                </div>
                <div class="stat-card">
                    <h3>Track Progress</h3>
                    <p>Know your issue status</p>
                </div>
                <div class="stat-card">
                    <h3>Get Updates</h3>
                    <p>Receive email notifications</p>
                </div>
            </div>

            <div class="card">
                <h2>How It Works</h2>
                <div class="issue-grid">
                    <div class="issue-card" style="text-align: center;">
                        <h3>1. Register</h3>
                        <p>Create an account to start reporting issues in your community.</p>
                    </div>
                    <div class="issue-card" style="text-align: center;">
                        <h3>2. Report</h3>
                        <p>Submit details about the issue including photos and location.</p>
                    </div>
                    <div class="issue-card" style="text-align: center;">
                        <h3>3. Track</h3>
                        <p>Monitor the status of your reports and receive updates.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 IssueSpot. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
