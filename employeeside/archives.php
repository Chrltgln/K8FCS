<?php
include '../settings/authenticate.php';
checkUserRole(['Employee']); 
include '../settings/config.php';

$query = "SELECT * FROM appointments WHERE status IN ('Approved', 'Declined') AND archived = 1";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Appointments</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/employee.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- script js for search -->
    <script src="../assets/js/dropdown.js" defer></script>
    <script src="assets/js/main.js" defer></script>
    <script src="assets/js/searchArchives.js" defer></script>
    <script src="../assets/js/hamburger.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<?php include 'includes/navbar.php'; ?>
<main class="main-content">
    <div class="appointments-container">
        <h1 class="appointments-title">Archived Appointments</h1>
        <input type="text" id="search" placeholder="Search by client name or transaction ID...">
    </div>

    <div class="appointments-list-wrapper">
        <div class="appointments-header">
            <div id="client-name">Client Name</div>
            <div>Transaction ID</div>
            <div>Date and Time</div>
            <div>Current Status</div>
            <div class="actions-header">Actions</div>
        </div>
        <div class="appointments-list" id="appointments-list">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
            ?>
            <div class="appointment-item">
                <div class="appointment-details">
                    <div class="client-name">
                        <div class="responsive-header">Client Name: </div>
                        <?php echo htmlspecialchars($row['clientname']); ?>
                    </div>
                    <div class="appointment-date">
                        <div class="responsive-header">Date and Time: </div>
                        <?php echo htmlspecialchars($row['recieve_at']); ?>
                    </div>
                    <div class="appointment-id">
                        <div class="responsive-header">Transaction ID: </div>
                        <?php echo htmlspecialchars($row['transaction_id']); ?>
                    </div>
                    <div class="appointment-status">
                        <div class="responsive-header">Status: </div>
                        <span style="color: <?php echo $row['status'] == 'Declined' ? 'red' : ($row['status'] == 'Approved' ? 'green' : 'black'); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </div>
                    <div class="appointment-actions-container" id="archives-actions-container">
                        <form action="view-client-files.php" method="get">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                            <button type="submit" class="client-files-button">Files</button>
                        </form>
                        <?php if (!empty($row['form_type'])): ?>
                            <a href="view-details.php?transaction_id=<?php echo htmlspecialchars($row['transaction_id']); ?>&form_type=<?php echo htmlspecialchars($row['form_type']); ?>" class="details-button" id="archive-view-details">View Details</a>
                        <?php else: ?>
                            <span class="details-button">No Form Type</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
            }
        } else {
            echo '<div class="no-appointments-message">No Existing Appointments in the Archive.</div>';
            echo '<div class="no-appointments-icon"><i class="fas fa-archive"></i></div>';
        }
        ?>
        </div>
    </div>
</main>
<script>
     setTimeout(function() {
            location.reload();
        }, 60000);
</script>
<footer class="footer">
    <p style="text-align: center;">Copyright &copy; All rights reserved.</p>
</footer>
</body>

</html>