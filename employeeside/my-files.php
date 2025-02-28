<?php
include '../settings/authenticate.php';
checkUserRole(['Employee']);
include 'processes/fetch-files.php'; // Include the file fetching logic
?>

<!DOCTYPE html> 
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Files</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/dropdown.js" defer></script>
    <script src="../assets/js/hamburger.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="assets/js/main.js" defer></script>
    <link rel="stylesheet" href="../assets/css/my-files.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

<main class="main-content">
<div class="my-files-container">
    <div class="container">
        <div class="header">
            <div class="header-top">
                <div class="header-title">
                    <h1 class="my-files-title">Employee Files</h1>
                </div>
                <div class="sort-by">
                    <span>Sort by:</span>
                    <a href="?sort=asc" class="sort-button <?php echo $sort_option == 'asc' ? 'active' : ''; ?>">A - Z</a>
                    <a href="?sort=desc" class="sort-button <?php echo $sort_option == 'desc' ? 'active' : ''; ?>">Z - A</a>
                </div>
            </div>
            <div class="header-bottom">
                <div class="search-container" id="search-file-container">
                    <input type="text" id="search" placeholder="Search a file name..." onkeyup="searchFiles()">
                </div>
                <button class="add-files-button">+ Add Files</button>
            </div>

            <!-- Upload File Container -->
            <div class="upload-file-container" id="uploadFileContainer">
                <div class="upload-file-content">
                    <span class="close-button" id="closeButton">&times;</span>
                    <h2>Upload Files</h2>
                    <form id="uploadForm" action="processes/upload-file.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="files[]" id="fileInput" multiple>
                        <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($user_email); ?>">
                        <button type="submit" id="uploadButton">Upload</button>
                    </form>
                </div>
            </div>
        </div>
       
        <div class="file-grid" id="file-grid">
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <div class="file-item">
                        <div class="file-name">
                            <span title="<?php echo htmlspecialchars($item['name']); ?>"><?php echo htmlspecialchars($item['name']); ?></span>
                            <span class="more-actions">&#x22EE;</span>
                        </div>
                        <div class="actions-popup">
                            <a href="processes/download-file.php?file=<?php echo urlencode($item['name']); ?>" class="download-button"><i class="fas fa-download"></i> Download</a>
                            <button class="delete-button" data-file="<?php echo htmlspecialchars($item['name']); ?>"><i class="fas fa-trash-alt"></i> Delete</button>
                        </div>
                        <div class="file-preview" data-file-path="uploads/<?php echo htmlspecialchars($user_email); ?>/<?php echo htmlspecialchars($item['name']); ?>">
                            <?php 
                            $filePath = "uploads/" . htmlspecialchars($user_email) . "/" . htmlspecialchars($item['name']);
                            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                            if (in_array(strtolower($fileExtension), $imageExtensions) && file_exists($filePath)): ?>
                                <img src="<?php echo $filePath; ?>" alt="Preview of <?php echo htmlspecialchars($item['name']); ?>" class="preview-image">
                            <?php elseif (strtolower($fileExtension) == 'pdf' && file_exists($filePath)): ?>
                                <img src="../assets/images/pdflogo.webp" alt="PDF Icon" class="preview-image">
                            <?php else: ?>
                                <img src="path/to/default/preview.png" alt="No preview available">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No files found.</p>
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
            <object id="preview-object" data="" type="application/pdf" width="120%" height="600px" style="display: none; overflow: auto;"></object>
        </div>
    </div>
</div>

    <footer class="footer">
        <p style="text-align: center;">Copyright &copy; All rights reserved.</p>
    </footer>

    <script>
        // prevent footer from going up when a sweetalert is shown
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length) {
                        mutation.addedNodes.forEach(function(node) {
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
            preview.addEventListener('click', function() {
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

        document.querySelector('.close-preview-button').addEventListener('click', function() {
            var modal = document.getElementById('filePreviewModal');
            modal.style.display = 'none';
            modal.style.visibility = 'hidden';
            modal.style.opacity = '0';
            document.getElementById('preview-picture').src = '';
            document.getElementById('preview-object').data = '';
        });

        window.onclick = function(event) {
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
            button.addEventListener('click', function(event) {
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
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.more-actions')) {
                document.querySelectorAll('.actions-popup').forEach(popup => {
                    popup.style.display = 'none';
                });
            }
        });

        function searchFiles() {
            var searchTerm = document.getElementById('search').value;
            var userEmail = "<?php echo htmlspecialchars($user_email); ?>";

            $.ajax({
                url: 'includes/searchfiles.php',
                type: 'POST',
                data: { search: searchTerm, email: userEmail },
                success: function(response) {
                    $('#file-grid').html(response);
                }
            });
        }
    </script>
</body>

</html>
