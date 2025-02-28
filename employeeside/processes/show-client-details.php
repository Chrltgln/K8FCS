<?php

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
?>