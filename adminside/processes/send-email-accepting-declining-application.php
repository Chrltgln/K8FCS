<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../../vendor/autoload.php';
include '../../settings/config.php';

session_start(); // Ensure the session is started

function logActivity($conn, $user_email, $action, $file_name)
{
    $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_email, $action, $file_name);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $transaction_id = $_POST['transaction_id'];
    $user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'unknown';

    if (isset($_POST['accept'])) {
        // Update the appointment status to 'Accepted'
        $query = "UPDATE appointments SET status='Accepted', accepted_at=NOW() WHERE id='$appointment_id'";
        if (mysqli_query($conn, $query)) {
            // Log activity
            logActivity($conn, $user_email, 'Accepted appointment w/ Transaction ID: ' . $transaction_id, 'N/A');

            // Fetch client email and other details from the database
            $query = "SELECT email, transaction_id, form_type FROM appointments WHERE id='$appointment_id'";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $client_email = $row['email'];
                $form_type = ucwords(str_replace('-', ' ', $row['form_type'])); // Format form_type

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
                    $mail->Subject = 'Appointment Accepted';
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
                                <h1>Application Accepted</h1>
                            </div>
                            <div class='content'>
                                <h1>Your $form_type Application has been <span style='color: green; font-weight: bolder;'>Accepted</span></h1>
                                <p>Please wait for an email for the Approved status to take the next step.</p>
                                <p>Please wait for a SMS, CALL, or Email.</p>
                                <p>Your transaction ID is: <strong>$transaction_id</strong></p>
                            </div>
                            <div class='footer'>
                                <p>&copy; " . date('Y') . " K8 Financial Consulting Services. All rights reserved.</p>
                            </div>
                        </div>
                    </body>
                    </html>";

                    $mail->send();
                    echo 'Message has been sent';
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                echo "No client email found for the given appointment ID.";
            }

            // Redirect to manage.php after successful update
            header('Location: ../accepted-appointments.php');
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['decline'])) {
        $remarks = $_POST['remarks'];
        // Update the appointment status to 'Declined'
        $query = "UPDATE appointments SET status='Declined', decline_at=NOW(), archived=1, remarks='$remarks' WHERE id='$appointment_id'";
        if (mysqli_query($conn, $query)) {
            // Log activity
            logActivity($conn, $user_email, 'Declined appointment w/ Transaction ID: ' . $transaction_id, 'N/A');

            // Fetch client email and other details from the database
            $query = "SELECT email, form_type FROM appointments WHERE id='$appointment_id'";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $client_email = $row['email'];
                $form_type = ucwords(str_replace('-', ' ', $row['form_type'])); // Format form_type

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

                    $mail->send();
                    echo 'Decline notification email has been sent';
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                echo "No client email found for the given appointment ID.";
            }

            // Redirect to manage.php after successful update
            header('Location: ../archives.php');
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>