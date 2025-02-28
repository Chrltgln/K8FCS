<?php
include '../../settings/config.php';

// Ensure the session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user_email is set in the session
if (!isset($_SESSION['user_email'])) {
    die('User email is not set in the session.');
}

$user_email = $_SESSION['user_email'];
$file_name = $_GET['file'];

// Sanitize the file name to prevent directory traversal attacks
$file_name = basename($file_name);

// Fetch the file from the database to ensure it belongs to the user
$stmt = $conn->prepare("SELECT file_name FROM files WHERE user_email = ? AND file_name = ?");
$stmt->bind_param("ss", $user_email, $file_name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Construct the correct file path
    $file_path = '../uploads/' . $user_email . '/' . $file_name;

    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit();
    } else {
        die('File not found.');
    }
} else {
    die('You do not have permission to download this file.');
}
?>
