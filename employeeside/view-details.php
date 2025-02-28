<?php
include '../settings/authenticate.php';
checkUserRole(['Employee']);
include '../settings/config.php';
include 'processes/view-details-logic.php';

// Get the transaction ID and form type from the query parameters
$transaction_id = $_GET['transaction_id'] ?? '';
$form_type = $_GET['form_type'] ?? '';

// Define the table names based on form type
$table_mapping = [
    'brand-new' => 'forms_brandnew_applicants',
    'sangla-orcr' => 'forms_sanglaorcr_applicants',
    'second-hand' => 'forms_secondhand_applicants'
];

// Check if the form type is valid
if (!array_key_exists($form_type, $table_mapping)) {
    die("Invalid form type.");
}

// Get the table name
$table_name = $table_mapping[$form_type];

// Fetch the detailed submission information from the appropriate table
$query = "SELECT * FROM $table_name WHERE transaction_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $transaction_id);
$stmt->execute();
$result = $stmt->get_result();
$details = $result->fetch_assoc();

// Define the fields for each form type
$form_fields = [
    'brand-new' => [
        'Personal Information' => ['first_name', 'last_name', 'middle_name', 'dob', 'marital_status', 'present_address', 'years_present_address'],
        'Vehicle Details' => ['year_model', 'make', 'type', 'transmition_type'],
        'Contact Information' => ['contact_number_1', 'contact_number_2', 'email', 'tin_number', 'sss_number'],
        'Family Information' => ['mother_maiden_first_name', 'mother_maiden_last_name', 'father_first_name', 'father_last_name'],
        'Employment Details' => ['employer_name', 'office_address', 'office_number', 'company_email', 'position', 'years_service', 'monthly_income'],
        'Borrower Information' => ['first_name_borrower', 'last_name_borrower', 'middle_name_borrower', 'date_of_birth_borrower', 'place_birth_borrower'],
        'Comments' => ['comments']
    ],
    'sangla-orcr' => [
        'Personal Information' => ['first_name', 'last_name', 'middle_name', 'dob', 'place_of_birth', 'marital_status', 'present_address', 'years_present_address'],
        'Vehicle Details' => ['year_model', 'make', 'type', 'transmition_type'],
        'Contact Information' => ['contact_number_1', 'contact_number_2', 'email', 'tin_number', 'sss_number'],
        'Family Information' => ['mother_maiden_first_name', 'mother_maiden_last_name', 'father_first_name', 'father_last_name'],
        'Employment Details' => ['employer_name', 'office_address', 'office_number', 'company_email', 'position', 'years_service', 'monthly_income'],
        'Borrower Information' => ['first_name_borrower', 'last_name_borrower', 'middle_name_borrower', 'date_of_birth_borrower', 'place_birth_borrower'],
    ],
    'second-hand' => [
        'Personal Information' => ['first_name', 'last_name', 'middle_name', 'dob', 'marital_status', 'present_address', 'years_present_address'],
        'Vehicle Details' => ['year_model', 'make', 'type', 'transmition_type'],
        'Contact Information' => ['contact_number_1', 'contact_number_2', 'email', 'tin_number', 'sss_number'],
        'Family Information' => ['mother_maiden_first_name', 'mother_maiden_last_name', 'father_first_name', 'father_last_name'],
        'Employment Details' => ['employer_name', 'office_address', 'office_number', 'company_email', 'position', 'years_service', 'monthly_income'],
        'Borrower Information' => ['first_name_borrower', 'last_name_borrower', 'middle_name_borrower', 'date_of_birth_borrower', 'place_birth_borrower'],
    ]
];
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Client Details</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/view-details.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="../assets/js/hamburger.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../assets/js/dropdown.js" defer></script>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                transform: scale(1.00);
                /* Changed scale to 100% */
            }
        }

        @media print {

            html,
            body {
                width: 100%;
                height: 100%;
                zoom: 1.00;
                /* Ensure 100% scale */
            }
        }

        @media print {
            @page {
                size: A4;
                /* Ensure A4 size */
            }
        }
    </style>
    <script>
        function toggleAccordion(panelId) {
            const panel = document.getElementById(panelId);
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }

        function removeTrendNotificationApp() {
            const isFirefox = typeof InstallTrigger !== 'undefined';
            if (isFirefox) {
                const observer = new MutationObserver((mutationsList, observer) => {
                    for (const mutation of mutationsList) {
                        if (mutation.type === 'childList') {
                            const trendNotificationApp = document.querySelector('.trend_notification_app_outer');
                            if (trendNotificationApp) {
                                console.log('Removing trend-notification-app div'); // Debugging: Log message
                                trendNotificationApp.remove();
                                observer.disconnect(); // Stop observing once the element is removed
                                break;
                            }
                        }
                    }
                });

                // Start observing the document body for added nodes
                observer.observe(document.body, { childList: true, subtree: true });
            }
        }

        document.addEventListener('DOMContentLoaded', removeTrendNotificationApp);
    </script>
</head>

<body>
    <?php include 'includes/navbar.php' ?>

    <div class="main-content">
        <div class="return-button-container" style="display: flex; justify-content: space-between;">
            <button type="button" class="return-button" onclick="window.location.href='pendingAppointment'">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>
        <div class="title-header">
            <h1 class="details-title">Appointment Details</h1>
            <?php if ($details): ?>
                <h3><strong>Transaction ID:</strong> <?php echo htmlspecialchars($details['transaction_id']); ?><br>
                    <strong>Type of Service :</strong> <?php echo htmlspecialchars($details['form_type']) ?>
                </h3>
            </div>
            <div class="print-download-container">
                <button type="button" class="print-button" onclick="downloadAndPrintPDF()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" class="download-button" onclick="downloadPDF()">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
            <div class="details-container">
                <?php foreach ($form_fields[$form_type] as $section => $fields): ?>
                    <button class="accordion"
                        onclick="toggleAccordion('<?php echo strtolower(str_replace(' ', '-', $section)); ?>-panel', this)"><?php echo $section; ?></button>
                    <div class="panel" id="<?php echo strtolower(str_replace(' ', '-', $section)); ?>-panel"
                        style="display: none;">
                        <form method="post" action="update_details.php">
                            <div class="details-content">
                                <?php
                                // Define custom labels for specific fields
                                $custom_labels = [
                                    'dob' => 'Date of Birth',
                                    'transmition_type' => 'Transmission Type'
                                ];
                                ?>
                                <?php foreach ($fields as $field): ?>
                                    <div class="form-group">
                                        <label for="<?php echo htmlspecialchars($field); ?>">
                                            <?php
                                            // Display custom label if it exists, otherwise generate label dynamically
                                            echo htmlspecialchars($custom_labels[$field] ?? ucfirst(str_replace('_', ' ', $field)));
                                            ?>:
                                        </label>
                                        <p id="<?php echo htmlspecialchars($field); ?>">
                                            <?php echo htmlspecialchars($details[$field] ?? 'N/A'); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No details found for this transaction.</p>
            <?php endif; ?>
        </div>

        <footer class="footer">
            <p style="text-align: center;">Copyright &copy; All rights reserved.</p>
        </footer>
        <script>
            let isTransitioning = false;

            function toggleAccordion(panelId, button) {
                if (isTransitioning) return;

                const panel = document.getElementById(panelId);
                isTransitioning = true;

                if (panel.classList.contains('show')) {
                    panel.classList.remove('show');
                    button.classList.remove('activeaccordion');
                    setTimeout(() => {
                        panel.style.display = 'none';
                        isTransitioning = false;
                    }, 400);
                } else {
                    panel.style.display = 'block';
                    setTimeout(() => {
                        panel.classList.add('show');
                        isTransitioning = false;
                    }, 10);
                    button.classList.add('activeaccordion');
                }
            }

            function downloadAndPrintPDF() {
                const url = '?transaction_id=<?php echo $transaction_id; ?>&form_type=<?php echo $form_type; ?>&generate_pdf=1';
                fetch(url)
                    .then(response => response.blob())
                    .then(blob => {
                        const url = URL.createObjectURL(blob);
                        const newWindow = window.open(url, '_blank');
                        if (newWindow) {
                            newWindow.onload = () => {
                                newWindow.focus();
                                newWindow.print();
                            };
                        } else {
                            console.error('Failed to open new window for printing.');
                        }
                    })
                    .catch(error => console.error('Error downloading PDF:', error));
            }

            function downloadPDF() {
                const url = '?transaction_id=<?php echo $transaction_id; ?>&form_type=<?php echo $form_type; ?>&generate_pdf=1';
                fetch(url)
                    .then(response => response.blob())
                    .then(blob => {
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        const lastName = '<?php echo $details['last_name']; ?>';
                        const transactionId = '<?php echo $details['transaction_id']; ?>';
                        link.download = `${lastName}_${transactionId}_Details.pdf`;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    })
                    .catch(error => console.error('Error downloading PDF:', error));
            }
        </script>
</body>

</html>