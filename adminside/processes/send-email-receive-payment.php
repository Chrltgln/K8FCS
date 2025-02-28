<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

function sendEmailForPaymentRecieve($to, $subject, $body)
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

function formatFormType($form_type)
{
    switch ($form_type) {
        case 'sangla-orcr':
            return 'Sangla ORCR';
        case 'brand-new':
            return 'Brand New';
        case 'second-hand':
            return 'Second Hand';
        default:
            return $form_type;
    }
}

function sendRecieveEmail($email, $clientname, $form_type, $transaction_id, $remarks)
{
    $formatted_form_type = formatFormType($form_type);
    $subject = 'Payment Received';
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
                color: #000000;
            }
            .footer {
                background-color: #f4f4f4;
                color: #888888;
                padding: 10px 0;
                text-align: center;
                font-size: 12px;
            }
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .logo img {
                max-width: 100px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Payment Received</h1>
            </div>
            <div class='content'>
                <p>Dear $clientname,</p>
                <p>We are pleased to inform you that we have received your payment for the $formatted_form_type.</p>
                <p>Transaction ID: <strong>$transaction_id</strong></p>
                <p>Thank you for your payment. We appreciate your business and look forward to serving you in the future.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " K8 Financial Consulting Services. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";

    // Log the email content for debugging
    error_log("Sending email to $email with subject $subject and body $body");

    return sendEmailForPaymentRecieve($email, $subject, $body);
}

function sendNotReceiveEmail($email, $clientname, $form_type, $transaction_id, $remarks)
{
    $formatted_form_type = formatFormType($form_type);
    $subject = 'Payment Declined';
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
                <h1>Payment Declined</h1>
            </div>
            <div class='content'>
                <p>Dear $clientname,</p>
                <p>We regret to inform you that your payment for the $formatted_form_type has been declined.</p>
                <p><strong>Transaction ID: </strong> $transaction_id</p>
                <p><strong>Reason:</strong> $remarks</p>
            
                 <p>Please SMS or Call us for more information for your loan.<br><a href='tel:09176195984'>0917-619-5984</a> | <a href='tel:09175281760'>0917-528-1760</a> | <a href='mailto:k8_fcs@yahoo.com'>k8_fcs@yahoo.com</a></p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " K8 Financial Consultancy Services. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";

    // Log the email content for debugging
    error_log("Sending email to $email with subject $subject and body $body");

    return sendEmailForPaymentRecieve($email, $subject, $body);
}
?>