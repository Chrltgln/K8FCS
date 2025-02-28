<?php
include '../settings/config.php';

// count employee users
$sql = "SELECT COUNT(*) as total_declined FROM appointments WHERE status ='Declined'";
$result = $conn->query($sql);

// Fetch
$total_declined = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_declined = $row['total_declined'];
}

$conn->close();
?>