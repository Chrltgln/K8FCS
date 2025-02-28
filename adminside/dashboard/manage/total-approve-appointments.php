<?php
include '../settings/config.php';

// Counts approved appointments
$sql = "SELECT COUNT(*) as total_approved FROM appointments WHERE status ='Approved'";
$result = $conn->query($sql);

// Fetch
$total_approved = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_approved = $row['total_approved'];
}

$conn->close();
?>