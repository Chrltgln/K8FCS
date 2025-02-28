<?php
include 'settings/check-if-loggedin.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'required/header.php'; ?>
    <title>K8 Financial Consultancy Services</title>
    <link rel="icon" href="assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/loader.css"><!-- Style for loader -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><!-- JQuery for loader -->
    <script src="assets/js/loader.js" defer></script><!-- Script for loader -->
    <script src="assets/js/img-control.js" defer></script>

</head>

<body>
    <!-- LOADER -->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <?php require 'required/navbar.php'; ?>


    <section class="header-container" style="background-image: url('assets/images/clientheader.webp')">
        <div class="header">
            <div class="logoContainer">
                <img src="assets/images/updated-logo.webp" id="logoheader">
            </div>
            <div class="header-title">
                <h1 id="k8fcsheadertitle">K8 Financial Consultancy Services</h1>
                <p id="k8fcsheaderdesc">Unlock the Keys to Your New Ride with Our Car Loans</p>
            </div>
        </div>
    </section>

    <br>

    <div class="background text-foreground">
        <section class="section-container">
            <div class="section-content">
                <div class="section-text">
                    <h2 class="section-subtitle" id="first-section-subtitle">About Us</h2>
                </div>
                <div class="section-body">
                    <div class="section-image">
                        <img src="assets/images/aboutus.webp" height="400" width="600" class="image-rounded"
                            id-="aboutUsPic">
                    </div>
                    <div class="section-titleparagraph">
                        <h1 class="section-title">K8 Financial Consultancy Services</h1>
                        <p class="section-paragraph">K8 Financial Consultancy Services offers expert financial guidance
                            tailored to
                            individual and business needs. Specializing in financial planning, investment strategies,
                            and risk management.
                            K8 FCS is dedicated to helping clients achieve financial stability and growth. K8 FCS is
                            committed to delivering high-quality services with transparency, integrity, and
                            professionalism,
                            making it a trusted partner in navigating complex financial landscapes. For more
                            information, click Learn More.</p>
                        <button class="button" onclick="window.location.href='php/aboutus.php'">Learn More</button>
                    </div>
                </div>
            </div>
    </div>
    </section>

    <section class="section-container muted-background">
        <div class="section-content">
            <div class="section-text">
                <h2 class="section-subtitle">Services</h2>
            </div>
            <div class="section-body">
                <div class="section-image">
                    <img src="assets/images/aboutus2.webp" height="400" width="600" class="image-rounded"
                        id-="aboutUsPic">
                </div>
                <div class="section-titleparagraph">
                    <h1 class="section-title">Sangla OR/CR</h1>
                    <p class="section-paragraph">Sangla OR/CR is a financial service that allows vehicle owners to
                        secure a loan using their vehicle’s
                        Official Receipt (OR) and Certificate of Registration (CR) as collateral. This loan option
                        provides quick access to funds
                        without the need to surrender the vehicle, making it a convenient solution for those in need of
                        immediate financial
                        assistance. The process is straightforward, with flexible repayment terms and competitive
                        interest rates, ensuring
                        that clients can manage their finances effectively while retaining use of their vehicle.
                    </p>
                    <button class="button" onclick="window.location.href='php/services.php'">Learn More</button>
                </div>
            </div>
        </div>
        </div>
    </section>

    <section class="section-container">
        <div class="section-content reveal">
            <div class="section-text">
                <h2 class="section-subtitle">Location</h2>
            </div>
            <div class="section-body">
                <div class="section-image">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d483.15791503704594!2d120.9198791!3d14.3541672!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d25e52fda957%3A0x4ca503d2a1072b58!2sK8%20Financial%20Consultancy%20Services!5e0!3m2!1sen!2sph!4v1732113029021!5m2!1sen!2sph"
                        width="600" height="400" style="border:0;" allowfullscreen="" loading="lazy" id="k8map"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="section-titleparagraph">
                    <h1 class="section-title">Where are we located?</h1>
                    <p class="section-paragraph" id="last-section-paragraph">K8 Financial Consultancy Services was established in March 2006 and its first location was at Palico, 
                        4 Emilio Aguinaldo Highway, Imus, Cavite in AMJ8 BUILDING. In the year 2021, it is now located in Phase 1 Block 37 Lot 2-A Mary 
                        Cris Complex Pasong Camachile 2 General Trias, Cavite.</p>
                </div>
            </div>
        </div>
    </section>
    <?php include 'required/footer.php'; ?>
    </div>




    <script src="assets/js/script.js"></script><!-- Script for Reveal Animation -->


</body>

</html>