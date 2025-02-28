<?php
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';

// Number of users per page
$users_per_page = 15;

// Get the current page number from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure the page number is at least 1

// Calculate the offset for the SQL query
$offset = ($page - 1) * $users_per_page;

// Fetch the total number of users
$total_users_query = "SELECT COUNT(*) as total FROM users";
$total_users_result = $conn->query($total_users_query);
$total_users_row = $total_users_result->fetch_assoc();
$total_users = $total_users_row['total'];

// Calculate the total number of pages
$total_pages = ceil($total_users / $users_per_page);

// Fetch users for the current page
$query = "
    SELECT u.*, 
           CASE 
               WHEN s.session_id IS NOT NULL AND s.logout_time IS NULL THEN 'Online' 
               ELSE 'Offline' 
           END AS login_status
    FROM users u
    LEFT JOIN sessions s ON u.id = s.user_id AND s.logout_time IS NULL
    ORDER BY login_status DESC, u.created_at DESC
    LIMIT $users_per_page OFFSET $offset
";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>  