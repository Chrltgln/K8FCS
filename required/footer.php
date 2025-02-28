<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Dynamically set the base path
    $basePath = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) ? '/K8FCS/' : '/';
    ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/footer.css">
</head>

<body>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-image-container">
            <div class="footer-item" id="k8logofooter">
                <a href="<?php echo $basePath; ?>index.php">
                    <img src="<?php echo $basePath; ?>assets/images/updated-logo.webp" alt="K8 FCS Logo" class="footer-logo">
                </a>
                <div class="footer-content">
                    <h2 class="footer-title-h2" id="k8fcstitle">K8 FCS</h2>
                </div>
            </div>
        </div>
        <div class="footer-item-container">
            <div class="footer-items-header">
                <div class="footer-item" id="footer-applynow">
                    <h2 class="footer-title">Apply Now</h2>
                    <p class="footer-text">
                        <a onclick="showSwal('Brand New')">Purchase Brand New</a><br>
                        <a onclick="showSwal('Second Hand')">Purchase Second Hand</a><br>
                        <a onclick="showSwal('Sangla OR/CR')">Sangla OR/CR</a>
                    </p>
                </div>
                <div class="footer-item">
                    <h2 class="footer-title">About Us</h2>
                    <p class="footer-text">
                        <a href="https://www.facebook.com/k8carloan" target="_blank">Facebook</a><br>
                        <a href="<?php echo $basePath; ?>php/meettheteam.php">Meet the Team</a><br>
                        <a href="https://maps.app.goo.gl/g4Wxyhau343AX8ck9" target="_blank">Location</a>
                    </p>
                </div>
            </div>
            <div class="footer-items-subheader">
                <div class="footer-item">
                    <h2 class="footer-title">Contact Us</h2>
                    <p class="footer-text">0917-619-5984<br>
                    0917-528-1760<br>
                    k8_fcs@yahoo.com
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
</body>

</html>
