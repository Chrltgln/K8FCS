<?php
include '../../settings/config.php';
include '../../settings/authenticate.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ...existing code... -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';

$query = "SELECT * FROM appointments WHERE status = 'Approved' AND archived = 0";
if ($searchTerm) {
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
                    <span style="color: <?php echo $row['status'] == 'Accepted' ? 'green' : ($row['status'] == 'Approved' ? 'green' : 'black'); ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span>
                </div>
                <div class="appointment-actions-container">
                    <div class="button-container">
                        <form action="update-payment-status.php" method="post" class="appointment-form">
                            <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($row['transaction_id']); ?>">
                            <input type="hidden" name="action" class="action-input">
                            <input type="text" name="reason" class="reason-input" placeholder="Reason for decline" style="display: none;">
                            <button type="submit" class="details-button approve-button" data-action="approve"><i class="fas fa-check"></i> Accept </button>
                            <button type="submit" class="details-button decline-button" data-action="decline"><i class="fas fa-times"></i> Decline </button>
                        </form>
                    </div>
                </div>   
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="no-appointments-message">No Pending Payments Available.</div>';
    echo '<div class="no-appointments-icon"><i class="fas fa-credit-card"></i></div>';
}
?>
<script>
function confirmAction(button, action) {
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
            const form = button.closest('form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action';
            input.value = action;
            form.appendChild(input);
            form.submit();

            // Call send-email-payment.php
            fetch('../processes/send-email-receive-payment.php', {
                method: 'POST',
                body: new URLSearchParams({
                    transaction_id: form.querySelector('input[name="transaction_id"]').value,
                    action: action
                })
            });
        }
    });
}
</script>
</body>
</html>