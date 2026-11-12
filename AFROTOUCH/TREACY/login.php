<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'seller') {
                $seller = getSellerByUserId($pdo, $user['id']);
                if (!$seller) {
                    header('Location: seller/settings.php?setup=required');
                    exit;
                }
                $_SESSION['seller_id'] = $seller['id'];
                if (!$seller['is_verified'] && !in_array($seller['verification_status'], ['pending', 'processing'])) {
                    header('Location: verify.php');
                    exit;
                }
                header('Location: seller/overview.php');
                exit;
            } elseif ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
                exit;
            } elseif ($user['role'] === 'buyer') {
                header('Location: buyer/dashboard.php');
                exit;
            }
            
            header('Location: home.php');
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}

include 'components/store-layout_start.php';
?>

<section class="auth-section">
    <div class="auth-container">
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your AfroTouch account</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form class="auth-form" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="your@email.com" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>
                <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn-submit">Sign In</button>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Create one</a></p>
        </div>
        
        <div class="demo-accounts">
            <p><strong>Demo Accounts (password: password):</strong></p>
            <p>Admin: admin@afrotouch.com</p>
            <p>Seller: felix@afrotouch.com</p>
            <p>Buyer: maria@example.com</p>
        </div>
    </div>
</section>

<style>
.auth-section {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.auth-container {
    background: white;
    padding: 3rem;
    border-radius: 24px;
    border: 1px solid var(--border-color);
    width: 100%;
    max-width: 450px;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-header h1 {
    font-family: var(--font-heading);
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.auth-header p {
    color: var(--text-muted);
}

.alert {
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.alert-error {
    background: #FDECEC;
    color: #C62828;
}

.auth-form .form-group {
    margin-bottom: 1.25rem;
}

.auth-form label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.auth-form input {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    font-size: 0.95rem;
}

.auth-form input:focus {
    border-color: var(--accent-orange);
    outline: none;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    cursor: pointer;
}

.forgot-link {
    color: var(--accent-orange);
    font-size: 0.9rem;
    text-decoration: none;
}

.btn-submit {
    width: 100%;
    background: var(--accent-orange);
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s;
}

.btn-submit:hover {
    transform: scale(1.02);
}

.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.auth-footer p {
    color: var(--text-muted);
}

.auth-footer a {
    color: var(--accent-orange);
    text-decoration: none;
    font-weight: 600;
}

.demo-accounts {
    margin-top: 2rem;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 12px;
    font-size: 0.85rem;
}

.demo-accounts p {
    margin-bottom: 0.25rem;
    color: var(--text-muted);
}

@media (max-width: 576px) {
    .auth-container {
        padding: 2rem 1.5rem;
    }
}
</style>

<?php include 'components/store-layout_end.php'; ?>
