<?php 
include '../settings/check-if-loggedin.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../required/header.php'; ?>
    <link rel="stylesheet" href="../assets/css/aboutus.css">
    <link rel="stylesheet" href="../assets/css/loader.css"><!-- Style for loader -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><!-- JQuery for loader -->
    <script src="../assets/js/img-control.js" defer></script>
    <title>About Us</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
</head>

<body>
    <!-- LOADER -->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <?php require '../required/navbar.php'; ?>


    <section class="header-container" style="background-image: url('../assets/images/clientheader.webp');">
        <div class="header">
            <div class="header-title">
                <h1 id="aboutusheadertitle">About Us</h1>
                <p id="aboutusheaderdesc">Learn more about K8 Financial Consultancy Services.</p>
            </div>
        </div>
    </section>


    <div class="background text-foreground">
        <section class="section-container">
            <div class="section-content reveal">
                <div class="section-text">
                    <h2 class="section-subtitle" id="first-section-subtitle">Our Purpose</h2>
                </div>
                    <div class="section-body">
                    <h1 class="section-title">What do we do?</h1>
                        <p class="section-paragraph">K8 Financial Consultancy Services has been empowering
                            car owners for over 18 years. We are a trusted partner,
                            helping you leverage your car's value through:
                        </p>
                        <p class="section-paragraph"><strong>Quick & Easy Cash Loans:</strong> Get the cash you need using
                            just your car's OR/CR (Official Receipt & Certificate of
                            Registration).
                        </p>
                        <p class="section-paragraph"><strong>Dream Car Financing:</strong> Turn your sights on a brand new or
                        pre-owned car. We'll help you finance it smoothly.
                        </p>
                        <p class="section-paragraph">K8 is Your One-Stop Shop for Car-Related Financial
                        Solutions.
                        </p>
                    </div>
                </div>      
            </div>
        </section>

        <section class="section-container muted-background">
        <div class="section-content reveal">
            <div class="section-text">
                <h2 class="section-subtitle">Meet The Team</h2>
            </div>
            <div class="section-body">
                    <h1 class="section-title">Who are they?</h1>
                    <p class="section-paragraph">Meet the team at K8 Financial Consultancy Services, where expertise and dedication drive 
                        our success. Our professionals bring a wealth of experience in financial planning and consultancy, committed to 
                        delivering personalized solutions and exceptional service. Get to know the individuals who are passionate about 
                        helping you achieve your financial goals with integrity and professionalism. Click Learn More to see the team.
                    </p>
                    <button class="button"  onclick="window.location.href='meettheteam.php'" target="_blank" target="_blank">Learn More</button>
            </div>
        </div>
        </div>
    </section>

    <section class="section-container">
        <div class="section-content reveal">
            <div class="section-text">
                <h2 class="section-subtitle">Contact Us</h2>
            </div>
            <div class="section-body">
                    <h1 class="section-title">Questions about our Services?</h1>
                    <p class="section-paragraph">Need assistance or have questions? Reach out to our team for prompt support. Click Learn More for contact information.
                    </p>
                    <button class="button" onclick="window.location.href='email-us.php'" target="_blank">Learn More</button>
            </div>
        </div>
        </div>
    </section>

    <section class="section-container muted-background">
        <div class="section-content reveal">
            <div class="section-text">
                <h2 class="section-subtitle">Frequently Asked Questions (FAQs):</h2>
            </div>
            <div class="section-body">
                <button class="accordion">REQUIREMENTS FOR EMPLOYED</button>
                <div class="panel">
                    <p>*2 VALID ID'S (if married/co-borrower submit each)</p>
                    <p>*Updated Certificate of Employee (COE)</p>
                    <p>*Latest 3 months payslip</p>
                    <p>*Bank payroll statement</p>
                    <p>*Latest Proof of Billing</p>
                </div>
                <button class="accordion">REQUIREMENTS FOR BUSINESS OWNER</button>
                <div class="panel">
                    <p>*2 VALID ID'S (both spouses if married/ comaker)</p>
                    <p>*DTI</p>
                    <p>*6 mos latest bank statement or copy of updated
                    passbook</p>
                    <p>*Trade References</p>
                    <p>*3 Major Clients</p>
                    <p>*3 Major Suppliers</p>
                    <p>*Latest Proof of Billing (Business and Home Address)</p>
                </div>
                <button class="accordion">REQUIREMENTS FOR OFW/SEAMAN</button>
                <div class="panel">
                    <p>*2 Govt issued VALID ID’S</p>
                    <p>*Proof of billing</p>
                    <p>*Passport</p>
                    <p>*Latest Contract</p>
                    <p>*3 months latest payslip</p>
                    <p>*Proof of Bank Remittance</p>
                </div>
                <button class="accordion">REFINANCING PROCEDURE</button>
                <div class="panel">
                    <p>Cash loan Using OR/CR as collateral procedure</p>
                    <p>*Fill out the K8FCS Application form.</p>
                    <p>*Expect a call for the initial interview and more
information followed by the list of necessary
requirements that we will send to you.</p>
                    <p>*2 to 3 working days approval and appraisal.</p>
                    <p>*After approval and appraisal pwede na po malaman:</p>
                    <p>- LOAN AMOUNT</p>
                    <p>- TERMS</p>
                    <p>- MONTHLY</p>
                    <p>- CHATTEL FEE</p>
                    <p>- COMPUTATION OF COMPREHENSIVE INSURANCE</p>
                </div> 
            </div>
        </div>
        </div>
    </section>

        <?php include '../required/footer.php'; ?>
    </div>
    <script src="../assets/js/loader.js"></script><!-- Script for loader -->
    <script src="../assets/js/script.js"></script><!-- Script for Reveal Animation -->
</body>

</html>
