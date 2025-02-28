<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

include '../settings/authenticate.php';
checkUserRole(['Employee']);
include '../settings/config.php';
include '../settings/generate-transaction.php';

function logActivity($conn, $user_email, $action, $file_name) {
    $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_email, $action, $file_name);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Appointments</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/employee.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- script js for search -->
    <script src="assets/js/searchPending.js" defer></script>
    <script src="../assets/js/dropdown.js" defer></script>
    <script src="../assets/js/hamburger.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="assets/js/main.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .loading-spinner {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 45%;
        left: 47%;
        transform: translate(-50%, -50%);
        border: 8px solid #f3f3f3;
        border-radius: 50%;
        border-top: 8px solid #3498db;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        }

        /* Ensure the loader is centered on mobile devices */
        @media (max-width: 768px) {
        .loading-spinner {
            width: 40px;
            height: 40px;
            border-width: 6px;
            top: 45%; /* Move the spinner a bit up */
            left: 44%; /* Move the spinner a bit to the left */
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <main class="main-content">
        <div class="appointments-container">
            <h1 class="appointments-title">Pending Applications</h1>
            <input type="text" id="search" placeholder="Search by client name or transaction ID...">
        </div>

        <div class="appointments-list-wrapper">
            <div class="appointments-header">
                <div id="client-name">Client Name</div>
                <div>Email Address</div>
                <div>Transaction ID</div>
                <div>Time and Date</div>
                <div class="actions-header">Actions</div>
            </div>
            <div class="appointments-list" id="pending-appointments-list">
                <?php
                $client_id = isset($_GET['client_id']) ? $_GET['client_id'] : null;
                $query = "SELECT * FROM appointments WHERE status='Processing'";
                if ($client_id) {
                    $query .= " AND id = " . intval($client_id);
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

                        echo '<div class="appointment-form-type" style="color: green;">' . $form_type_display . '</div>';
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
            </div>
        </div>
        <div class="loading-overlay"></div>
        <div class="loading-spinner"></div>
    </main>
    <footer class="footer">
        <p style="text-align: center;">Copyright &copy; All rights reserved.</p>
    </footer>
    <script>
function confirmAction(button, action) {
    let swalOptions = {
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#218838',
        cancelButtonColor: '#585a5e',
        confirmButtonText: 'Yes',
        didOpen: () => {
            document.body.classList.remove('swal2-shown', 'swal2-height-auto');
        },
        didClose: () => {
            document.body.classList.remove('swal2-shown', 'swal2-height-auto');
        }
    };

    if (action === 'decline') {
        swalOptions.input = 'textarea';
        swalOptions.inputPlaceholder = 'Enter remarks here...';
        swalOptions.inputValidator = (value) => {
            if (!value) {
                return 'You need to write something!';
            }
        };
    }

    Swal.fire(swalOptions).then((result) => {
        if (result.isConfirmed) {
            const form = button.closest('form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = action;
            form.appendChild(input);

            if (action === 'decline') {
                const remarksInput = document.createElement('input');
                remarksInput.type = 'hidden';
                remarksInput.name = 'remarks';
                remarksInput.value = result.value;
                form.appendChild(remarksInput);
            }

            document.querySelector('.loading-overlay').style.display = 'block';
            document.querySelector('.loading-spinner').style.display = 'block';

            fetch('processes/send-email-accepting-declining-application.php', {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.text())
            .then(data => {
                document.querySelector('.loading-overlay').style.display = 'none';
                document.querySelector('.loading-spinner').style.display = 'none';
                Swal.fire({
                    title: 'Success!',
                    text: 'The appointment has been accepted.',
                    icon: 'success',
                    confirmButtonText: 'Okay',
                    didOpen: () => {
                        document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                    },
                    didClose: () => {
                        document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                    }
                }).then(() => {
                    window.location.href = 'acceptedAppointment.php';
                });
            })
            .catch(error => {
                document.querySelector('.loading-overlay').style.display = 'none';
                document.querySelector('.loading-spinner').style.display = 'none';
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an error processing the appointment.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    didOpen: () => {
                        document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                    },
                    didClose: () => {
                        document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                    }
                });
            });
        }
    });
}

 // Add event listener to remove classes when SweetAlert is shown or hidden
 document.addEventListener('DOMContentLoaded', () => {
            Swal.mixin({
                didOpen: () => {
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                },
                didClose: () => {
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                }
            });
        });
</script>
</body>

</html>
