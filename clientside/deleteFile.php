<?php
include '../settings/config.php';
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit;
}

$user_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['file_id'], $_POST['file_name'])) {
    $file_id = (int) $_POST['file_id'];
    $file_name = mysqli_real_escape_string($conn, $_POST['file_name']);

    // Fetch the file path from the database
    $stmt = $conn->prepare("SELECT file_name FROM files WHERE id = ? AND user_email = ?");
    $stmt->bind_param("is", $file_id, $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the file from the server
        $file_path = '../uploads/' . $file_name;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete the file record from the database
        $stmt = $conn->prepare("DELETE FROM files WHERE id = ? AND user_email = ?");
        $stmt->bind_param("is", $file_id, $user_email);
        if ($stmt->execute()) {
            $_SESSION['upload_message'] = "File deleted successfully!";
            
            // Log the deletion in the activity log
            $action = "deleted a file";
            $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user_email, $action, $file_name);
            if (!$stmt->execute()) {
                error_log("Failed to insert into activity log: " . $stmt->error);
            }
        } else {
            $_SESSION['upload_message'] = "Error deleting file: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['upload_message'] = "File not found.";
    }
}

header('Location: profile.php');
exit;
?>