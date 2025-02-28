<?php
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

// Check if the user requested a PDF
if (isset($_GET['generate_pdf'])) {
    $data = [];
    foreach ($form_fields[$form_type] as $section => $fields) {
        foreach ($fields as $field) {
            $data[] = [
                'section' => $section,
                'field' => $field,
                'value' => $details[$field] ?? 'N/A'
            ];
        }
    }

    $clientName = $details['first_name'] . ' ' . $details['last_name'];
    $reportTitle = $clientName . "'s Details";
    $timestamp = date('Ymd_His');
    $outputFileName = $clientName . '_details_' . $timestamp . '.pdf';

    include 'generate-pdf.php';
    generatePDF($data, $reportTitle, $outputFileName);
}
?>
