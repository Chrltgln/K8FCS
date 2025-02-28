<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/K8FCS/assets/css/navbar.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/K8FCS/assets/js/allAlerts.js" defer></script>
    <script src="/K8FCS/assets/js/dropdown.js" defer></script>
    <script src="/K8FCS/assets/js/hamburger.js" defer></script>
    <title>K8 FCS</title>
</head>

<body>
    <nav class="navbar">
        <div class="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <a href="index">

            <div class="logo-container">
                    <img src="/K8FCS/assets/images/updated-logo.webp" alt="K8 FCS logo" class="logo">
                
                <span class="title">K8 FCS</span>
            </div>
        </a>

        <ul class="nav-links">
            <li class="nav-item"><a href="/K8FCS/index" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="/K8FCS/php/aboutus" class="nav-link">About Us</a></li>
            <li class="nav-item"><a href="/K8FCS/php/services" class="nav-link">Services</a></li>
            <li class="nav-item dropdown">
                <a href="javascript:void(0);" class="nav-link dropbtn" id="applyDropdownBtn">Apply Now</a>
                <div id="applyDropdown" class="dropdown-content">
                    <a href="javascript:void(0);" class="dropdown-item" onclick="showSwal('Brand New')">Brand New</a>
                    <a href="javascript:void(0);" class="dropdown-item" onclick="showSwal('Second Hand')">Second Hand</a>
                    <a href="javascript:void(0);" class="dropdown-item" onclick="showSwal('Sangla OR/CR')">Sangla OR/CR</a>
                </div>
            </li>
            <li class="nav-item"><a href="/K8FCS/php/email-us.php" class="nav-link">Contact Us</a></li>
        </ul>
        <div class="auth-links">
            
            <a href="/K8FCS/php/login" class="auth-link">Sign In</a>
            <a href="/K8FCS/php/signup" class="auth-link">Sign Up</a>
        </div>
       
    </nav>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-mobile">
            <img src="/K8FCS/assets/images/updated-logo.webp" alt="K8 FCS logo" class="logo">
            <span class="title">K8 FCS</span>
        </div>
        <ul class="sidebar-links">
            <li><a href="/K8FCS/index">Home</a></li>
            <li><a href="/K8FCS/php/aboutus">About Us</a></li>
            <li><a href="/K8FCS/php/services">Services</a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn" id="applyDropdownBtnMobile">Apply Now</a>
                <div id="applyDropdownMobile" class="dropdown-content">
                <a href="javascript:void(0);" class="dropdown-item" onclick="showSwal('Brand New')">Brand New</a>
                    <a href="javascript:void(0);" class="dropdown-item" onclick="showSwal('Second Hand')">Second Hand</a>
                    <a href="javascript:void(0);" class="dropdown-item" onclick="showSwal('Sangla OR/CR')">Sangla OR/CR</a>
                </div>
            </li>
            <li><a href="/K8FCS/php/email-us">Contact Us</a></li>
        </ul>
    </div>
    
</body>

</html>