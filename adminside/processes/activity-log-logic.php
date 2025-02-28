<?php
include '../settings/config.php';
require_once('../tcpdf/tcpdf.php');
include 'generate-pdf.php'; // Include the generate-pdf.php file

$results_per_page = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;
$selected_role = isset($_GET['role']) ? $_GET['role'] : '';

$query = "SELECT al.user_email, al.action, al.timestamp, u.role, CONCAT(u.first_name, ' ', u.last_name) AS client_name 
          FROM activity_log al 
          JOIN users u ON al.user_email = u.email 
          WHERE ('$selected_role' = '' OR u.role = '$selected_role' OR ('$selected_role' = 'K8FCS System' AND al.action LIKE '%Automatically%'))
          AND ('$selected_role' != 'Employee' OR al.action NOT LIKE '%Automatically%')
          AND ('$selected_role' != 'Client' OR al.action NOT LIKE '%Automatically%')
          ORDER BY al.timestamp DESC 
          LIMIT $start_from, $results_per_page";
$result = mysqli_query($conn, $query);

$data = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (strpos($row['action'], 'Automatically') !== false) {
            $row['user_email'] = 'System Automated';
            $row['client_name'] = 'System Automated';
            $row['role'] = 'System Automated';
        }
        $data[] = [
            'email' => $row['user_email'],
            'client_name' => $row['client_name'],
            'action' => $row['action'],
            'role' => $row['role'],
            'time_stamp' => $row['timestamp']
        ];
    }
}

if (isset($_GET['generate_pdf'])) {
    // Fetch all records for the PDF
    $query = "SELECT al.user_email, al.action, al.timestamp, u.role, CONCAT(u.first_name, ' ', u.last_name) AS client_name 
              FROM activity_log al 
              JOIN users u ON al.user_email = u.email 
              WHERE ('$selected_role' = '' OR u.role = '$selected_role' OR ('$selected_role' = 'K8FCS System' AND al.action LIKE '%Automatically%'))
              AND ('$selected_role' != 'Employee' OR al.action NOT LIKE '%Automatically%')
              AND ('$selected_role' != 'Client' OR al.action NOT LIKE '%Automatically%')
              ORDER BY al.timestamp DESC";
    $result = mysqli_query($conn, $query);

    $data = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (strpos($row['action'], 'Automatically') !== false) {
                $row['user_email'] = 'System Automated';
                $row['client_name'] = 'System Automated';
                $row['role'] = 'System Automated';
            }
            $data[] = [
                'email' => $row['user_email'],
                'client_name' => $row['client_name'],
                'action' => $row['action'],
                'role' => $row['role'],
                'time_stamp' => $row['timestamp']
            ];
        }
    }

    $columns = ['Email', 'Client Name', 'Action', 'Role', 'Time Stamp'];
    $outputFileName = 'Activity_Log_' . date('Y-m-d_H-i-s') . '.pdf';
    generatePDF($data, 'Activity Log', $columns, $outputFileName, false);
    exit;
}
?>