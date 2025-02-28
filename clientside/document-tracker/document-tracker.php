<?php
include '../settings/config.php';

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit;
}

$email = $_SESSION['email'];

// SQL query to fetch the latest status update and all status updates
$sql = "
    SELECT a.status, a.recieve_at, su.status_type AS status_type, su.updated_at, a.transaction_id, a.form_type, a.archived, a.paid
    FROM appointments a
    LEFT JOIN status_updates su ON a.id = su.appointment_id
    WHERE a.email = ?
    ORDER BY su.updated_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$latest_status = 'No status available';
$receive_timestamp = 'N/A';
$update_timestamp = 'N/A';
$status_updates = [];

if ($result->num_rows > 0) {
    // Fetch the latest status update
    $latest_row = $result->fetch_assoc();
    $latest_status = $latest_row['status_type'];
    $receive_timestamp = date("F j, Y, g:i A", strtotime($latest_row['recieve_at']));
    if ($latest_row['updated_at']) {
        $update_timestamp = date("F j, Y, g:i A", strtotime($latest_row['updated_at']));
    }

    // Fetch all status updates
    $result->data_seek(0); // Reset the pointer to the beginning
    while ($row = $result->fetch_assoc()) {
        $status_updates[] = [
            'status_type' => $row['status_type'],
            'updated_at' => date("F j, Y, g:i A", strtotime($row['updated_at'])),
            'transaction_id' => $row['transaction_id'],
            'form_type' => $row['form_type'],
            'archived' => $row['archived'],
            'paid' => $row['paid'],
            'status' => $row['status']
        ];
    }
}

$stmt->close();
$conn->close();
?>
<p style="font-style: italic;" class="status-tracker-note">Note: This status shows only the current application</p>
<div class="tracker-details">
    <h4 class="tracker-title">Status Updates</h4>
    
    <?php if ($latest_status === 'No status available'): ?>
        <p class="tracker-text">No additional status updates available.</p>
    <?php else: ?>
        <div class="status-update">
            <?php
            $message = '';
            $latest_update = $status_updates[0]; // Get the latest update
            $message_style = '';

            if ($latest_update['status'] == 'Processing' && $latest_update['form_type'] == 'brand-new' && $latest_update['archived'] == 0 && is_null($latest_update['paid'])) {
                $message = "Your application is now {$latest_update['status_type']}!";
            }
            if ($latest_update['status'] == 'Processing' && $latest_update['form_type'] == 'sangla-orcr' && $latest_update['archived'] == 0 && is_null($latest_update['paid'])) {
                $message = "Your application is now {$latest_update['status_type']}!";
            }
            if ($latest_update['status'] == 'Processing' && $latest_update['form_type'] == 'second-hand' && $latest_update['archived'] == 0 && is_null($latest_update['paid'])) {
                $message = "Your application is now {$latest_update['status_type']}!";
            }
            if ($latest_update['status'] == 'Accepted' && $latest_update['form_type'] == 'brand-new' && $latest_update['archived'] == 0 && is_null($latest_update['paid'])) {
                $message = "Your application is now {$latest_update['status_type']}!";
            }
            if ($latest_update['status'] == 'Accepted' && $latest_update['form_type'] == 'sangla-orcr' && $latest_update['archived'] == 0 && is_null($latest_update['paid'])) {
                $message = "Your application is now {$latest_update['status_type']}!";
            }
            if ($latest_update['status'] == 'Accepted' && $latest_update['form_type'] == 'second-hand' && $latest_update['archived'] == 0 && is_null($latest_update['paid'])) {
                $message = "Your application is now {$latest_update['status_type']}!";
            }
            if ($latest_update['status'] == 'Approved' && $latest_update['archived'] == 0 && is_null($latest_update['paid'])) {
                $message = "Your application is now {$latest_update['status_type']}. Please set up a payment, thank you!";
            }
            if ($latest_update['status'] == 'Approved' && $latest_update['archived'] == 1 && $latest_update['paid'] == 1) {
                $message = 'Your transaction is completed';
            }
            if ($latest_update['status'] == 'Approved' && $latest_update['archived'] == 1 && $latest_update['paid'] == 2) {
                $message = 'Your transaction is completed';
            }
            if ($latest_update['status'] == 'Approved' && $latest_update['archived'] == 1 && $latest_update['paid'] == 0) {
                $message = "Your application is now {$latest_update['status_type']} due to unable to process payment";
            }
            if ($latest_update['status'] == 'Declined' && $latest_update['form_type'] == 'brand-new' && $latest_update['archived'] == 1 && is_null($latest_update['paid'])) {
                $message = 'Your application has been declined.';
                $message_style = 'style="color: red;"';
            }
            if ($latest_update['status'] == 'Declined' && $latest_update['form_type'] == 'sangla-orcr' && $latest_update['archived'] == 1 && is_null($latest_update['paid'])) {
                $message = 'Your application has been declined.';
                $message_style = 'style="color: red;"';
            }
            if ($latest_update['status'] == 'Declined' && $latest_update['form_type'] == 'second-hand' && $latest_update['archived'] == 1 && is_null($latest_update['paid'])) {
                $message = 'Your application has been declined.';
                $message_style = 'style="color: red;"';
            }

            echo "<span class='status-type' {$message_style}>{$message}</span>";
            echo "<span class='status-timestamp'>{$latest_update['updated_at']}</span>";
            echo "<span class='status-transaction-id'>{$latest_update['transaction_id']}</span>";
            echo "<span class='status-form-type'>" . ucwords(str_replace('-', ' ', $latest_update['form_type'])) . "</span>";
            ?>
        </div>
    <?php endif; ?>
</div>