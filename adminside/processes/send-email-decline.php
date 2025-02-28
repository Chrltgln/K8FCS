<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendEmailForDecline($to, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'k8financialconsultingservices@gmail.com'; // SMTP username
        $mail->Password = 'jzph gxsh itrw pbqx'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS
        $mail->Port = 587; // Port for STARTTLS

        // Disable SSL certificate verification (for testing purposes)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom('k8financialconsultingservices@gmail.com', 'K8 Financial Consulting Services');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        $errorMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}. Exception: " . $e->getMessage();
        error_log($errorMessage);
        return $errorMessage;
    }
}

function sendDeclineEmail($email, $clientname, $form_type, $transaction_id, $remarks)
{
    $subject = 'Appointment Declined';
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .header {
                background-color: #f44336;
                color: #ffffff;
                padding: 10px 0;
                text-align: center;
            }
            .content {
                padding: 20px;
                text-align: center;
            }
            .content h1 {
                color: #f44336;
            }
            .content p {
                font-size: 16px;
                line-height: 1.5;
            }
            .footer {
                background-color: #f4f4f4;
                color: #888888;
                padding: 10px 0;
                text-align: center;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Appointment Declined</h1>
            </div>
            <div class='content'>
                <h1>Your $form_type Application has been <span style='color: red; font-weight: bolder;'>Declined</span></h1>
                <p>Remarks: $remarks</p>
                <p>Thank you for your understanding.</p>
                <p>We apologize for any inconvenience this may have caused. Please feel free to apply again at your earliest convenience.</p>
                <p>If you have any questions or concerns, please feel free to contact us at <a href='mailto:k8financialconsultingservices@gmail.com'>K8FCS EMAIL</a></p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " K8 Financial Consulting Services. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";

    // Log the email content for debugging
    error_log("Sending email to $email with subject $subject and body $body");

    return sendEmailForDecline($email, $subject, $body);
}
?>