<?php

include '../settings/config.php';
require_once('../tcpdf/tcpdf.php');

$report_duration = 'all'; 
$start_date = '';
$end_date = '';
if (isset($_GET['report_duration']) && in_array($_GET['report_duration'], ['daily', '1-week', '1-month', '3-months', '6-months', '1-year', 'custom', 'all'])) {
    $report_duration = $_GET['report_duration'];
}

$date_range = '';
if ($report_duration === 'custom' && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
    $date_range = "AND created_at >= '$start_date' AND created_at < '$end_date'";
} elseif ($report_duration !== 'all') {
    switch ($report_duration) {
        case 'daily':
            $date_range = "AND created_at >= CURDATE() AND created_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '1-week':
            $date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND created_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '1-month':
            $date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND created_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '3-months':
            $date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) AND created_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '6-months':
            $date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND created_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '1-year':
            $date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND created_at < CURDATE() + INTERVAL 1 DAY";
            break;
    }
}

$clients_per_page = 15;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $clients_per_page;

$total_clients_query = "SELECT COUNT(*) as total FROM users 
                        WHERE role IN ('Client', 'Employee', 'Admin') 
                        $date_range";
$total_clients_result = $conn->query($total_clients_query);
$total_clients_row = $total_clients_result->fetch_assoc();
$total_clients = $total_clients_row['total'];
$total_pages = ceil($total_clients / $clients_per_page);

$query = "SELECT first_name, last_name, address, dob, phone, role, created_at 
          FROM users 
          WHERE role IN ('Client', 'Employee', 'Admin') 
          $date_range 
          ORDER BY created_at DESC 
          LIMIT $offset, $clients_per_page";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

include 'generate-pdf.php';

// Check if the user requested a PDF
if (isset($_GET['generate_pdf'])) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $dob = new DateTime($row['dob']);
        $created_at = new DateTime($row['created_at']);
        
        $data[] = [
            'full_name' => $row['first_name'] . ' ' . $row['last_name'],
            'address' => $row['address'],
            'date_of_birth' => $dob->format('F j, Y'), 
            'phone' => $row['phone'],
            'role' => $row['role'],
            'created_at' => $created_at->format('F j, Y g:i:sA') 
        ];
    }

    $columns = ['Full Name', 'Address', 'Date of Birth', 'Phone', 'Role', 'Created At'];
    $reportTitle = 'User Accounts Report';
    $outputFileName = 'user_accounts_report.pdf';

    generatePDF($data, $reportTitle, $columns, $outputFileName);
}
?>