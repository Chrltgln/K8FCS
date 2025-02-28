<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it has not been started yet
}
include '../settings/config.php';
require_once('processes/send-email-receive-payment.php');

date_default_timezone_set('Asia/Manila'); // Set timezone to Asia/Manila

function logActivity($conn, $user_email, $action, $file_name) {
    $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_email, $action, $file_name);
    $stmt->execute();
    $stmt->close();
}


if (isset($_SESSION['user_email'])) {
    $logged_in_email = $_SESSION['user_email'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $transaction_id = $_POST['transaction_id'];
        $action = $_POST['action'];
        $reason = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($action === 'approve') {
            $query = "UPDATE appointments SET archived = 1, paid = 1 WHERE transaction_id = ?";
        } elseif ($action === 'decline') {
            $query = "UPDATE appointments SET status = 'Approved', archived = 1, paid = 0, decline_at = NOW(), remarks = ? WHERE transaction_id = ?";
        }

        if ($stmt = $conn->prepare($query)) {
            if ($action === 'decline') {
                $stmt->bind_param("ss", $reason, $transaction_id);
            } else {
                $stmt->bind_param("s", $transaction_id);
            }
            if ($stmt->execute()) {
                // Fetch client details from the database
                $query = "SELECT email, clientname, form_type, remarks FROM appointments WHERE transaction_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('s', $transaction_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $email = $row['email'];
                    $clientname = $row['clientname'];
                    $form_type = $row['form_type'];
                    $remarks = $row['remarks'];

                    // Send the appropriate email based on the action
                    if ($action === 'approve') {
                        $emailResult = sendRecieveEmail($email, $clientname, $form_type, $transaction_id, $remarks);
                    } elseif ($action === 'decline') {
                        $emailResult = sendNotReceiveEmail($email, $clientname, $form_type, $transaction_id, $remarks);
                    }

                    if ($emailResult !== true) {
                        echo "Action completed but email could not be sent. Error: $emailResult";
                    }

                    // Log activity
                    if ($action === 'approve') {
                        logActivity($conn, $_SESSION['user_email'], 'Accepted Payment of Transaction ID: ' . $transaction_id, 'N/A');
                    } elseif ($action === 'decline') {
                        logActivity($conn, $_SESSION['user_email'], 'Decline ' . $reason . ' w/ Transaction ID: ' . $transaction_id, 'N/A');
                    }
                } else {
                    echo "No client details found for the given transaction ID.";
                }
                header("Location: archives.php");
                exit();
            } else {
                echo "Error updating record: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }
} else {
    echo "User is not logged in.";
}

$conn->close();
?>