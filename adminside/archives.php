<?php
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';

// Pagination settings
$limit = 6;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Sorting and filtering settings
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'transaction_id';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$form_type = isset($_GET['form_type']) ? $_GET['form_type'] : '';

// Fetch only the approved and archived appointments once
$query = "SELECT a.*, 
          COALESCE(f.transaction_id, s.transaction_id, b.transaction_id) as form_transaction_id,
          COALESCE(f.year_model, s.year_model, b.year_model) as year_model,
          COALESCE(f.make, s.make, b.make) as make,
          COALESCE(f.type, s.type, b.type) as type,
          COALESCE(f.transmition_type, s.transmition_type, b.transmition_type) as transmition_type,
          COALESCE(f.first_name, s.first_name, b.first_name) as first_name,
          COALESCE(f.last_name, s.last_name, b.last_name) as last_name,
          COALESCE(f.middle_name, s.middle_name, b.middle_name) as middle_name,
          COALESCE(f.dob, s.dob, b.dob) as dob,
          COALESCE(f.place_of_birth, s.place_of_birth, b.place_of_birth) as place_of_birth,
          COALESCE(f.marital_status, s.marital_status, b.marital_status) as marital_status,
          COALESCE(f.present_address, s.present_address, b.present_address) as present_address,
          COALESCE(f.years_present_address, s.years_present_address, b.years_present_address) as years_present_address,
          COALESCE(f.ownership, s.ownership, b.ownership) as ownership,
          COALESCE(f.ownership_other, s.ownership_other, b.ownership_other) as ownership_other,
          COALESCE(f.previous_address, s.previous_address, b.previous_address) as previous_address,
          COALESCE(f.years_previous_address, s.years_previous_address, b.years_previous_address) as years_previous_address,
          COALESCE(f.tin_number, s.tin_number, b.tin_number) as tin_number,
          COALESCE(f.sss_number, s.sss_number, b.sss_number) as sss_number,
          COALESCE(f.contact_number_1, s.contact_number_1, b.contact_number_1) as contact_number_1,
          COALESCE(f.contact_number_2, s.contact_number_2, b.contact_number_2) as contact_number_2,
          COALESCE(f.email, s.email, b.email) as email,
          COALESCE(f.dependents, s.dependents, b.dependents) as dependents,
          COALESCE(f.mother_maiden_first_name, s.mother_maiden_first_name, b.mother_maiden_first_name) as mother_maiden_first_name,
          COALESCE(f.mother_maiden_last_name, s.mother_maiden_last_name, b.mother_maiden_last_name) as mother_maiden_last_name,
          COALESCE(f.mother_maiden_middle_name, s.mother_maiden_middle_name, b.mother_maiden_middle_name) as mother_maiden_middle_name,
          COALESCE(f.father_first_name, s.father_first_name, b.father_first_name) as father_first_name,
          COALESCE(f.father_last_name, s.father_last_name, b.father_last_name) as father_last_name,
          COALESCE(f.father_middle_name, s.father_middle_name, b.father_middle_name) as father_middle_name,
          COALESCE(f.employer_name, s.employer_name, b.employer_name) as employer_name,
          COALESCE(f.office_address, s.office_address, b.office_address) as office_address,
          COALESCE(f.office_number, s.office_number, b.office_number) as office_number,
          COALESCE(f.company_email, s.company_email, b.company_email) as company_email,
          COALESCE(f.position, s.position, b.position) as position,
          COALESCE(f.years_service, s.years_service, b.years_service) as years_service,
          COALESCE(f.monthly_income, s.monthly_income, b.monthly_income) as monthly_income,
          COALESCE(f.credit_cards, s.credit_cards, b.credit_cards) as credit_cards,
          COALESCE(f.credit_history, s.credit_history, b.credit_history) as credit_history,
          COALESCE(f.comments, s.comments, b.comments) as comments,
          COALESCE(s.relationship_borrower, b.relationship_borrower) as relationship_borrower,
          COALESCE(s.first_name_borrower, b.first_name_borrower) as first_name_borrower,
          COALESCE(s.last_name_borrower, b.last_name_borrower) as last_name_borrower,
          COALESCE(s.middle_name_borrower, b.middle_name_borrower) as middle_name_borrower,
          COALESCE(s.date_of_birth_borrower, b.date_of_birth_borrower) as date_of_birth_borrower,
          COALESCE(s.place_birth_borrower, b.place_birth_borrower) as place_birth_borrower,
          COALESCE(s.residential_address_borrower, b.residential_address_borrower) as residential_address_borrower,
          COALESCE(s.years_stay_borrower, b.years_stay_borrower) as years_stay_borrower,
          COALESCE(s.contact_number_borrower, b.contact_number_borrower) as contact_number_borrower,
          COALESCE(s.email_address_borrower, b.email_address_borrower) as email_address_borrower,
          COALESCE(s.tin_number_borrower, b.tin_number_borrower) as tin_number_borrower,
          COALESCE(s.sss_number_borrower, b.sss_number_borrower) as sss_number_borrower,
          s.mother_maiden_first_name_CoBorrower as mother_maiden_first_name_CoBorrower,
          s.mother_maiden_last_name_CoBorrower as mother_maiden_last_name_CoBorrower,
          s.mother_maiden_middle_name_CoBorrower as mother_maiden_middle_name_CoBorrower,
          s.father_first_name_CoBorrower as father_first_name_CoBorrower,
          s.father_last_name_CoBorrower as father_last_name_CoBorrower,
          s.father_middle_name_CoBorrower as father_middle_name_CoBorrower
          FROM appointments a 
          LEFT JOIN forms_secondhand_applicants f ON a.transaction_id = f.transaction_id AND a.form_type = 'second-hand'
          LEFT JOIN forms_sanglaorcr_applicants s ON a.transaction_id = s.transaction_id AND a.form_type = 'sangla-orcr'
          LEFT JOIN forms_brandnew_applicants b ON a.transaction_id = b.transaction_id AND a.form_type = 'brand-new'
          WHERE a.status IN ('Approved', 'Declined') AND a.archived = 1";
if ($form_type) {
    $query .= " AND a.form_type = ?";
}
$query .= " ORDER BY a.$sort $order LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
if ($form_type) {
    $stmt->bind_param("sii", $form_type, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

// Store results
$all_client_details = [];
while ($row = $result->fetch_assoc()) {
    $all_client_details[] = $row;
}

// Total count for pagination
$query_count = "SELECT COUNT(*) as total FROM appointments WHERE status IN ('Approved','Declined') AND archived = 1";
if ($form_type) {
    $query_count .= " AND form_type = '$form_type'";
}
$result_count = $conn->query($query_count);
$total_count = $result_count->fetch_assoc()['total'];

// Apply pagination
$client_details = $all_client_details;

// Define the fields for each form type
$form_fields = [
    'forms_brandnew_applicants' => [
        'Vehicle Details' => ['year_model', 'make', 'type', 'transmition_type'],
        'Personal Information' => ['first_name', 'last_name', 'middle_name', 'dob', 'place_of_birth', 'marital_status', 'present_address', 'years_present_address', 'ownership', 'ownership_other', 'previous_address', 'years_previous_address', 'tin_number', 'sss_number'],
        'Contact Information' => ['contact_number_1', 'contact_number_2', 'email'],
        'Family Information' => ['mother_maiden_first_name', 'mother_maiden_last_name', 'mother_maiden_middle_name', 'father_first_name', 'father_last_name', 'father_middle_name'],
        'Employment Details' => ['employer_name', 'office_address', 'office_number', 'company_email', 'position', 'years_service', 'monthly_income'],
        'Additional Information' => ['dependents', 'income_source', 'income_source_other', 'credit_cards', 'credit_history'],
        'Comments' => ['comments']
    ],
    'forms_sanglaorcr_applicants' => [
        'Vehicle Details' => ['year_model', 'make', 'type', 'transmition_type'],
        'Personal Information' => ['first_name', 'last_name', 'middle_name', 'dob', 'place_of_birth', 'marital_status', 'present_address', 'years_present_address', 'ownership', 'ownership_other', 'previous_address', 'years_previous_address', 'tin_number', 'sss_number'],
        'Contact Information' => ['contact_number_1', 'contact_number_2', 'email',],
        'Family Information' => ['mother_maiden_first_name', 'mother_maiden_last_name', 'mother_maiden_middle_name', 'father_first_name', 'father_last_name', 'father_middle_name'],
        'Borrower Information' => ['first_name_borrower', 'last_name_borrower', 'middle_name_borrower', 'date_of_birth_borrower', 'place_birth_borrower', 'residential_address_borrower', 'years_stay_borrower', 'contact_number_borrower', 'email_address_borrower', 'tin_number_borrower', 'sss_number_borrower'],
        'Co-Borrower Information' => ['mother_maiden_first_name_CoBorrower', 'mother_maiden_last_name_CoBorrower', 'mother_maiden_middle_name_CoBorrower', 'father_first_name_CoBorrower', 'father_last_name_CoBorrower', 'father_middle_name_CoBorrower'],
        'Comments' => ['comments']
    ],
    'forms_secondhand_applicants' => [
        'Vehicle Details' => ['year_model', 'make', 'type', 'transmition_type'],
        'Personal Information' => ['first_name', 'last_name', 'middle_name', 'dob', 'place_of_birth', 'marital_status', 'present_address', 'years_present_address', 'ownership', 'ownership_other', 'previous_address', 'years_previous_address', 'tin_number', 'sss_number'],
        'Contact Information' => ['contact_number_1', 'contact_number_2', 'email'],
        'Family Information' => ['mother_maiden_first_name', 'mother_maiden_last_name', 'mother_maiden_middle_name', 'father_first_name', 'father_last_name', 'father_middle_name'],
        'Employment Details' => ['employer_name', 'office_address', 'office_number', 'company_email', 'position', 'years_service', 'monthly_income'],
        'Borrower Information' => ['first_name_borrower', 'last_name_borrower', 'middle_name_borrower', 'date_of_birth_borrower', 'place_birth_borrower', 'residential_address_borrower', 'years_stay_borrower', 'contact_number_borrower', 'email_address_borrower', 'tin_number_coborrower', 'sss_number_coborrower'],
        'Comments' => ['comments']
    ]
];
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom styles here */
    </style>
</head>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>Archived Clients</h2>
            </div>

            <!-- Search Input -->
            <div class="container" id="details-container">
                <div id="archive-header-actions-container">
                    <div class="sort-options">
                        <div id="accepted-sort-by-container">
                            <form method="get" action="">
                                <label for="sort">Sort by:</label> <br>
                                <select class="accepted-sort-options" name="sort" id="accepted-sort"
                                    onchange="this.form.submit()">
                                    <option value="clientname" <?php if ($sort == 'clientname')
                                        echo 'selected'; ?>>Client
                                        Name</option>
                                    <option value="transaction_id" <?php if ($sort == 'transaction_id')
                                        echo 'selected'; ?>>Transaction ID</option>
                                    <option value="appointment_date" <?php if ($sort == 'appointment_date')
                                        echo 'selected'; ?>>Appointment Date</option>
                                    <option value="appointment_time" <?php if ($sort == 'appointment_time')
                                        echo 'selected'; ?>>Appointment Time</option>
                                </select>
                        </div>
                        <div id="accepted-sort-by-container">
                            <label for="order">Order:</label> <br>
                            <select class="accepted-sort-options" name="order" id="accepted-order"
                                onchange="this.form.submit()">
                                <option value="ASC" <?php if ($order == 'ASC')
                                    echo 'selected'; ?>>Ascending</option>
                                <option value="DESC" <?php if ($order == 'DESC')
                                    echo 'selected'; ?>>Descending</option>
                            </select>
                        </div>
                        </form>
                        <form method="get" action="">
                            <div id="accepted-sort-form-type-container">
                                <label for="form_type">Form Type:</label> <br>
                                <select class="accepted-sort-options" name="form_type" id="accepted-form_type"
                                    onchange="this.form.submit()">
                                    <option value="" <?php if ($form_type == '')
                                        echo 'selected'; ?>>All</option>
                                    <option value="brand-new" <?php if ($form_type == 'brand-new')
                                        echo 'selected'; ?>>
                                        Brand New</option>
                                    <option value="sangla-orcr" <?php if ($form_type == 'sangla-orcr')
                                        echo 'selected'; ?>>Sangla Orcr</option>
                                    <option value="second-hand" <?php if ($form_type == 'second-hand')
                                        echo 'selected'; ?>>Second Hand</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="search-container">
                        <br>
                        <input type="text" id="search-input" onkeyup="filterClients()"
                            placeholder="Search for clients...">
                    </div>
                </div>
                <hr>
                <div class="submission-info">
                    <?php if (empty($client_details)): ?>
                        <p style="color:white;">No Existing Appointments in the Archive</p>
                    <?php else: ?>
                        <?php foreach ($client_details as $client): ?>
                            <div class="info-card">
                                <h3 class="card-title" onclick="toggleCardContent(this)" style="display: flex; justify-content: space-between;">
                                    <span id="client-name-archives"><?php echo htmlspecialchars($client['clientname']); ?></span>
                                    <span id="transaction-id-archives" style="padding-right: 3rem;"><?php echo htmlspecialchars($client['transaction_id']); ?></span>
                                </h3>
                                <div class="card-content" style="display: none;">
                                    <div class="view-details-header-actions">
                                        <button class="details-action-button" onclick="window.location.href='view-client-files.php?email=<?php echo urlencode($client['email']); ?>'">View Files</button>
                                        <button class="details-action-button" onclick="window.print()">Print</button>
                                        <button class="details-action-button" onclick="downloadPDF()">Download</button>
                                    </div>
                                    <div class="view-details-header">
                                        <div class="view-details-header-title">
                                            <h4>Type of Application: <?php
                                            $formType = ucwords(str_replace('-', ' ', $client['form_type']));
                                            $formType = str_replace('Orcr', 'ORCR', $formType);
                                            echo $formType;
                                            ?></h4>
                                        </div>
                                        <div class="view-details-header-title">
                                            <h4>Appointment Date: <?php echo date('F j, Y', strtotime($client['appointment_date'])); ?></h4>
                                        </div>
                                        <div class="view-details-header-title">
                                            <h4>Appointment Time: <?php echo date('g:i:s A', strtotime($client['appointment_time'])); ?></h4>
                                        </div>
                                    </div>
                                    
                                    <?php
                                    $current_form_type = 'forms_' . strtolower(str_replace('-', '', $client['form_type'])) . '_applicants';
                                    if (isset($form_fields[$current_form_type])) {
                                        foreach ($form_fields[$current_form_type] as $section => $fields): ?>
                                            <button class="accordion-section"><?php echo $section; ?></button>
                                            <div class="accordion-content">
                                                <ul>
                                                    <?php foreach ($fields as $field): ?>
                                                        <li>
                                                            <?php
                                                            $fieldFormatted = ucwords(str_replace('_', ' ', $field));
                                                            if ($field == 'make') {
                                                                $fieldFormatted = 'Car Name';
                                                            } elseif ($field == 'type') {
                                                                $fieldFormatted = 'Type of Car';
                                                            } elseif ($field == 'transmition_type') {
                                                                $fieldFormatted = 'Transmission Type';
                                                            } elseif ($field == 'dob') {
                                                                $fieldFormatted = 'Date of Birth';
                                                            } elseif ($field == 'tin_number') {
                                                                $fieldFormatted = 'TIN Number';
                                                            } elseif ($field == 'sss_number') {
                                                                $fieldFormatted = 'SSS Number';
                                                            }
                                                            echo $fieldFormatted;
                                                            ?>: <br class="mobile-break"><?php echo htmlspecialchars($client[$field] ?? 'N/A'); ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endforeach;
                                    } else {
                                        echo "<p style=color:white;>No details available for this form type.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- Pagination Controls -->
                <div class="pagination" id="archives-pagination">
                    <?php
                    $total_pages = ceil($total_count / $limit);
                    for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>


        </main>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var acc = document.getElementsByClassName("accordion-section");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function () {
                // Close all other accordions
                for (var j = 0; j < acc.length; j++) {
                    if (acc[j] !== this) {
                        acc[j].classList.remove("active");
                        acc[j].nextElementSibling.style.display = "none";
                    }
                }
                // Toggle the clicked accordion
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                }
            });
        }
    });

    function toggleCardContent(element) {
        var card = element.parentElement;
        var allCards = document.getElementsByClassName("info-card");
        
        // Close all other cards
        for (var i = 0; i < allCards.length; i++) {
            if (allCards[i] !== card) {
                allCards[i].classList.remove("expanded");
                allCards[i].querySelector(".card-content").style.display = "none";
            }
        }
        
        // Toggle the clicked card
        card.classList.toggle("expanded");
        var content = card.querySelector(".card-content");
        if (content.style.display === "block") {
            content.style.display = "none";
        } else {
            content.style.display = "block";
        }
    }
</script>
</body>

</html>