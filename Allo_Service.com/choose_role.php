<?php 
session_start();
require_once 'includes/auth_check.php';
redirectIfLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Choose Role | ALLO_SERVICE</title>
  <link rel="stylesheet" href="assets/css/auth.css?v=1.1">
  <style>
    /* Specific overrides for choose_role if needed */
    .auth-box {
      max-width: 450px;
    }
    .role-options {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 30px;
    }
    /* Mobile Scale-down */
    @media (max-width: 480px) {
      .auth-box { padding: 30px 20px; }
      h2 { font-size: 1.5rem; }
      .subtitle { font-size: 0.9rem; margin-bottom: 20px; }
      .auth-btn { padding: 12px; font-size: 0.9rem; }
    }
  </style>
</head>
<body>
  <div class="auth-page">
    <div class="auth-overlay"></div>

    <div class="top-left-return">
      <a href="index.php">← Back to Home</a>
    </div>

    <div class="auth-container">
      <div class="auth-box">
        <h2>Welcome to <strong>ALLO_SERVICE</strong></h2>
        <p class="subtitle">Choose your role to continue:</p>

        <div class="role-options">
          <a href="client/client_login.php" class="auth-btn" style="text-decoration:none; display:block;">I am a Client</a>
          <a href="provider/provider_login.php" class="auth-btn" style="text-decoration:none; display:block; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); box-shadow: none;">I am a Professional</a>
        </div>
        
        <div class="auth-links">
          <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Connecting local talent with home and office needs.</p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
