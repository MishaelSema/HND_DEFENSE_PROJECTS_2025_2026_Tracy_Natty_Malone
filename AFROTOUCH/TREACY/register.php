<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'buyer';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed, $role]);
            
            $success = 'Account created successfully!';
            
            header('Location: login.php?registered=1');
            exit;
        }
    }
}

include 'components/store-layout_start.php';
?>

<section class="auth-section">
    <div class="auth-container">
        <div class="auth-header">
            <h1>Create Account</h1>
            <p>Join AfroTouch and start your journey</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form class="auth-form" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Your full name" required>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="your@email.com" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="At least 6 characters" required>
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Repeat your password" required>
            </div>
            
            <div class="form-group">
                <label>I want to:</label>
                <div class="role-options">
                    <label class="role-option">
                        <input type="radio" name="role" value="buyer" checked>
                        <span class="role-content">
                            <i data-lucide="shopping-bag"></i>
                            <div>
                                <strong>Shop</strong>
                                <small>Buy authentic African crafts</small>
                            </div>
                        </span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="seller">
                        <span class="role-content">
                            <i data-lucide="store"></i>
                            <div>
                                <strong>Sell</strong>
                                <small>Start selling my crafts</small>
                            </div>
                        </span>
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Create Account</button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Sign in</a></p>
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
    max-width: 500px;
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

.auth-form input[type="text"],
.auth-form input[type="email"],
.auth-form input[type="password"] {
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

.role-options {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.role-option {
    cursor: pointer;
}

.role-option input {
    display: none;
}

.role-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    transition: all 0.2s;
}

.role-option input:checked + .role-content {
    border-color: var(--accent-orange);
    background: rgba(211, 84, 0, 0.05);
}

.role-content i {
    color: var(--accent-orange);
}

.role-content strong {
    display: block;
    font-size: 0.95rem;
}

.role-content small {
    font-size: 0.8rem;
    color: var(--text-muted);
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
    margin-top: 0.5rem;
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

@media (max-width: 576px) {
    .auth-container {
        padding: 2rem 1.5rem;
    }
}
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

<?php include 'components/store-layout_end.php'; ?>
