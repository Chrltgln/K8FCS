<?php
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start(); // Start the session

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
        $mail->AltBody = strip_tags($body); // Plain text version of the email

        $mail->send();
        return true;
    } catch (Exception $e) {
        $errorMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}. Exception: " . $e->getMessage();
        error_log($errorMessage);
        return $errorMessage;
    }
}

// Generate OTP
function generateOTP($length = 6) {
    $characters = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $otp;
}

// Handle OTP request
header('Content-Type: application/json'); // Ensure the response is JSON
$response = ['success' => false, 'message' => ''];
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input');
        }

        if (isset($data['email'])) {
            $email = $data['email'];

            // Generate OTP
            $otp = generateOTP();

            // Store OTP in session
            $_SESSION['otp'] = $otp;

            // Create email body
            $subject = 'Your OTP Code';
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
                            text-align: center;
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
                            <h1>One Time Passcode</h1>
                        </div>
                        <div class='content'>
                            <h1>$otp</h1>
                            <p>Please use this code to complete your verification process. If you did not request this code, please ignore this email.</p>
                            <p><strong>Do not share this OTP with anyone.</strong></p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " K8 Financial Consulting Services. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>";

            // Send email
            $emailResult = sendEmail($email, $subject, $body);

            if ($emailResult === true) {
                $response['success'] = true;
                $response['message'] = 'OTP sent successfully';
            } else {
                $response['message'] = $emailResult;
            }
        } elseif (isset($data['otp'])) {
            // Verify OTP
            $enteredOtp = $data['otp'];
            if ($enteredOtp === $_SESSION['otp']) {
                $response['success'] = true;
                $response['message'] = 'OTP verified successfully';
            } else {
                $response['message'] = 'Invalid OTP';
            }
        } else {
            throw new Exception('Invalid request data');
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);