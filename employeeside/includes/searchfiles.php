<?php
include '../../settings/authenticate.php';
include '../../settings/config.php';

$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';

$query = "
    SELECT file_name, file_description, DATE_FORMAT(upload_date, '%M %e, %Y %h:%i %p') AS formatted_upload_date 
    FROM files
    WHERE user_email = ? AND (file_name LIKE ? OR file_description LIKE ?)
    UNION ALL
    SELECT ORCR_filename AS file_name, 'ORCR File' AS file_description, DATE_FORMAT(appointed_at, '%M %e, %Y %h:%i %p') AS formatted_upload_date
    FROM forms_sanglaorcr_applicants fsa
    JOIN appointments a ON fsa.transaction_id = a.transaction_id
    WHERE a.email = ? AND (a.email LIKE ? OR a.transaction_id LIKE ?)
";
$searchTermWildcard = '%' . $searchTerm . '%';
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssss", $email, $searchTermWildcard, $searchTermWildcard, $email, $searchTermWildcard, $searchTermWildcard);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fileName = htmlspecialchars($row['file_name']);
        $fileDescription = htmlspecialchars($row['file_description']);
        $uploadDate = htmlspecialchars($row['formatted_upload_date']);
        // Create a safe download link
        if ($fileDescription === 'ORCR File' && isset($row['transaction_id'])) {
            $transactionId = htmlspecialchars($row['transaction_id']);
            $downloadLink = "../../clientside/uploads/orcr/". urldecode($email) . "/" . urldecode($transactionId) . "/" . rawurlencode($fileName);
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
                <div class="file-description"><?php echo $fileDescription; ?></div>
                
                <a href="processes/download-file.php?file=<?php echo urlencode($fileName); ?>" class="download-button">
                    <i class="fas fa-download"></i> Download
                </a>
                <button class="delete-button" data-file="<?php echo htmlspecialchars($item['name']); ?>"><i class="fas fa-trash-alt"></i> Delete</button>
            </div>
            <div class="file-preview" data-file-path="uploads/<?php echo htmlspecialchars($email); ?>/<?php echo htmlspecialchars($row['file_name']); ?>">
                <?php 
                $filePath = "uploads/" . htmlspecialchars($email) . "/" . htmlspecialchars($row['file_name']);
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                if (in_array(strtolower($fileExtension), $imageExtensions)): ?>
                    <img src="<?php echo $filePath; ?>" alt="Preview of <?php echo $fileName; ?>" class="preview-image">
                <?php elseif (strtolower($fileExtension) == 'pdf'): ?>
                    <img src="../assets/images/pdflogo.webp" alt="PDF Icon" class="preview-image">
                <?php else: ?>
                    <img src="../../assets/images/default-preview.png" alt="No preview available">
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="no-appointments-message-wrapper">';
    echo '<div class="no-appointments-message">No files found for this client.</div>';
    echo '<div class="no-appointments-icon"><i class="fas fa-file-alt"></i></div>';
    echo '</div>';
}
?>
<script>
    // Reinitialize more actions functionality
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
</script>
