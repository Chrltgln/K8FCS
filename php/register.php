<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../settings/config.php';
include 'user.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    // Validate OTP
    if (!isset($_SESSION['otp']) || $_SESSION['otp'] !== $otp) {
        $response['message'] = 'Invalid OTP';
        echo json_encode($response);
        exit();
    }

    // Check if email already exists
    $query = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $query->bind_result($count);
    $query->fetch();
    $query->close();

    if ($count > 0) {
        // Email already exists
        $response['message'] = 'Email already exists';
        echo json_encode($response);
        exit();
    }

    // Sanitize and validate input data
    $user = new User($conn);
    $first_name = $user->sanitize_input($_POST["first_name"]);
    $middle_name = $user->sanitize_input($_POST["middle_name"]);
    $last_name = $user->sanitize_input($_POST["last_name"]);
    $age = intval($user->sanitize_input($_POST["age"]));
    $gender = $user->sanitize_input($_POST["gender"]);
    $dob = $user->sanitize_input($_POST["dob"]);
    $address = $user->sanitize_input($_POST["address"]);
    $email = filter_var($user->sanitize_input($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $phone = $user->sanitize_input($_POST["phone"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    $result = $user->register($first_name, $middle_name, $last_name, $age, $gender, $dob, $address, $email, $phone, $password, $confirm_password);
    if ($result === true) {
        $response['success'] = true;
        $response['message'] = 'Welcome to K8 Financial Consultancy Services!';
    } else {
        $response['message'] = $result;
    }

    echo json_encode($response);
    exit();
}
?>