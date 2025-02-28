<?php
include '../settings/config.php'; // Include your database connection file
include 'processes/activity-log-logic.php'; // Include the new logic file

// Define how many results you want per page
$results_per_page = 8;

// Find out the number of results stored in the database
$query = "SELECT COUNT(*) AS total FROM activity_log";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_results = $row['total'];

// Determine the total number of pages available
$total_pages = ceil($total_results / $results_per_page);

// Determine which page number visitor is currently on
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
if ($page > $total_pages)
    $page = $total_pages;

// Determine the SQL LIMIT starting number for the results on the displaying page
$start_from = ($page - 1) * $results_per_page;
if ($start_from < 0)
    $start_from = 0; // Ensure $start_from is non-negative

// Determine the selected role filter
$selected_role = isset($_GET['role']) ? $_GET['role'] : '';

// Fetch activity logs, user roles, and client names from the database with limit
$query = "SELECT al.user_email, al.action, al.timestamp, u.role, CONCAT(u.first_name, ' ', u.last_name) AS client_name 
          FROM activity_log al 
          JOIN users u ON al.user_email = u.email 
          WHERE ('$selected_role' = '' OR u.role = '$selected_role' OR ('$selected_role' = 'K8FCS System' AND al.action LIKE '%Automatically%'))
          AND ('$selected_role' != 'Employee' OR al.action NOT LIKE '%Automatically%')
          AND ('$selected_role' != 'Client' OR al.action NOT LIKE '%Automatically%')
          ORDER BY al.timestamp DESC 
          LIMIT $start_from, $results_per_page";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Activity Logs</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .legend p {
            display: inline-block;
            margin-right: 20px;
        }

        .legend span {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>Activity Logs</h2>
            </div>

            <div class="container">
                <!-- Filter Form -->
                <form method="GET" action="">
                    <label for="role">Filter by Role:</label>
                    <select name="role" id="select-role" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="Employee" <?php if ($selected_role === 'Employee')
                            echo 'selected'; ?>>Employee
                        </option>
                        <option value="Client" <?php if ($selected_role === 'Client')
                            echo 'selected'; ?>>Client</option>
                        <option value="K8FCS System" <?php if ($selected_role === 'System Automated')
                            echo 'selected'; ?>>
                            System Automated</option>
                    </select>
                </form>

                <!-- Legend -->
                <div class="legend">
                    <p><span style="background-color: lightgreen;"></span> Employee</p>
                    <p><span style="background-color: skyblue;"></span> Client</p>
                    <p><span style="background-color: #ff6666;"></span> System Automated</p>
                </div>

                <!-- Download PDF Button -->
                <div class="download-pdf" id="download-pdf-logs">
                    <a href="?generate_pdf=1&role=<?php echo urlencode($selected_role); ?>" class="btn">Download PDF</a>
                </div>

                <div class="table-wrapper" id="activity-log-wrapper">
                    <div class="table" id="activity-log-table">
                        <!-- Header Row -->
                        <div class="table-header">
                            <div class="header-item">Email</div>
                            <div class="header-item">Client Name</div>
                            <div class="header-item">Action</div>
                            <div class="header-item">Role</div>
                            <div class="header-item">Time Stamp</div>
                        </div>

                        <!-- Display each client -->
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <?php
                                if (strpos($row['action'], 'Automatically') !== false) {
                                    $row['user_email'] = 'System Automated';
                                    $row['client_name'] = 'System Automated';
                                    $row['role'] = 'System Automated';
                                }
                                ?>
                                <div class="table-row" style="color: 
                                    <?php
                                    if ($row['role'] === 'Employee')
                                        echo 'lightgreen';
                                    elseif ($row['role'] === 'Client')
                                        echo 'skyblue';
                                    elseif ($row['role'] === 'System Automated')
                                        echo '#ff6666';
                                    ?>">
                                    <div class="row-item"><?php echo htmlspecialchars($row['user_email']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($row['client_name']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($row['action']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($row['role']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($row['timestamp']); ?></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="table-row">
                                <div class="row-item" colspan="5">No logs available</div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <?php if ($total_results > 0): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&role=<?php echo urlencode($selected_role); ?>">Previous</a>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $page - 5);
                        $end_page = min($total_pages, $page + 4);
                        if ($end_page - $start_page < 9) {
                            $end_page = min($total_pages, $start_page + 9);
                        }
                        if ($end_page - $start_page < 9) {
                            $start_page = max(1, $end_page - 9);
                        }

                        // Ensure the end page does not exceed the total pages with data for the selected role
                        $query = "SELECT COUNT(*) AS total FROM activity_log al 
              JOIN users u ON al.user_email = u.email 
              WHERE ('$selected_role' = '' OR u.role = '$selected_role' OR ('$selected_role' = 'K8FCS System' AND al.action LIKE '%Automatically%'))
              AND ('$selected_role' != 'Employee' OR al.action NOT LIKE '%Automatically%')
              AND ('$selected_role' != 'Client' OR al.action NOT LIKE '%Automatically%')";
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_assoc($result);
                        $total_results_with_data = $row['total'];
                        $total_pages_with_data = ceil($total_results_with_data / $results_per_page);
                        $end_page = min($end_page, $total_pages_with_data);
                        ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&role=<?php echo urlencode($selected_role); ?>"
                                class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages_with_data): ?>
                            <a href="?page=<?php echo $page + 1; ?>&role=<?php echo urlencode($selected_role); ?>">Next</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <br>
        </main>
    </div>
    <script src="assets/js/script.js"></script>
</body>

</html>