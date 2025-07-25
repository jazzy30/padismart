<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true); // Passing true enables exceptions

try {
    // Server settings
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';
    $mail->Username = 'your@gmail.com';
    $mail->Password = 'your_app_password'; // Use App Password if 2FA is enabled
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Alternative ports you might need:
    // - TLS: 587
    // - SSL: 465
    // - Office365: 587
    // - Gmail: 587 (for TLS) or 465 (for SSL)

    // Recipients
    $mail->setFrom('noreply@yourdomain.com', 'Your Application Name');
    $mail->addAddress($email, $name);     // Add a recipient

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Your Subject Here';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the plain text version for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
