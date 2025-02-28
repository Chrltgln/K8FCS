<?php
require_once '../settings/authenticate.php';
checkUserRole(['Employee']);
require_once '../settings/config.php'; 

// Ensure the session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user_email is set in the session
if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
} else {
    // Handle the case where user_email is not set in the session
    die('User email is not set in the session.');
}

// Get the sort option and search query from the query string
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'asc';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Determine the SQL ORDER BY clause based on the sort option
$order_by = $sort_option == 'desc' ? 'file_name DESC' : 'file_name ASC';

// Fetch files for the user based on the search query and sort option
if ($search_query) {
    $stmt = $conn->prepare("SELECT file_name AS name FROM files WHERE user_email = ? AND file_name LIKE ? ORDER BY $order_by");
    $like_query = '%' . $search_query . '%';
    $stmt->bind_param("ss", $user_email, $like_query);
} else {
    $stmt = $conn->prepare("SELECT file_name AS name FROM files WHERE user_email = ? ORDER BY $order_by");
    $stmt->bind_param("s", $user_email);
}
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>