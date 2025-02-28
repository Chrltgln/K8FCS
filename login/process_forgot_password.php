<?php
ob_start();
include '../settings/config.php';
include 'send_email.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $token = bin2hex(random_bytes(50));
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Delete any existing token for this email
            $query = "DELETE FROM password_resets WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();

            // Insert new token
            $query = "INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $email, $token, $expiry);
            $stmt->execute();

            $resetLink = "http://k8financial.com/login/reset_password.php?token=" . urlencode($token);

            $result = sendPasswordResetEmail($email, $resetLink);
            if ($result === true) {
                header("Location: forgot_password.php?forgot-password-message=" . urlencode("Check your email for the password reset link."));
            } else {
                header("Location: forgot_password.php?forgot-password-message=" . urlencode($result));
            }
        } else {
            header("Location: forgot_password.php?forgot-password-message=" . urlencode("Email not found."));
        }
    } else {
        header("Location: forgot_password.php?forgot-password-message=" . urlencode("Invalid email format."));
    }
    exit();
}
ob_end_flush();
?>