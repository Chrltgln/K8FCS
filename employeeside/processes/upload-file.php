<?php
include '../../settings/authenticate.php';
checkUserRole(['Employee']);
include '../../settings/config.php'; // Include your database connection file

$response = ['success' => false, 'message' => ''];

$allowed_extensions = ['svg', 'png', 'jpg', 'jpeg', 'webp', 'pdf', 'xlsx', 'docx'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    $user_email = $_SESSION['user_email'];
    $upload_dir = '../uploads/' . $user_email . '/';

    // Ensure the directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    foreach ($_FILES['files']['name'] as $key => $file_name) {
        $file_tmp = $_FILES['files']['tmp_name'][$key];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validate file extension
        if (!in_array($file_ext, $allowed_extensions)) {
            $response['message'] = 'Unsupported File Type: ' . $file_name . '. Supported extensions are: ' . implode(', ', $allowed_extensions);
            continue;
        }

        $file_base = pathinfo($file_name, PATHINFO_FILENAME);
        $new_file_name = $file_base . '.' . $file_ext;
        $counter = 1;

        // Check if file exists and append a number if it does
        while (file_exists($upload_dir . $new_file_name)) {
            $new_file_name = $file_base . '(' . $counter . ').' . $file_ext;
            $counter++;
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
            // Insert file record into the database
            $stmt = $conn->prepare("INSERT INTO files (user_email, file_name) VALUES (?, ?)");
            if ($stmt === false) {
                throw new Exception('Prepare statement failed: ' . $conn->error);
            }
            $stmt->bind_param("ss", $user_email, $new_file_name);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'File uploaded successfully';
            } else {
                $response['message'] = 'Database insert failed: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'File upload failed';
        }
    }
} else {
    $response['message'] = 'Invalid request';
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
