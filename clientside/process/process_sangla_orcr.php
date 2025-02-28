<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../settings/config.php';
include '../../settings/generate-transaction.php';
require '../../vendor/autoload.php';

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: {$conn->connect_error}");
}

// Get form fields
$fields = [
    // Applicant Information
    'appointment_date' => $_POST['appointment_date'] ?? '',
    'appointment_time' => $_POST['appointment_time'] ?? '',
    'year_model' => $_POST['year_model'] ?? '',
    'transmition_type' => $_POST['transmition_type'] ?? '',
    'make' => $_POST['make'] ?? '',
    'type' => $_POST['type'] ?? '',
    'first_name' => $_POST['first_name'] ?? '',
    'last_name' => $_POST['last_name'] ?? '',
    'middle_name' => $_POST['middle_name'] ?? '',
    'dob' => $_POST['date_of_birth'] ?? '',
    'place_of_birth' => $_POST['place_of_birth'] ?? '',
    'marital_status' => $_POST['marital_status'] ?? '',
    'present_address' => $_POST['present_address'] ?? '',
    'years_present_address' => $_POST['years_present_address'] ?? '',
    'ownership' => $_POST['ownership'] ?? '',
    'ownership_other' => $_POST['ownership_other'] ?? '',
    'previous_address' => $_POST['previous_address'] ?? '',
    'years_previous_address' => $_POST['years_previous_address'] ?? '',
    'contact_number_1' => $_POST['contact_number_1'] ?? '',
    'contact_number_2' => $_POST['contact_number_2'] ?? '',
    'email' => $_POST['email'] ?? '',
    'tin_number' => $_POST['tin_number'] ?? '',
    'sss_number' => $_POST['sss_number'] ?? '',
    'dependents' => $_POST['dependents'] ?? '',
    'mother_maiden_first_name' => $_POST['mother_maiden_first_name'] ?? '',
    'mother_maiden_last_name' => $_POST['mother_maiden_last_name'] ?? '',
    'mother_maiden_middle_name' => $_POST['mother_maiden_middle_name'] ?? '',
    'father_first_name' => $_POST['father_first_name'] ?? '',
    'father_last_name' => $_POST['father_last_name'] ?? '',
    'father_middle_name' => $_POST['father_middle_name'] ?? '',

    // Primary Borrower
    'income_source' => $_POST['income_source'] ?? '',
    'income_source_other' => $_POST['income_source_other'] ?? '',
    'employer_name' => $_POST['employer_name'] ?? '',
    'office_address' => $_POST['office_address'] ?? '',
    'office_number' => $_POST['office_number'] ?? '',
    'company_email' => $_POST['company_email'] ?? '',
    'position' => $_POST['position'] ?? '',
    'years_service' => $_POST['years_service'] ?? '',
    'monthly_income' => $_POST['monthly_income'] ?? '',
    'credit_cards' => $_POST['credit_cards'] ?? '',
    'credit_history' => $_POST['credit_history'] ?? '',

    // Co Borrower or Spouse Information
    'relationship_borrower' => $_POST['relationship_borrower'] ?? '',
    'first_name_borrower' => $_POST['first_name_borrower'] ?? '',
    'last_name_borrower' => $_POST['last_name_borrower'] ?? '',
    'middle_name_borrower' => $_POST['middle_name_borrower'] ?? '',
    'date_of_birth_borrower' => $_POST['date_of_birth_borrower'] ?? '',
    'place_birth_borrower' => $_POST['place_birth_borrower'] ?? '',
    'residential_address_borrower' => $_POST['residential_address_borrower'] ?? '',
    'years_stay_borrower' => $_POST['years_stay_borrower'] ?? '',
    'contact_number_borrower' => $_POST['contact_number_borrower'] ?? '',
    'email_address_borrower' => $_POST['email_address_borrower'] ?? '',
    'tin_number_borrower' => $_POST['tin_number_borrower'] ?? '',
    'sss_number_borrower ' => $_POST['sss_number_borrower'] ?? '',
    'mother_maiden_first_name_CoBorrower' => $_POST['mother_maiden_first_name_CoBorrower'] ?? '',
    'mother_maiden_last_name_CoBorrower' => $_POST['mother_maiden_last_name_CoBorrower'] ?? '',
    'mother_maiden_middle_name_CoBorrower' => $_POST['mother_maiden_middle_name_CoBorrower'] ?? '',
    'father_first_name_CoBorrower' => $_POST['father_first_name_CoBorrower'] ?? '',
    'father_last_name_CoBorrower' => $_POST['father_last_name_CoBorrower'] ?? '',
    'father_middle_name_CoBorrower' => $_POST['father_middle_name_CoBorrower'] ?? '',
    
];

// Prepare SQL columns and placeholders for binding
$columns = implode(", ", array_keys($fields));
$placeholders = implode(", ", array_fill(0, count($fields), '?'));

// Prepare the SQL query for inserting or updating application details
$sql_applications = "INSERT INTO forms_sanglaorcr_applicants ($columns, transaction_id, ORCR_filename) 
                     VALUES ($placeholders, ?, ?)
                     ON DUPLICATE KEY UPDATE 
                     year_model=VALUES(year_model), make=VALUES(make), type=VALUES(type), transmition_type=VALUES(transmition_type), 
                     first_name=VALUES(first_name), last_name=VALUES(last_name), middle_name=VALUES(middle_name), dob=VALUES(dob), 
                     marital_status=VALUES(marital_status), present_address=VALUES(present_address), years_present_address=VALUES(years_present_address), 
                     ownership=VALUES(ownership), ownership_other=VALUES(ownership_other), previous_address=VALUES(previous_address), 
                     years_previous_address=VALUES(years_previous_address), contact_number_1=VALUES(contact_number_1), contact_number_2=VALUES(contact_number_2), 
                     email=VALUES(email), tin_number=VALUES(tin_number),sss_number=VALUES(sss_number), dependents=VALUES(dependents), mother_maiden_first_name=VALUES(mother_maiden_first_name), 
                     mother_maiden_last_name=VALUES(mother_maiden_last_name), mother_maiden_middle_name=VALUES(mother_maiden_middle_name), 
                     father_first_name=VALUES(father_first_name), father_last_name=VALUES(father_last_name), father_middle_name=VALUES(father_middle_name), 
                     income_source=VALUES(income_source), income_source_other=VALUES(income_source_other), employer_name=VALUES(employer_name), 
                     office_address=VALUES(office_address), office_number=VALUES(office_number), company_email=VALUES(company_email), 
                     position=VALUES(position), years_service=VALUES(years_service), monthly_income=VALUES(monthly_income), credit_cards=VALUES(credit_cards), 
                     credit_history=VALUES(credit_history), relationship_borrower=VALUES(relationship_borrower), first_name_borrower=VALUES(first_name_borrower), 
                     last_name_borrower=VALUES(last_name_borrower), middle_name_borrower=VALUES(middle_name_borrower), date_of_birth_borrower=VALUES(date_of_birth_borrower), 
                     place_birth_borrower=VALUES(place_birth_borrower), residential_address_borrower=VALUES(residential_address_borrower), 
                     years_stay_borrower=VALUES(years_stay_borrower), contact_number_borrower=VALUES(contact_number_borrower), 
                     email_address_borrower=VALUES(email_address_borrower), tin_number_borrower=VALUES(tin_number_borrower), sss_number_borrower=VALUES(sss_number_borrower),
                     appointment_date=VALUES(appointment_date), appointment_time=VALUES(appointment_time), ORCR_filename=VALUES(ORCR_filename)";

// Prepare the statement
$stmt_app = $conn->prepare($sql_applications);
if ($stmt_app === false) {
    die("Prepare failed: {$conn->error}");
}

// Collect values for binding
$values = array_map([$conn, 'real_escape_string'], array_values($fields));
$transaction_id = generateTransactionID(); // Assuming you have this function
$values[] = $transaction_id;  // Add the transaction ID to the values array

// Handle file upload
$client_name = $fields['first_name'] . ' ' . $fields['last_name'];
$email = $fields['email'];
$date = date('Y-m-d_H-i-s');

$ORCR_filenames = [];
$combined_dir = "../uploads/orcr/{$email}/{$transaction_id}"; // Updated directory structure

// Create directory if it doesn't exist
if (!file_exists($combined_dir)) {
    mkdir($combined_dir, 0777, true);
}

// Handle ORCR file upload
if (isset($_FILES['combined_file'])) {
    foreach ($_FILES['combined_file']['name'] as $key => $name) {
        if ($_FILES['combined_file']['error'][$key] === 0) {
            $ORCR_filename = "{$client_name}_ORCR_{$date}_{$key}." . pathinfo($name, PATHINFO_EXTENSION);
            $ORCR_filepath = "{$combined_dir}/{$ORCR_filename}";
            if (move_uploaded_file($_FILES['combined_file']['tmp_name'][$key], $ORCR_filepath)) {
                $ORCR_filenames[] = $ORCR_filename;
            }
        }
    }
}

// Convert filenames array to a string for storage
$ORCR_filenames_str = implode(',', $ORCR_filenames);

// Append filename to values array
$values[] = $ORCR_filenames_str;

// Bind all parameters
$stmt_app->bind_param(
    str_repeat('s', count($values)), // Bind as many 's' (strings) as there are values
    ...$values // Spread operator to pass the array as arguments
);

// Execute the statement and check for errors
if (!$stmt_app->execute()) {
    die("Error: {$stmt_app->error}");
}

// Insert into appointments table
$sql_appointments = "INSERT INTO appointments (clientname, transaction_id, email, form_type, appointment_date, appointment_time) 
                     VALUES (?, ?, ?, 'sangla-orcr', ?, ?)
                     ON DUPLICATE KEY UPDATE 
                     appointment_date=VALUES(appointment_date), appointment_time=VALUES(appointment_time)";
$stmt_appmt = $conn->prepare($sql_appointments);
if ($stmt_appmt === false) {
    die("Prepare failed: {$conn->error}");
}

// Client name and email for appointments table
$clientname = $fields['first_name'] . ' ' . $fields['last_name'];

// Bind parameters for appointments
$stmt_appmt->bind_param(
    'sssss',
    $clientname,                // clientname
    $transaction_id,            // transaction_id
    $fields['email'],           // email
    $fields['appointment_date'],// appointment_date
    $fields['appointment_time'] // appointment_time
);

// Execute statement for appointments
if (!$stmt_appmt->execute()) {
    die("Error: {$stmt_appmt->error}");
}

// Get the last inserted appointment ID
$appointment_id = $stmt_appmt->insert_id;

// Insert initial status update
$sql_status_update = "INSERT INTO status_updates (appointment_id, status, status_type) VALUES (?, ?, ?)";
$stmt_status = $conn->prepare($sql_status_update);

if ($stmt_status === false) {
    die("Prepare failed: {$conn->error}");
}

$initial_status = 'Application received';
$status_type = 'Processing';

$stmt_status->bind_param(
    'iss',
    $appointment_id, // appointment_id
    $initial_status, // status
    $status_type     // status_type
);

if (!$stmt_status->execute()) {
    die("Error: {$stmt_status->error}");
}

// Send an email to the client using send-email-wait-for-accept.php
include 'send-email-wait-for-accept.php';

$_SESSION['form_success'] = true;

// Redirect to the homepage with a success parameter using POST method
echo '<form id="successForm" action="../sangla-orcr.php" method="post">
        <input type="hidden" name="success" value="1">
      </form>
      <script type="text/javascript">
        sessionStorage.setItem("formSuccess", "true");
        document.getElementById("successForm").submit();
      </script>';

// Close connections
$stmt_app->close();
$stmt_appmt->close();
$stmt_status->close();
$conn->close();
?>