<?php
include '../settings/config.php';

// Counts processing appointments
$sql = "SELECT COUNT(*) as total_accepted FROM appointments WHERE status ='Accepted'";
$result = $conn->query($sql);

// Fetch
$total_accepted = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_accepted = $row['total_accepted'];
}

$conn->close();
?>