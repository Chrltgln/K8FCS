<?php
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';

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
        'Vehicle Details' => ['year_model', 'make', 'type', 'transmition_type'],
        'Personal Information' => ['first_name', 'last_name', 'middle_name', 'dob', 'marital_status', 'present_address', 'years_present_address'],
        'Contact Information' => ['contact_number_1', 'contact_number_2', 'email', 'tin_number', 'sss_number'],
        'Family Information' => ['mother_maiden_first_name', 'mother_maiden_last_name', 'father_first_name', 'father_last_name'],
        'Employment Details' => ['employer_name', 'office_address', 'office_number', 'company_email', 'position', 'years_service', 'monthly_income']
    ],
    'sangla-orcr' => [
        'Vehicle Details' => ['year_model', 'make', 'type', 'transmition_type'],
        'Personal Information' => ['first_name', 'last_name', 'middle_name', 'dob', 'place_of_birth', 'marital_status', 'present_address', 'years_present_address'],
        'Contact Information' => ['contact_number_1', 'contact_number_2', 'email', 'tin_number', 'sss_number'],
        'Family Information' => ['mother_maiden_first_name', 'mother_maiden_last_name', 'father_first_name', 'father_last_name'],
        'Borrower Information' => ['first_name_borrower', 'last_name_borrower', 'middle_name_borrower', 'date_of_birth_borrower', 'place_birth_borrower'],
        'Comments' => ['comments']
    ],
    'second-hand' => [
        'Vehicle Details' => ['year_model', 'make', 'type', 'transmition_type'],
        'Personal Information' => ['first_name', 'last_name', 'middle_name', 'dob', 'marital_status', 'present_address', 'years_present_address'],
        'Contact Information' => ['contact_number_1', 'contact_number_2', 'email', 'tin_number', 'sss_number'],
        'Family Information' => ['mother_maiden_first_name', 'mother_maiden_last_name', 'father_first_name', 'father_last_name'],
        'Employment Details' => ['employer_name', 'office_address', 'office_number', 'company_email', 'position', 'years_service', 'monthly_income'],
        'Borrower Information' => ['first_name_borrower', 'last_name_borrower', 'middle_name_borrower', 'date_of_birth_borrower', 'place_birth_borrower'],
        'Comments' => ['comments']
    ]
];

// Extract first name and last name from the details
$first_name = htmlspecialchars($details['first_name'] ?? 'N/A');
$last_name = htmlspecialchars($details['last_name'] ?? 'N/A');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Client Details</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleCard(cardId) {
            const cardContent = document.getElementById(cardId);
            cardContent.style.display = cardContent.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>Submission Details of: <?php echo $first_name . ' ' . $last_name; ?></h2>

            </div>
            <div class="container">
                <a href="pending-appointments.php" class="back-button">
                    <span class="material-icons-outlined">arrow_back</span> Back
                </a>
                <div class="container" id="details-container">
                    <div class="submission-info">

                        <div class="view-details-header">

                            <h2>Transaction ID: <br
                                    class="mobile-break"><?php echo htmlspecialchars($transaction_id); ?>
                            </h2>
                            <h2>Form Type: <br class="mobile-break">
                                <?php
                                $formattedFormType = ucwords(str_replace('-', ' ', $form_type));
                                $formattedFormType = str_replace('Orcr', 'ORCR', $formattedFormType);
                                echo $formattedFormType;
                                ?>
                            </h2>
                        </div>
                        <?php
                        // Loop through the defined fields for the selected form type
                        foreach ($form_fields[$form_type] as $section => $fields): ?>
                            <div class="info-card">
                                <h3 class="card-title"
                                    onclick="toggleCardforViewAllClientDetails('<?php echo strtolower(str_replace(' ', '-', $section)); ?>-card')">
                                    <?php echo $section; ?>
                                </h3>
                                <div class="card-content"
                                    id="<?php echo strtolower(str_replace(' ', '-', $section)); ?>-card"
                                    style="display: none;">
                                    <div class="view-details-container">
                                        <?php foreach ($fields as $field): ?>
                                            <p><?php echo ucwords(str_replace(['_', 'transmition type', 'tin number', 'sss number'], [' ', 'Transmission Type', 'TIN Number', 'SSS Number'], $field)); ?>:
                                                <?php echo htmlspecialchars($details[$field] ?? 'N/A'); ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>