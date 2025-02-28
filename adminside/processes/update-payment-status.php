<?php
session_start();

include '../../settings/config.php';
include 'send-email-receive-payment.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = $_POST['transaction_id'];
    $action = $_POST['action'];
    $reason = $_POST['reason'] ?? '';

    // Fetch client details for email
    $client_query = "SELECT clientname, email, form_type FROM appointments WHERE transaction_id = ?";
    $stmt = $conn->prepare($client_query);
    $stmt->bind_param('s', $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $client = $result->fetch_assoc();

    if ($action === 'approve') {
        $query = "UPDATE appointments SET approve_at = NOW(), archived = 1, paid = 1 WHERE transaction_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $transaction_id);
        $stmt->execute();

        // Send email
        sendRecieveEmail($client['email'], $client['clientname'], $client['form_type'], $transaction_id, 'Your payment has been received');

    } elseif ($action === 'decline') {
        $query = "UPDATE appointments SET decline_at = NOW(), status = 'Approved', archived = 1, paid = 0, remarks = ? WHERE transaction_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $reason, $transaction_id);
        $stmt->execute();

        // Send email
        sendNotReceiveEmail($client['email'], $client['clientname'], $client['form_type'], $transaction_id, $reason);
    }

    if ($stmt->execute()) {
        header("Location: ../archives.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>