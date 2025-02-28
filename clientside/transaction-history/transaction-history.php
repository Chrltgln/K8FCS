<?php
include '../settings/config.php';

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit;
}

$email = $_SESSION['email'];

// SQL query to fetch all status updates
$sql = "
    SELECT a.status, a.recieve_at, su.status_type AS status_type, su.updated_at, a.transaction_id, a.form_type, a.archived, a.paid, a.remarks
    FROM appointments a
    LEFT JOIN status_updates su ON a.id = su.appointment_id
    WHERE a.email = ?
    ORDER BY su.updated_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$completed_transactions = [];
$processed_transaction_ids = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transaction_id = $row['transaction_id'];

        // Skip if the transaction ID has already been processed
        if (in_array($transaction_id, $processed_transaction_ids)) {
            continue;
        }

        $status_update = [
            'status_type' => $row['status_type'],
            'updated_at' => date("F j, Y, g:i A", strtotime($row['updated_at'])),
            'transaction_id' => $row['transaction_id'],
            'form_type' => $row['form_type'],
            'archived' => $row['archived'],
            'paid' => $row['paid'],
            'status' => $row['status'],
            'remarks' => $row['remarks']
        ];

        if ($row['status'] == 'Approved' && $row['form_type'] == 'second-hand' && $row['archived'] == 1 && $row['paid'] == 1) {
            $message = 'Your transaction is completed';
        } elseif ($row['status'] == 'Approved' && $row['form_type'] == 'brand-new' && $row['archived'] == 1 && $row['paid'] == 1) {
            $message = 'Your transaction is completed';
        } elseif ($row['status'] == 'Approved' && $row['form_type'] == 'sangla-orcr' && $row['archived'] == 1 && $row['paid'] == 2) {
            $message = 'Your transaction is completed';
        } elseif ($row['status'] == 'Approved' && $row['form_type'] == 'brand-new' && $row['archived'] == 1 && $row['paid'] == 0) {
            $message = 'Your application is now ' . $row['status_type'] . ' due to unable to process payment';
        } elseif ($row['status'] == 'Approved' && $row['form_type'] == 'sangla-orcr' && $row['archived'] == 1 && $row['paid'] == 0) {
            $message = 'Your application is now ' . $row['status_type'] . ' due to unable to process payment';
        } elseif ($row['status'] == 'Declined' && $row['form_type'] == 'brand-new' && $row['archived'] == 1 && is_null($row['paid'])) {
            $message = 'Your application has been declined.';
        } elseif ($row['status'] == 'Declined' && $row['form_type'] == 'sangla-orcr' && $row['archived'] == 1 && is_null($row['paid'])) {
            $message = 'Your application has been declined.';
        } elseif ($row['status'] == 'Declined' && $row['form_type'] == 'second-hand' && $row['archived'] == 1 && is_null($row['paid'])) {
            $message = 'Your application has been declined.';
        } else {
            continue; // Skip non-completed transactions
        }

        $completed_transactions[] = [
            'message' => $message,
            'updated_at' => $status_update['updated_at'],
            'transaction_id' => $status_update['transaction_id'],
            'form_type' => $status_update['form_type'],
            'remarks' => $status_update['remarks']
        ];

        // Mark the transaction ID as processed
        $processed_transaction_ids[] = $transaction_id;
    }
}

$stmt->close();
$conn->close();
?>

<div class="tracker-details">
    <h4 class="tracker-title">Completed Transactions</h4>
    <?php if (empty($completed_transactions)): ?>
        <p class="tracker-text">No completed transactions available.</p>
    <?php else: ?>
        <?php foreach ($completed_transactions as $completed): ?>
            <div class="status-update" data-remarks="<?php echo htmlspecialchars($completed['remarks']); ?>">
                <span class="status-type"><?php echo $completed['message']; ?></span>
                <span class="status-timestamp"><?php echo $completed['updated_at']; ?></span>
                <span class="status-transaction-id"><?php echo $completed['transaction_id']; ?></span>
                <span class="status-form-type"><?php echo ucwords(str_replace('-', ' ', $completed['form_type'])); ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal to display remarks -->
<div id="remarksModal" class="modal">
    <div class="modal-content">
        <h3><strong>Remarks:</strong></h3>
        <span class="close">&times;</span>
        <p id="remarksText"></p>
    </div>
</div>

<style>
/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>

<script>
// JavaScript to handle the click event and display the remarks
document.addEventListener('DOMContentLoaded', function() {
    var statusUpdates = document.querySelectorAll('.status-update');
    var modal = document.getElementById('remarksModal');
    var modalContent = document.getElementById('remarksText');
    var span = document.getElementsByClassName('close')[0];

    statusUpdates.forEach(function(update) {
        update.addEventListener('click', function() {
            var remarks = this.getAttribute('data-remarks');
            modalContent.textContent = remarks;
            modal.style.display = 'block';
        });
    });

    span.onclick = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
});
</script>