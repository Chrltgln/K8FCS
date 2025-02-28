<?php


include '../settings/config.php';
require_once('../tcpdf/tcpdf.php');

$report_duration = 'daily';
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
    $date_range = "AND approve_at >= '$start_date' AND approve_at < '$end_date'";
} elseif ($report_duration !== 'all') {
    switch ($report_duration) {
        case 'daily':
            $date_range = "AND approve_at >= CURDATE() AND approve_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '1-week':
            $date_range = "AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND approve_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '1-month':
            $date_range = "AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND approve_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '3-months':
            $date_range = "AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) AND approve_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '6-months':
            $date_range = "AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND approve_at < CURDATE() + INTERVAL 1 DAY";
            break;
        case '1-year':
            $date_range = "AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND approve_at < CURDATE() + INTERVAL 1 DAY";
            break;
    }
}

$clients_per_page = 15;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $clients_per_page;

$total_clients_query = "SELECT COUNT(*) as total FROM appointments 
                        WHERE ((status = 'Declined' AND archived = 1 AND paid = 0) 
                        OR (status = 'Approved' AND archived = 1 AND paid = 1)) 
                        $date_range";
$total_clients_result = $conn->query($total_clients_query);
$total_clients_row = $total_clients_result->fetch_assoc();
$total_clients = $total_clients_row['total'];
$total_pages = ceil($total_clients / $clients_per_page);

$query = "SELECT a.recieve_at, a.clientname, a.bank_partner, a.remarks, a.status, u.phone 
          FROM appointments a 
          LEFT JOIN users u ON a.email = u.email 
          WHERE ((a.status = 'Declined' AND a.archived = 1 AND a.paid = 0) 
          OR (a.status = 'Approved' AND a.archived = 1 AND a.paid = 1)) 
          $date_range   
          ORDER BY a.approve_at DESC 
          LIMIT $offset, $clients_per_page";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

include 'generate-pdf.php';

// Check if the user requested a PDF
if (isset($_GET['generate_pdf'])) {
    $data = [];
    $query = "SELECT a.recieve_at, a.clientname, a.bank_partner, a.remarks, a.status, u.phone 
              FROM appointments a 
              LEFT JOIN users u ON a.email = u.email 
              WHERE ((a.status = 'Declined' AND a.archived = 1 AND a.paid = 0) 
              OR (a.status = 'Approved' AND a.archived = 1 AND a.paid = 1)) 
              $date_range 
              ORDER BY a.approve_at DESC";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        while ($client = $result->fetch_assoc()) {
            $client = array_change_key_case($client, CASE_LOWER);
            $client['date'] = !empty($client['recieve_at']) ? $client['recieve_at'] : 'N/A';
            $client['client_name'] = !empty($client['clientname']) ? $client['clientname'] : 'N/A';
            $client['contact_number'] = !empty($client['phone']) ? $client['phone'] : 'N/A';
            $client['bank'] = !empty($client['bank_partner']) ? $client['bank_partner'] : 'N/A';
            $client['remarks'] = !empty($client['remarks']) ? $client['remarks'] : 'N/A';
            $client['status'] = !empty($client['status']) ? $client['status'] : 'N/A';

            unset($client['recieve_at']);
            unset($client['clientname']);
            unset($client['bank_partner']);
            unset($client['phone']);
            $data[] = $client;
        }
    }
    $columns = ['Date', 'Client Name', 'Contact Number', 'Bank', 'Remarks','Status'];
    $outputFileName = 'Application_Report_' . date('Y-m-d_H-i-s') . '.pdf';
    $includeTotal = false; // Set to true or false based on your requirement
    $includeStatus = true; // Include the status column for application report 
    generatePDF($data, 'Application Report', $columns, $outputFileName, $includeTotal, $includeStatus);
    exit;
}

?>