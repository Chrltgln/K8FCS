<?php
include '../settings/config.php';
include '../settings/authenticate.php';
checkUserRole(['Employee']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients for Approval</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/acceptedAppointment.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- script js for search -->
    <script src="assets/js/search.js" defer></script>
    <script src="../assets/js/dropdown.js" defer></script>
    <script src="../assets/js/hamburger.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="assets/js/main.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <style>
        select,
        .swal2-input {
            margin: 1em 2em 3px;
            width: 10rem;
            border: 1px solid hsl(0, 0%, 85%);
            color: inherit;
        }

        .swal2-html-container {
            overflow: hidden;
        }

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
            <h1 class="appointments-title">Clients for Approval</h1>
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
                // Fetch appointments with status 'Accepted' or 'Approved' and not archived
                $query = "SELECT * FROM appointments WHERE status = 'Accepted' AND archived = 0 AND (form_type = 'sangla-orcr' OR form_type = 'brand-new' OR form_type = 'second-hand')";
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
                                        <span style="color: green;">
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
                                    <span style="color: <?php echo $row['status'] == 'Accepted' ? 'green' : 'black'; ?>">
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
            </div>
        </div>
        </div>
    </main>
    <div class="loading-overlay"></div>
    <div class="loading-spinner"></div>
    <script>
        function openApproveModal(appointmentId, formType) {
            if (formType === 'sangla-orcr') {
                Swal.fire({
                    title: 'Approve Appointment',
                    html: `
                   <div class="swal-grid-container">
                        <div class="grid-input">
                            <label for="bank-partner">Select Bank Partner:</label>
                            <select id="bank-partner" class="swal2-input">
                                <option value=""></option>
                                <option value="JACCS">JACCS</option>
                                <option value="ORICO">ORICO</option>
                                <option value="Banco De Oro">Banco De Oro</option>
                                <option value="Security Bank">Security Bank</option>
                                <option value="MayBank">MayBank</option>
                            </select>
                        </div>
                        
                        <div class="grid-input">
                            <label for="term">Select Term:</label>
                            <select id="term" name="term" class="swal2-input" required>
                                <option value=""></option>
                                <option value="6">6 months</option>
                                <option value="12">12 months</option>
                                <option value="18">18 months</option>
                                <option value="24">24 months</option>
                                <option value="30">30 months</option>
                                <option value="36">36 months</option>
                                <option value="42">42 months</option>
                                <option value="48">48 months</option>
                                <option value="54">54 months</option>
                                <option value="60">60 months</option>
                            </select>
                        </div>
                        
                        <div class="grid-input">
                            <label for="amount-finance">Enter Amount Finance:</label>
                            <input type="number" id="amount-finance" name="amount_finance" class="swal2-input" required placeholder="e.g., 500000">
                        </div>
                        
                        <div class="grid-input">
                            <label for="maturity">Enter Maturity:</label>
                            <input type="date" id="maturity" name="maturity" class="swal2-input" required>
                        </div>
                        
                        <div class="grid-input">
                            <label for="check-release">Enter Check Release:</label>
                            <input type="date" id="check-release" name="check_release" class="swal2-input" required>
                        </div>
                    </div>
                        <div style="text-align: left; margin-top: 1rem;" >
                            <label for="approve-remarks"  style="text-align: left;">Remarks:</label> <br>
                            <textarea id="approve-remarks" class="swal2-textarea" placeholder="Enter your remarks here..."></textarea>
                        </div>
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const bankPartner = document.getElementById('bank-partner').value;
                        const term = document.getElementById('term').value;
                        const amountFinance = document.getElementById('amount-finance').value;
                        const maturity = document.getElementById('maturity').value;
                        const checkRelease = document.getElementById('check-release').value;
                        const remarks = document.getElementById('approve-remarks').value;

                        const today = new Date().toISOString().split('T')[0];

                        if (!bankPartner) {
                            Swal.showValidationMessage('Please select a bank partner.');
                            return false;
                        }

                        if (!term) {
                            Swal.showValidationMessage('Please select a term.');
                            return false;
                        }

                        if (!amountFinance) {
                            Swal.showValidationMessage('Please enter the amount finance.');
                            return false;
                        }

                        if (!maturity) {
                            Swal.showValidationMessage('Please enter the maturity date.');
                            return false;
                        }

                        if (maturity < today) {
                            Swal.showValidationMessage('Maturity date cannot be in the past.');
                            return false;
                        }

                        const maturityDate = new Date(maturity);
                        if (maturityDate.getDay() === 0 || maturityDate.getDay() === 7) {
                            Swal.showValidationMessage('Maturity date cannot be on a sunday.');
                            return false;
                        }

                        if (!checkRelease) {
                            Swal.showValidationMessage('Please enter the check release date.');
                            return false;
                        }

                        if (checkRelease < today) {
                            Swal.showValidationMessage('Check release date cannot be in the past.');
                            return false;
                        }

                        const checkReleaseDate = new Date(checkRelease);
                        if (checkReleaseDate.getDay() === 0 || checkReleaseDate.getDay() === 7) {
                            Swal.showValidationMessage('Check release date cannot be on a sunday.');
                            return false;
                        }

                        if (!remarks) {
                            Swal.showValidationMessage('Please enter your remarks.');
                            return false;
                        }

                        document.querySelector('.loading-overlay').style.display = 'block';
                        document.querySelector('.loading-spinner').style.display = 'block';

                        return fetch('processes/approve-sangla-orcr.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `appointment_id=${appointmentId}&bank_partner=${encodeURIComponent(bankPartner)}&remarks=${encodeURIComponent(remarks)}&term=${encodeURIComponent(term)}&amount_finance=${encodeURIComponent(amountFinance)}&maturity=${encodeURIComponent(maturity)}&check_release=${encodeURIComponent(checkRelease)}`
                        })
                            .then(response => {
                                document.querySelector('.loading-overlay').style.display = 'none';
                                document.querySelector('.loading-spinner').style.display = 'none';
                                if (!response.ok) {
                                    throw new Error(response.statusText)
                                }
                                return response.text()
                            })
                            .catch(error => {
                                document.querySelector('.loading-overlay').style.display = 'none';
                                document.querySelector('.loading-spinner').style.display = 'none';
                                Swal.showValidationMessage(`Request failed: ${error}`)
                            })
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    didOpen: () => {
                        document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                    },
                    didClose: () => {
                        document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Approved!',
                            text: 'The appointment has been approved.',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            } else {
                // Redirect to payment-ui.php for 'brand-new' or 'second-hand'
                window.location.href = `payment-ui.php?appointment_id=${appointmentId}&form_type=${formType}`;
            }
        }

        function openDeclineModal(appointmentId) {
            Swal.fire({
                title: 'Decline Appointment',
                html: `
               <div class="grid-input" id="decline-bank-partner">
                    <label for="bank-partner">Select Bank Partner:</label>
                    <select id="bank-partner" class="swal2-input">
                        <option value=""></option>
                        <option value="JACCS">JACCS</option>
                        <option value="ORICO">ORICO</option>
                        <option value="Banco De Oro">Banco De Oro</option>
                        <option value="Security Bank">Security Bank</option>
                        <option value="MayBank">MayBank</option>
                    </select>
                </div>
                <div style="text-align: left; margin-top: 1rem;">
                    <label for="decline-remarks">Remarks:</label>
                    <textarea id="decline-remarks" class="swal2-textarea" placeholder="Enter your remarks here..."></textarea>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const bankPartner = document.getElementById('bank-partner').value;
                    const remarks = document.getElementById('decline-remarks').value;

                    if (!bankPartner) {
                        Swal.showValidationMessage('Please select a bank partner.');
                        return false;
                    }

                    if (!remarks) {
                        Swal.showValidationMessage('Please enter your remarks.');
                        return false;
                    }

                    document.querySelector('.loading-overlay').style.display = 'block';
                    document.querySelector('.loading-spinner').style.display = 'block';

                    return fetch('processes/decline-appointment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `appointment_id=${appointmentId}&bank_partner=${encodeURIComponent(bankPartner)}&remarks=${encodeURIComponent(remarks)}`
                    })
                        .then(response => {
                            document.querySelector('.loading-overlay').style.display = 'none';
                            document.querySelector('.loading-spinner').style.display = 'none';
                            if (!response.ok) {
                                throw new Error(response.statusText)
                            }
                            return response.text()
                        })
                        .catch(error => {
                            document.querySelector('.loading-overlay').style.display = 'none';
                            document.querySelector('.loading-spinner').style.display = 'none';
                            Swal.showValidationMessage(`Request failed: ${error}`)
                        })
                },
                allowOutsideClick: () => !Swal.isLoading(),
                didOpen: () => {
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                },
                didClose: () => {
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Declined!',
                        text: 'The appointment has been declined.',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        function confirmApprove(appointmentId, formType) {
            Swal.fire({
                title: 'Are you sure?',
                html: "Do you want to approve this appointment?<br><br>Clicking Yes will redirect you to set the payment details.",
                icon: 'warning',
                confirmButtonColor: '#218838',
                cancelButtonColor: '#585a5e',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                didOpen: () => {
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                },
                didClose: () => {
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    openApproveModal(appointmentId, formType);
                }
            });
        }

        function confirmDecline(appointmentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to decline this appointment?",
                icon: 'warning',
                confirmButtonColor: '#218838',
                cancelButtonColor: '#585a5e',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                didOpen: () => {
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                },
                didClose: () => {
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    openDeclineModal(appointmentId);
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
    <script>
        setTimeout(function () {
            location.reload();
        }, 60000);
    </script>
    <footer class="footer">
        <p style="text-align: center;">Copyright &copy; All rights reserved.</p>
    </footer>
</body>

</html>