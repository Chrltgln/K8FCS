<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../required/headerOnLogin.php' ?>
    <title>Meet the Team</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/meettheteam.css">
    <link rel="stylesheet" href="../assets/css/loader.css"><!-- Style for loader -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><!-- JQuery for loader -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <?php require '../required/navbarOnLogin.php' ?>

    <section class="header-container" style="background-image: url('../assets/images/clientheader.webp');">
        <div class="header">
            <div class="header-title">
                <h1 id="meettheteamheadertitle">Meet The Team</h1>
                <p id="meettheteamheaderdesc">K8 Financial Consultancy Services, we pride ourselves on our dedicated and
                    skilled team of
                    financial professionals who are committed to your success.</p>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="team-container">
            <div class="team-card">
                <div class="team-img">
                     <img src="../assets/images/mildred.webp" alt="Mildred Peña">
                </div>
                <h2>Mildred Peña</h2>
                <p><strong>Owner of K8 FCS</strong></p>
                <p class="team-member-description">The owner of K8FCS is a visionary leader, driving innovation and
                    growth in financial services, focused on client satisfaction, operational excellence, and expanding
                    the company’s reach in the industry.</p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/mildred.pena.353"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
            <div class="team-card">
                <div class="team-img">
                    <img src="../assets/images/joybaltazar.webp" alt="Joy Baltazar">
                </div>
                <h2>Joy Baltazar</h2>
                <p><strong>Marketing (Auto Loan)</strong></p>
                <p class="team-member-description">Marketing an auto loan focuses on attracting customers with
                    competitive rates, flexible terms, and quick approvals,
                    using online ads, social media, and dealership partnerships with dealerships to reach a broader
                    audience.</p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/jhoy.silos.baltazar"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
            <div class="team-card">
                <div class="team-img">
                    <img src="../assets/images/Nelia Liongson.webp" alt="Nelia Liongson">
                </div>
                <h2>Nelia Liongson</h2>
                <p><strong>Marketing (Car Insurance)</strong></p>
                <p class="team-member-description">Marketing car insurance emphasizes affordable, comprehensive
                    coverage, and reliable customer support.
                    Targeted campaigns, partnerships with car dealerships and policy sign-ups effectively.</p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/manelia.liongson?mibextid=ZbWKwL"><i
                            class="fab fa-facebook-f"></i></a>
                </div>
            </div>
        </div>
    </section>

    <?php require '../required/footerOnLogin.php' ?>
    <script src="../assets/js/loader.js"></script><!-- Script for loader -->
</body>

</html>