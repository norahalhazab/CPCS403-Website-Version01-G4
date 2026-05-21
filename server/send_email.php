<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../vendor/autoload.php";

function sendWelcomeEmail($toEmail, $toName)
{
    $safeName = htmlspecialchars($toName, ENT_QUOTES, "UTF-8");

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;

        // IMPORTANT:
        // Use your Gmail address and your Google App Password.
        // Do not use your normal Gmail password.
        $mail->Username = "escaperedsea@gmail.com";
        $mail->Password = "rdeq dcos eblq rxqn";

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom("escaperedsea@gmail.com", "Red Sea Escapes");
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = "Welcome to Red Sea Escapes";

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; line-height:1.6;'>
                <h2 style='color:#0f766e;'>Welcome to Red Sea Escapes, {$safeName}!</h2>
                <p>Your account has been created successfully.</p>
                <p>You can now explore Red Sea resorts, activities, and bookings.</p>
                <p>Thank you for joining us.</p>
            </div>
        ";

        $mail->AltBody = "Welcome to Red Sea Escapes, {$toName}! Your account was created successfully.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
?>
