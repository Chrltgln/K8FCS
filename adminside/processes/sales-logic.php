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

if ($conn) {
    $query = "SELECT clientname, transaction_id, form_type, payment_description, amount, status 
          FROM appointments 
          WHERE ((status = 'Declined' AND archived = 1 AND paid = 0) 
          OR (status = 'Approved' AND archived = 1 AND paid = 1)) 
          $date_range 
          ORDER BY approve_at DESC 
          LIMIT $clients_per_page OFFSET $offset";
    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }
} else {
    die("Database connection is closed.");
}

include 'generate-pdf.php';
// Helper function to format form_type
function formatFormType($form_type) {
    switch ($form_type) {
        case 'sangla-orcr':
            return 'Sangla ORCR';
        case 'brand-new':
            return 'Brand New';
        case 'second-hand':
            return 'Second Hand';
        default:
            $words = explode('-', $form_type);
            $formatted_words = array_map('ucfirst', $words);
            return implode(' ', $formatted_words);
    }
}
// Check if the user requested a PDF
if (isset($_GET['generate_pdf'])) {
    $data = [];
    $query = "SELECT clientname, transaction_id, form_type, payment_description, amount 
          FROM appointments 
          WHERE ((status = 'Declined' AND archived = 1 AND paid = 0) 
          OR (status = 'Approved' AND archived = 1 AND paid = 1)) 
          $date_range 
          ORDER BY approve_at DESC";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        while ($client = $result->fetch_assoc()) {
            $client = array_change_key_case($client, CASE_LOWER);
            $client['client_name'] = $client['clientname']; // Ensure the key matches the column name
            unset($client['clientname']); // Remove the old key
            $client['form_type'] = formatFormType($client['form_type']); // Format the form_type
            $data[] = $client;
        }
    }
    $columns = ['Client Name', 'Transaction ID', 'Form Type', 'Payment Description', 'Amount'];
    $outputFileName = 'Sales_Report_' . date('Y-m-d_H-i-s') . '.pdf';
    $includeTotal = true; // Set to true or false based on your requirement
    $includeStatus = false; // Exclude the status column for sales report
    generatePDF($data, 'Sales Report', $columns, $outputFileName, $includeTotal, $includeStatus);
    exit;
}

?>