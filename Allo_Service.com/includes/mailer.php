<?php
// includes/mailer.php

/**
 * Sends an email using PHPMailer or fallback to native mail()
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email content (plain text)
 * @return bool Success status
 */
function send_mail($to, $subject, $body) {
    // Check if PHPMailer is available
    $phpmailer_path = __DIR__ . '/PHPMailer/src/PHPMailer.php';
    
    if (file_exists($phpmailer_path)) {
        // PHPMailer Integration (to be configured by user)
        /*
        require __DIR__ . '/PHPMailer/src/Exception.php';
        require __DIR__ . '/PHPMailer/src/PHPMailer.php';
        require __DIR__ . '/PHPMailer/src/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'user@example.com';
            $mail->Password = 'secret';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('no-reply@alloservice.com', 'Allo_Service');
            $mail->addAddress($to);
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
        */
        // If PHPMailer is there but not configured, we might still want fallback
    }

    // Fallback to native PHP mail()
    $headers = "From: Allo_Service <no-reply@alloservice.com>\r\n";
    $headers .= "Reply-To: no-reply@alloservice.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    return @mail($to, $subject, $body, $headers);
}
?>
