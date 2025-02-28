<?php
session_start();

// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    include 'settings/config.php';

    $user_id = $_SESSION['user_id'];
    $current_time = date('Y-m-d H:i:s');

    // Check the last access time
    $stmt = $conn->prepare("SELECT login_time FROM sessions WHERE user_id = ? AND logout_time IS NULL");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($login_time);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        $inactive_time = strtotime($current_time) - strtotime($login_time);
        if ($inactive_time > 300) { // 5 minutes
            // Update the logout_time for the inactive session
            $stmt = $conn->prepare("UPDATE sessions SET logout_time = ? WHERE user_id = ? AND logout_time IS NULL");
            $stmt->bind_param("si", $current_time, $user_id);
            $stmt->execute();

            // Destroy the session
            session_unset();
            session_destroy();
            header('Location: php/login.php?error=Session Expired due to inactivity. Please login again.');
            exit();
        } else {
            // Update the login_time to extend the session
            $stmt = $conn->prepare("UPDATE sessions SET login_time = ? WHERE user_id = ? AND logout_time IS NULL");
            $stmt->bind_param("si", $current_time, $user_id);
            $stmt->execute();
        }
    }

    // Redirect based on user role
    switch ($_SESSION['role']) {
        case 'Admin':
            header('Location: adminside/index'); 
            break;
        case 'Employee':
            header('Location: employeeside/homepage'); 
            break;
        case 'Client':
            header('Location: clientside/homepage'); 
            break;
        default:
            header('Location: index'); 
            break;
    }
    exit();
}
?>