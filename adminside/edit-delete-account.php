<?php
include 'processes/edit-delete-logic.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>Manage Account</h2>
            </div>

            <div class="container" id="details-container">
                <div class="table-wrapper" id="edit-delete-account-wrapper">
                    <div class="table" id="edit-delete-account-table">
                        <div class="table-header">
                            <div class="header-item">First Name</div>
                            <div class="header-item">Middle Name</div>
                            <div class="header-item">Last Name</div>
                            <div class="header-item">E-mail Address</div>
                            <div class="header-item">Role</div>
                            <div class="header-item">Action</div>
                        </div>
                        <div class="table-body">
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="table-row">
                                    <div class="row-item"><?php echo htmlspecialchars($row['first_name']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($row['middle_name']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($row['last_name']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($row['email']); ?></div>
                                    <div class="row-item"><?php echo htmlspecialchars($row['role']); ?></div>
                                    <div class="row-item">
                                        <button class="btn_edit" data-id="<?php echo $row['id']; ?>">Edit</button>
                                        <button class="btn_delete" data-id="<?php echo $row['id']; ?>">Delete</button>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
    $(document).ready(function () {
        $('.btn_edit').on('click', function () {
            var userId = $(this).data('id');
            console.log('Edit button clicked for user ID:', userId);

            $.ajax({
                url: 'edit-delete-account.php',
                type: 'GET',
                data: { id: userId },
                success: function (response) {
                    console.log('AJAX response:', response);
                    var user = JSON.parse(response);
                    Swal.fire({
                        title: 'Edit User',
                        html: `
                        <style>
                            .swal2-row {
                                display: flex;
                                justify-content: space-between;
                                margin-bottom: 10px;
                            }
                            .swal2-row .swal2-input, .swal2-select {
                                width: 100%;
                                border: 1px solid #d2d6dc;
                            }
                        </style>
                        <div id="swal-2-edit-container">
                            <div class="swal2-row">
                                <input type="hidden" id="user_id" value="${user.id}">
                                <input type="text" id="first_name" class="swal2-input" placeholder="First Name" value="${user.first_name}">
                            </div>
                            <div class="swal2-row">
                                <input type="text" id="middle_name" class="swal2-input" placeholder="Middle Name" value="${user.middle_name}">
                            </div>
                            <div class="swal2-row">
                                <input type="text" id="last_name" class="swal2-input" placeholder="Last Name" value="${user.last_name}">
                            </div>
                            <div class="swal2-row">
                                <input type="number" id="age" class="swal2-input" placeholder="Age" value="${user.age}">
                            </div>
                            <div class="swal2-row">
                                <input type="text" id="gender" class="swal2-input" placeholder="Gender" value="${user.gender}">
                            </div>
                            <div class="swal2-row">
                                <input type="date" id="dob" class="swal2-input" placeholder="Date of Birth" value="${user.dob}">
                            </div>
                            <div class="swal2-row">
                                <input type="text" id="address" class="swal2-input" placeholder="Address" value="${user.address}">
                            </div>
                            <div class="swal2-row">
                                <input type="email" id="email" class="swal2-input" placeholder="Email" value="${user.email}">
                            </div>
                            <div class="swal2-row">
                                <input type="text" id="phone" class="swal2-input" placeholder="Phone" value="${user.phone}">
                            </div>
                            <div class="swal2-row">
                                <select id="role" class="swal2-select">
                                    <option value="Employee" ${user.role === 'Employee' ? 'selected' : ''}>Employee</option>
                                    <option value="Client" ${user.role === 'Client' ? 'selected' : ''}>Client</option>
                                </select>
                            </div>
                        </div>
                        `,
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Edit',
                        cancelButtonText: 'Close',
                        didOpen: () => {
                            document.querySelector('.swal2-popup').id = 'edit-popup-swal';
                        },
                        preConfirm: () => {
                            return {
                                user_id: document.getElementById('user_id').value,
                                first_name: document.getElementById('first_name').value,
                                middle_name: document.getElementById('middle_name').value,
                                last_name: document.getElementById('last_name').value,
                                age: document.getElementById('age').value,
                                gender: document.getElementById('gender').value,
                                dob: document.getElementById('dob').value,
                                address: document.getElementById('address').value,
                                email: document.getElementById('email').value,
                                phone: document.getElementById('phone').value,
                                role: document.getElementById('role').value
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'edit-delete-account.php',
                                type: 'POST',
                                data: {
                                    update_user: true,
                                    user_id: result.value.user_id,
                                    first_name: result.value.first_name,
                                    middle_name: result.value.middle_name,
                                    last_name: result.value.last_name,
                                    age: result.value.age,
                                    gender: result.value.gender,
                                    dob: result.value.dob,
                                    address: result.value.address,
                                    email: result.value.email,
                                    phone: result.value.phone,
                                    role: result.value.role
                                },
                                success: function (response) {
                                    console.log('Update response:', response);
                                    var res = JSON.parse(response);
                                    if (res.status === 'success') {
                                        Swal.fire('Updated!', 'User details have been updated.', 'success').then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error!', res.message, 'error');
                                    }
                                }
                            });
                        }
                    });
                }
            });
        });

        $('.btn_delete').on('click', function () {
            var userId = $(this).data('id');
            console.log('Delete button clicked for user ID:', userId);

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'edit-delete-account.php',
                        type: 'POST',
                        data: {
                            delete_user: true,
                            user_id: userId
                        },
                        success: function (response) {
                            console.log('Delete response:', response);
                            var res = JSON.parse(response);
                            if (res.status === 'success') {
                                Swal.fire('Deleted!', 'User has been deleted.', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
</body>

<script src="assets/js/script.js"></script>

</html>