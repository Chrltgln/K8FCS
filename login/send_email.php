<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PDO;

require '../vendor/autoload.php';

function getUserName($email)
{
    // Database connection
    include '../settings/config.php';

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "SELECT first_name, last_name FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();
        $conn->close();

        return $user ? $user : null;
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        return null;
    }
}

function sendPasswordResetEmail($toEmail, $resetLink)
{
    $user = getUserName($toEmail);
    if (!$user) {
        return "User not found.";
    }

    $firstName = htmlspecialchars($user['first_name']);
    $lastName = htmlspecialchars($user['last_name']);

    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'k8financialconsultingservices@gmail.com';
        $mail->Password = 'jzph gxsh itrw pbqx'; // Use your app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Disable SSL certificate verification (for testing only)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Email content
        $mail->setFrom('k8financialconsultingservices@gmail.com', 'K8 Financial Consulting Services');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    text-align: center;
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
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    color: #ffffff;
                    background-color: #4CAF50;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 20px;
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
            <div class="container">
                <div class="header">
                    <h1>Password Reset Request</h1>
                </div>
                <div class="content">
                    <p>Hi ' . $firstName . ' ' . $lastName . ',</p>
                    <p>Click the button below to reset your password:</p>
                    <a href="' . htmlspecialchars($resetLink) . '" class="button">Reset Password</a>
                    <p>This link will expire in 15 minutes. Hurry up to change your password.</p>
                    <p>Have a nice day. Thank you for supporting us.</p>
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' K8 Financial Consulting Services. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        $mail->AltBody = 'Click the link to reset your password: ' . htmlspecialchars($resetLink);

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return "Mail Error: " . $mail->ErrorInfo;
    }
}
?>