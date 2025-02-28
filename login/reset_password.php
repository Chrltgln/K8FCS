<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../assets/js/script.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .swal2-confirm {
            width: 150px;
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
    </style>
</head>

<body>
    <!-- LOADER -->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <div class="login-container">
        <div class="logo">
            <a href="../index.php">
                <img src="../assets/images/updated-logo.webp" width="150" height="150" alt="Company Logo" />
            </a>
        </div>
        <h3 class="subheader">Reset Password</h3>

        <div id="response-message" style="display:none;"></div>

        <?php
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            echo '<form id="reset-password-form" action="process_reset_password.php" method="post">
                <input type="hidden" name="token" value="' . htmlspecialchars($token) . '" />
                <label for="password">New Password:</label>
                <div class="form-group" id="reset-password-form-group">
                    <input type="password" id="password" name="password" placeholder="Ex. Abcd1234"/>
                    <div class="toggle-password-container">
                        <span id="toggle-password" style="cursor: pointer;">
                            <i class="fas fa-eye" style="color: gray;"></i>
                        </span>
                    </div>
                </div>
                <button type="submit">Reset Password</button>
            </form>';
        } else {
            echo '<div class="message" style="color:red;">Invalid or expired token.</div>';
        }
        ?>

        <a class="back-to-login" href="../php/login.php">Back to Sign In</a>
    </div>
    <footer>
        <p>Copyright© 2024. All rights reserved</p>
    </footer>
    <script src="../assets/js/loader.js"></script>
    <script>
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