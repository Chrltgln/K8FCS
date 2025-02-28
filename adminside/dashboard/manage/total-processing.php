<?php
include '../settings/config.php';

// Query to count client users
$sql = "SELECT COUNT(*) as total_processing FROM appointments WHERE status ='Processing'";
$result = $conn->query($sql);

// Fetch the result
$total_processing = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_processing = $row['total_processing'];
}

$conn->close();
?>