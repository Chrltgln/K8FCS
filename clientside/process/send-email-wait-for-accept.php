<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
    $mail->SMTPAuth = true;
    $mail->Username = 'k8financialconsultingservices@gmail.com'; // SMTP username
    $mail->Password = 'jzph gxsh itrw pbqx'; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Disable certificate verification (for testing purposes only)
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Recipients
    $mail->setFrom('k8financialconsultingservices@gmail.com', 'K8 Financial Consulting Services');
    $mail->addAddress($fields['email']); // Add a recipient

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Application is Waiting for Approval';
    $mail->Body = "
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
                background-color: #4CAF50;
                color: #ffffff;
                padding: 10px 0;
                text-align: center;
            }
            .content {
                padding: 20px;
                text-align: left;
            }
            .content h1 {
                color: #4CAF50;
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
                <h1>K8 Financial Consulting Services</h1>
            </div>
            <div class='content'>
                <h1>Application Waiting for Approval</h1>
                <p>Dear {$fields['first_name']} {$fields['last_name']},</p>
                <p>Your application is currently waiting for approval. We will notify you once it has been reviewed.</p>
                <p>Thank you for choosing K8FCS.</p>
                <br/>
                <p>Please SMS or Call us for more information for your loan.<br><a href='tel:09176195984'>0917-619-5984</a> | <a href='tel:09175281760'>0917-528-1760</a> | <a href='mailto:k8_fcs@yahoo.com'>k8_fcs@yahoo.com</a></p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " K8 Financial Consulting Services. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";

    $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>