<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']); 
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'k8financialconsultingservices@gmail.com'; // company's email
        $mail->Password = 'jzph gxsh itrw pbqx'; // app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Disable SSL certificate verification (temporary solution)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom('k8financialconsultingservices@gmail.com', "$firstName $lastName"); // sender: company email
        $mail->addAddress('k8financialconsultingservices@gmail.com'); // recipient: company
        $mail->addReplyTo($email, "$firstName $lastName"); // reply-to: guest user's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New message from $firstName $lastName";
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
                    <h1>Message from $firstName $lastName </h1>
                </div>
                <div class='content'>
                    <p><strong>From :</strong> $email</p>
                    <p>$message</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " K8 Financial Consulting Services. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
        $mail->AltBody = "Name: $firstName $lastName\nEmail: $email\nMessage: $message";

        $mail->send();
        header("Location: ../../index.php");
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>