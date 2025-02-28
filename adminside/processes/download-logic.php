<?php
include '../../settings/authenticate.php';
checkUserRole(['Admin']);
include '../../settings/config.php';

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $filePath = "../../uploads/$file";

    if (file_exists($filePath)) {
        // File found in ../../uploads
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        // Fetch email and transaction_id from the database
        $stmt = $conn->prepare("
            SELECT users.email, forms_sanglaorcr_applicants.transaction_id 
            FROM forms_sanglaorcr_applicants 
            JOIN users ON forms_sanglaorcr_applicants.email = users.email 
            WHERE forms_sanglaorcr_applicants.ORCR_filename = ?
        ");
        $stmt->bind_param("s", $file);
        $stmt->execute();
        $stmt->bind_result($email, $transaction_id);
        $stmt->fetch();
        $stmt->close();

        if ($email && $transaction_id) {
            $filePath = "../../clientside/uploads/orcr/$email/$transaction_id/$file";

            if (file_exists($filePath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            }
        }

        // Check in employee uploads
        $stmt = $conn->prepare("
            SELECT email 
            FROM users 
            WHERE email = (SELECT user_email FROM files WHERE file_name = ?)
        ");
        $stmt->bind_param("s", $file);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();

        if ($email) {
            $filePath = "../../employeeside/uploads/$email/$file";

            if (file_exists($filePath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            } else {
                echo "File not found.";
            }
        } else {
            echo "Invalid request.";
        }
    }
} else {
    echo "Invalid request.";
}
?>
