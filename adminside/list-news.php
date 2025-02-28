<?php
include '../settings/config.php'; // Include your database connection file

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Function to delete news item and its associated image
function deleteNewsItem($id)
{
    global $conn;
    // Get the background image file name
    $query = "SELECT background_image FROM news WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $imagePath = '../assets/images/news/' . $row['background_image'];

    // Delete the image file if it exists
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Delete the news item from the database
    $query = "DELETE FROM news WHERE id = $id";
    mysqli_query($conn, $query);
}

// Function to update news item
function updateNewsItem($id, $title, $subtitle, $background_image, $font_color)
{
    global $conn;
    $query = "UPDATE news SET title = ?, subtitle = ?, background_image = ?, font_color = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssssi', $title, $subtitle, $background_image, $font_color, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Check for expired news items and delete them
$query = "SELECT id FROM news WHERE expiry_date < NOW()";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    deleteNewsItem($row['id']);
}

// Handle manual deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deleteNewsItem($id);
    header('Location: list-news.php');
    exit;
}

// Handle manual update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $font_color = $_POST['font_color'];
    
    // Handle background image upload
    if (!empty($_FILES['background_image']['name'])) {
        $uploadsDir = '../assets/images/news/';
        $backgroundImage = basename($_FILES['background_image']['name']);
        $fileInfo = pathinfo($backgroundImage);
        $webpImage = $fileInfo['filename'] . '.webp';
        
        // Ensure the uploads/news directory exists
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
        
        // Check if the WebP file already exists and append a number to make it unique
        $webpPath = $uploadsDir . $webpImage;
        $counter = 1;
        while (file_exists($webpPath)) {
            $webpImage = $fileInfo['filename'] . "($counter).webp";
            $webpPath = $uploadsDir . $webpImage;
            $counter++;
        }
        
        // Move the uploaded file to a temporary location
        $tempPath = $_FILES['background_image']['tmp_name'];
        $tempImagePath = $uploadsDir . $backgroundImage;
        move_uploaded_file($tempPath, $tempImagePath);
        
        // Check if the uploaded file is already a WebP image
        if ($fileInfo['extension'] === 'webp') {
            // Rename the uploaded WebP file to the unique name
            rename($tempImagePath, $webpPath);
        } else {
            // Convert the image to WebP format
            $imageType = exif_imagetype($tempImagePath);
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($tempImagePath);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($tempImagePath);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($tempImagePath);
                    break;
                default:
                    die('Unsupported image type');
            }
            
            // Save the image as WebP
            imagewebp($image, $webpPath);
            imagedestroy($image);
            
            // Remove the original uploaded file
            unlink($tempImagePath);
        }
        
        // Delete the old background image file
        $oldImagePath = $uploadsDir . $_POST['current_background_image'];
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }
        
        $background_image = $webpImage;
    } else {
        $background_image = $_POST['current_background_image'];
    }
    
    updateNewsItem($id, $title, $subtitle, $background_image, $font_color);
    header('Location: list-news.php');
    exit;
}

// Define how many results you want per page
$results_per_page = 8;

// Find out the number of results stored in the database
$query = "SELECT COUNT(*) AS total FROM news";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_results = $row['total'];

// Determine the total number of pages available
$total_pages = ceil($total_results / $results_per_page);

// Determine which page number visitor is currently on
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
if ($page > $total_pages) $page = $total_pages;

// Determine the SQL LIMIT starting number for the results on the displaying page
$start_from = ($page - 1) * $results_per_page;
if ($start_from < 0) $start_from = 0; // Ensure $start_from is non-negative

$query = "SELECT * FROM news LIMIT $start_from, $results_per_page";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>List News</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This would permanently delete the news item.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#585a5e',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'list-news.php?delete=' + id;
                }
            });
        }

        function editNews(id, title, subtitle, background_image, font_color) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-subtitle').value = subtitle;
            document.getElementById('current-background_image').value = background_image;
            document.getElementById('edit-font_color').value = font_color;
            document.getElementById('edit-form').style.display = 'block';
        }
    </script>
</head>

<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
                <h2>News List</h2>
            </div>

            <div class="container">
                <div class="table-wrapper" id="list-news-wrapper">
                    <div class="table" id="list-news-table">
                        <div class="table-header">
                            <div class="header-item">Title</div>
                            <div class="header-item">Subtitle</div>
                            <div class="header-item">Background Image</div>
                            <div class="header-item">Font Color</div>
                            <div class="header-item">Expiry Date</div>
                            <div class="header-item">Actions</div>
                        </div>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <div class="table-row">
                                    <div class="row-item"><?php echo $row['title']; ?></div>
                                    <div class="row-item"><?php echo $row['subtitle']; ?></div>
                                    <div class="row-item"><img
                                            src="../assets/images/news/<?php echo $row['background_image']; ?>"
                                            alt="Background Image" width="100"></div>
                                    <div class="row-item"><?php echo $row['font_color']; ?></div>
                                    <div class="row-item"><?php echo $row['expiry_date']; ?></div>
                                    <div class="row-item">
                                        <a href="javascript:void(0);"
                                        onclick="editNews('<?php echo $row['id']; ?>', '<?php echo $row['title']; ?>', '<?php echo $row['subtitle']; ?>', '<?php echo $row['background_image']; ?>', '<?php echo $row['font_color']; ?>')" class="btn">Edit</a>
                                        <a href="javascript:void(0);"
                                            onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn">Delete</a>
                                    </div>
                                </div>
                            <?php } ?>
                        </tbody>
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

            <div id="edit-form" style="display:none;">
                <h2>Edit News</h2>
                <form method="post" action="list-news.php" enctype="multipart/form-data">
                    <input type="hidden" id="edit-id" name="id">
                    <input type="hidden" id="current-background_image" name="current_background_image">
                    <label for="edit-title">Title:</label>
                    <input type="text" id="edit-title" name="title" required>
                    <label for="edit-subtitle">Subtitle:</label>
                    <input type="text" id="edit-subtitle" name="subtitle" required>
                    <label for="edit-background_image">Background Image:</label>
                    <input type="file" id="edit-background_image" name="background_image" accept="image/*">
                    <label for="edit-font_color">Font Color:</label>
                    <input type="text" id="edit-font_color" name="font_color" required>
                    <button type="submit" name="update">Update</button>
                </form>
            </div>
        </main>
    </div>
    <script src="assets/js/script.js"></script>
</body>

</html>