<?php
require_once '../db_connection.php';
require_once '../vendor/autoload.php';

function sendTemporaryPasswordEmail($email, $username, $temporaryPassword) {
    $subject = "Your Password Has Been Updated";
    
    $message = "
    <html>
    <head>
        <title>Password Update Notification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #017a2b; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .password { font-size: 18px; font-weight: bold; padding: 10px; background-color: #e9e9e9; margin: 10px 0; }
            .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>PLP Faculty Profiling System</h2>
            </div>
            <div class='content'>
                <p>Hello,</p>
                <p>Your account password has been updated by an administrator. Here are your new login credentials:</p>
                <p><strong>Username:</strong> $username</p>
                <p><strong>Temporary Password:</strong></p>
                <div class='password'>$temporaryPassword</div>
                <p>Please log in using this temporary password and change it immediately for security purposes.</p>
                <p>If you didn't request this change, please contact the system administrator immediately.</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

function sendEmail($to, $subject, $htmlBody) {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.brevo.com';
        $mail->SMTPAuth = true;
        $mail->Username = '8cc35a002@smtp-brevo.com';
        $mail->Password = 'JSh1qV4zbR7DWaI0';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $mail->setFrom('plp.no.reply1@gmail.com', 'No-Reply');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>