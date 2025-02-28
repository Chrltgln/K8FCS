<?php
// Check if the user is logged in and has the admin role
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';
include 'processes/send-email-receive-payment.php'; // Include the email sending script



// Get the current page number from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of clients per page
$offset = ($page - 1) * $limit;

// Get the selected sort option from the query string, default to 'transaction_id' if not set
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'transaction_id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Get the selected form type from the query string, default to '' if not set
$form_type = isset($_GET['form_type']) ? $_GET['form_type'] : '';

// Get the search query from the query string, default to '' if not set
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Get the total number of clients
$total_clients_query = "SELECT COUNT(*) as total FROM appointments WHERE status = 'Approved' AND archived = 0";
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
$query = "SELECT * FROM appointments WHERE status = 'Approved' AND archived = 0";
if ($form_type) {
    $query .= " AND form_type = '$form_type'";
}
if ($search_query) {
    $query .= " AND (clientname LIKE '%$search_query%' OR transaction_id LIKE '%$search_query%')";
}
$query .= " ORDER BY $sort $sort_order LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = $_POST['transaction_id'];
    $action = $_POST['action'];
    $reason = $_POST['reason'] ?? '';

    if ($action === 'approve') {
        $update_query = "UPDATE appointments SET status = 'Accepted', archived = 0 WHERE transaction_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('s', $transaction_id);
        $stmt->execute();

        // Fetch client details for email
        $client_query = "SELECT clientname, email, form_type FROM appointments WHERE transaction_id = ?";
        $stmt = $conn->prepare($client_query);
        $stmt->bind_param('s', $transaction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $client = $result->fetch_assoc();

        // Send email
        sendRecieveEmail($client['email'], $client['clientname'], $client['form_type'], $transaction_id, 'Your payment has been accepted.');

    } elseif ($action === 'decline') {
        $update_query = "UPDATE appointments SET status = 'Declined', archived = 1 WHERE transaction_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('s', $transaction_id);
        $stmt->execute();

        // Fetch client details for email
        $client_query = "SELECT clientname, email, form_type FROM appointments WHERE transaction_id = ?";
        $stmt = $conn->prepare($client_query);
        $stmt->bind_param('s', $transaction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $client = $result->fetch_assoc();

        // Send email
        sendNotReceiveEmail($client['email'], $client['clientname'], $client['form_type'], $transaction_id, $reason);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Accept Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
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
                <h2>Accept Payment</h2>
            </div>
            <div class="container">
                <div id="archive-header-actions-container">
                    <div class="sort-options">
                        <div id="approve-payment-sort-by-container">
                            <form method="get" action="">
                                <label for="sort">Sort by:</label> <br>
                                <select class="approve-payment-sort-options" name="sort" id="approve-payment-sort" onchange="this.form.submit()">
                                    <option value="transaction_id" <?php if ($sort == 'transaction_id') echo 'selected'; ?>>Transaction ID</option>
                                </select>
                        </div>
                        <div id="approve-payment-sort-by-container">
                            <label for="order">Order:</label> <br>
                            <select class="approve-payment-sort-options" name="order" id="approve-payment-order" onchange="this.form.submit()">
                                <option value="ASC" <?php if ($sort_order == 'ASC') echo 'selected'; ?>>Ascending</option>
                                <option value="DESC" <?php if ($sort_order == 'DESC') echo 'selected'; ?>>Descending</option>
                            </select>
                        </div>
                        </form>
                        <form method="get" action="">
                            <div id="approve-payment-form-type-container">
                                <label for="form_type">Form Type:</label> <br>
                                <select class="approve-payment-sort-options" name="form_type" id="approve-payment-form_type" onchange="this.form.submit()">
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
                <div class="table-wrapper" id="approve-payment-table-wrapper">
                    <div class="table" id="approve-payment-table">
                        <!-- Header Row -->
                        <div class="table-header">
                            <div class="header-item">Client Name</div>
                            <div class="header-item">Transaction ID</div>
                            <div class="header-item">Status</div>
                            <div class="header-item">Actions</div>
                        </div>
                        
                        <!-- Table Data -->
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $formattedFormType = ucwords(str_replace(['_', '-'], ' ', trim($row['form_type'])));
                                        if (stripos($row['form_type'], 'orcr') !== false) {
                                            $formattedFormType = str_ireplace('orcr', 'ORCR', $formattedFormType);
                                        }
                                        ?>
                                <div class="table-row">
                                    <div class="row-item"><?= htmlspecialchars($row["clientname"]) ?><br>
                                        <span class="formatted-form-type" style="color: green;"><?php echo htmlspecialchars($formattedFormType); ?></span>
                                    </div>
                                    <div class="row-item"><?= htmlspecialchars($row["transaction_id"]) ?></div>
                                    <div class="row-item"><?= htmlspecialchars($row["status"]) ?></div>
                                    <div class="row-item">
                                        <?php if ($row['status'] === 'Approved'): ?>
                                            <form id="approve-form-<?= $row['transaction_id'] ?>" action="processes/update-payment-status.php" method="post">
                                                <input type="hidden" name="transaction_id" value="<?= htmlspecialchars($row['transaction_id']) ?>">
                                                <input type="hidden" name="action" value="">
                                                <input type="hidden" name="reason" class="reason-input">
                                                <button type="button" onclick="confirmAction('approve', '<?= $row['transaction_id'] ?>')" class="btn accept" id="pending-acceptbtn">Accept</button>
                                                <button type="button" onclick="confirmAction('decline', '<?= $row['transaction_id'] ?>')" class="btn decline" id="pending-declinebtn">Decline</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="table-row">
                                <div class="row-item" colspan="4">No client data</div>
                            </div>
                        <?php endif; ?>
                        <?php $conn->close(); ?>
                    </div>
                </div>
                <div class="pagination" id="approve-payment-pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $sort_order; ?>&form_type=<?php echo $form_type; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
        <!-- End Main -->
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        function confirmAction(action, transactionId) {
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to ${action} this transaction.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                input: action === 'decline' ? 'text' : null,
                inputPlaceholder: action === 'decline' ? 'Briefly state the reason for declining' : null,
                preConfirm: (reason) => {
                    if (action === 'decline') {
                        document.querySelector(`#approve-form-${transactionId} .reason-input`).value = reason;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(`approve-form-${transactionId}`);
                    form.querySelector('input[name="action"]').value = action;
                    form.submit();
                }
            });
        }

        $(document).ready(function() {
            $('#search-input').on('input', function() {
                var searchQuery = $(this).val();
                var url = new URL(window.location.href);
                url.searchParams.set('search', searchQuery);
                window.history.pushState({}, '', url);
                $.get(url, function(data) {
                    var newTable = $(data).find('#approve-payment-table-wrapper').html();
                    $('#approve-payment-table-wrapper').html(newTable);
                    var newPagination = $(data).find('#approve-payment-pagination').html();
                    $('#approve-payment-pagination').html(newPagination);
                });
            });
        });
    </script>
</body>
</html>
