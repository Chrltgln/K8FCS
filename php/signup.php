<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../required/header.php'; ?>
    <title>Sign Up</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/signup.css">
    <link rel="stylesheet" href="../assets/css/loader.css"><!-- Style for loader -->
    <script src="../assets/js/script.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><!-- JQuery for loader -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        /* Custom style for SweetAlert2 button */
        .swal2-confirm, .swal2-cancel {
            width: 100px;
        }

        .swal2-input-label {
            text-align: center;
        }

        body.swal2-height-auto {
            height: 100vh !important;
        }

        .error-border {
            border: 1px solid red !important;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
        }

        .valid-check {
            color: green;
        }

        .invalid-check {
            color: red;
        }

        .valid-check::before {
            content: '✔ ';
            color: green;
            font-weight: bold;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        .form-group-inline {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .form-group-inline input[type="checkbox"] {
            margin-right: 10px;
        }

        .grecaptcha-badge {
            visibility: visible;
        }

        .loading-spinner {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 8px solid #f3f3f3;
            border-radius: 50%;
            border-top: 8px solid #3498db;
            width: 60px;
            height: 60px;
            animation: spin 2s linear infinite;
            box-sizing: border-box; /* Ensure padding and border are included in the element's total width and height */
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
        .swal2-actions {
            display: flex;
            flex-wrap: nowrap;
        }

        .swal2-input {
            width: 88% !important;
            margin-top: 1rem !important;
        }

        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-container .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
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
    <div class="loading-overlay"></div>
    <div class="loading-spinner"></div>

    <div class="login-container">
        <div class="logo">
            <a href="../index.php">
                <img src="../assets/images/updated-logo.webp" width="150" height="150" alt="Company Logo" />
            </a>
        </div>
        <h3 class="subheader">Welcome to K8 Financial Consultancy Services</h3>

        <h1>Sign Up</h1>
        <span class="error-message" id="form-error-message">
            <?php
            if (isset($_GET['error'])) {
                echo htmlspecialchars($_GET['error']);
            }
            ?>
        </span>

        <form id="signup-form" action="register.php" method="post" autocomplete="off">
            <div class="form-grid">
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" placeholder="Juan" autocomplete="off" />
                </div>
                <div class="form-group">
                    <label for="middle-name">Middle Name</label>
                    <input type="text" id="middle-name" name="middle_name" placeholder="Santos" autocomplete="off" />
                </div>
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" placeholder="Dela Cruz" autocomplete="off" />
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" autocomplete="off" />
                    <span class="error-message" id="dob-error" style="color: red; display: none;">You must be at least 18 years old</span>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" autocomplete="off">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" placeholder="21" autocomplete="off" readonly />
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address"
                        placeholder="General Trias, Cavite" autocomplete="off" />
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" placeholder="jsdelacruz@gmail.com" autocomplete="off" />
                    <span class="error-message" id="email-error" style="color: red; display: none;">Please enter a valid email</span>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="09123456789" autocomplete="off" />
                    <span class="error-message" id="phone-error" style="color: red; display: none;">Please enter a valid phone number</span>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" autocomplete="off" />
                        <i class="fas fa-eye toggle-password" style="color: gray;" id="toggle-password"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="password-container">
                        <input type="password" id="confirm-password" name="confirm_password" autocomplete="off" />
                        <i class="fas fa-eye toggle-password" style="color: gray;" id="toggle-confirm-password"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div id="password-requirements-container">
                        <ul id="password-requirements">
                            <li id="length" class="invalid-check">At least 8 characters long</li>
                            <li id="uppercase" class="invalid-check">At least 1 uppercase letter</li>
                            <li id="special" class="invalid-check">At least 1 special character</li>
                            <li id="number" class="invalid-check">At least 1 number</li>
                        </ul>
                    </div>
                </div>

                <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
            </div>
            <div class="signup-container">
                <button type="submit">Register</button>
                <br />
                <p class="signup-caption">Already have an account? <a href="login.php" class="forgot-password"
                        id="signup">Sign in</a></p>
            </div>
        </form>
    </div>

    <script src="../assets/js/loader.js"></script><!-- Script for loader -->
    
    <script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById('signup-form');
    const fields = ['first-name', 'middle-name', 'last-name', 'age', 'gender', 'dob', 'address', 'email', 'phone', 'password', 'confirm-password'];
    const passwordInput = document.getElementById('password');
    const passwordRequirements = document.getElementById('password-requirements');
    const passwordRequirementsContainer = document.getElementById('password-requirements-container');
    const lengthRequirement = document.getElementById('length');
    const uppercaseRequirement = document.getElementById('uppercase');
    const specialRequirement = document.getElementById('special');
    const numberRequirement = document.getElementById('number');
    const formErrorMessage = document.getElementById('form-error-message');
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phone-error');
    const dobInput = document.getElementById('dob');
    const dobError = document.getElementById('dob-error');
    const ageInput = document.getElementById('age');

    function validateField(field) {
        const input = document.getElementById(field);
        if (field === 'email') {
            return validateEmail();
        } else if (field === 'phone') {
            return validatePhone();
        } else if (field === 'dob') {
            return validateDOB();
        } else {
            if (!input.value.trim()) {
                input.classList.add('error-border');
                return false;
            } else {
                input.classList.remove('error-border');
                return true;
            }
        }
    }

    function validateEmail() {
        const emailValue = emailInput.value;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Regex pattern for a valid email
        if (emailPattern.test(emailValue)) {
            emailInput.classList.remove('error-border');
            emailError.style.display = 'none';
            return true;
        } else {
            emailInput.classList.add('error-border');
            emailError.style.display = 'block';
            return false;
        }
    }

    function validatePhone() {
        const phoneValue = phoneInput.value;
        const phonePattern = /^\d{11}$/; // Regex pattern for 11-digit phone number
        if (phonePattern.test(phoneValue)) {
            phoneInput.classList.remove('error-border');
            phoneError.style.display = 'none';
            return true;
        } else {
            phoneInput.classList.add('error-border');
            phoneError.style.display = 'block';
            return false;
        }
    }

    function validateDOB() {
        if (!dobInput.value) {
            dobInput.classList.remove('error-border');
            dobError.style.display = 'none';
            ageInput.value = '';
            return false;
        }

        const dobValue = new Date(dobInput.value);
        const today = new Date();
        const minYear = 1900;
        let age = today.getFullYear() - dobValue.getFullYear();
        const monthDifference = today.getMonth() - dobValue.getMonth();
        const dayDifference = today.getDate() - dobValue.getDate();

        if (monthDifference < 0 || (monthDifference === 0 && dayDifference < 0)) {
            age--;
        }

        if (dobValue > today || dobValue.getFullYear() < minYear) {
            dobInput.classList.add('error-border');
            dobError.textContent = 'Please enter a valid date of birth';
            dobError.style.display = 'block';
            ageInput.value = '';
            return false;
        }

        if (age >= 18) {
            dobInput.classList.remove('error-border');
            dobError.style.display = 'none';
            ageInput.value = age;
            return true;
        } else {
            dobInput.classList.add('error-border');
            dobError.textContent = 'You must be at least 18 years old';
            dobError.style.display = 'block';
            ageInput.value = '';
            return false;
        }
    }

    function validatePassword() {
        const passwordValue = passwordInput.value;
        let isValid = true;

        if (passwordValue.length >= 8) {
            lengthRequirement.classList.remove('invalid-check');
            lengthRequirement.classList.add('valid-check');
        } else {
            lengthRequirement.classList.remove('valid-check');
            lengthRequirement.classList.add('invalid-check');
            isValid = false;
        }

        if (/[A-Z]/.test(passwordValue)) {
            uppercaseRequirement.classList.remove('invalid-check');
            uppercaseRequirement.classList.add('valid-check');
        } else {
            uppercaseRequirement.classList.remove('valid-check');
            uppercaseRequirement.classList.add('invalid-check');
            isValid = false;
        }

        if (/[!@#$%^&*(),.?":{}|<>]/.test(passwordValue)) {
            specialRequirement.classList.remove('invalid-check');
            specialRequirement.classList.add('valid-check');
        } else {
            specialRequirement.classList.remove('valid-check');
            specialRequirement.classList.add('invalid-check');
            isValid = false;
        }

        if (/\d/.test(passwordValue)) {
            numberRequirement.classList.remove('invalid-check');
            numberRequirement.classList.add('valid-check');
        } else {
            numberRequirement.classList.remove('valid-check');
            numberRequirement.classList.add('invalid-check');
            isValid = false;
        }

        return isValid;
    }

    // Attach event listener to the dob input
    dobInput.addEventListener('input', validateDOB);

    ageInput.addEventListener('input', function () {
        if (ageInput.value) {
            ageInput.classList.remove('error-border');
        }
    });

    form.addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    let isValid = true;
    formErrorMessage.textContent = '';

    // Check if any fields are blank
    let allFieldsBlank = true;
    fields.forEach(function (field) {
        const input = document.getElementById(field);
        if (input.value.trim()) {
            allFieldsBlank = false;
        }
        if (!validateField(field)) {
            isValid = false;
        }
    });

    if (allFieldsBlank) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Form',
            text: 'All fields are blank. Please fill out the form.',
        });
        return;
    }

    // Check if any individual field is blank
    let missingFields = false;
    fields.forEach(function (field) {
        const input = document.getElementById(field);
        if (input.value.trim() === "") {
            missingFields = true;
            input.classList.add('error-border');
        }
    });

    if (missingFields) {
        Swal.fire({
            icon: 'warning',
            title: 'Incomplete Form',
            text: 'Please fill out all required fields.',
        });
        return;
    }

    // Validate email
    if (!validateEmail()) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
        });
        return;
    }

    // Validate phone
    if (!validatePhone()) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Phone Number',
            text: 'Please enter a valid 11-digit phone number.',
        });
        return;
    }

    // Validate date of birth
    if (!validateDOB()) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Date of Birth',
            text: 'You must be at least 18 years old.',
        });
        return;
    }

    // Check if passwords match
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    if (password !== confirmPassword) {
        document.getElementById('password').classList.add('error-border');
        document.getElementById('confirm-password').classList.add('error-border');
        Swal.fire({
            icon: 'warning',
            title: 'Password Mismatch',
            text: 'Passwords do not match.',
        });
        return;
    }

    // Check if password meets requirements
    if (!validatePassword()) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Password',
            text: 'Please ensure your password meets all requirements.',
        });
        return;
    }

    Swal.fire({
        title: 'Confirm Registration',
        text: 'Are you sure you want to submit your registration?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading spinner and overlay
            document.querySelector('.loading-overlay').style.display = 'block';
            document.querySelector('.loading-spinner').style.display = 'block';

            // Send OTP to the email
            const email = document.getElementById('email').value;
            fetch('process/send-email-otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log(data); // Log the response data for debugging
                // Hide loading spinner and overlay
                document.querySelector('.loading-overlay').style.display = 'none';
                document.querySelector('.loading-spinner').style.display = 'none';

                if (data.success) {
                    Swal.fire({
                        title: 'Enter OTP',
                        input: 'text',
                        inputLabel: 'An OTP has been sent to your email. Please enter it below:',
                        inputPlaceholder: 'Enter OTP',
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        cancelButtonText: 'Cancel'
                    }).then((otpResult) => {
                        if (otpResult.isConfirmed) {
                            const otp = otpResult.value;
                            // Show loading spinner and overlay
                            document.querySelector('.loading-overlay').style.display = 'block';
                            document.querySelector('.loading-spinner').style.display = 'block';

                            // Submit the form via AJAX with OTP
                            const formData = new FormData(form);
                            formData.append('otp', otp);
                            fetch('register.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Hide loading spinner and overlay
                                document.querySelector('.loading-overlay').style.display = 'none';
                                document.querySelector('.loading-spinner').style.display = 'none';

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Successfully Registered',
                                        text: 'Welcome to K8 Financial Consultancy Services',
                                    }).then(() => {
                                        window.location.href = 'login.php';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Registration Failed',
                                        text: data.message,
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Server Error',
                                    text: 'There was an error processing your request. Please try again later.',
                                });
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'OTP Sending Failed',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'There was an error sending the OTP. Please try again later.',
                });
            });
        }
    });
});

    fields.forEach(function (field) {
        const input = document.getElementById(field);
        input.addEventListener('blur', function () {
            validateField(field);
        });
        input.addEventListener('input', function () {
            if (field !== 'email' && field !== 'phone' && field !== 'dob' && input.value.trim()) {
                input.classList.remove('error-border');
            }
        });
    });

    emailInput.addEventListener('input', validateEmail);
    phoneInput.addEventListener('input', validatePhone);
    dobInput.addEventListener('input', validateDOB);

    passwordInput.addEventListener('focus', function () {
        passwordRequirementsContainer.style.display = 'block';
    });

    passwordInput.addEventListener('blur', function () {
        if (!passwordInput.value) {
            passwordRequirementsContainer.style.display = 'none';
        }
    });

    passwordInput.addEventListener('input', function () {
        validatePassword();
        passwordRequirementsContainer.style.display = 'block'; // Ensure it stays visible during input
    });

    grecaptcha.ready(function () {
        grecaptcha.execute('your_site_key', { action: 'submit' }).then(function (token) {
            document.getElementById('g-recaptcha-response').value = token;
        });
    });
});

document.getElementById('toggle-password').addEventListener('click', function() {
    var passwordInput = document.getElementById('password');
    var icon = this;
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

document.getElementById('toggle-confirm-password').addEventListener('click', function() {
    var confirmPasswordInput = document.getElementById('confirm-password');
    var icon = this;
    if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        confirmPasswordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>


</body>

</html>