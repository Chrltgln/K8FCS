<?php
include '../settings/config.php';

$user_email = $_SESSION['email'];

// SQL query to fetch action, timestamp, and file_name from activity_log table
$stmt = $conn->prepare("SELECT action, timestamp, file_name FROM activity_log WHERE user_email = ? ORDER BY timestamp DESC");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

echo "<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 10px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        font-weight: bold;
    }
</style>";

echo "<table>";
echo "<tr><th>Logs</th><th>File name</th><th>Timestamp</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . htmlspecialchars($row['action']) . "</td><td>" . htmlspecialchars($row['file_name']) . "</td><td>" . htmlspecialchars($row['timestamp']) . "</td></tr>";
}

echo "</table>";

$stmt->close();
$conn->close();
?>
