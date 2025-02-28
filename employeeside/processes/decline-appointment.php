<?php
include '../../settings/config.php';
include '../../settings/authenticate.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';

checkUserRole(['Employee']);

function logActivity($conn, $user_email, $action, $file_name) {
    $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_email, $action, $file_name);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $bank_partner = $_POST['bank_partner'];
    $remarks = $_POST['remarks'];
    $user_email = $_SESSION['user_email']; // Fetch user_email from session

    // Update the appointment status in the database
    $sql = "UPDATE appointments SET status = 'Declined', archived = 1, paid = NULL, bank_partner = ?, remarks = ?, decline_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $bank_partner, $remarks, $appointment_id);

    if ($stmt->execute()) {
        echo "Appointment declined successfully.";

        // Fetch client email and other details from the database
        $query = "SELECT email, transaction_id, form_type FROM appointments WHERE id='$appointment_id'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $client_email = $row['email'];
            $transaction_id = $row['transaction_id'];
            $form_type = $row['form_type'];

            // Send email notification
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
                $mail->addAddress($client_email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Appointment Declined';
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
                            <p>With a transaction ID of <strong>$transaction_id</strong>.</p>
                            <p>Bank Partner: <strong>$bank_partner</strong></p>
                            <p>Remarks: <strong>$remarks</strong></p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " K8 Financial Consultancy Services. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>";

                $mail->send();
                echo ' Decline notification email has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            // Log activity
            logActivity($conn, $user_email, 'Declined Transaction ID: ' . $transaction_id, 'decline-appointment.php');
        } else {
            echo "No client email found for the given appointment ID.";
        }
    } else {
        echo "Error declining appointment: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>