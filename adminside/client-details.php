<?php
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';

// Initialize filtering variables
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '1970-01-01';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Fetch total number of users within the date range
$total_query = "SELECT COUNT(*) as total FROM users WHERE created_at BETWEEN ? AND ?";
$stmt_total = $conn->prepare($total_query);
$stmt_total->bind_param("ss", $from_date, $to_date);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total_count = $total_row['total'];

// Fetch user details
$query = "SELECT u.id, u.first_name, u.middle_name, u.last_name, u.age, u.gender, u.dob, u.address, u.email, u.phone, u.role, u.profile_picture, u.created_at FROM users u WHERE u.created_at BETWEEN ? AND ? ORDER BY u.created_at $order LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssii", $from_date, $to_date, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$client_details = [];
while ($row = $result->fetch_assoc()) {
    $client_details[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        h5 {
            font-weight: normal;
        }
        .info-card .card-content ul {
            padding: 0;
            margin: 0;
        }
        .info-card .card-content ul li {
            margin-bottom: 5px;
        }
        .profile-picture-container {
            float: right;
            margin-left: 20px;
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid black;
            border-radius: 50%;
            overflow: hidden;
        }
        .profile-picture-container img {
            max-width: 100%;
            max-height: 100%;
        }
        .no-profile-picture {
            font-size: 20px;
            text-align: center;
        }
        .card-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.2em;
            flex-wrap: wrap;
        }
        .card-title .client-email {
            font-weight: normal;
            font-size: 0.9em;
            color: white;
            margin-right: 3rem;
        } 

        #swal-to-date {
            margin: 0;
        }

        #swal-from-date {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>Accounts Details</h2>
            </div>

            <div class="container" id="details-container">
                <div id="archive-header-actions-container">
                    <div class="sort-options">
                        <button onclick="showFilterModal()">Filter by Date Range</button>
                    </div>
                    <div class="search-container" id="client-details-search-container">
                        <input type="text" id="search-input" onkeyup="filterClients()" placeholder="Search by name or email">
                    </div>
                </div>
                <div class="submission-info">
                    <?php foreach ($client_details as $client): ?>
                        <div class="info-card">
                            <h3 class="card-title" onclick="toggleCardContent(this)">
                                <span class="client-name"><?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name']); ?></span>
                                <span class="client-email"><?php echo htmlspecialchars($client['email']); ?></span>
                            </h3>
                            <div class="card-content" style="display: none;">
                                <div class="view-details-header-actions">
                                    <button class="details-action-button" onclick="window.location.href='view-client-files.php?email=<?php echo urlencode($client['email']); ?>'">View Files</button>
                                </div>
                                <div class="view-details-container">
                                    <div class="profile-picture-container">
                                        <?php if (!empty($client['profile_picture'])): ?>
                                            <img src="../clientside/<?php echo htmlspecialchars($client['profile_picture']); ?>" alt="Profile Picture">
                                        <?php else: ?>
                                            <p class="no-profile-picture">No Profile Picture</p>
                                        <?php endif; ?>
                                    </div>
                                    <h5>Personal Information</h5>
                                    <ul>
                                        <li>First Name: <?php echo htmlspecialchars($client['first_name'] ?? 'N/A'); ?></li>
                                        <li>Middle Name: <?php echo htmlspecialchars($client['middle_name'] ?? 'N/A'); ?></li>
                                        <li>Last Name: <?php echo htmlspecialchars($client['last_name'] ?? 'N/A'); ?></li>
                                        <li>Age: <?php echo htmlspecialchars($client['age'] ?? 'N/A'); ?></li>
                                        <li>Gender: <?php echo htmlspecialchars($client['gender'] ?? 'N/A'); ?></li>
                                        <li>Date of Birth: <?php echo htmlspecialchars($client['dob'] ?? 'N/A'); ?></li>
                                        <li>Address: <?php echo htmlspecialchars($client['address'] ?? 'N/A'); ?></li>
                                        <li>Email: <?php echo htmlspecialchars($client['email'] ?? 'N/A'); ?></li>
                                        <li>Phone: <?php echo htmlspecialchars($client['phone'] ?? 'N/A'); ?></li>
                                        <li>Role: <?php echo htmlspecialchars($client['role'] ?? 'N/A'); ?></li>
                                        <li>Created At: <?php echo htmlspecialchars($client['created_at'] ?? 'N/A'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="pagination" id="archives-pagination">
                    <?php
                    $total_pages = ceil($total_count / $limit);
                    for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>&order=<?php echo urlencode($order); ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        function toggleCardContent(element) {
            const content = element.nextElementSibling;
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        }

        function filterClients() {
            const input = document.getElementById('search-input');
            const filter = input.value.toLowerCase();
            const cards = document.getElementsByClassName('info-card');

            Array.from(cards).forEach(card => {
                const title = card.querySelector('.card-title').innerText.toLowerCase();
                const email = card.querySelector('.client-email').innerText.toLowerCase();
                card.style.display = title.includes(filter) || email.includes(filter) ? '' : 'none';
            });
        }

        function showFilterModal() {
            Swal.fire({
                title: 'Filter by Date Range',
                html: `
                    <div style="text-align: left; padding: 2rem;">
                        <div style="margin-bottom: 10px;">
                            <label for="swal-from-date" style="display: inline-block; width: 80px;">From:</label>
                            <input type="date" id="swal-from-date" class="swal2-input" value="<?php echo htmlspecialchars($from_date); ?>" style="display: inline-block; width: calc(100% - 90px);">
                        </div><br>
                        <div style="margin-bottom: 10px;">
                            <label for="swal-to-date" style="display: inline-block; width: 80px;">To:</label>
                            <input type="date" id="swal-to-date" class="swal2-input" value="<?php echo htmlspecialchars($to_date); ?>" style="display: inline-block; width: calc(100% - 90px);">
                        </div><br>
                        <div>
                            <label for="swal-order" style="display: inline-block; width: 80px;">Order:</label>
                            <select id="swal-order" class="swal2-input" style="display: inline-block; width: calc(100% - 90px);">
                                <option value="ASC" <?php if ($order == 'ASC') echo 'selected'; ?>>Ascending</option>
                                <option value="DESC" <?php if ($order == 'DESC') echo 'selected'; ?>>Descending</option>
                            </select>
                        </div>
                    </div>
                `,
                focusConfirm: false,
                preConfirm: () => {
                    const fromDate = document.getElementById('swal-from-date').value;
                    const toDate = document.getElementById('swal-to-date').value;
                    const order = document.getElementById('swal-order').value;
                    window.location.href = `?from_date=${fromDate}&to_date=${toDate}&order=${order}`;
                }
            });
        }
    </script>
</body>

</html>
