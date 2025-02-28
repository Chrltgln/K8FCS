<?php

include '../../settings/authenticate.php';
checkUserRole(['Employee']); 
include '../../settings/config.php'; // Include your database connection file

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file'])) {
        $file_name = $_POST['file'];
        $user_email = $_SESSION['user_email'];
        $file_path = '../uploads/' . $user_email . '/' . $file_name;

        // Check if the file exists
        if (file_exists($file_path)) {
            // Delete the file from the server
            if (unlink($file_path)) {
                // Delete the file record from the database
                $stmt = $conn->prepare("DELETE FROM files WHERE user_email = ? AND file_name = ?");
                if ($stmt === false) {
                    throw new Exception('Prepare statement failed: ' . $conn->error);
                }
                $stmt->bind_param("ss", $user_email, $file_name);
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'File deleted successfully';
                } else {
                    $response['message'] = 'Database delete failed: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['message'] = 'File delete failed';
            }
        } else {
            $response['message'] = 'File not found';
        }
    } else {
        $response['message'] = 'Invalid request';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>