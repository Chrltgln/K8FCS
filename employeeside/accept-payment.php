<?php
include '../settings/config.php';
include '../settings/authenticate.php';
checkUserRole(['Employee']);
include 'update-payment-status.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Payment</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/checkpayment.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- script js for search -->
    <script src="assets/js/searchPayment.js" defer></script>
    <script src="../assets/js/dropdown.js" defer></script>
    <script src="../assets/js/hamburger.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="assets/js/main.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Include SweetAlert2 -->
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
            <h1 class="appointments-title">Check Payment</h1>
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
            <div class="appointments-list" id="check-payment-appointments-list">
                <?php
                // Fetch appointments with status 'Accepted' or 'Approved' and not archived
                $query = "SELECT * FROM appointments WHERE status = 'Approved' AND archived = 0";
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
                                            <button type="button" class="details-button approve-button" data-action="approve"><i class="fas fa-check"></i> Accept </button>
                                            <button type="button" class="details-button decline-button" data-action="decline"><i class="fas fa-times"></i> Decline </button>
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
            </div>
        </div>
    </main>
    <div class="loading-overlay"></div>
    <div class="loading-spinner"></div>
    <script>
        document.querySelectorAll('.approve-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const form = button.closest('form');
                const actionInput = form.querySelector('.action-input');
                actionInput.value = button.getAttribute('data-action');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to accept this payment.",
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
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.querySelector('.loading-overlay').style.display = 'block';
                        document.querySelector('.loading-spinner').style.display = 'block';
                        fetch('update-payment-status.php', {
                            method: 'POST',
                            body: new FormData(form)
                        })
                        .then(response => response.text())
                        .then(data => {
                            document.querySelector('.loading-overlay').style.display = 'none';
                            document.querySelector('.loading-spinner').style.display = 'none';
                            Swal.fire({
                                title: 'Success!',
                                text: 'Payment has been accepted.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                didOpen: () => {
                                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                                },
                                didClose: () => {
                                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                                }
                            }).then(() => {
                                window.location.href = 'archives.php';
                            });
                        })
                        .catch(error => {
                            document.querySelector('.loading-overlay').style.display = 'none';
                            document.querySelector('.loading-spinner').style.display = 'none';
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an error accepting the payment.',
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
            });
        });

        document.querySelectorAll('.decline-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const form = button.closest('form');
                const actionInput = form.querySelector('.action-input');
                const reasonInput = form.querySelector('.reason-input');
                actionInput.value = button.getAttribute('data-action');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to decline this payment.",
                    input: 'text',
                    inputPlaceholder: 'Briefly state the reason for declining',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#218838',
                    cancelButtonColor: '#585a5e',
                    confirmButtonText: 'Yes',
                    preConfirm: (reason) => {
                        reasonInput.value = reason;
                    },
                    didOpen: () => {
                        document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                    },
                    didClose: () => {
                        document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.querySelector('.loading-overlay').style.display = 'block';
                        document.querySelector('.loading-spinner').style.display = 'block';
                        fetch('update-payment-status.php', {
                            method: 'POST',
                            body: new FormData(form)
                        })
                        .then(response => response.text())
                        .then(data => {
                            document.querySelector('.loading-overlay').style.display = 'none';
                            document.querySelector('.loading-spinner').style.display = 'none';
                            Swal.fire({
                                title: 'Success!',
                                text: 'Payment has been declined.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                didOpen: () => {
                                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                                },
                                didClose: () => {
                                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                                }
                            }).then(() => {
                                window.location.href = 'archives.php';
                            });
                        })
                        .catch(error => {
                            document.querySelector('.loading-overlay').style.display = 'none';
                            document.querySelector('.loading-spinner').style.display = 'none';
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an error declining the payment.',
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
            });
        });

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