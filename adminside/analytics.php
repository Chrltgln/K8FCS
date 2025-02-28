<?php
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include 'processes/analytics-logic.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Analytics</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .charts-card {
            flex: 1;
        }
    </style>
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
                <h2>Analytics</h2>
            </div>
            <div class="container">
                <div class="charts-container">
                    <div class="charts-card">
                        <h1 class="chart-title">USER ACCOUNTS</h1>
                        <h2 style="text-align: center;">
                            <?php echo $users_data['total_clients'] + $users_data['total_employees'] + $users_data['total_admins']; ?>
                        </h2>
                    </div>
                    <div class="charts-card">
                        <h1 class="chart-title">PENDING CLIENTS</h1>
                        <h2 style="text-align: center;">
                            <?php echo $appointments_data['total_processing'] + $appointments_data['total_accepted']; ?>
                        </h2>
                    </div>


                </div>
            </div>
            <div class="container">
                <div class="filter-download-container">
                    <div class="report-filter">

                        <select class="custom-select" id="analytics-duration" name="analytics-duration"
                            onchange="updateAnalytics()">
                            <option value="">Select Duration</option>
                            <option value="daily" <?php if ($duration == 'daily')
                                echo 'selected'; ?>>Daily</option>
                            <option value="1-week" <?php if ($duration == '1-week')
                                echo 'selected'; ?>>1 Week</option>
                            <option value="1-month" <?php if ($duration == '1-month')
                                echo 'selected'; ?>>1 Month</option>
                            <option value="3-months" <?php if ($duration == '3-months')
                                echo 'selected'; ?>>3 Months
                            </option>
                            <option value="6-months" <?php if ($duration == '6-months')
                                echo 'selected'; ?>>6 Months
                            </option>
                            <option value="1-year" <?php if ($duration == '1-year')
                                echo 'selected'; ?>>1 Year</option>
                            <option value="custom" <?php if ($duration == 'custom')
                                echo 'selected'; ?>>Custom Range
                            </option>
                        </select>
                    </div>
                    <button onclick="displayAll()" class="btn" id="display_all_button_analytics">Display All</button>
                </div>
                <div class="charts-container">
                    <div class="charts-card-scroll">
                        <h2 class="chart-title">USERS</h2>
                        <div id="users-chart-data">
                            <div id="pie-chart"></div>
                        </div>
                    </div>
                    <div class="charts-card-scroll">
                        <h2 class="chart-title">APPOINTMENTS</h2>
                            <div id="users-chart-data">
                                <div id="appointments-chart"></div>
                            </div>
                    </div>
                </div>
                <div>
                    <h2>Appointments Data</h2>

                    <div class="filter-download-container">
                        <form method="GET" action="analytics.php">
                            <label for="appointments-data">Select year:</label>
                            <select class="custom-select" id="appointments-data" name="year"
                                onchange="this.form.submit()">
                                <?php
                                $currentYear = date("Y");
                                for ($year = $currentYear; $year >= 2010; $year--) {
                                    echo '<option value="' . $year . '"';
                                    if ($selected_year == $year)
                                        echo ' selected';
                                    echo '>' . $year . '</option>';
                                }
                                ?>
                            </select>
                        </form>
                    </div>
                    <div class="charts-container">
                        <div class="charts-card-scroll">
                            <h2 class="chart-title">PROCESSING</h2>
                            <div id="charts-card-appointments-data">
                                <div id="appointment-processing-chart-monthly"></div>
                            </div>
                        </div>
                        <div class="charts-card-scroll">
                            <h2 class="chart-title">ACCEPTED</h2>
                            <div id="charts-card-appointments-data">
                                <div id="appointment-accepted-chart-monthly"></div>
                            </div>
                        </div>
                        <div class="charts-card-scroll">
                            <h2 class="chart-title">PAID</h2>
                            <div id="charts-card-appointments-data">
                                <div id="appointment-paid-chart-monthly"></div>
                            </div>
                        </div>
                        <div class="charts-card-scroll">
                            <h2 class="chart-title">DECLINED</h2>
                            <div id="charts-card-appointments-data">
                                <div id="appointment-notaccepted-chart-monthly"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </main>
    <!-- End Main -->
    </div>
    <script>
        // BAR CHART FOR USERS
        const totalClient = <?php echo $users_data['total_clients']; ?>;
        const totalEmployee = <?php echo $users_data['total_employees']; ?>;
        const totalAdmin = <?php echo $users_data['total_admins']; ?>;

        // BAR CHART FOR APPOINTMENTS
        const totalProcessing = <?php echo $appointments_data['total_processing']; ?>;
        const totalAccepted = <?php echo $appointments_data['total_accepted']; ?>;
        const totalDeclined = <?php echo $appointments_data['total_declined']; ?>;
        const totalApproved = <?php echo $appointments_data['total_approved']; ?>;
        const totalNotContinued = <?php echo $appointments_data['total_not_continued']; ?>;

        // BAR CHART FOR MONTHLY PROCESSING APPOINTMENTS
        const totalMonthlyProcessing = <?php echo json_encode($monthly_processing_clients_data); ?>;

        // BAR CHART FOR MONTHLY ACCEPTED APPOINTMENTS
        const totalMonthlyAccepted = <?php echo json_encode($monthly_accepted_clients_data); ?>;

        // BAR CHART FOR MONTHLY APPROVED APPOINTMENTS
        const totalMonthlyApprovedWithPayment = <?php echo json_encode($monthly_approved_clients_data); ?>;

        // BAR CHART FOR MONTHLY NOT ACCEPTED APPOINTMENTS
        const totalMonthlyNotAcceptedClient = <?php echo json_encode($monthly_declined_clients_data); ?>;


    </script>
    <script src="assets/js/charts.js"></script>
    <script src="assets/js/script.js"></script>

</body>

</html>