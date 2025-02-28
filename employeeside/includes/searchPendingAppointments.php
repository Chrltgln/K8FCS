<?php
include '../../settings/authenticate.php';
include '../../settings/config.php';
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

$query = "SELECT * FROM appointments WHERE status='Processing'";
if (!empty($searchTerm)) {
    $query .= " AND (clientname LIKE '%$searchTerm%' OR transaction_id LIKE '%$searchTerm%')";
}
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="appointment-item">';
        echo '<div class="appointment-details">';

        // Combine Client Name and Form Type
        echo '<div class="client-name-form-type">';
        echo '<div class="client-name"><div class="responsive-header">Client Name: </div>' . htmlspecialchars($row['clientname']) . '</div>';

        $form_type_display = '';
        switch ($row['form_type']) {
            case 'sangla-orcr':
                $form_type_display = 'Sangla ORCR';
                break;
            case 'brand-new':
                $form_type_display = 'Brand New';
                break;
            case 'second-hand':
                $form_type_display = 'Second Hand';
                break;
            default:
                $form_type_display = htmlspecialchars($row['form_type']);
                break;
        }

        echo '<div class="appointment-form-type" style="color: green; font-style: italic;"><div class="responsive-header">Form Type: </div>' . $form_type_display . '</div>';
        echo '</div>';

        // Display Client Email
        echo '<div class="client-email"><div class="responsive-header">Email: </div>' . htmlspecialchars($row['email']) . '</div>';

        // Reverse: Display Transaction ID first
        echo '<div class="appointment-id"><div class="responsive-header">Transaction ID: </div>' . htmlspecialchars($row['transaction_id']) . '</div>';

        // Reverse: Display Date and Time second
        echo '<div class="appointment-date"><div class="responsive-header">Date and Time: </div>' . htmlspecialchars($row['recieve_at']) . '</div>';

        echo '<div class="appointment-actions-container">';
        
        echo '<form method="post" action="processes/send-email-accepting-declining-application.php" class="appointment-form">';
        echo '<input type="hidden" name="appointment_id" value="' . htmlspecialchars($row['id']) . '">';
        echo '<div class="button-container">';
        echo '<button type="button" class="accept-button" onclick="confirmAction(this, \'accept\')"><i class="fas fa-check"></i> Accept</button>';
        echo '<button type="button" class="decline-button" onclick="confirmAction(this, \'decline\')"><i class="fas fa-times"></i> Decline</button>';
        echo '</div>';
        echo '</form>';
        echo '<div class="viewdetails-container">';
        echo '<form action="view-client-files.php" method="get" class="file-button-container">';
        echo '<input type="hidden" name="email" value="' . htmlspecialchars($row['email']) . '">';
        echo '<button type="submit" class="client-files-button" id="pending-files-button">Files</button>';
        echo '</form>'; 
        echo '<a href="view-details.php?transaction_id=' . htmlspecialchars($row['transaction_id']) . '&form_type=' . htmlspecialchars($row['form_type']) . '" class="details-button" id="pending-details-button">Details</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="no-appointments-message">No Pending Appointments Available.</div>';
    echo '<div class="no-appointments-icon"><i class="fas fa-clipboard"></i></div>';
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
            input.name = action;
            form.appendChild(input);
            form.submit();

            // Call send-email-accepting-declining-application.php
            fetch('../processes/send-email-accepting-declining-application.php', {
                method: 'POST',
                body: new URLSearchParams({
                    appointment_id: form.querySelector('input[name="appointment_id"]').value,
                    action: action
                })
            });
        }
    });
}
</script>
</body>
</html>