<?php
include '../../settings/authenticate.php';
include '../../settings/config.php';

$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';

$query = "SELECT * FROM appointments WHERE status IN ('Approved', 'Declined')";
if (!empty($searchTerm)) {
    $query .= " AND (clientname LIKE '%$searchTerm%' OR transaction_id LIKE '%$searchTerm%')";
}
$result = $conn->query($query);

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
