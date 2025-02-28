<?php
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include 'processes/booked-account-report-logic.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Booked Account Report</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
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
                <h2>Booked Account Report</h2>
            </div>

            <!-- Filter and Download PDF Button -->
            <div class="container">
                <div class="filter-download-container">
                    <div class="report-filter">
                        <label for="report-duration">Select Report Duration:</label>
                        <select class="custom-select" id="report-duration" name="report-duration"
                            onchange="updateReport()">
                            <option value="daily" <?php if ($report_duration == 'daily')
                                echo 'selected'; ?>>Daily Report
                            </option>
                            <option value="1-week" <?php if ($report_duration == '1-week')
                                echo 'selected'; ?>>1 Week
                                Report</option>
                            <option value="1-month" <?php if ($report_duration == '1-month')
                                echo 'selected'; ?>>1 Month
                                Report</option>
                            <option value="3-months" <?php if ($report_duration == '3-months')
                                echo 'selected'; ?>>3
                                Months Report</option>
                            <option value="6-months" <?php if ($report_duration == '6-months')
                                echo 'selected'; ?>>6
                                Months Report</option>
                            <option value="1-year" <?php if ($report_duration == '1-year')
                                echo 'selected'; ?>>1 Year
                                Report</option>
                            <option value="custom" <?php if ($report_duration == 'custom')
                                echo 'selected'; ?>>Custom
                                Range</option>
                            <option value="all" <?php if ($report_duration == 'all')
                                echo 'selected'; ?>>Display All
                            </option>
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
                <div class="table-wrapper" id="booked-account-report-wrapper">
                    <div class="table"  id="booked-account-report-table">
                        <!-- Header Row -->
                        <div class="table-header">
                            <div class="header-item">Client Name</div>
                            <div class="header-item">Bank Partner</div>
                            <div class="header-item">Unit</div>
                            <div class="header-item">Term</div>
                            <div class="header-item">Amount Finance</div>
                            <div class="header-item">Date Booked</div>
                            <div class="header-item">Maturity</div>
                            <div class="header-item">Check Release</div>
                        </div>
                        <!-- Data Rows -->
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($client = $result->fetch_assoc()): ?>
                                <div class="table-row">
                                    <div class="row-item"><?php echo !empty($client['clientname']) ? htmlspecialchars($client['clientname']) : '<span style="color: orange;">Not Applicable</span>'; ?></div>
                                    <div class="row-item"><?php echo !empty($client['bank_partner']) ? htmlspecialchars($client['bank_partner']) : '<span style="color: orange;">Not Applicable</span>'; ?></div>
                                    <div class="row-item">
                                        <?php echo $client['status'] == 'Declined' ? '<span style="color: orange;">No data <br> Cause: Declined.</span>' : (!empty($client['unit']) ? htmlspecialchars($client['unit']) : '<span style="color: orange;">Not Applicable</span>'); ?>
                                    </div>
                                    <div class="row-item">
                                        <?php echo $client['status'] == 'Declined' ? '<span style="color: orange;">No data <br> Cause: Declined.</span>' : (!empty($client['term']) ? htmlspecialchars($client['term']) : '<span style="color: orange;">Not Applicable</span>'); ?>
                                    </div>
                                    <div class="row-item"><?php echo !empty($client['amount_finance']) ? htmlspecialchars($client['amount_finance']) : '<span style="color: orange;">N/A</span>'; ?></div>
                                    <div class="row-item"><?php echo !empty($client['appointment_date']) ? htmlspecialchars($client['appointment_date']) : '<span style="color: orange;">N/A</span>'; ?></div>
                                    <div class="row-item"><?php echo !empty($client['maturity']) ? htmlspecialchars($client['maturity']) : '<span style="color: orange;">N/A</span>'; ?></div>
                                    <div class="row-item"><?php echo !empty($client['check_release']) ? htmlspecialchars($client['check_release']) : '<span style="color: orange;">N/A</span>'; ?></div>
                                
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="table-row">
                                <div class="row-item" colspan="6">No records found.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&report_duration=<?php echo $report_duration; ?>"
                            class="<?php echo ($i === $current_page) ? 'active' : ''; ?>">
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