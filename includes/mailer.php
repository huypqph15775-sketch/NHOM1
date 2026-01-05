<?php
// Simple mailer helper. Prefer installing PHPMailer and configuring SMTP.
// Place PHPMailer in includes/PHPMailer or install via Composer and adjust autoload path.

function send_email_otp($to, $subject, $body_html, &$error = null){
    $error = null;
    // Try to use PHPMailer if available
    // Try Composer autoload in project root
    if (file_exists(__DIR__ . '/../vendor/autoload.php')){
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')){
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // Load configuration from includes/mailer_config.php if present
            if (file_exists(__DIR__ . '/mailer_config.php')) {
                require_once __DIR__ . '/mailer_config.php';
            }

            // Configure these settings to match your Gmail/SMTP account
            $smtpHost = $smtpHost ?? 'smtp.gmail.com';
            $smtpUser = $smtpUser ?? '';
            $smtpPass = $smtpPass ?? ''; // Use app password if 2FA enabled
            $smtpPort = $smtpPort ?? 587;
            $smtpSecure = $smtpSecure ?? 'tls';

            $mail->isSMTP();
            // SMTP debug disabled for production
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->SMTPSecure = $smtpSecure;
            $mail->Port = $smtpPort;

            if (!empty($smtpUser)) {
                $mail->setFrom($smtpUser, 'PhoneStore');
            } else {
                $mail->setFrom('no-reply@phonestore.local', 'PhoneStore');
            }
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body_html;

            $mail->send();
            return true;
        } catch (\Exception $e) {
            $error = 'Mailer error: ' . $e->getMessage();
            return false;
        }
    }

    // Fallback to PHP mail(). Note: for Gmail SMTP, configure php.ini SMTP settings or use PHPMailer.
    $headers = "MIME-Version: 1.0\r\n" .
               "Content-type: text/html; charset=UTF-8\r\n" .
               "From: PhoneStore <no-reply@phonestore.local>\r\n";

    $sent = mail($to, $subject, $body_html, $headers);
    if (!$sent) $error = 'Unable to send email using mail()';
    return $sent;
}

?>