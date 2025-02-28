<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
include '../../settings/config.php';
include '../../settings/authenticate.php';
checkUserRole(['Employee']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $bank_partner = $_POST['bank_partner'];
    $remarks = $_POST['remarks'];
    $term = $_POST['term'];
    $amount_finance = $_POST['amount_finance'];
    $maturity = $_POST['maturity'];
    $check_release = $_POST['check_release'];

    // Update the appointment status in the database
    $sql = "UPDATE appointments SET status = 'Approved', archived = 1, paid = 2, bank_partner = ?, remarks = ?, term = ?, amount_finance = ?, maturity = ?, check_release = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissi", $bank_partner, $remarks, $term, $amount_finance, $maturity, $check_release, $appointment_id);

    if ($stmt->execute()) {
        // Fetch client email and other details from the database
        $query = "SELECT email, transaction_id, form_type FROM appointments WHERE id='$appointment_id'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $client_email = $row['email'];
            $transaction_id = $row['transaction_id'];
            $form_type = $row['form_type'];

            // Format the form_type
            switch ($form_type) {
                case 'sangla-orcr':
                    $formatted_form_type = 'Sangla ORCR';
                    break;
                case 'brand-new':
                    $formatted_form_type = 'Brand New';
                    break;
                case 'second-hand':
                    $formatted_form_type = 'Second Hand';
                    break;
                default:
                    $formatted_form_type = $form_type;
                    break;
            }

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
                $mail->Subject = 'Application Approved';
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
                        a {
                            text-decoration: none;
                            color: inherit;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>Application Approved</h1>
                        </div>
                        <div class='content'>
                            <h1>Congratulations! Your $formatted_form_type application has been <span style='color:green;'>Approved</span></h1>
                            <p>Your transaction ID is: <strong>$transaction_id</strong></p>
                            <p>Amount Finance: <strong>$amount_finance</strong></p>
                            <p>Maturity: <strong>$maturity</strong></p>
                            <p>Term: <strong>$term</strong></p>
                            <p>Check Release: <strong>$check_release</strong></p>
                            <p>Please SMS or Call us for more information for your loan.<br><a href='tel:09176195984'>0917-619-5984</a> | <a href='tel:09175281760'>0917-528-1760</a> | <a href='mailto:k8_fcs@yahoo.com'>k8_fcs@yahoo.com</a></p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " K8 Financial Consulting Services. All rights reserved.</p>
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

        echo "Appointment approved successfully.";
    } else {
        echo "Error approving appointment: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>