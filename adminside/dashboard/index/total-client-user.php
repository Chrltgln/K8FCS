<?php
include '../settings/config.php';

// Query to count client users
$sql = "SELECT COUNT(*) as total_clients FROM users WHERE role ='Client'";
$result = $conn->query($sql);

// Fetch the result
$total_clients = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_clients = $row['total_clients'];
}

$conn->close();
?>