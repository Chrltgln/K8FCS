<?php
include '../settings/authenticate.php';
checkUserRole(['Admin']);
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
    echo "No client found with this email.";
    exit;
}
$sort_option = $_GET['sort'] ?? 'asc';
$sort_order = $sort_option === 'desc' ? 'DESC' : 'ASC';

// Fetch client files based on the email
$fileSql = "
    SELECT file_name, file_description, upload_date, NULL AS transaction_id
    FROM files
    WHERE user_email = ?
    UNION ALL
    SELECT ORCR_filename AS file_name, 'ORCR File' AS file_description, appointed_at AS upload_date, fsa.transaction_id
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Client Details</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="../clientside/assets/css/profile.css"> <!-- Include profile.css for modal styles -->
    <script>
        function toggleCard(cardId) {
            const cardContent = document.getElementById(cardId);
            cardContent.style.display = cardContent.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>FILES</h2>
            </div>

            <div class="container">
                <a href="pending-appointments.php" class="back-button">
                    <span class="material-icons-outlined">arrow_back</span> Back
                </a>

                <div class="client-files-container">
                    <h2 class="client-files-title">Documents of <?php echo $firstName . ' ' . $lastName; ?></h2>
                    <?php if ($fileResult->num_rows > 0): ?>
                        <?php while ($row = $fileResult->fetch_assoc()): ?>
                            <div class="file-card">
                                <div class="file-name"><?php echo htmlspecialchars($row['file_name']); ?></div>
                                <div class="file-action-buttons">
                                    <a class="edit-button" id="preview-view-client-files" data-file-path="/K8FCS/uploads/<?php echo htmlspecialchars($email); ?>/<?php echo htmlspecialchars($row['file_name']); ?>">Preview</a>
                                    <?php if ($row['file_description'] === 'ORCR File'): ?>
                                        <a class="edit-button" id="download-view-client-files"
                                            href="processes/download.php?file=<?php echo urlencode($row['file_name']); ?>&type=orcr&email=<?php echo urlencode($email); ?>&transaction_id=<?php echo urlencode($row['transaction_id']); ?>"
                                            download>Download</a>
                                    <?php else: ?>
                                        <a class="edit-button" id="download-view-client-files"
                                            href="processes/download.php?file=<?php echo urlencode($row['file_name']); ?>&type=client&email=<?php echo urlencode($email); ?>"
                                            download>Download</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No files found for this user.</p>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal for file preview -->
    <div id="filePreviewModal" class="modal">
        <div class="modal-content-preview">
            <div class="modal-header-container">
                <span class="close-preview-button">&times;</span>
            </div>
            <div id="imagePreviewContainer">
                <img id="preview-picture" src="" alt="Preview Image">
                <object id="preview-object" data="" type="application/pdf" width="120%" height="600px" style="display: none; overflow: auto;"></object>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        // File preview functionality
        document.querySelectorAll('#preview-view-client-files').forEach(button => {
            button.addEventListener('click', function() {
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

                document.getElementById('filePreviewModal').style.visibility = 'visible';
                document.getElementById('filePreviewModal').style.opacity = '1';
            });
        });

        document.querySelector('.close-preview-button').addEventListener('click', function() {
            document.getElementById('filePreviewModal').style.visibility = 'hidden';
            document.getElementById('filePreviewModal').style.opacity = '0';
            document.getElementById('preview-picture').src = '';
            document.getElementById('preview-object').data = '';
        });

        window.onclick = function(event) {
            var modal = document.getElementById('filePreviewModal');
            if (event.target == modal) {
                modal.style.visibility = 'hidden';
                modal.style.opacity = '0';
                document.getElementById('preview-picture').src = '';
                document.getElementById('preview-object').data = '';
            }
        };
    </script>
</body>

</html>