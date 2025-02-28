<?php

include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';

// Number of users per page
$users_per_page = 15;

// Get the current page number from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
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

// Fetch users for the current page, ordered by creation date in descending order
$query = "SELECT * FROM users ORDER BY created_at DESC LIMIT $users_per_page OFFSET $offset";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = (int) $_POST['user_id'];
    $delete_query = "DELETE FROM users WHERE id = $user_id";
    if ($conn->query($delete_query) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}

// Handle AJAX request for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = (int) $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $update_query = "UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, age = ?, gender = ?, dob = ?, address = ?, email = ?, phone = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sssissssssi', $first_name, $middle_name, $last_name, $age, $gender, $dob, $address, $email, $phone, $role, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}

// Handle AJAX request for fetching user details
if (isset($_GET['id'])) {
    $user_id = (int) $_GET['id'];
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = $conn->query($query);

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    } else {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    }
    exit;
}
?>