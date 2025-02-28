<?php
// Check if the user is logged in and has the admin role
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';
include 'dashboard/index/total-client-user.php';
include 'dashboard/index/total-employee-user.php';
include 'dashboard/index/total-pending-appointments.php';
include 'dashboard/index/total-approve-appointments.php';

// Example connection code (make sure it's correct)
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the number of items per page
$items_per_page = 5;

// Get the current page number from the URL (default is 1 if not set)
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// Calculate the offset for the SQL query
$offset = ($page - 1) * $items_per_page;

// Count the total number of processing records
$sql_count = "SELECT COUNT(*) as total FROM appointments WHERE status = 'Processing'";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_records = $row_count['total'];

// Calculate the total number of pages
$total_pages = ceil($total_records / $items_per_page);

// Fetch the processing data for the current page
$sql = "SELECT clientname, status, form_type,appointment_date,appointment_time FROM appointments WHERE status = 'Processing' LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($sql);

// Store the fetched data
$processing_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['appointment_date'] = date('m-d-Y', strtotime($row['appointment_date']));
        $row['appointment_time'] = date('h:i A', strtotime($row['appointment_time']));
        $processing_data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Montserrat Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body>
    <div class="grid-container">

        <!-- Header -->
        <?php include 'required/header.php'; ?>

        <!-- Sidebar -->
        <?php include 'required/sidebar.php'; ?>

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <h2>Dashboard</h2>
            </div>

            <div class="main-cards">
                <div class="card">
                    <div class="card-inner">
                        <h3>Client</h3>
                        <span class="material-icons-outlined">inventory_2</span>
                    </div>
                    <h1><?php echo $total_clients; ?></h1>
                </div>

                <div class="card">
                    <div class="card-inner">
                        <h3>Employee</h3>
                        <span class="material-icons-outlined">category</span>
                    </div>
                    <h1><?php echo $total_employees; ?></h1>
                </div>

                <div class="card">
                    <div class="card-inner">
                        <h3>Pending</h3>
                        <span class="material-icons-outlined">
                            timer
                        </span>
                    </div>
                    <h1><?php echo $total_pending; ?></h1>
                </div>

                <div class="card">
                    <div class="card-inner">
                        <h3>Approved</h3>
                        <span class="material-icons-outlined">check</span>
                    </div>
                    <h1><?php echo $total_approved; ?></h1>
                </div>
            </div>

            <div class="charts">
                <div class="charts-card" id="charts1">
                    <h2 class="chart-title" id="index-processing-chart-title">Processing List</h2>
                    <div class="table-wrapper" >
                    <div class="table" id="index-processing-table">
                        <div class="table-header" id="index-table-header">
                            <div class="header-item">Client Name</div>
                            <div class="header-item">Appointment Date</div>
                            <div class="header-item">Appointment Time</div>
                            <div class="header-item">Form Type</div>
                            <div class="header-item">Status</div>
                        </div>
                        <?php if (!empty($processing_data)): ?>
                            <?php foreach ($processing_data as $item): ?>
                                <div class="table-row">
                                    <div class="row-item"><?php echo $item['clientname']; ?></div>
                                    <div class="row-item"><?php echo $item['appointment_date']; ?></div>
                                    <div class="row-item"><?php echo $item['appointment_time']; ?></div>
                                    <div class="row-item">
                                        <?php
                                        $formattedFormType = ucwords(str_replace(['_', '-'], ' ', $item['form_type']));
                                        if (stripos($item['form_type'], 'orcr') !== false) {
                                            $formattedFormType = str_ireplace('orcr', 'ORCR', $formattedFormType);
                                        }
                                        echo $formattedFormType;
                                        ?>
                                    </div>
                                    <div class="row-item">
                                        <span class="status-indicator <?php echo strtolower($item['status']); ?>"></span>
                                        <?php echo $item['status']; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No processing data found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                    <!-- Pagination -->
                    <div class="pagination" id="index-processing-list-pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>">Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>">Next</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="charts-card" id="charts2">
                    <h2 class="chart-title" id="index-report-chart-title">Reports</h2>
                    <div id="bar-chart"></div> <!-- New div for the bar chart -->
                </div>
            </div>
        </main>
        <!-- End Main -->

    </div>

    <!-- Scripts -->
    <script src="assets/js/charts.js"></script>
    <script src="assets/js/script.js"></script>
    
    
    <script>
        const totalClient = <?php echo $total_clients; ?>;
        const totalEmployee = <?php echo $total_employees; ?>;
        const totalApproved = <?php echo $total_approved; ?>;
        const totalDeclined = <?php echo $total_pending; ?>;
    </script>

</body>

</html>