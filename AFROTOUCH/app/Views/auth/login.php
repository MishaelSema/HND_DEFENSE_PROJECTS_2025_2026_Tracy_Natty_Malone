<div class="auth-container">
    <div class="auth-header">
        <h2>Welcome Back</h2>
        <p>Login to your AfroTouch account</p>
    </div>
    
    <?php if (\App\Services\Session::has('error')): ?>
        <div class="alert alert-error"><?= \App\Services\Session::flash('error') ?></div>
    <?php endif; ?>
    
    <form action="/AFROTOUCH/public/login" method="POST" class="auth-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="your@email.com">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Your password">
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    
    <p class="auth-link">Don't have an account? <a href="/AFROTOUCH/public/register">Register</a></p>
</div>

<style>
.auth-container {
    max-width: 400px;
    margin: 0 auto;
    padding: 60px 20px;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h2 {
    font-family: 'Instrument Serif', serif;
    font-size: 32px;
    margin: 0 0 8px;
    color: #2D1F1A;
}

.auth-header p {
    color: #6D5D57;
    margin: 0;
}

.alert {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-error {
    background: #FFEBEE;
    color: #C62828;
}

.auth-form .form-group {
    margin-bottom: 20px;
}

.auth-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2D1F1A;
}

.auth-form input {
    width: 100%;
    padding: 14px 15px;
    border: 1px solid #E8E2DE;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.auth-form input:focus {
    outline: none;
    border-color: #D35400;
}

.btn {
    padding: 14px 24px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn-primary {
    background: #D35400;
    color: white;
    width: 100%;
}

.btn-primary:hover {
    background: #b94700;
    transform: translateY(-1px);
}

.btn-block {
    width: 100%;
}

.auth-link {
    text-align: center;
    margin-top: 25px;
    color: #6D5D57;
}

.auth-link a {
    color: #D35400;
    font-weight: 600;
    text-decoration: none;
}

.auth-link a:hover {
    text-decoration: underline;
}
</style>
