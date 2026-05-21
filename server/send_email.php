<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/autoload.php";

function sendWelcomeEmail($toEmail, $toName)
{
    $mail = new PHPMailer(true);

    try {

        // SMTP SETTINGS
        $mail->isSMTP();

        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;

        // YOUR GMAIL (SENDER)
        $mail->Username = "norahalhazab@gmail.com";

        // YOUR GOOGLE APP PASSWORD
        $mail->Password = "fitx jdcq ertm fpoe";

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = 587;

        // SENDER INFO
        $mail->setFrom(
            "norahalhazab@gmail.com",
            "Red Sea Escapes"
        );

        // RECEIVER = THE USER WHO REGISTERED
        $mail->addAddress($toEmail, $toName);

        // EMAIL CONTENT
        $mail->isHTML(true);

        $mail->Subject = "Welcome to Red Sea Escapes";

        $mail->Body = "
            <div style='font-family: Arial, sans-serif;'>

                <h2 style='color:#0f766e;'>
                    Welcome to Red Sea Escapes, $toName!
                </h2>

                <p>
                    Your account has been created successfully.
                </p>

                <p>
                    You can now explore resorts, activities, and bookings.
                </p>

                <p>
                    Thank you for joining us.
                </p>

            </div>
        ";

        $mail->AltBody =
            "Welcome to Red Sea Escapes, $toName! Your account was created successfully.";

        // SEND EMAIL
        $mail->send();

        return true;

    } catch (Exception $e) {

        return false;
    }
}
?>