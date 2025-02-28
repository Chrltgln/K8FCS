<?php
include '../settings/config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $fontColor = $_POST['font-color'];
    $expiryDate = $_POST['expiry-date'];
    $uploadsDir = '../assets/images/news/';
    $backgroundImage = basename($_FILES['background-image']['name']); // Only the file name
    $fileInfo = pathinfo($backgroundImage);
    $webpImage = $fileInfo['filename'] . '.webp'; // WebP file name

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
    $tempPath = $_FILES['background-image']['tmp_name'];
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

    // Insert the news item into the database
    $query = "INSERT INTO news (title, subtitle, background_image, font_color, expiry_date) VALUES ('$title', '$subtitle', '$webpImage', '$fontColor', '$expiryDate')";
    mysqli_query($conn, $query);

    header('Location: list-news');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Update News</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function validateForm() {
            const title = document.getElementById('title').value;
            const wordCount = title.trim().split(/\s+/).length;

            if (wordCount < 3 || wordCount > 4) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Title',
                    text: 'The title must contain between 3 and 4 words.',
                });
                return false;
            }

            const image = document.getElementById('background-image').files[0];
            if (image) {
                const img = new Image();
                img.src = URL.createObjectURL(image);
                img.onload = function () {
                    const width = img.naturalWidth;
                    const height = img.naturalHeight;
                    URL.revokeObjectURL(img.src);

                    console.log(`Image dimensions: ${width}x${height}`);

                    if (width < 1500 || width > 1920 || height < 450 || height > 550) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Image Dimensions',
                            text: 'The image dimensions must be between 1500x500 and 1661x500. Preffered size is 1661 x 466.',
                        });
                        return false;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to update the news?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'News updated successfully!',
                            }).then(() => {
                                document.querySelector('form').submit();
                            });
                        }
                    });
                };
                return false;
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const expiryDateInput = document.getElementById('expiry-date');
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const currentDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            expiryDateInput.setAttribute('min', currentDateTime);

            expiryDateInput.addEventListener('input', function() {
                const selectedDateTime = new Date(expiryDateInput.value);
                if (selectedDateTime < now) {
                    const selectedDate = expiryDateInput.value.split('T')[0];
                    const currentDate = currentDateTime.split('T')[0];
                    if (selectedDate === currentDate) {
                        expiryDateInput.value = currentDateTime;
                    }
                }
            });
        });
    </script>
</head>
<body>
    <div class="grid-container">
        <?php include 'required/header.php'; ?>
        <?php include 'required/sidebar.php'; ?>

        <main class="main-container">
            <div class="main-title">
            <h2>Insert News</h2>
            </div>

            <div class="container">
                
                <form action="update-news.php" method="post" enctype="multipart/form-data" class="paymentui"
                    onsubmit="return validateForm()">
                    <div class="form-group">
                        <label for="title">Insert Title:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="subtitle">Insert Subtitle:</label>
                        <input type="text" id="subtitle" name="subtitle" required>
                    </div>
                    <div class="form-group">
                        <label for="background-image">Choose a Background Image:</label>
                        <input type="file" id="background-image" name="background-image" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="font-color">Choose Font Color:</label>
                        <select id="font-color" name="font-color" required>
                            <option value="white">White</option>
                            <option value="black">Black</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="expiry-date">Set Expiry Date:</label>
                        <input type="datetime-local" id="expiry-date" name="expiry-date" required>
                    </div>
                    <button type="submit">Submit</button>
                </form>

                <div class="charts"></div>

                    <div class="charts-card" id="pendingchart">
                        <a href="list-news.php" class="chart-title">
                            List
                        </a>
                        <div id="bar-chart"></div>
                    </div>
                </div>

                <br>
        </main>
    </div>
    <script src="assets/js/script.js"></script>
</body>

</html>