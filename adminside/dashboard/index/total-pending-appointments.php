<?php
include '../settings/config.php';

// Counts processing appointments
$sql = "SELECT COUNT(*) as total_pending FROM appointments WHERE status ='Processing'";
$result = $conn->query($sql);

// Fetch
$total_pending = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_pending = $row['total_pending'];
}

$conn->close();
?>