<?php
date_default_timezone_set('Asia/Manila'); // Set timezone to Asia/Manila
// Ensure $user_email is defined
$user_email = $_SESSION['user_email'] ?? 'default@example.com';

// Delete files older than 7 days based on upload_date in the files table
$stmt = $conn->prepare("SELECT id, file_name, upload_date FROM files WHERE user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

while ($file = $result->fetch_assoc()) {
    $file_path = "../uploads/" . $file['file_name'];
    if (is_file($file_path) && strtotime($file['upload_date']) <= strtotime('-7 days')) {
        unlink($file_path);
        // Log the deletion in the activity log
        $action = "Automatic Deletion by the system"; ;
        $stmt_log = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
        $stmt_log->bind_param("sss", $user_email, $action, $file['file_name']);
        if (!$stmt_log->execute()) {
            error_log("Failed to insert into activity log: " . $stmt_log->error);
        }
        // Delete the file record from the database
        $stmt_delete = $conn->prepare("DELETE FROM files WHERE id = ?");
        $stmt_delete->bind_param("i", $file['id']);
        if (!$stmt_delete->execute()) {
            error_log("Failed to delete file record: " . $stmt_delete->error);
        }
    }
}
?>
<div class="files-title-header">
    <h2 class="card-title"></h2>  
    <div class="button-container">
        <button class="popup-button">Upload</button>
    </div>
</div>

<!-- Note about file deletion -->
<p class="deletion-note">Note: All submitted files will be automatically deleted by the system after 7 days</p>

<?php if ($upload_message): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: '<?php echo htmlspecialchars($upload_message); ?>',
                icon: '<?php echo strpos($upload_message, 'successfully') !== false ? 'success' : 'error'; ?>',
                confirmButtonText: 'OK'
            }).then(() => {
                if (window.shouldShowLoader) {
                    document.querySelector('.loader-wrapper').style.display = 'block';
                }
            });
        });
    </script>
<?php endif; ?>

<form id="uploadForm" method="POST" action="../clientside/uploadFile.php" enctype="multipart/form-data">
    <div class="upload-container hidden">
        <div class="form-group">
            <label class="form-label" for="file">Choose File:</label>
            <input class="form-input" type="file" id="file" name="file" required />
        </div>
        <div class="form-group">
            <label class="form-label" for="file_description">File Description:</label>
            <select class="form-input" id="file_description" name="file_description">
                <option value="" disabled selected>Select File Description</option>
                <option value="Identification Documents">Identification Documents</option>
                <option value="Income Verification">Income Verification</option>
                <option value="Credit Information">Credit Information</option>
                <option value="Proof of Residence">Proof of Residence</option>
                <option value="Legal Documents">Legal Documents</option>
                <option value="Down Payment">Down Payment</option>
            </select>
        </div>
        <div class="button-container">
            <button class="submit-button" type="submit">Upload</button>
        </div>
    </div>
</form>

<div class="file-header">
    <span class="file-header-item">File Name:</span>
    <span class="file-header-item">Description:</span>
    <span class="file-header-item">Date:</span>
    <span class="file-header-item">Actions:</span>
</div>
<div class="file-list">
    <?php
    $stmt = $conn->prepare("SELECT id, file_name, file_description, upload_date FROM files WHERE user_email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    $has_files = false;

    if ($result->num_rows > 0):
        $has_files = true;
        while ($file = $result->fetch_assoc()):
            ?>
            <div class="file-item">
                <div class="file-details">
                    <img aria-hidden="true" alt="file-icon" src="assets/images/file-icon.png" class="file-icon" />
                    <span><?php echo htmlspecialchars($file['file_name']); ?></span>
                </div>
                <div class="file-description">
                    <h1 class="responsive-header">Description:</h1>
                    <span><?php echo htmlspecialchars($file['file_description']); ?></span>
                </div>
                <div class="file-date">
                    <h1 class="responsive-header">Date:</h1>
                    <span><?php echo date('F d,Y', strtotime($file['upload_date'])); ?></span>
                </div>
                <div class="file-actions">
                    <button class="preview-button" data-file-path="../uploads/<?php echo htmlspecialchars($user_email); ?>/<?php echo htmlspecialchars($file['file_name']); ?>"><i class="fas fa-eye"></i></button>
                    <a class="edit-button download-link" href="../uploads/<?php echo htmlspecialchars($user_email); ?>/<?php echo htmlspecialchars($file['file_name']); ?>" download><i class="fas fa-download"></i></a>
                    <form method="POST" action="deleteFile.php" class="delete-form" style="display:inline;">
                        <input type="hidden" name="file_id" value="<?php echo htmlspecialchars($file['id']); ?>">
                        <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($file['file_name']); ?>">
                        <button id="delete-button" type="submit"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <!-- Fetch and display transaction softcopies -->
    <?php
    $transaction_dir = "uploads/orcr/{$user_email}/";
    if (is_dir($transaction_dir)) {
        $transaction_folders = scandir($transaction_dir);
        foreach ($transaction_folders as $transaction_id) {
            if ($transaction_id !== '.' && $transaction_id !== '..') {
                $transaction_files = scandir($transaction_dir . $transaction_id);
                foreach ($transaction_files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $has_files = true;
                        // Fetch receive_at date from appointments table
                        $stmt = $conn->prepare("SELECT recieve_at FROM appointments WHERE transaction_id = ?");
                        $stmt->bind_param("s", $transaction_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $appointment = $result->fetch_assoc();
                        $receive_at = $appointment['recieve_at'] ?? null;
                        
                        // Check if the file is older than 7 days
                        if ($receive_at && strtotime($receive_at) <= strtotime('-7 days')) {
                            // Delete the file
                            unlink($transaction_dir . $transaction_id . '/' . $file);
                            
                           // Log the deletion in the activity log
                            $action = "Automation deletion by the system";
                            $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
                            $stmt->bind_param("sss", $user_email, $action, $file);
                            if (!$stmt->execute()) {
                                error_log("Failed to insert into activity log: " . $stmt->error);
                            }
                            continue;
                        }
                        ?>
                        <div class="file-item">
                            <div class="file-details">
                                <img aria-hidden="true" alt="file-icon" src="assets/images/file-icon.png" class="file-icon" />
                                <span><?php echo htmlspecialchars($file); ?></span>
                            </div>
                            <div class="file-description">
                                <h1 class="responsive-header">Transaction ID:</h1>
                                <span><?php echo htmlspecialchars($transaction_id); ?></span>
                                <p class="system-upload-note" style="font-size: 12px; font-style: italic;">Note: This file was uploaded by the system.</p>
                                <p class="system-upload-note" style="font-size: 12px; font-style: italic;">Auto Deletion Date: <span style="font-weight: bold;"><?php echo date('F d,Y h:i A', strtotime($receive_at . ' +7 days')); ?></p></span> 
                            </div>
                            <div class="file-date">
                                <h1 class="responsive-header">Date:</h1>
                                <span><?php echo date('F d,Y h:i A', filemtime($transaction_dir . $transaction_id . '/' . $file)); ?></span>
                            </div>
                            <div class="file-actions">
                                <button class="preview-button" data-file-path="<?php echo $transaction_dir . $transaction_id . '/' . htmlspecialchars($file); ?>"><i class="fas fa-eye"></i></button>
                                <a class="edit-button download-link" href="<?php echo $transaction_dir . $transaction_id . '/' . htmlspecialchars($file); ?>" download><i class="fas fa-download"></i></a>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
        }
    }
    if (!$has_files) {
        echo '<p style="text-align:center; font-weight:bolder;">No Files Available</p>';
    }
    ?>
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

<script src="../clientside/assets/js/script.js"></script>
<script>
    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        var fileInput = document.getElementById('file');
        var fileDescription = document.getElementById('file_description');
        const validFileTypes = ['image/webp', 'image/jpeg', 'application/pdf', 'image/png', 'image/svg+xml'];
        
        if (!fileInput.value) {
            event.preventDefault();
            Swal.fire({
                title: 'Error!',
                text: 'Please select a file to upload.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        if (!fileDescription.value) {
            event.preventDefault();
            Swal.fire({
                title: 'Error!',
                text: 'Please select a file description.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }

        const file = fileInput.files[0];
        if (file) {
            const fileType = file.type;
            if (!validFileTypes.includes(fileType)) {
                event.preventDefault();
                Swal.fire({
                    title: 'Invalid File Type',
                    text: 'Please upload .webp, .jpg, .pdf, .png, or .svg files only.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                fileInput.value = ''; // Clear the file input
                return false;
            }
        }
    });

    // File preview functionality
    document.querySelectorAll('.preview-button').forEach(button => {
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

    document.querySelector('.close-button').addEventListener('click', function() {
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

    // Add confirmation prompt for file deletion
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this file?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#585a5e',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Add confirmation prompt for file download
    document.querySelectorAll('.download-link').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var href = this.getAttribute('href');
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to download this file?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#585a5e',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    var a = document.createElement('a');
                    a.href = href;
                    a.download = '';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            });
        });
    });
</script>