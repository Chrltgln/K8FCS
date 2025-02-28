<?php
session_start();
include '../../settings/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['user_id'];

    // Delete the task from the database
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the homepage
    header("Location: ../homepage.php");
    exit();
}
?>