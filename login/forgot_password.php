<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/Login.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <script src="../assets/js/script.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    .swal2-confirm {
        width: 150px;
    }
    </style>
</head>

<body>
    <!-- LOADER -->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <div class="login-container"> <!-- Changed class to match login.php -->
        <div class="logo">
            <a href="../index.php">
                <img src="../assets/images/updated-logo.webp" width="150" height="150" alt="Company Logo" />
            </a>
        </div>
        <h3 class="subheader">Forgot Password</h3> <!-- Changed h1 to h3 for consistency -->
        <label for="email" id="forget-password-label">Enter your email:</label>
        <form action="process_forgot_password.php" method="post">
            <div class="form-group" id="forget-password-form-group">
                <input type="email" id="email" name="email"/>
            </div>
            <button type="submit">Submit</button>
        </form>

        <a class="back-to-login" href="../php/login.php">Back to Sign In</a>
    </div>
    <footer>
        <p>Copyright© 2024. All rights reserved</p>
    </footer>
    <script src="../assets/js/loader.js"></script>
</body>

</html>