<?php
include '../../settings/config.php';

// Define and execute the SQL query
$sql = "SELECT clientname, email, status, appointment_date, appointment_time, form_type FROM appointments WHERE status = 'Processing' AND archived = 0 ORDER BY appointment_date DESC, appointment_time DESC";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusClass = strtolower($row["status"]);
        $formattedFormType = ucwords(str_replace('-', ' ', $row["form_type"]));
        echo '<div class="client">';
        echo '<div class="client-info">';
        echo '<h3>' . htmlspecialchars($formattedFormType) . '</h3>';
        echo '<p>Name: <strong>' . htmlspecialchars($row["clientname"]) . '</strong></p>';
        echo '<p class="appointment-date">Appointment Date : ' . htmlspecialchars($row["appointment_date"]) . '</p>';
        echo '<p class="appointment-time">Appointment Time : ' . htmlspecialchars($row["appointment_time"]) . '</p>';
        echo '<p>Status: <span class="status ' . $statusClass . '">';
        echo '<span class="status-dot ' . $statusClass . '"></span>';
        echo htmlspecialchars($row["status"]) . '</span></p>';
        echo '</div>';
        echo '<div class="appointment-info">';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<p>No appointments found.</p>';
}

$conn->close();
?>
