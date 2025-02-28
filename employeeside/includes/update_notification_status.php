<?php
include '../../settings/config.php'; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $appointmentId = $_POST['id'];
        if (isset($_POST['remove']) && $_POST['remove'] == 'true') {
            $query_update = "UPDATE appointments SET mark_as_read = 2 WHERE id = ?";
        } else {
            $query_update = "UPDATE appointments SET mark_as_read = 1 WHERE id = ?";
        }
        $stmt = $conn->prepare($query_update);
        $stmt->bind_param('i', $appointmentId);
        $stmt->execute();
        $stmt->close();
    } else {
        $query_update_all = "UPDATE appointments SET mark_as_read = 1 WHERE status = 'Processing' AND mark_as_read IS NULL AND archived = 0";
        $conn->query($query_update_all);
    }
}
?>
