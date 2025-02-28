<?php
include '../settings/config.php';
include '../settings/authenticate.php';
checkUserRole(['Client']); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$change_password_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $change_password_message = 'All fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $change_password_message = 'New passwords do not match.';
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($old_password, $hashed_password)) {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hashed_password, $user_id);
            if ($stmt->execute()) {
                $change_password_message = 'Password successfully changed.';
                
                // Log the password change in the activity log
                $user_email = $_SESSION['email'];
                $action = "Changed password";
                $file_name = "N/A";
                $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $user_email, $action, $file_name);
                if (!$stmt->execute()) {
                    error_log("Failed to insert into activity log: " . $stmt->error);
                }
            } else {
                $change_password_message = 'Failed to change password. Please try again later.';
            }
            $stmt->close();
        } else {
            $change_password_message = 'Old password is incorrect.';
        }
    }
}

$_SESSION['change_password_message'] = $change_password_message;

header('Location: profile.php');
exit;
?>