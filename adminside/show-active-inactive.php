<?php
include 'processes/show-active-inactive-logic.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active & Inactive Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>Active & Inactive Account</h2>
            </div>

            <div class="container">
                <div class="table-wrapper" id="show-active-inactive-details-container">
                <div class="table" id="show-active-inactive-table">
                    <div class="table-header">
                        <div class="header-item">First Name</div>
                        <div class="header-item">Middle Name</div>
                        <div class="header-item">Last Name</div>
                        <div class="header-item">E-mail Address</div>
                        <div class="header-item">Role</div>
                        <div class="header-item">Active Status</div>
                    </div>
                    <table>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="table-row">
                            <div class="row-item"><?php echo htmlspecialchars($row['first_name']); ?></div>
                                <div class="row-item"><?php echo htmlspecialchars($row['middle_name']); ?></div>
                                <div class="row-item"><?php echo htmlspecialchars($row['last_name']); ?></div>
                                <div class="row-item"><?php echo htmlspecialchars($row['email']); ?></div>
                                <div class="row-item"><?php echo htmlspecialchars($row['role']); ?></div>
                                <div class="row-item"
                                    style="color: <?php echo $row['login_status'] == 'Online' ? 'green' : 'red'; ?>;">
                                    <?php echo htmlspecialchars($row['login_status']); ?>
                                </div>

                            </div>
                        <?php endwhile; ?>
                    </tbody>
                    </table>
                </div>
                </div>
                <div class="pagination" id="show-active-inactive-pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>