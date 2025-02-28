<?php
$type = $_GET['type'] ?? '';
$fileName = urldecode($_GET['file'] ?? '');
$email = urldecode($_GET['email'] ?? '');
$transactionId = urldecode($_GET['transaction_id'] ?? '');

if (!$fileName || !$email) {
    echo "Error: Missing parameters.";
    exit;
}

if ($type === 'orcr') {
    $filePath = "../../clientside/uploads/orcr/" . urldecode($email) . "/" . urldecode($transactionId) . "/" . urldecode($fileName);
} else {
    $filePath = "../../uploads/" . urldecode($email) . "/" . urldecode($fileName);
}


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
    echo "Error: File not found.";
    exit;
}
?>
