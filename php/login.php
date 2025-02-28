<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../required/header.php'; ?>
    <title>Login</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/Login.css">
    <link rel="stylesheet" href="../assets/css/loader.css"><!-- Style for loader -->
    <script src="../assets/js/script.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><!-- JQuery for loader -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    .swal2-confirm {
        width: 150px;
    }

    body.swal2-height-auto {
        height: 100vh !important;
    }

    .error-border {
        border: 2px solid red;
    }

    .error-message {
        color: red;
        margin-top: 10px;
    }

    .loading-spinner {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 45%;
        left: 47%;
        transform: translate(-50%, -50%);
        border: 8px solid #f3f3f3;
        border-radius: 50%;
        border-top: 8px solid #3498db;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
    }

    .form-group {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 400px;
        margin-left: 0;
        margin-bottom: 1rem;
    }

    .form-group input {
        flex: 1;
    }

    .toggle-password-container {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }

    input[type="password"]::-webkit-input-decoration {
        display: none;
    }

    /* Ensure the loader is centered on mobile devices */
    @media (max-width: 768px) {
        .loading-spinner {
            width: 40px;
            height: 40px;
            border-width: 6px;
            top: 45%; /* Move the spinner a bit up */
            left: 44%; /* Move the spinner a bit to the left */
        }
    }
</style>

<body>
    <!-- LOADER -->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>
    <div class="loading-overlay"></div>
    <div class="loading-spinner"></div>

    <div class="login-container">
        <div class="logo">
            <a href="../index.php">
                <img src="../assets/images/updated-logo.webp" width="150" height="150" alt="Company Logo" />
            </a>
        </div>
        <h3 class="subheader">Welcome to K8 Financial Consultancy Services</h3>
        <h1>Login</h1>
        <form action="process_login.php" method="post">
            
        
        <div id="error-message" class="error-message"></div>
        <label for="email">Email:</label>
            <div class="form-group">
                <input type="email" id="email" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" />
            </div>

            <label for="password">Password: </label>
            <div class="form-group">
                    <input type="password" id="password" name="password" />
                    <div class="toggle-password-container">
                        <span id="toggle-password" style="cursor: pointer;">
                            <i class="fas fa-eye" style="color: gray;"></i>
                        </span>
                    </div>
            </div>

            <a href="../login/forgot_password.php" class="forgot-password">Forgot Password?</a>

            <div class="signup-container">
                <button type="submit">Sign In</button>
                <p class="signup-caption">Don't have an account yet? <a href="../php/signup.php" class="forgot-password"
                        id="signup">Sign up</a> </p>
            </div>
            
        </form>

    </div>
    <script src="../assets/js/loader.js"></script><!-- Script for loader -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get("error");

            if (error) {
                let errorMessage = '';
                if (error === "missing_field") {
                    errorMessage = "Please fill all required fields.";
                    if (!document.getElementById("email").value.trim()) {
                        document.getElementById("email").classList.add("error-border");
                    }
                    if (!document.getElementById("password").value.trim()) {
                        document.getElementById("password").classList.add("error-border");
                    }
                } else if (error === "wrong_credentials") {
                    errorMessage = "Incorrect email or password.";
                    document.getElementById("email").classList.add("error-border");
                    document.getElementById("password").classList.add("error-border");
                } else if (error === "Session Expired due to inactivity. Please login again.") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Session Expired',
                        text: 'Session Expired due to inactivity. Please login again.',
                        confirmButtonText: 'OK'
                    });
                }
                document.getElementById("error-message").innerText = errorMessage;
            }

            document.getElementById("email").addEventListener("input", function() {
                this.classList.remove("error-border");
                document.getElementById("error-message").innerText = '';
            });

            document.getElementById("password").addEventListener("input", function() {
                this.classList.remove("error-border");
                document.getElementById("error-message").innerText = '';
            });

            document.querySelector('form').addEventListener('submit', function(event) {
                document.querySelector('.loading-overlay').style.display = 'block';
                document.querySelector('.loading-spinner').style.display = 'block';
            });
        });

        //show-password
        document.getElementById('toggle-password').addEventListener('click', function() {
            var passwordInput = document.getElementById('password');
            var passwordIcon = document.querySelector('#toggle-password i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>