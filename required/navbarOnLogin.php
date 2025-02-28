<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/required/navbarOnLogin.css">
    <!-- FontAwesome for the user icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Navbar</title>
</head>

<body>
    <nav class="navbar">
        <div class="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <a href="homepage">

            <div class="logo-container">
                    <img src="../assets/images/updated-logo.webp" alt="K8 FCS logo" class="logo">
                    <span class="title">K8 FCS</span>
            </div>
        </a>
        <ul class="nav-links">
            <li class="nav-item"><a href="../clientside/homepage" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="../clientside/aboutus" class="nav-link">About Us</a></li>
            <li class="nav-item"><a href="../clientside/services" class="nav-link">Services</a></li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropbtn" id="applyDropdownBtn">Apply Now</a>
                <div id="applyDropdown" class="dropdown-content">
                    <a href="../clientside/brand-new" class="dropdown-item">Brand New</a>
                    <a href="../clientside/second-hand" class="dropdown-item">Second Hand</a>
                    <a href="../clientside/sangla-orcr" class="dropdown-item">Sangla OR/CR</a>
                </div>
            </li>
            <li class="nav-item"><a href="../clientside/email-us" class="nav-link">Contact Us</a></li>
        </ul>
        <div class="auth-links">
            <div class="dropdown">
                <a href="#" class="nav-link dropbtn" id="userDropdownBtn">
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                </a>
                <div id="userDropdown" class="dropdown-content">
                    <a href="../clientside/profile" class="dropdown-item">Profile</a>
                    <a href="../php/logout" class="dropdown-item">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-mobile">
            <img src="../assets/images/updated-logo.webp" alt="K8 FCS logo" class="logo">
            <span class="title">K8 FCS</span>
        </div>
        <ul class="sidebar-links">
            <li><a href="../clientside/homepage" class="nav-link">Home</a></li>
            <li><a href="../clientside/aboutus" class="nav-link">About Us</a></li>
            <li><a href="../clientside/services" class="nav-link" id="services">Services</a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn" id="applyDropdownBtnMobile">Apply Now</a>
                <div id="applyDropdownMobile" class="dropdown-content">
                    <a href="../clientside/brand-new" class="nav-link">Brand New</a>
                    <a href="../clientside/second-hand" class="nav-link">Second Hand</a>
                    <a href="../clientside/sangla-orcr" class="nav-link">Sangla OR/CR</a>
                </div>
            </li>
            <li><a href="../clientside/email-us" class="nav-link">Contact Us</a></li>
        </ul>
    </div>

    <script src="../assets/js/dropdown.js" defer></script>
    <script src="../assets/js/hamburger.js" defer></script>
    <script src="../assets/js/loader.js" defer></script>
</body>

</html>