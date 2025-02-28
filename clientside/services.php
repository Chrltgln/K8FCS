<?php
include '../settings/authenticate.php';
checkUserRole(['Client']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../required/headerOnLogin.php' ?>
    <link rel="stylesheet" href="../assets/css/services.css">
    <link rel="stylesheet" href="../assets/css/loader.css"><!-- Style for loader -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><!-- JQuery for loader -->
    <?php include 'security/idleLogout.php' ?>
    <title>K8 FCS | SERVICES</title>
    <link rel="icon" href="../assets/images/updated-logo.png" type="image/png">
</head>

<body>
    <!-- LOADER -->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>
    <?php require '../required/navbarOnLogin.php' ?>

    <section class="header-container" style="background-image: url('../assets/images/clientheader.webp')">
        <div class="header">
            <div class="header-title">
                <h1 id="servicesheadertitle">Services</h1>
                <p id="servicesheaderdesc">Explore our specialized car loan services designed to get you behind the wall
                    with ease.</p>
            </div>
        </div>
    </section>

    <div class="services-card-container">
        <div class="services-card reveal">
            <img src="../assets/images/services/autoloan.webp" alt="Auto Loan" height="600" width="400"
                class="services-image">
            <div class="services-card-content">
                <h2>Brand New</h2>
                <p>Auto car loan is a type of financing for purchasing a vehicle, which is then paid back in monthly
                    installments over a specified period. The vehicle itself typically serves as collateral for the
                    loan, and interest rates, terms, and conditions vary based on the lender and the borrower's credit
                    profile. This type of loan allows borrowers to spread the cost of a new or used car over time,
                    making it more manageable to afford a vehicle that meets their needs.</p>
                <div class="button-container">
                    <button onclick="window.location.href='brand-new.php'">Apply Now</button>
                </div>
            </div>
        </div>

        <div class="services-card reveal">
            <img src="../assets/images/services/usedcarloan.webp" alt="Used Car Loan">
            <div class="services-card-content">
                <h2>Second Hand</h2>
                <p>A second-hand car, also known as a used car, is a vehicle that has had one or more previous owners
                    before being offered for sale again. These cars vary widely in terms of age, mileage, and condition,
                    depending on how they were maintained by their previous owners. Second-hand cars can be a practical
                    choice for car loans, offering more affordable financing options due to their lower purchase price
                    compared to new vehicles.</p>
                <div class="button-container">
                    <button onclick="window.location.href='second-hand.php'">Apply Now</button>
                </div>
            </div>
        </div>

        <div class="services-card reveal">
            <img src="../assets/images/services/sanglaorcr.webp" alt="Sangla OR/CR">
            <div class="services-card-content">
                <h2>Sangla OR/CR</h2>
                <p>Sangla OR/CR cars refer to the practice of using a vehicle's Official Receipt and Certificate of
                    Registration (OR/CR) as collateral for a loan. In this arrangement, the car owner temporarily
                    surrenders these documents to the lender while retaining possession and use of the vehicle. This
                    type of loan is often chosen for its quick approval and minimal requirements, making it a convenient
                    option for those who need immediate cash.</p>
                <div class="button-container">
                    <button onclick="window.location.href='sangla-orcr.php'">Apply Now</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../required/footerOnLogin.php' ?>
    <script src="../assets/js/loader.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Flag to detect internal navigation
            let isInternalNavigation = false;

            // Add event listeners to all internal links
            document.querySelectorAll('a.nav-link, a.dropdown-item').forEach(link => {
                link.addEventListener('click', () => {
                    isInternalNavigation = true;
                });
            });

            // Add event listener for page refresh
            window.addEventListener('beforeunload', function (e) {
                if (!isInternalNavigation && e.returnValue === undefined) {
                    navigator.sendBeacon('../php/logout.php');
                }
            });
        });
    </script>
</body>

</html>