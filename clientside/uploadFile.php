<?php
date_default_timezone_set('Asia/Manila');
include '../settings/config.php';
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$user_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $file_name = mysqli_real_escape_string($conn, $file['name']);
    $file_tmp_name = $file['tmp_name'];
    $file_description = mysqli_real_escape_string($conn, $_POST['file_description']);
    
    // Define upload directory
    $upload_dir = '../uploads/' . $user_email . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $upload_file = $upload_dir . basename($file_name);

    // Check file type
    $valid_file_types = ['image/webp', 'image/jpeg', 'application/pdf', 'image/png', 'image/svg+xml'];
    $file_type = mime_content_type($file_tmp_name);

    if (!in_array($file_type, $valid_file_types)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please upload .webp, .jpg, .pdf, .png, or .svg files only.',
            }).then(() => {
                window.location.href = 'profile.php';
            });
        </script>";
        exit;
    }

    // Move the uploaded file to the server
    if (move_uploaded_file($file_tmp_name, $upload_file)) {
        // Insert file details into the database
        $stmt = $conn->prepare("INSERT INTO files (user_email, file_name, file_description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user_email, $file_name, $file_description);
        if ($stmt->execute()) { 
            // Log the upload activity
            $action = "uploaded a file";
            $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user_email, $action, $file_name);
            if (!$stmt->execute()) {
                error_log("Failed to insert into activity log: " . $stmt->error);
            }
            
            $_SESSION['upload_message'] = "File uploaded successfully!";
        } else {
            $_SESSION['upload_message'] = "Error uploading file.";
        }
        $stmt->close();
    } else {
        $_SESSION['upload_message'] = "Error moving file.";
    }
}

header('Location: profile.php');
exit;
?>