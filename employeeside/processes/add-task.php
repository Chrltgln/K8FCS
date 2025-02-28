<?php
session_start();
include '../../settings/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = $_POST['task'];
    $user_email = $_SESSION['user_id'];

    // Insert the task into the database
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task) VALUES (?, ?)");
    $stmt->bind_param("ss", $user_email, $task);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the homepage
    header("Location: ../homepage.php");
    exit();
}
?>