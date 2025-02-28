<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Check if the user is logged in and has the admin role
include '../settings/authenticate.php';
require '../vendor/autoload.php';
checkUserRole(['Admin']);
include '../settings/config.php';

function logActivity($conn, $user_email, $action, $file_name)
{
    $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_email, $action, $file_name);
    $stmt->execute();
    $stmt->close();
}

// Get the current page number from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 8; // Number of clients per page
$offset = ($page - 1) * $limit;

// Get the selected sort option from the query string, default to 'clientname' if not set
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'clientname';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Get the selected form type from the query string, default to '' if not set
$form_type = isset($_GET['form_type']) ? $_GET['form_type'] : '';

// Get the search query from the query string, default to '' if not set
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Get the total number of clients
$total_clients_query = "SELECT COUNT(*) as total FROM appointments WHERE status = 'processing'";
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
$query = "SELECT * FROM appointments WHERE status = 'processing'";
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
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                <h2>Pending Clients</h2>
            </div>

            <div class="container">
                <div id="archive-header-actions-container">
                    <div class="sort-options">
                        <div id="pending-sort-by-container">
                            <form method="get" action="">
                                <label for="sort">Sort by:</label> <br>
                                <select class="pending-sort-options" name="sort" id="pending-sort"
                                    onchange="this.form.submit()">
                                    <option value="appointment_date" <?php if ($sort == 'appointment_date')
                                        echo 'selected'; ?>>
                                        Appointment Date</option>
                                    <option value="appointment_time" <?php if ($sort == 'appointment_time')
                                        echo 'selected'; ?>>
                                        Appointment Time</option>
                                </select>
                        </div>
                        <div id="pending-sort-by-container">
                            <label for="order">Order:</label> <br>
                            <select class="pending-sort-options" name="order" id="pending-order"
                                onchange="this.form.submit()">
                                <option value="ASC" <?php if ($sort_order == 'ASC')
                                    echo 'selected'; ?>>Ascending</option>
                                <option value="DESC" <?php if ($sort_order == 'DESC')
                                    echo 'selected'; ?>>Descending</option>
                            </select>
                        </div>
                        </form>
                        <form method="get" action="">
                            <div id="pending-sort-form-type-container">
                                <label for="form_type">Form Type:</label> <br>
                                <select class="pending-sort-options" name="form_type" id="pending-form_type"
                                    onchange="this.form.submit()">
                                    <option value="" <?php if ($form_type == '')
                                        echo 'selected'; ?>>All</option>
                                    <option value="brand-new" <?php if ($form_type == 'brand-new')
                                        echo 'selected'; ?>>Brand New
                                    </option>
                                    <option value="sangla-orcr" <?php if ($form_type == 'sangla-orcr')
                                        echo 'selected'; ?>>Sangla
                                        Orcr</option>
                                    <option value="second-hand" <?php if ($form_type == 'second-hand')
                                        echo 'selected'; ?>>Second
                                        Hand</option>
                                </select>
                            </div>

                        </form>
                    </div>
                    <div class="search-container">
                        <br>
                        <input type="text" id="search-input"
                            placeholder="Search by Transaction ID or Client Name"
                            value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                </div>
                <div class="table-wrapper" id="pending-table-wrapper">
                    <div class="table" id="pending-appointments-table">
                        <!-- Header Row -->
                        <div class="table-header">
                            <div class="header-item">Client Details</div>
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
                                    <div class="row-item" id="pending-row-item">
                                        <?php echo htmlspecialchars($client['clientname']); ?><br>
                                        <span class="formatted-form-type"
                                            style="color: green;"><?php echo htmlspecialchars($formattedFormType); ?></span>
                                    </div>
                                    <div class="row-item" id="pending-row-item">
                                        <?php echo htmlspecialchars($client['transaction_id']); ?>
                                    </div>
                                    <div class="row-item" id="pending-row-item">
                                        <?php echo htmlspecialchars($client['appointment_date']); ?>
                                    </div>
                                    <div class="row-item" id="pending-row-item">
                                        <?php echo htmlspecialchars($client['appointment_time']); ?>
                                    </div>
                                    <div class="row-item" id="pending-buttons-container">
                                        <div id="accepted-upper-buttons">
                                            <form method="post"
                                                action="processes/send-email-accepting-declining-application.php"
                                                style="display:inline;">
                                                <input type="hidden" name="appointment_id"
                                                    value="<?php echo htmlspecialchars($client['id']); ?>">
                                                <input type="hidden" name="transaction_id"
                                                    value="<?php echo htmlspecialchars($client['transaction_id']); ?>">
                                                <button type="button" class="btn accept" id="pending-acceptbtn"
                                                    onclick="confirmAction(this, 'accept')">Accept</button>
                                                <button type="button" class="btn decline" id="pending-declinebtn"
                                                    onclick="confirmAction(this, 'decline')">Decline</button>
                                            </form>
                                        </div>
                                        <div id="accepted-lower-buttons">
                                            <form action="view-client-files.php" method="get" style="display:inline;">
                                                <input type="hidden" name="email"
                                                    value="<?php echo htmlspecialchars($client['email']); ?>">
                                                <button type="submit" class="btn files" id="pending-file-button">Files</button>
                                            </form>
                                            <a href="view-details.php?transaction_id=<?php echo htmlspecialchars($client['transaction_id']); ?>&form_type=<?php echo htmlspecialchars($client['form_type']); ?>"
                                                class="btn details" id="pending-viewbtn">Details</a>
                                        </div>

                                        </form>
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
                <div class="pagination" id="pending-pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
    </div>

    </main>
    <!-- End Main -->
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        $(document).ready(function () {
            $('#search-input').on('input', function () {
                var searchQuery = $(this).val();
                var url = new URL(window.location.href);
                url.searchParams.set('search', searchQuery);
                window.history.pushState({}, '', url);
                $.get(url, function (data) {
                    var newTable = $(data).find('#pending-table-wrapper').html();
                    $('#pending-table-wrapper').html(newTable);
                    var newPagination = $(data).find('#pending-pagination').html();
                    $('#pending-pagination').html(newPagination);
                });
            });
        });

        function confirmAction(button, action) {
            let swalOptions = {
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#218838',
                cancelButtonColor: '#585a5e',
                confirmButtonText: 'Yes'
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

                    form.submit();
                }
            });
        }
    </script>
</body>

</html>