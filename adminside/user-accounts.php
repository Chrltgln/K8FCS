<?php
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include 'processes/user-accounts-logic.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Accounts Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
                <h2>User Accounts Reports</h2>
            </div>

            <!-- Filter and Download PDF Button -->
            <div class="container">
                <div class="filter-download-container">
                    <div class="report-filter">
                        <label for="report-duration" id="label-select">Select Report Duration:</label>
                        <select class="custom-select" id="report-duration" name="report-duration" onchange="updateReport()">
                            <option value="daily" <?php if ($report_duration == 'daily') echo 'selected'; ?>>Daily Report</option>
                            <option value="1-week" <?php if ($report_duration == '1-week') echo 'selected'; ?>>1 Week Report</option>
                            <option value="1-month" <?php if ($report_duration == '1-month') echo 'selected'; ?>>1 Month Report</option>
                            <option value="3-months" <?php if ($report_duration == '3-months') echo 'selected'; ?>>3 Months Report</option>
                            <option value="6-months" <?php if ($report_duration == '6-months') echo 'selected'; ?>>6 Months Report</option>
                            <option value="1-year" <?php if ($report_duration == '1-year') echo 'selected'; ?>>1 Year Report</option>
                            <option value="custom" <?php if ($report_duration == 'custom') echo 'selected'; ?>>Custom Range</option>
                            <option value="all" <?php if ($report_duration == 'all') echo 'selected'; ?>>Display All</option>
                        </select>
                    </div>
                    <div class="download-pdf">
                    <a href="?generate_pdf=1&report_duration=<?php echo $report_duration; ?>" class="btn">Download
                            PDF</a>
                        <button type="button" class="btn" onclick="downloadAndPrintPDF()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <!-- Display the processing data -->
                <div class="table-wrapper" id="user-accounts-wrapper">
                    <div class="table" id="user-accounts-table">
                        <div class="table-header">
                            <div class="header-item">Full Name</div>
                            <div class="header-item">Address</div>
                            <div class="header-item">Date of Birth</div>
                            <div class="header-item">Phone</div>
                            <div class="header-item">Role</div>
                            <div class="header-item">Account Created</div>
                        </div>
                        <!-- Data Rows -->
                        <div class="table-row-container">
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($user = $result->fetch_assoc()): ?>
                                    <div class="table-row">
                                        <div class="row-item"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                        <div class="row-item"><?php echo htmlspecialchars($user['address']); ?></div>
                                        <div class="row-item"><?php echo date('F j, Y', strtotime($user['dob'])); ?></div>
                                        <div class="row-item"><?php echo htmlspecialchars($user['phone']); ?></div>
                                        <div class="row-item"><?php echo htmlspecialchars($user['role']); ?></div>
                                        <div class="row-item"><?php echo date('F j, Y g:i:sA', strtotime($user['created_at'])); ?></div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="table-row">
                                    <div class="row-item" colspan="6">No records found.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&report_duration=<?php echo $report_duration; ?>" class="<?php echo ($i === $current_page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>

            <br>
        </main>
        <!-- End Main -->
    </div>
    <script src="assets/js/script.js"></script>
</body>

</html>