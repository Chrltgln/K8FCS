<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../../vendor/autoload.php';
    require '../../settings/config.php';

    session_start(); // Start the session to access session variables

    function logActivity($conn, $user_email, $action, $file_name) {
        $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user_email, $action, $file_name);
        $stmt->execute();
        $stmt->close();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $appointment_id = $_POST['appointment_id'];
        $user_email = $_SESSION['user_email']; // Fetch user_email from session

        // Check if a file is uploaded
        $file_name = isset($_FILES['file']['name']) && !empty($_FILES['file']['name']) ? $_FILES['file']['name'] : 'N/A';

        // Fetch transaction_id from the database
        $query = "SELECT transaction_id FROM appointments WHERE id='$appointment_id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        // Generate a new transaction ID if it doesn't exist
        if (empty($row['transaction_id'])) {
            $transaction_id = generateTransactionID();
        } else {
            $transaction_id = $row['transaction_id'];
        }

        if (isset($_POST['accept'])) {
            // Update the appointment status to 'Accepted'
            $query = "UPDATE appointments SET status='Accepted', transaction_id='$transaction_id', accepted_at=NOW(), archived=0 WHERE id='$appointment_id'";
            if (mysqli_query($conn, $query)) {
                // Insert into status_updates
                $statusType = 'Accepted'; // Ensure this value fits the column definition
                $statusUpdateQuery = "INSERT INTO status_updates (appointment_id, status, status_type, updated_at) VALUES ('$appointment_id', 'Accepted', '$statusType', NOW())";
                mysqli_query($conn, $statusUpdateQuery);

                // Log activity
                logActivity($conn, $user_email, 'Accepted Appointment of Transaction ID: ' . $transaction_id, $file_name);

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
                        $mail->Subject = 'Application Accepted';
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

                // Redirect to acceptedAppointment.php after successful update
                header('Location: ../acceptedAppointment.php');
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } elseif (isset($_POST['decline'])) {
            $remarks = $_POST['remarks']; // Get the remarks from the form

            // Update the appointment status to 'Declined' and insert remarks
            $query = "UPDATE appointments SET status='Declined', transaction_id='$transaction_id', archived=1, paid=NULL, remarks='$remarks' WHERE id='$appointment_id'";
            if (mysqli_query($conn, $query)) {
                // Insert into status_updates
                $statusType = 'Declined'; // Ensure this value fits the column definition
                $statusUpdateQuery = "INSERT INTO status_updates (appointment_id, status, status_type, updated_at) VALUES ('$appointment_id', 'Declined', '$statusType', NOW())";
                mysqli_query($conn, $statusUpdateQuery);

                // Log activity
                logActivity($conn, $user_email, 'Declined Transaction ID: ' . $transaction_id, $file_name);

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
                                    text-align: left;
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
                                    <p>With a transaction ID of <strong>$transaction_id</strong>.</p>
                                    <br/>
                                    <p>Please SMS or Call us for more information for your loan.<br><a href='tel:09176195984'>0917-619-5984</a> | <a href='tel:09175281760'>0917-528-1760</a> | <a href='mailto:k8_fcs@yahoo.com'>k8_fcs@yahoo.com</a></p>
                                </div>
                                <div class='footer'>
                                    <p>&copy; " . date('Y') . " K8 Financial Consultancy Services. All rights reserved.</p>
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

                // Redirect to pendingAppointment.php after successful update
                header('Location: ../pendingAppointment.php');
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    }
    ?>