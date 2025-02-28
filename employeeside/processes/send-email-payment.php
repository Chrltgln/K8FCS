<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendEmail($to, $subject, $body)
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

function sendApprovalEmail($email, $clientname, $form_type, $transaction_id, $checkoutUrl, $amount_finance, $term, $maturity, $check_release, $description)
{
    $formatted_form_type = formatFormType($form_type);
    $subject = 'Application Approved';
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
            .button {
                display: block;
                width: fit-content;
                padding: 10px 20px;
                font-size: 16px;
                color: #ffffff;
                background-color: #4CAF50;
                text-align: center;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px auto;
                text-transform: uppercase;
            }
            .footer {
                background-color: #f4f4f4;
                color: #888888;
                padding: 10px 0;
                text-align: center;
                font-size: 12px;
            }
            ul {
                list-style-type: none;
                padding: 0;
            }
            ul li {
                text-decoration: none;
                color: #000000;
            }
            a {
                color: #ffffff;
                text-decoration: none;
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
                <h1>Application Approved</h1>
            </div>
            <div class='content'>
                <p>Dear $clientname,</p>
                <p>Your $formatted_form_type Application has been approved.</p>
                <h2 style='color: #000000;'>Please click the button to see the <strong>Exact amount</strong> of fees:</h2>
                <ul>
                    <li>$description</li>
                </ul>
                <p>Your transaction ID is: <strong>$transaction_id</strong></p>
                <p>Amount Finance: <strong>$amount_finance</strong></p>
                <p>Term: <strong>$term months</strong></p>
                <p>Maturity Date: <strong>$maturity</strong></p>
                <p>Check Release Date: <strong>$check_release</strong></p>
                <a href='$checkoutUrl' class='button'>Click to Pay</a>
                <p>Thank you!</p>
                <br />
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

    return sendEmail($email, $subject, $body);
}
?>