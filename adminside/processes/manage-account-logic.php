<?php
include '../settings/config.php';

// Initialize error message
$errorMessage = "";

// Function to check if the account already exists
function accountExists($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gather data from the form
    $firstName = trim($_POST['first_name']);
    $middleName = trim($_POST['middle_name']);
    $lastName = trim($_POST['last_name']);
    $age = trim($_POST['age']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    // Validation checks
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($role)) {
        $errorMessage = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } elseif ($role === 'Employee' && (!is_numeric($age) || $age <= 0)) {
        $errorMessage = "Age must be a positive number.";
    } elseif ($role === 'Employee' && !preg_match("/^[0-9]{11}$/", $phone)) {
        $errorMessage = "Phone number must be 10 digits.";
    } elseif (accountExists($conn, $email)) {
        $errorMessage = "An account with this email already exists.";
    } else {
        // Hash the password
        $password = password_hash($password, PASSWORD_BCRYPT);

        // Set additional fields to null if role is Admin
        if ($role === 'Admin') {
            $age = 'admin';
            $gender = 'admin';
            $dob = 'admin';
            $address = 'admin';
            $phone = 'admin';
        }

        // Prepare and execute the insert statement
        $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, age, gender, dob, address, email, phone, password, role, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->bind_param("sssisssssss", $firstName, $middleName, $lastName, $age, $gender, $dob, $address, $email, $phone, $password, $role);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            $successMessage = "Account added successfully!";
        } else {
            $errorMessage = "Error adding account: " . $stmt->error;
        }
    }
}
?>