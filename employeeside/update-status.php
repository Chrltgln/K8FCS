<?php
include '../settings/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and validate it
    $appointment_id = $_POST['appointment_id'] ?? null;
    $status_type = $_POST['status_type'] ?? null;

    if ($appointment_id === null || $status_type === null) {
        echo "Error: Missing required fields.";
        exit;
    }

    // Determine the new status based on the status type
    $new_status = '';
    if ($status_type === 'Approved') {
        $new_status = 'Approved';
        $sql = "UPDATE appointments SET status = ?, approve_at = NOW() WHERE id = ?";
    } elseif ($status_type === 'Accepted') {
        $new_status = 'Accepted';
        $sql = "UPDATE appointments SET status = ?, accepted_at = NOW() WHERE id = ?";
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

        // Redirect or show success message
        header('Location: acceptedAppointment.php');
        exit;
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>