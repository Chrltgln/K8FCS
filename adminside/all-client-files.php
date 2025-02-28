<?php
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';

// Ensure $conn is properly initialized
if (!$conn) {
    echo "Error: Database connection not established.";
    exit;
}

// Pagination setup
$limit = 6; // Number of files per page
$folderLimit = 10; // Number of folders per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$folderPage = isset($_GET['folder_page']) ? (int) $_GET['folder_page'] : 1;
$folderOffset = ($folderPage - 1) * $folderLimit;

// Fetch all clients and their files
$clientSql = "
    SELECT users.id, users.first_name, users.last_name, users.email, files.file_name, forms_sanglaorcr_applicants.ORCR_filename, forms_sanglaorcr_applicants.transaction_id
    FROM users 
    LEFT JOIN files ON users.email = files.user_email
    LEFT JOIN forms_sanglaorcr_applicants ON users.email = forms_sanglaorcr_applicants.email
    WHERE files.user_email IS NOT NULL OR forms_sanglaorcr_applicants.ORCR_filename IS NOT NULL
";

$clientResult = $conn->query($clientSql);

$clients = [];
if ($clientResult->num_rows > 0) {
    // Organize the data by clients
    while ($row = $clientResult->fetch_assoc()) {
        $userId = $row['id'];
        $userEmail = $row['email'];
        $transactionId = $row['transaction_id'];
        if (!isset($clients[$userId])) {
            $clients[$userId]['name'] = $row['first_name'] . ' ' . $row['last_name'];
            $clients[$userId]['email'] = $row['email']; // Add email to the clients array
            $clients[$userId]['files'] = [];
        }
        if (!empty($row['file_name']) && !in_array($row['file_name'], $clients[$userId]['files'])) {
            $clients[$userId]['files'][] = $row['file_name']; // Collect files under each client
        }
        if (!empty($row['ORCR_filename']) && !in_array($row['ORCR_filename'], $clients[$userId]['files'])) {
            $clients[$userId]['files'][] = $row['ORCR_filename']; // Collect ORCR files under each client
        }

        // Fetch files from the specified directory
        $directoryPath = "../clientside/uploads/orcr/$userEmail/$transactionId/";
        if (is_dir($directoryPath)) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath));
            foreach ($files as $file) {
                if ($file->isFile() && !in_array($file->getFilename(), $clients[$userId]['files'])) {
                    $clients[$userId]['files'][] = $file->getFilename();
                }
            }
        }
    }
}

// Fetch all employees and their files using fetch-files.php logic
$employees = [];
try {
    $employeeSql = "
        SELECT users.id, users.first_name, users.last_name, users.email
        FROM users 
        WHERE users.role = 'Employee'
    ";

    $employeeResult = $conn->query($employeeSql);

    if ($employeeResult->num_rows > 0) {
        // Organize the data by employees
        while ($row = $employeeResult->fetch_assoc()) {
            $employeeId = $row['id'];
            $employeeEmail = $row['email'];
            if (!isset($employees[$employeeId])) {
                $employees[$employeeId]['name'] = $row['first_name'] . ' ' . $row['last_name'];
                $employees[$employeeId]['email'] = $row['email']; // Add email to the employees array
                $employees[$employeeId]['files'] = [];
            }

            // Fetch files for the employee
            $stmt = $conn->prepare("SELECT file_name AS name FROM files WHERE user_email = ?");
            $stmt->bind_param("s", $employeeEmail);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($fileRow = $result->fetch_assoc()) {
                if (!in_array($fileRow['name'], $employees[$employeeId]['files'])) {
                    $employees[$employeeId]['files'][] = $fileRow['name'];
                }
            }
            $stmt->close();

            // Fetch files from the specified directory
            $directoryPath = "../employeeside/uploads/$employeeEmail/";
            if (is_dir($directoryPath)) {
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath));
                foreach ($files as $file) {
                    if ($file->isFile() && !in_array($file->getFilename(), $employees[$employeeId]['files'])) {
                        $employees[$employeeId]['files'][] = $file->getFilename();
                    }
                }
            }
        }
    }
} catch (mysqli_sql_exception $e) {
    // Handle the case where the employees table does not exist
    error_log("Error fetching employee data: " . $e->getMessage());
}

// Pagination handler for AJAX requests
if (isset($_GET['client_index']) && isset($_GET['page'])) {
    $clientIndex = (int) $_GET['client_index'];
    $clientIds = array_keys($clients);

    // Check if the client index is valid
    if (!isset($clientIds[$clientIndex])) {
        echo "Invalid client index.";
        exit;
    }

    $clientId = $clientIds[$clientIndex];
    $clientFiles = $clients[$clientId]['files'];

    // Check if clientFiles is empty
    if (empty($clientFiles)) {
        echo "No files found for this client.";
        exit;
    }

    $total_files = count($clientFiles);
    $total_pages = ceil($total_files / $limit);
    $paginated_files = array_slice($clientFiles, $offset, $limit);

    if (!empty($paginated_files)) {
        echo '<div class="file-grid">';
        foreach ($paginated_files as $index => $file) {
            if ($index % 2 == 0) {
                echo '<div class="file-row">';
            }
            ?>
            <div class="file-card">
                <div class="file-name"><?php echo htmlspecialchars($file); ?></div>
                <a class="edit-button" href="processes/download-logic.php?file=<?php echo urlencode($file); ?>" download>Download</a>
            </div>
            <?php
            if ($index % 2 == 1) {
                echo '</div>';
            }
        }
        if ($index % 2 == 0) {
            echo '</div>';
        }
        echo '</div>';

        // Pagination Links
        echo '<div class="pagination">';
        if ($page > 1) {
            echo '<a href="javascript:void(0);" onclick="previousPage(' . $clientIndex . ', ' . $page . ')">Previous</a>';
        }
        if ($page < $total_pages) {
            echo '<a href="javascript:void(0);" onclick="nextPage(' . $clientIndex . ', ' . $page . ')">Next</a>';
        }
        echo '</div>';
    } else {
        echo "No files found for this client.";
    }
    exit;
}

// Pagination handler for employee AJAX requests
if (isset($_GET['employee_index']) && isset($_GET['page'])) {
    $employeeIndex = (int) $_GET['employee_index'];
    $employeeIds = array_keys($employees);

    // Check if the employee index is valid
    if (!isset($employeeIds[$employeeIndex])) {
        echo "Invalid employee index.";
        exit;
    }

    $employeeId = $employeeIds[$employeeIndex];
    $employeeFiles = $employees[$employeeId]['files'];

    // Check if employeeFiles is empty
    if (empty($employeeFiles)) {
        echo "No files found for this employee.";
        exit;
    }

    $total_files = count($employeeFiles);
    $total_pages = ceil($total_files / $limit);
    $paginated_files = array_slice($employeeFiles, $offset, $limit);

    if (!empty($paginated_files)) {
        echo '<div class="file-grid">';
        foreach ($paginated_files as $index => $file) {
            if ($index % 2 == 0) {
                echo '<div class="file-row">';
            }
            ?>
            <div class="file-card">
                <div class="file-name"><?php echo htmlspecialchars($file); ?></div>
                <a class="edit-button" href="processes/download-logic.php?file=<?php echo urlencode($file); ?>" download>Download</a>
            </div>
            <?php
            if ($index % 2 == 1) {
                echo '</div>';
            }
        }
        if ($index % 2 == 0) {
            echo '</div>';
        }
        echo '</div>';

        // Pagination Links
        echo '<div class="pagination">';
        if ($page > 1) {
            echo '<a href="javascript:void(0);" onclick="previousEmployeePage(' . $employeeIndex . ', ' . $page . ')">Previous</a>';
        }
        if ($page < $total_pages) {
            echo '<a href="javascript:void(0);" onclick="nextEmployeePage(' . $employeeIndex . ', ' . $page . ')">Next</a>';
        }
        echo '</div>';
    } else {
        echo "No files found for this employee.";
    }
    exit;
}

// Folder pagination
$total_folders = count($clients);
$total_folder_pages = ceil($total_folders / $folderLimit);
$paginated_clients = array_slice($clients, $folderOffset, $folderLimit);

// Folder pagination for employees
$total_employee_folders = count($employees);
$total_employee_folder_pages = ceil($total_employee_folders / $folderLimit);
$paginated_employees = array_slice($employees, $folderOffset, $folderLimit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<style>
    .file-grid {
    display: flex;
    flex-direction: column;
}

.file-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.file-card {
    flex: 1;
    margin: 0 10px;
}
</style>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>Client Files</h2>
            </div>
            <hr>
            <div class="container" id="details-container">
                <?php if (empty($clients)): ?>
                    <p>No clients found.</p>
                <?php else: ?>
                    <div class="folder-container">
                        <?php foreach ($paginated_clients as $index => $client): ?>
                            <div class="folder"
                                onclick="window.location.href='view-client-files.php?email=<?php echo urlencode($client['email']); ?>'">
                                <h3>
                                    <span class="material-icons-outlined folder-icon">folder</span>
                                    <?php echo htmlspecialchars($client['name']); ?>
                                    <span class="folder-marker" id="marker-<?php echo $index; ?>"></span>
                                </h3>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $total_folder_pages; $i++): ?>
                            <a href="?folder_page=<?php echo $i; ?>"
                                class="<?php echo ($i === $folderPage) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="main-title">
                <h2>Employee Files</h2>
            </div>
            <hr>
            <div class="container" id="employee-details-container">
                <?php if (empty($employees)): ?>
                    <p>No employees found.</p>
                <?php else: ?>
                    <div class="folder-container">
                        <?php foreach ($paginated_employees as $index => $employee): ?>
                            <div class="folder"
                                onclick="window.location.href='view-client-files.php?email=<?php echo urlencode($employee['email']); ?>'">
                                <h3>
                                    <span class="material-icons-outlined folder-icon">folder</span>
                                    <?php echo htmlspecialchars($employee['name']); ?>
                                    <span class="folder-marker" id="employee-marker-<?php echo $index; ?>"></span>
                                </h3>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $total_employee_folder_pages; $i++): ?>
                            <a href="?folder_page=<?php echo $i; ?>"
                                class="<?php echo ($i === $folderPage) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    function openClientFilesModal(clientIndex, clientName) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `all-client-files.php?client_index=${clientIndex}&page=1`, true);
        xhr.onload = function () {
            if (this.status === 200) {
                Swal.fire({
                    title: `Files of ${clientName}`,
                    html: this.responseText,
                    width: '70%',
                    showCloseButton: true,
                    showCancelButton: false,
                    focusConfirm: false,
                    confirmButtonText: 'Close'
                });
            } else {
                console.error('Failed to load files:', this.statusText);
                alert('Error loading files. Please try again later.');
            }
        };
        xhr.onerror = function () {
            console.error('Request error.');
            alert('An error occurred while making the request. Please check your internet connection.');
        };
        xhr.send();
    }

    function loadPage(clientIndex, page) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `all-client-files.php?client_index=${clientIndex}&page=${page}`, true);
        xhr.onload = function () {
            if (this.status === 200) {
                Swal.update({
                    html: this.responseText
                });
            } else {
                console.error('Failed to load files:', this.statusText);
                alert('Error loading files. Please try again later.');
            }
        };
        xhr.onerror = function () {
            console.error('Request error.');
            alert('An error occurred while making the request. Please check your internet connection.');
        };
        xhr.send();
    }

    function nextPage(clientIndex, currentPage) {
        loadPage(clientIndex, currentPage + 1);
    }

    function previousPage(clientIndex, currentPage) {
        if (currentPage > 1) {
            loadPage(clientIndex, currentPage - 1);
        }
    }

    function openEmployeeFilesModal(employeeIndex, employeeName) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `all-client-files.php?employee_index=${employeeIndex}&page=1`, true);
        xhr.onload = function () {
            if (this.status === 200) {
                Swal.fire({
                    title: `Files of ${employeeName}`,
                    html: this.responseText,
                    width: '70%',
                    showCloseButton: true,
                    showCancelButton: false,
                    focusConfirm: false,
                    confirmButtonText: 'Close'
                });
            } else {
                console.error('Failed to load files:', this.statusText);
                alert('Error loading files. Please try again later.');
            }
        };
        xhr.onerror = function () {
            console.error('Request error.');
            alert('An error occurred while making the request. Please check your internet connection.');
        };
        xhr.send();
    }

    function loadEmployeePage(employeeIndex, page) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `all-client-files.php?employee_index=${employeeIndex}&page=${page}`, true);
        xhr.onload = function () {
            if (this.status === 200) {
                Swal.update({
                    html: this.responseText
                });
            } else {
                console.error('Failed to load files:', this.statusText);
                alert('Error loading files. Please try again later.');
            }
        };
        xhr.onerror = function () {
            console.error('Request error.');
            alert('An error occurred while making the request. Please check your internet connection.');
        };
        xhr.send();
    }

    function nextEmployeePage(employeeIndex, currentPage) {
        loadEmployeePage(employeeIndex, currentPage + 1);
    }

    function previousEmployeePage(employeeIndex, currentPage) {
        if (currentPage > 1) {
            loadEmployeePage(employeeIndex, currentPage - 1);
        }
    }
</script>

    <script src="assets/js/script.js"></script>
</body>

</html>