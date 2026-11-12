<?php
// includes/captcha.php
// Simple math CAPTCHA

function generate_captcha() {
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $sum = $num1 + $num2;
    $captcha_token = bin2hex(random_bytes(16));
    
    $_SESSION['captcha_question'] = "$num1 + $num2";
    $_SESSION['captcha_answer'] = $sum;
    $_SESSION['captcha_token'] = $captcha_token;
    
    return $captcha_token;
}

function verify_captcha($answer, $token) {
    if (!isset($_SESSION['captcha_answer']) || !isset($_SESSION['captcha_token'])) {
        return false;
    }
    
    if ($_SESSION['captcha_token'] !== $token) {
        return false;
    }
    
    $correct = (int)$_SESSION['captcha_answer'] === (int)$answer;
    
    unset($_SESSION['captcha_question']);
    unset($_SESSION['captcha_answer']);
    unset($_SESSION['captcha_token']);
    
    return $correct;
}

function captcha_field() {
    $token = generate_captcha();
    $question = $_SESSION['captcha_question'];
    
    return '<div class="captcha-container" style="background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 15px;">
        <label style="display: block; font-weight: 600; color: #64748b; margin-bottom: 10px;">Security Check</label>
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <span style="font-size: 1.2rem; font-weight: 700; color: #1e293b;">' . htmlspecialchars($question) . ' = ?</span>
            <input type="hidden" name="captcha_token" value="' . htmlspecialchars($token) . '">
            <input type="text" name="captcha_answer" required placeholder="Answer" style="padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px; width: 80px; text-align: center; font-size: 1rem;">
        </div>
    </div>';
}
?>
