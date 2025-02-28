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
                        WHERE status = 'Approved' AND archived = 1 AND (paid = 1 OR paid = 2) 
                        $date_range";
$total_clients_result = $conn->query($total_clients_query);
$total_clients_row = $total_clients_result->fetch_assoc();
$total_clients = $total_clients_row['total'];
$total_pages = ceil($total_clients / $clients_per_page);

$query = "SELECT a.recieve_at, a.clientname, a.bank_partner, a.remarks, a.status, u.phone, 
                 CASE 
                     WHEN fb.transaction_id IS NOT NULL THEN CONCAT_WS(' ', fb.year_model, fb.make, fb.type, fb.transmition_type)
                     WHEN fs.transaction_id IS NOT NULL THEN CONCAT_WS(' ', fs.year_model, fs.make, fs.type, fs.transmition_type)
                     WHEN fsh.transaction_id IS NOT NULL THEN CONCAT_WS(' ', fsh.year_model, fsh.make, fsh.type, fsh.transmition_type)
                     ELSE 'Not Applicable'
                 END AS unit,
                 a.amount_finance
          FROM appointments a 
          LEFT JOIN users u ON a.email = u.email 
          LEFT JOIN forms_brandnew_applicants fb ON a.transaction_id = fb.transaction_id
          LEFT JOIN forms_sanglaorcr_applicants fs ON a.transaction_id = fs.transaction_id
          LEFT JOIN forms_secondhand_applicants fsh ON a.transaction_id = fsh.transaction_id
          WHERE a.status = 'Approved' AND a.archived = 1 AND (a.paid = 1 OR a.paid = 2) 
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
    $query = "SELECT a.clientname, 
                     CASE 
                         WHEN fb.transaction_id IS NOT NULL THEN CONCAT_WS(' ', fb.year_model, fb.make, fb.type, fb.transmition_type)
                         WHEN fs.transaction_id IS NOT NULL THEN CONCAT_WS(' ', fs.year_model, fs.make, fs.type, fs.transmition_type)
                         WHEN fsh.transaction_id IS NOT NULL THEN CONCAT_WS(' ', fsh.year_model, fsh.make, fsh.type, fsh.transmition_type)
                         ELSE 'Not Applicable'
                     END AS unit,
                     a.amount_finance 
              FROM appointments a 
              LEFT JOIN forms_brandnew_applicants fb ON a.transaction_id = fb.transaction_id 
              LEFT JOIN forms_sanglaorcr_applicants fs ON a.transaction_id = fs.transaction_id 
              LEFT JOIN forms_secondhand_applicants fsh ON a.transaction_id = fsh.transaction_id 
              LEFT JOIN users u ON a.email = u.email 
              WHERE a.status = 'Approved' AND a.archived = 1 AND (a.paid = 1 OR a.paid = 2) 
              $date_range 
              ORDER BY a.approve_at DESC";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        while ($client = $result->fetch_assoc()) {
            $client = array_change_key_case($client, CASE_LOWER);
            $client['client_name'] = !empty($client['clientname']) ? $client['clientname'] : 'N/A';
            $client['unit'] = !empty($client['unit']) ? $client['unit'] : 'N/A';
            $client['amount_finance'] = !empty($client['amount_finance']) ? $client['amount_finance'] : '0.00';
            $client['commission_fee'] = floatval($client['amount_finance']) * 0.05;

            unset($client['clientname']);
            $data[] = $client;
        }
    }
    $columns = ['Client Name', 'Unit', 'Amount Finance', 'Commission Fee'];
    $outputFileName = 'Commission_Report_' . date('Y-m-d_H-i-s') . '.pdf';
    $includeTotal = true; // Include total amount and commission fee
    generatePDF($data, 'Commission Report', $columns, $outputFileName, $includeTotal, false);
    exit;
}

?>