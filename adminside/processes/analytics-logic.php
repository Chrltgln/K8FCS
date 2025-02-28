<?php
// Check if the user is logged in and has the admin role
include '../settings/config.php';

// Get the duration and year from the URL
$duration = isset($_GET['duration']) ? $_GET['duration'] : 'all';
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$start_date = '';
$end_date = '';
$date_range = '';
$user_date_range = '';

if ($duration === 'custom' && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = date('Y-m-d', strtotime($_GET['end_date'] . ' +1 day'));
    $date_range = "AND recieve_at >= '$start_date' AND recieve_at < '$end_date'
    AND accepted_at >= '$start_date' AND accepted_at < '$end_date'
    AND decline_at >= '$start_date' AND decline_at < '$end_date'
    AND approve_at >= '$start_date' AND approve_at < '$end_date'";
    $user_date_range = "AND created_at >= '$start_date' AND created_at < '$end_date'";
} elseif ($duration !== 'all') {
    switch ($duration) {
        case 'daily':
            $date_range = "AND (decline_at >= CURDATE()
                                OR recieve_at >= CURDATE()
                                OR approve_at >= CURDATE()
                                OR accepted_at >= CURDATE())";
            $user_date_range = "AND created_at >= CURDATE()";
            break;
        case '1-week':
            $date_range = "AND decline_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) 
                            AND recieve_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) 
                            AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) 
                            AND accepted_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
            $user_date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
            break;
        case '1-month':
            $date_range = "AND decline_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) 
                            AND recieve_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) 
                            AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) 
                            AND accepted_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            $user_date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            break;
        case '3-months':
            $date_range = "AND decline_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) 
                            AND recieve_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) 
                            AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) 
                            AND accepted_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
            $user_date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
            break;
        case '6-months':
            $date_range = "AND decline_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
                            AND recieve_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
                            AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
                            AND accepted_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            $user_date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            break;
        case '1-year':
            $date_range = "AND decline_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) 
                            AND recieve_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) 
                            AND approve_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) 
                            AND accepted_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            $user_date_range = "AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            break;
    }
}

// Fetch data for the users bar graph
$sql_users = "SELECT 
                (SELECT COUNT(*) FROM users WHERE role = 'Employee' $user_date_range) AS total_employees,
                (SELECT COUNT(*) FROM users WHERE role = 'Client' $user_date_range) AS total_clients,
                (SELECT COUNT(*) FROM users WHERE role = 'Admin' $user_date_range) AS total_admins";
$result_users = $conn->query($sql_users);
$users_data = $result_users->fetch_assoc();

// Fetch data for the appointments bar graph
$sql_appointments = "SELECT 
                        (SELECT COUNT(*) FROM appointments WHERE status = 'Processing' $date_range) AS total_processing,
                        (SELECT COUNT(*) FROM appointments WHERE status = 'Accepted' $date_range) AS total_accepted,
                        (SELECT COUNT(*) FROM appointments WHERE status = 'Declined' AND archived = 1 AND paid = 0 $date_range) AS total_declined,
                        (SELECT COUNT(*) FROM appointments WHERE status = 'Approved' AND archived = 1 AND (paid = 1 OR paid = 2) $date_range) AS total_approved,
                        (SELECT COUNT(*) FROM appointments WHERE status = 'Approved' AND archived = 1 AND paid = 0 $date_range) AS total_not_continued";
$result_appointments = $conn->query($sql_appointments);
$appointments_data = $result_appointments->fetch_assoc();

// Fetch data for the monthly processing appointments   
$sql_monthly_processing_clients = "
    SELECT 
        DATE_FORMAT(recieve_at, '%M') AS month, 
        COUNT(*) AS total 
    FROM 
        appointments 
    WHERE 
        status = 'Processing' 
        AND archived = 0 
        AND YEAR(recieve_at) = $selected_year
    GROUP BY 
        DATE_FORMAT(recieve_at, '%Y-%m')
    ORDER BY 
        DATE_FORMAT(recieve_at, '%m')"; // Ensure the months are ordered correctly
$result_monthly_processing_clients = $conn->query($sql_monthly_processing_clients);

$monthly_processing_clients_data = [];
while ($row = $result_monthly_processing_clients->fetch_assoc()) {
    $monthly_processing_clients_data[$row['month']] = $row['total'];
}

// Fetch data for the monthly accepted appointments
$sql_monthly_accepted_clients = "
    SELECT 
        DATE_FORMAT(accepted_at, '%M') AS month, 
        COUNT(*) AS total 
    FROM 
        appointments 
    WHERE 
        status = 'Accepted' 
        AND archived = 0 
        AND YEAR(accepted_at) = $selected_year
    GROUP BY 
        DATE_FORMAT(accepted_at, '%Y-%m')
    ORDER BY 
        DATE_FORMAT(accepted_at, '%m')"; // Ensure the months are ordered correctly
$result_monthly_accepted_clients = $conn->query($sql_monthly_accepted_clients);

$monthly_accepted_clients_data = [];
while ($row = $result_monthly_accepted_clients->fetch_assoc()) {
    $monthly_accepted_clients_data[$row['month']] = $row['total'];
}

// Fetch data for the monthly approved appointments
$sql_monthly_approved_clients = "
    SELECT 
        DATE_FORMAT(approve_at, '%M') AS month, 
        COUNT(*) AS total 
    FROM 
        appointments 
    WHERE 
        status = 'Approved' 
        AND archived = 1
        AND paid = 1
        AND YEAR(approve_at) = $selected_year
    GROUP BY 
        DATE_FORMAT(approve_at, '%Y-%m')
    ORDER BY 
        DATE_FORMAT(approve_at, '%m')"; // Ensure the months are ordered correctly
$result_monthly_approved_clients = $conn->query($sql_monthly_approved_clients);

$monthly_approved_clients_data = [];
while ($row = $result_monthly_approved_clients->fetch_assoc()) {
    $monthly_approved_clients_data[$row['month']] = $row['total'];
}

// Fetch data for the monthly notaccepted appointments
$sql_monthly_declined_clients = "
    SELECT 
        DATE_FORMAT(decline_at, '%M') AS month, 
        COUNT(*) AS total 
    FROM 
        appointments 
    WHERE 
        status = 'Declined' 
        AND archived = 1
        AND YEAR(decline_at) = $selected_year
    GROUP BY 
        DATE_FORMAT(decline_at, '%Y-%m')
    ORDER BY 
        DATE_FORMAT(decline_at, '%m')"; // Ensure the months are ordered correctly
$result_monthly_declined_clients = $conn->query($sql_monthly_declined_clients);

$monthly_declined_clients_data = [];
while ($row = $result_monthly_declined_clients->fetch_assoc()) {
    $monthly_declined_clients_data[$row['month']] = $row['total'];
}

// Fetch the data for the Appointment Data according to the selected year
$sql_appointment_data_year = "
    SELECT 
        DATE_FORMAT(approve_at, '%Y') AS year, 
        COUNT(*) AS total 
    FROM 
        appointments 
    WHERE 
        YEAR(approve_at) AND archived = 1 AND paid = 1 = $selected_year 
        OR YEAR(recieve_at) = $selected_year
        OR YEAR(accepted_at) = $selected_year
        OR YEAR(decline_at) = $selected_year
    GROUP BY 
        DATE_FORMAT(approve_at, '%Y')
    ORDER BY 
        DATE_FORMAT(approve_at, '%Y')";
$result_appointment_data_year = $conn->query($sql_appointment_data_year);

$appointment_data_year = [];
while ($row = $result_appointment_data_year->fetch_assoc()) {
    $appointment_data_year[$row['year']] = $row['total'];
}
?>