<?php
// Check if the user is logged in and has the admin role
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';

// Get the current page number from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // Number of clients per page
$offset = ($page - 1) * $limit;

// Get the selected sort option from the query string, default to 'clientname' if not set
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'clientname';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Get the selected form type from the query string, default to '' if not set
$form_type = isset($_GET['form_type']) ? $_GET['form_type'] : '';

// Get the search query from the query string, default to '' if not set
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Get the total number of clients
$total_clients_query = "SELECT COUNT(*) as total FROM appointments WHERE status = 'Accepted' AND archived = 0";
if ($form_type) {
    $total_clients_query .= " AND form_type = '$form_type'";
}
if ($search_query) {
    $total_clients_query .= " AND (clientname LIKE '%$search_query%' OR transaction_id LIKE '%$search_query%')";
}
$total_clients_result = $conn->query($total_clients_query);
$total_clients_row = $total_clients_result->fetch_assoc();
$total_clients = $total_clients_row['total'];

// Calculate the total number of pages
$total_pages = ceil($total_clients / $limit);

// Fetch clients for the current page with sorting
$query = "SELECT * FROM appointments WHERE status = 'Accepted' AND archived = 0";
if ($form_type) {
    $query .= " AND form_type = '$form_type'";
}
if ($search_query) {
    $query .= " AND (clientname LIKE '%$search_query%' OR transaction_id LIKE '%$search_query%')";
}
$query .= " ORDER BY $sort $sort_order LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Pending Client</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .row-item .btn {
            display: inline-block;
            margin-right: 5px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="grid-container">
        <!-- Header -->
        <?php include 'required/header.php'; ?>
        <!-- Sidebar -->
        <?php include 'required/sidebar.php'; ?>

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <h2>Approve Clients</h2>
            </div>
            <div class="container">
                <div id="archive-header-actions-container">
                    <div class="sort-options">
                        <div id="accepted-sort-by-container">
                            <form method="get" action="">
                                <label for="sort">Sort by:</label> <br>
                                <select class="accepted-sort-options" name="sort" id="accepted-sort" onchange="this.form.submit()">
                                    <option value="clientname" <?php if ($sort == 'clientname') echo 'selected'; ?>>Client Name</option>
                                    <option value="transaction_id" <?php if ($sort == 'transaction_id') echo 'selected'; ?>>Transaction ID</option>
                                    <option value="appointment_date" <?php if ($sort == 'appointment_date') echo 'selected'; ?>>Appointment Date</option>
                                    <option value="appointment_time" <?php if ($sort == 'appointment_time') echo 'selected'; ?>>Appointment Time</option>
                                </select>
                        </div>
                        <div id="accepted-sort-by-container">
                            <label for="order">Order:</label> <br>
                            <select class="accepted-sort-options" name="order" id="accepted-order" onchange="this.form.submit()">
                                <option value="ASC" <?php if ($sort_order == 'ASC') echo 'selected'; ?>>Ascending</option>
                                <option value="DESC" <?php if ($sort_order == 'DESC') echo 'selected'; ?>>Descending</option>
                            </select>
                        </div>
                        </form>
                        <form method="get" action="">
                            <div id="accepted-sort-form-type-container">
                                <label for="form_type">Form Type:</label> <br>
                                <select class="accepted-sort-options" name="form_type" id="accepted-form_type" onchange="this.form.submit()">
                                    <option value="" <?php if ($form_type == '') echo 'selected'; ?>>All</option>
                                    <option value="brand-new" <?php if ($form_type == 'brand-new') echo 'selected'; ?>>Brand New</option>
                                    <option value="sangla-orcr" <?php if ($form_type == 'sangla-orcr') echo 'selected'; ?>>Sangla Orcr</option>
                                    <option value="second-hand" <?php if ($form_type == 'second-hand') echo 'selected'; ?>>Second Hand</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="search-container">
                        <br>
                        <input type="text" id="search-input" placeholder="Search by Transaction ID or Client Name" value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                </div>
                <div class="table-wrapper" id="accepted-table-wrapper">
                    <div class="table" id="accepted-appointments-table">
                        <!-- Header Row -->
                        <div class="table-header">
                            <div class="header-item">Client Name</div>
                            <div class="header-item">Transaction ID</div>
                            <div class="header-item">Appointment Date</div>
                            <div class="header-item">Appointment Time</div>
                            <div class="header-item">Actions</div>
                        </div>
                        
                        <!-- Display each client -->
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($client = $result->fetch_assoc()):
                                // Format the form type
                                $formattedFormType = ucwords(str_replace(['_', '-'], ' ', trim($client['form_type'])));
                                if (stripos($client['form_type'], 'orcr') !== false) {
                                    $formattedFormType = str_ireplace('orcr', 'ORCR', $formattedFormType);
                                }
                                ?>
                                <div class="table-row">
                                    <div class="row-item">
                                        <?php echo htmlspecialchars($client['clientname']); ?>
                                        <br>
                                        <span class="formatted-form-type" style="color: green;"><?php echo htmlspecialchars($formattedFormType); ?></span>
                                    </div>
                                    <div class="row-item"><?php echo htmlspecialchars($client['transaction_id']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($client['appointment_date']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($client['appointment_time']); ?></div>
                                    <div class="row-item" id="accepted-buttons-container">
                                        <div id="accepted-upper-buttons">
                                            <form id="approve-form-<?php echo $client['id']; ?>" method="post" action="payment-ui.php"
                                                style="display:inline;">
                                                <input type="hidden" name="appointment_id"
                                                    value="<?php echo htmlspecialchars($client['id']); ?>">
                                                <input type="hidden" name="status_type" value="Approved">
                                                <input type="hidden" name="email"
                                                    value="<?php echo htmlspecialchars($client['email']); ?>">
                                                <input type="hidden" name="clientname"
                                                    value="<?php echo htmlspecialchars($client['clientname']); ?>">
                                                <input type="hidden" name="form_type"
                                                    value="<?php echo htmlspecialchars($client['form_type']); ?>">
                                                <input type="hidden" id="bank-partner-<?php echo $client['id']; ?>" name="bank_partner">
                                                <input type="hidden" id="remarks-<?php echo $client['id']; ?>" name="remarks">
                                                <button type="button" class="btn accept" id="accepted-approve-button"
                                                    onclick="showApproveSwal('<?php echo $client['id']; ?>', '<?php echo htmlspecialchars($client['email']); ?>', '<?php echo htmlspecialchars($client['clientname']); ?>')">Approve</button>
                                            </form>
                                            <form id="decline-form-<?php echo $client['id']; ?>" method="post" action="payment-ui.php"
                                                style="display:inline;">
                                                <input type="hidden" name="appointment_id"
                                                    value="<?php echo htmlspecialchars($client['id']); ?>">
                                                <input type="hidden" name="status_type" value="Declined">
                                                <input type="hidden" name="email"
                                                    value="<?php echo htmlspecialchars($client['email']); ?>">
                                                <input type="hidden" name="clientname"
                                                    value="<?php echo htmlspecialchars($client['clientname']); ?>">
                                                    <input type="hidden" name="form_type"
                                                    value="<?php echo htmlspecialchars($client['form_type']); ?>">
                                                <input type="hidden" id="remarks-<?php echo $client['id']; ?>" name="remarks">
                                                <input type="hidden" id="bank-partner-<?php echo $client['id']; ?>" name="bank_partner">
                                                <button type="button" class="btn decline" id="accepted-decline-button"
                                                    onclick="showDeclineSwal('<?php echo $client['id']; ?>')">Decline</button>
                                            </form>
                                        </div>  
                                        <div id="accepted-lower-buttons">
                                            <form action="view-client-files.php" method="get" style="display:inline;">
                                                <input type="hidden" name="email"
                                                    value="<?php echo htmlspecialchars($client['email']); ?>">
                                                <button type="submit" class="btn files" id="accepted-file-button">Files</button>
                                            </form>
                                            <a href="view-details.php?transaction_id=<?php echo htmlspecialchars($client['transaction_id']); ?>&form_type=<?php echo htmlspecialchars($client['form_type']); ?>"
                                                class="btn details" id="accepted-details-button">Details</a>
                                        </div>
                                    </div>
                                </div>
                                
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="table-row">
                                <div class="row-item" colspan="4">No clients in processing status.</div>
                            </div>
                        <?php endif; ?>
                    </div>                                  
                </div>
            <div class="pagination" id="accepted-pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $sort_order; ?>&form_type=<?php echo $form_type; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
            </div>
        </main>
        <!-- End Main -->
    </div>

    <script src="assets/js/script.js"></script>
    <script>
    $(document).ready(function() {
        $('#search-input').on('input', function() {
            var searchQuery = $(this).val();
            var url = new URL(window.location.href);
            url.searchParams.set('search', searchQuery);
            window.history.pushState({}, '', url);
            $.get(url, function(data) {
                var newTable = $(data).find('#accepted-table-wrapper').html();
                $('#accepted-table-wrapper').html(newTable);
                var newPagination = $(data).find('#accepted-pagination').html();
                $('#accepted-pagination').html(newPagination);
            });
        });
    });
    </script>
</body>

</html>