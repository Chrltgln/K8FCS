<?php
include '../settings/config.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['password'];

    // Validate password
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $newPassword)) {
        $response = ['status' => 'error', 'message' => 'Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character.'];
    } else {
        // Validate token
        $stmt = $conn->prepare("SELECT email, expiry FROM password_resets WHERE token = ?");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            $expiry = $row['expiry'];

            // Debugging: Log the expiry time
            error_log("Token expiry time: " . $expiry);

            // Check if the token has expired
            if (strtotime($expiry) > time()) {
                // Token is valid and not expired

                // Update password in the database
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param('ss', $hashedPassword, $email);
                $stmt->execute();

                // Delete the token from the database
                $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                $stmt->bind_param('s', $token);
                $stmt->execute();

                $response = ['status' => 'success', 'message' => 'Password has been updated successfully.'];
            } else {
                // Token has expired
                $response = ['status' => 'error', 'message' => 'Already used, Token not found or expired token.'];
            }
        } else {
            // Token not found
            $response = ['status' => 'error', 'message' => 'Already used, Token not found or expired token.'];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>