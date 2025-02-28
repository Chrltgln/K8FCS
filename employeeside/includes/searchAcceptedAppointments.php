<?php
include '../../settings/authenticate.php';
include '../../settings/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';

$query = "SELECT * FROM appointments WHERE status IN ('Approved', 'Accepted') AND archived = 0";
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
                    <div class="appointment-type">
                        <?php echo htmlspecialchars($row['clientname']); ?> <br>
                        <span style="font-style: italic; color: green; font-size: 13px;">
                            <?php
                            $formType = str_replace('-', ' ', $row['form_type']);
                            echo ucwords(htmlspecialchars($formType));
                            ?>
                        </span>
                    </div>
                </div>

                <div class="appointment-id">
                    <div class="responsive-header">Transaction ID: </div>
                    <?php echo htmlspecialchars($row['transaction_id']); ?>
                </div>
                <div class="appointment-date">
                    <div class="responsive-header">Date and Time: </div>
                    <?php echo htmlspecialchars($row['recieve_at']); ?>
                </div>
                <div class="appointment-status">
                    <div class="responsive-header">Current Status: </div>
                    <span style="color: <?php echo $row['status'] == 'Accepted' ? 'orange' : 'black'; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span>
                </div>
                <div class="appointment-actions-container">
                    <div class="appointment-form">
                        <div class="button-container">
                            <button type="button" class="approve-button"
                                onclick="confirmApprove(<?php echo htmlspecialchars($row['id']); ?>, '<?php echo htmlspecialchars($row['form_type']); ?>')"><i
                                    class="fas fa-check"></i> Approve</button>

                            <button type="button" class="decline-button"
                                onclick="confirmDecline(<?php echo htmlspecialchars($row['id']); ?>)"><i
                                    class="fas fa-times"></i> Decline</button>
                        </div>
                        <div class="viewdetails-container">
                            <form action="view-client-files.php" method="get" class="file-button-container">
                                <input type="hidden" name="email"
                                    value="<?php echo htmlspecialchars($row['email']); ?>">
                                <button type="submit" class="client-files-button">Files</button>
                            </form>
                            <a href="view-details.php?transaction_id=<?php echo htmlspecialchars($row['transaction_id']); ?>&form_type=<?php echo htmlspecialchars($row['form_type']); ?>"
                                class="details-button" id="approved-view-details">Details</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php }
} else {
    echo '<div class="no-appointments-message">No Clients for Approval Available.</div>';
    echo '<div class="no-appointments-icon"><i class="fas fa-clipboard"></i></div>';
}
?>
<script>
function confirmAction(button, action, id, formType = '') {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#218838',
        cancelButtonColor: '#585a5e',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            // Call send-email-payment.php
            fetch('../processes/send-email-payment.php', {
                method: 'POST',
                body: new URLSearchParams({
                    appointment_id: id,
                    action: action,
                    form_type: formType
                })
            }).then(() => {
                location.reload();
            });
        }
    });
}
</script>
</body>
</html>