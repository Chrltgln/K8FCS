<?php
require_once '../../settings/config.php';

if (isset($_POST['appointment_date'])) {
    $appointment_date = $_POST['appointment_date'];

    $stmt = $conn->prepare("SELECT appointment_time, COUNT(*) as count FROM appointments WHERE appointment_date = ? GROUP BY appointment_time");
    $stmt->bind_param("s", $appointment_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $taken_slots = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode($taken_slots);
} else {
    echo json_encode(["error" => "No appointment_date provided"]);
}
?>