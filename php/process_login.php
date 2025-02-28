<?php
include '../settings/config.php';
include 'user.php';

session_start(); // Start the session

// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

$user = new User($conn);

$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    header("Location: ../php/login.php?error=missing_field&email=" . urlencode($email) . "&password=" . urlencode($password));
    exit();
}

$result = $user->login($email, $password);
if ($result === 'Admin' || $result === 'Client' || $result === 'Employee') {
    $_SESSION['role'] = $result;

    // Get the user ID
    $user_id = $user->getUserIdByEmail($email);

    // Fetch user details
    $user_details = $user->getUserDetailsById($user_id);
    $_SESSION['first_name'] = $user_details['first_name'];
    $_SESSION['middle_name'] = $user_details['middle_name'];
    $_SESSION['last_name'] = $user_details['last_name'];
    $_SESSION['email'] = $user_details['email'];
    $_SESSION['contact_number'] = $user_details['contact_number'];
    $_SESSION['address'] = $user_details['address']; // Ensure the address is fetched correctly
    $_SESSION['dob'] = $user_details['date_of_birth']; // Ensure the session variable is set correctly

    // Check if a session record exists for the user and if logout_time is not updated
    $stmt = $conn->prepare("SELECT session_id FROM sessions WHERE user_id = ? AND logout_time IS NULL");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    $session_id = session_id();
    $login_time = date('Y-m-d H:i:s');

    if ($stmt->num_rows > 0) {
        // Create a new session ID
        session_regenerate_id(true);
        $session_id = session_id();

        // Update the existing session record with the new session ID
        $stmt = $conn->prepare("UPDATE sessions SET session_id = ?, login_time = ? WHERE user_id = ? AND logout_time IS NULL");
        $stmt->bind_param("ssi", $session_id, $login_time, $user_id);
    } else {
        // Insert a new session record
        $stmt = $conn->prepare("INSERT INTO sessions (user_id, session_id, login_time) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $session_id, $login_time);
    }
    $stmt->execute();

    // Redirect based on role
    if ($result === 'Admin') {
        header("Location: ../adminside/index.php");
    } elseif ($result === 'Client') {
        header("Location: ../clientside/homepage.php");
    } elseif ($result === 'Employee') {
        header("Location: ../employeeside/homepage.php");
    }
} else {
    header("Location: ../php/login.php?error=wrong_credentials&email=" . urlencode($email) . "&password=" . urlencode($password));
}
?>