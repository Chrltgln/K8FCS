<?php
include_once '../settings/authenticate.php';
checkUserRole(['Admin']);
include 'processes/manage-account-logic.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Account</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Custom styles here */

        .main-title h2 {
            margin-bottom: 20px;
        }

        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        select {
            width: 80%;
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
            box-sizing: border-box; /* Ensure padding and border are included in the width */
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="date"]:focus,
        select:focus {
            border-color: #007BFF;
            outline: none;
        }

        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 1024px) {
            .form-container {
                display: flex;
                flex-wrap: wrap;
                /* Allow wrapping to next line */
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="date"],
            select {
                width: calc(80% - 0px);
                /* 50% width for two columns, adjust for margin */
                padding: 10px;
                margin: 5px;
                border: 1px solid #ccc;
                border-radius: 5px;
                transition: border-color 0.3s;
            }

            @media screen and (max-width: 426px) {
                .form-container {
                    display: flex;
                    flex-wrap: wrap;
                    /* Allow wrapping to next line */
                    justify-content: center;
                    /* Center content horizontally */
                    align-items: center;
                    /* Center content vertically */
                }

                .form-row {
                    display: grid;
                    grid-template-columns: repeat(1, 1fr);
                    gap: 15px;
                    /* Adjust gap for spacing between rows */
                    width: 100%;
                    /* Make sure the grid takes full width */
                }

                input[type="text"],
                input[type="email"],
                input[type="password"],
                input[type="date"],
                select {
                    width: calc(80% - 10px);
                    /* Adjust width slightly */
                    padding: 10px;
                    margin: 5px auto;
                    /* Center margin */
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    transition: border-color 0.3s;
                    display: block;
                    /* Ensure inputs are block-level elements */
                }
            }


        }
    </style>
</head>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>Add Account</h2>
            </div>
            <!-- Feedback messages -->
            <div class="container" id="details-container">
                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($successMessage)): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            Swal.fire({
                                title: 'Success!',
                                text: '<?php echo $successMessage; ?>',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                <?php endif; ?>
                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($errorMessage)): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            Swal.fire({
                                title: 'Error!',
                                text: '<?php echo $errorMessage; ?>',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                <?php endif; ?>

                <!-- Account Addition Form -->
                
                <form action="" method="POST" id="manage-account-form">
                    <div class="form-field">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name">
                    </div>
                    <div class="form-field">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name">
                    </div>
                    <div class="form-field">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name">
                    </div>
                    <div class="form-field additional-field">
                        <label for="age">Age</label>
                        <input type="text" id="age" name="age">
                    </div>
                    <div class="form-field additional-field">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-field additional-field">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob">
                    </div>
                    <div class="form-field additional-field">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address">
                    </div>
                    <div class="form-field additional-field">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone">
                    </div>
                    <div class="form-field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>
                    <div class="form-field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password">
                    </div>
                    <div>
                        <label for="role" id="role-label">Please select a role</label>
                        <select id="role" name="role">
                            <option value="">--Select--</option>
                            <option value="Admin">Admin</option>
                            <option value="Employee">Employee</option>
                        </select>
                    </div>
                    <div id="add-account-button-container" class="form-field">
                        
                        <button type="submit">Add Account</button>
                        <button type="button" style="background-color: gray;" id="back-button">Back</button>
                    </div>
                    <br/>
                </form>
            </div>
            <div class="charts">
                <!-- Removed Manage Account section -->
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('manage-account-form');
        const roleSelect = document.getElementById('role');
        const roleLabel = document.getElementById('role-label');
        const allFields = document.querySelectorAll('.form-field');
        const additionalFields = document.querySelectorAll('.additional-field');
        const backButton = document.getElementById('back-button');

        // Hide all fields except role by default
        allFields.forEach(field => field.style.display = 'none');
        roleSelect.parentElement.style.display = 'block';

        roleSelect.addEventListener('change', function () {
            roleLabel.textContent = 'Role';
            if (roleSelect.value === 'Admin') {
                allFields.forEach(field => field.style.display = 'block');
                additionalFields.forEach(field => field.style.display = 'none');
            } else if (roleSelect.value === 'Employee') {
                allFields.forEach(field => field.style.display = 'block');
                additionalFields.forEach(field => field.style.display = 'block');
            } else {
                allFields.forEach(field => field.style.display = 'none');
                roleSelect.parentElement.style.display = 'block';
            }
        });

        backButton.addEventListener('click', function () {
            allFields.forEach(field => field.style.display = 'none');
            roleSelect.value = '';
            roleLabel.textContent = 'Please select a role';
            roleSelect.parentElement.style.display = 'block';
        });

        form.addEventListener('submit', function (event) {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const age = document.getElementById('age').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const role = document.getElementById('role').value;

            if (firstName === '') {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'First Name is required.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (lastName === '') {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Last Name is required.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (email === '') {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Email is required.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!/^\S+@\S+\.\S+$/.test(email)) {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Please enter a valid email address.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (password.length < 8) {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must be at least 8 characters long.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!/[A-Z]/.test(password)) {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must contain at least 1 uppercase letter.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!/\d/.test(password)) {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must contain at least 1 number.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!/[!@#$%^&*]/.test(password)) {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must contain at least 1 special character.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (password === email) {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must not be the same as the email.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (role === 'Employee') {
                if (age === '' || isNaN(age) || age <= 0) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please enter a valid age.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (phone === '' || !/^\d{11}$/.test(phone)) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please enter a valid 11-digit phone number.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }

            if (role === '') {
                event.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Role is required.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
        });
    });
    </script>
    
</body>

</html>