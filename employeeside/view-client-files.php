<?php
// Include necessary files with correct relative paths
include '../settings/authenticate.php';
checkUserRole(['Employee']);
include '../settings/config.php';

// Retrieve the client's email from the query parameters
$email = $_GET['email'] ?? '';

if (!$email) {
    echo "Error: No email provided.";
    exit;
}

// Ensure $conn is properly initialized
if (!$conn) {
    echo "Error: Database connection not established.";
    exit;
}

// Delete files older than 30 days
$deleteSql = "
    DELETE FROM files
    WHERE upload_date < NOW() - INTERVAL 30 DAY
";
$conn->query($deleteSql);

// Remove files from the uploads directory
$oldFiles = $conn->query("
    SELECT file_name 
    FROM files
    WHERE upload_date < NOW() - INTERVAL 30 DAY
");

while ($oldFile = $oldFiles->fetch_assoc()) {
    $filePath = "../uploads/" . $oldFile['file_name'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

// Fetch client information based on the email
$clientSql = "SELECT first_name, last_name FROM users WHERE email = ?";
$clientStmt = $conn->prepare($clientSql);
$clientStmt->bind_param("s", $email);
$clientStmt->execute();
$clientResult = $clientStmt->get_result();

if ($clientResult->num_rows > 0) {
    $client = $clientResult->fetch_assoc();
    $firstName = htmlspecialchars($client['first_name']);
    $lastName = htmlspecialchars($client['last_name']);
} else {
    $firstName = "Unknown";
    $lastName = "";
}

// Retrieve sort option from query parameters
$sort_option = $_GET['sort'] ?? 'asc';
$sort_order = $sort_option === 'desc' ? 'DESC' : 'ASC';

// Fetch client files and ORCR files based on the email and sort option
$fileSql = "
    SELECT file_name, file_description, DATE_FORMAT(upload_date, '%M %e, %Y %h:%i %p') AS formatted_upload_date, NULL AS transaction_id
    FROM files
    WHERE user_email = ?
    UNION ALL
    SELECT ORCR_filename AS file_name, 'ORCR File' AS file_description, DATE_FORMAT(appointed_at, '%M %e, %Y %h:%i %p') AS formatted_upload_date, fsa.transaction_id
    FROM forms_sanglaorcr_applicants fsa
    JOIN appointments a ON fsa.transaction_id = a.transaction_id
    WHERE a.email = ?
    ORDER BY file_name $sort_order
";
$fileStmt = $conn->prepare($fileSql);
$fileStmt->bind_param("ss", $email, $email);
$fileStmt->execute();
$fileResult = $fileStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Files</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/view-client-files.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- script js for search -->
    <script src="../assets/js/dropdown.js" defer></script>
    <script src="assets/js/main.js" defer></script>
    <script src="../assets/js/hamburger.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <main class="main-content">
        <div class="return-button-container" style="display: flex; justify-content: space-between;">
            <button type="button" class="return-button" onclick="window.location.href='pendingAppointment'">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>
        <div class="my-files-container">
            <div class="container">
                <div class="header">
                    <div class="header-top">
                        <div class="header-title">
                            <h1 class="my-files-title">
                                <?php echo $firstName; ?>'s Files
                            </h1>
                        </div>
                        <div class="sort-by">
                            <span>Sort by:</span>
                            <a href="?email=<?php echo urlencode($email); ?>&sort=asc"
                                class="sort-button <?php echo $sort_option == 'asc' ? 'active' : ''; ?>">A - Z</a>
                            <a href="?email=<?php echo urlencode($email); ?>&sort=desc"
                                class="sort-button <?php echo $sort_option == 'desc' ? 'active' : ''; ?>">Z - A</a>
                        </div>
                    </div>
                    <div class="header-bottom">
                        <input type="text" id="search" placeholder="Search by file name or transaction ID...">
                    </div>

                    <!-- Upload File Container -->
                    <div class="upload-file-container" id="uploadFileContainer">
                        <div class="upload-file-content">
                            <span class="close-button" id="closeButton">&times;</span>
                            <h2>Upload Files</h2>
                            <form id="uploadForm" action="processes/upload-file.php" method="post"
                                enctype="multipart/form-data">
                                <input type="file" name="files[]" id="fileInput" multiple>
                                <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($email); ?>">
                                <button type="submit" id="uploadButton">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="file-grid" id="file-grid">
                    <?php if ($fileResult->num_rows > 0): ?>
                        <?php while ($row = $fileResult->fetch_assoc()): ?>
                            <?php
                            $fileName = htmlspecialchars($row['file_name']);
                            $fileDescription = htmlspecialchars($row['file_description']);
                            $uploadDate = htmlspecialchars($row['formatted_upload_date']);
                            // Create a safe download link
                            if ($fileDescription === 'ORCR File') {
                                $transactionId = htmlspecialchars($row['transaction_id']);
                                $downloadLink = "../clientside/uploads/orcr/" . urldecode($email) . "/" . urldecode($transactionId) . "/" . rawurlencode($fileName);
                            } else {
                                $downloadLink = "../uploads/" . rawurlencode($email) . "/" . rawurlencode($fileName);
                            }
                            ?>
                            <div class="file-item">
                                <div class="file-name">
                                    <span title="<?php echo $fileName; ?>"><?php echo $fileName; ?></span>
                                    <span class="more-actions">&#x22EE;</span>
                                </div>
                                <div class="actions-popup">
                                    <a href="processes/download-file.php?file=<?php echo $fileName; ?>" class="download-button">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                                <div class="file-preview" data-file-path="<?php echo $downloadLink; ?>">
                                    <?php
                                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                    if (in_array(strtolower($fileExtension), $imageExtensions)): ?>
                                        <img src="<?php echo $downloadLink; ?>" alt="Preview of <?php echo $fileName; ?>"
                                            class="preview-image">
                                    <?php elseif (strtolower($fileExtension) == 'pdf'): ?>
                                        <img src="../assets/images/pdflogo.webp" alt="PDF Icon" class="preview-image">
                                    <?php else: ?>
                                        <img src="path/to/default/preview.png" alt="No preview available">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No files found for this client.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal for file preview -->
    <div id="filePreviewModal" class="modal">
        <div class="modal-content-preview">
            <div class="modal-header-container">
                <span class="close-preview-button">&times;</span>
            </div>
            <div id="imagePreviewContainer">
                <img id="preview-picture" src="" alt="Preview Image">
                <object id="preview-object" data="" type="application/pdf" width="120%" height="600px"
                    style="display: none; overflow: auto;"></object>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p style="text-align: center;">Copyright &copy; All rights reserved.</p>
    </footer>
    <script>
        $(document).ready(function () {
            $('#search').on('input', function () {
                var searchTerm = $(this).val();
                var email = "<?php echo htmlspecialchars($email); ?>";
                $.ajax({
                    url: 'includes/searchfiles.php',
                    type: 'POST',
                    data: { search: searchTerm, email: email },
                    success: function (data) {
                        $('#file-grid').html(data);
                        $('.no-appointments-message-wrapper').remove(); // Remove existing wrapper
                        if (data.indexOf('no-appointments-message-wrapper') !== -1) {
                            $('<div class="no-appointments-message-wrapper"><div class="no-appointments-message">No files found for this client.</div><div class="no-appointments-icon"><i class="fas fa-file-alt"></i></div></div>').insertAfter('#file-grid');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + error);
                    }
                });
            });
        });

        // prevent footer from going up when a sweetalert is shown
        document.addEventListener('DOMContentLoaded', function () {
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length) {
                        mutation.addedNodes.forEach(function (node) {
                            if (node.classList && node.classList.contains('swal2-container')) {
                                document.body.insertBefore(node, document.querySelector('footer'));
                            }
                        });
                    }
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class' && document.body.classList.contains('swal2-height-auto')) {
                        observer.disconnect(); // Disconnect observer before removing the class
                        document.body.classList.remove('swal2-height-auto');
                        observer.observe(document.body, { childList: true, attributes: true, subtree: true }); // Reconnect observer
                    }
                });
            });

            observer.observe(document.body, { childList: true, attributes: true, subtree: true });
        });

        // File preview functionality
        document.querySelectorAll('.file-preview').forEach(preview => {
            preview.addEventListener('click', function () {
                var filePath = this.getAttribute('data-file-path');
                var img = document.getElementById('preview-picture');
                var obj = document.getElementById('preview-object');

                // Reset both img and object
                img.style.display = 'none';
                img.src = '';
                obj.style.display = 'none';
                obj.data = '';

                if (filePath.endsWith('.pdf')) {
                    obj.style.display = 'block';
                    obj.data = filePath;
                } else {
                    img.style.display = 'block';
                    img.src = filePath;
                }

                var modal = document.getElementById('filePreviewModal');
                modal.style.display = 'block';
                modal.style.visibility = 'visible';
                modal.style.opacity = '1';
            });
        });

        document.querySelector('.close-preview-button').addEventListener('click', function () {
            var modal = document.getElementById('filePreviewModal');
            modal.style.display = 'none';
            modal.style.visibility = 'hidden';
            modal.style.opacity = '0';
            document.getElementById('preview-picture').src = '';
            document.getElementById('preview-object').data = '';
        });

        window.onclick = function (event) {
            var modal = document.getElementById('filePreviewModal');
            if (event.target == modal) {
                modal.style.display = 'none';
                modal.style.visibility = 'hidden';
                modal.style.opacity = '0';
                document.getElementById('preview-picture').src = '';
                document.getElementById('preview-object').data = '';
            }
        };

        // More actions functionality
        document.querySelectorAll('.more-actions').forEach(button => {
            button.addEventListener('click', function (event) {
                event.stopPropagation(); // Prevent the click event from propagating to the window
                var popup = this.closest('.file-item').querySelector('.actions-popup');
                document.querySelectorAll('.actions-popup').forEach(p => {
                    if (p !== popup) {
                        p.style.display = 'none';
                    }
                });
                popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
            });
        });

        // Close the actions popup when clicking outside
        window.addEventListener('click', function (event) {
            if (!event.target.matches('.more-actions')) {
                document.querySelectorAll('.actions-popup').forEach(popup => {
                    popup.style.display = 'none';
                });
            }
        });
    </script>
</body>

</html>

<?php
$clientStmt->close();
$fileStmt->close();
$conn->close();
?>