<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/includes/mailer.php';
require __DIR__ . '/includes/mailer_config.php';

$err = null;
$to = (isset($smtpUser) && $smtpUser !== 'your-email@gmail.com') ? $smtpUser : 'test@example.com';
echo "Sending test OTP to: $to\n";
$ok = send_email_otp($to, 'PHPMailer SMTP debug test', '<p>Test message</p>', $err);
var_dump($ok);
echo "ERR: " . ($err ?: 'none') . "\n";

?>
