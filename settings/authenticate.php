<?php
session_start(); 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, set a query parameter for unauthorized access
    header('Location: ../php/login.php?unauthorized=not_logged_in');
    exit();
}

// Include database configuration
require_once 'config.php';

// Fetch user details based on user_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email, first_name, middle_name, last_name, address, role, dob, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_email, $first_name, $middle_name, $last_name, $address, $role, $dob, $phonenumber);
$stmt->fetch();
$stmt->close();

// Store user details in session
$_SESSION['user_email'] = $user_email;
$_SESSION['first_name'] = $first_name;
$_SESSION['middle_name'] = $middle_name;
$_SESSION['last_name'] = $last_name;
$_SESSION['address'] = $address; // Ensure the address is stored in the session
$_SESSION['dob'] = $dob;
$_SESSION['clientName'] = $first_name . ' ' . $middle_name . ' ' . $last_name;
$_SESSION['role'] = $role;
$_SESSION['phone'] = $phonenumber;

// Function to check user role and redirect if not authorized
function checkUserRole($roles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        header('Location: ../php/login.php?unauthorized=wrong_role');
        exit();
    }
}
?>