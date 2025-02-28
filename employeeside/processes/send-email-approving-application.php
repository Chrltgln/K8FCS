<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
require '../../settings/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and validate it
    $appointment_id = $_POST['appointment_id'] ?? null;
    $status_type = $_POST['status_type'] ?? null;

    if ($appointment_id === null || $status_type === null) {
        echo "Error: Missing required fields.";
        exit;
    }

    // Determine the new status based on the status type
    if ($status_type === 'Approved') {
        $new_status = 'Approved';
        $sql = "UPDATE appointments SET status = ?, approve_at = NOW(), archived = 1 WHERE id = ?";
    } else {
        echo "Error: Invalid status type.";
        exit;
    }

    // Prepare and execute the update statement for the appointments table
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $appointment_id);

    if ($stmt->execute()) {
        // Insert the new status update into the status_updates table
        $sql = "INSERT INTO status_updates (appointment_id, status, status_type) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $appointment_id, $new_status, $status_type);
        $stmt->execute();

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
                $mail->Subject = 'Appointment Approved';
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
                            <h1>Application Approved</h1>
                        </div>
                        <div class='content'>
                            <h1>Your $form_type Application has been <span style='color: green; font-weight: bolder;'>Approved</span></h1>
                            <p>We will send you a SMS or Call for the <strong>Exact amount</strong> of the following fees:</p>
                            <p>Chattel Mortgage Fee<br>Comprehensive Car Insurance</p>
                            <p>Your transaction ID is: <strong>$transaction_id</strong></p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " K8 Financial Consultancy Services. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>";

                $mail->send();
                echo 'Notification email has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "No client email found for the given appointment ID.";
        }

        // Redirect or show success message
        header('Location: ../acceptedAppointment.php');
        exit;
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>