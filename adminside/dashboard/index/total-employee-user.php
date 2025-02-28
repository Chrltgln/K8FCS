<?php
include '../settings/config.php';

// count employee users
$sql = "SELECT COUNT(*) as total_employees FROM users WHERE role ='Employee'";
$result = $conn->query($sql);

// Fetch
$total_employees = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_employees = $row['total_employees'];
}

$conn->close();
?>