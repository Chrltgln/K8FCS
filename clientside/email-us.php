<?php include '../settings/authenticate.php';
checkUserRole(['Client']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php include '../required/headerOnLogin.php' ?>
    <title>Contact Us</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/emailus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../assets/js/img-control.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
            box-sizing: border-box;
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
    </style>
</head>

<body>
  
    <?php require '../required/navbarOnLogin.php'; ?>

    <section class="header-container"  style="background-image: url('../assets/images/clientheader.webp')">
        <div class="header">
            <div class="header-title">
                <h1 id="contactusheadertitle">Contact Us</h1>
                <p id="contactusheaderdesc">Get in touch with K8 Financial Consultancy Services.</p>
            </div>
        </div>
    </section>

    <div class="loading-overlay"></div>
    <div class="loading-spinner"></div>

    <section class="contact-container">
        <div class="container">
            <div class="left-section">
                <!-- Image or profile representation can go here -->
                <img src="../assets/images/emailus-image.webp" alt="Person Image">
            </div>
            <div class="right-section">
                <h2 class="contact-title">Do you have a question? <br>Feel free to contact us!</h2>
                <form action="process/send-email-client-question.php" method="POST" onsubmit="return confirmSubmission(event)">
                    <label for="first-name">Name</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="first-name" name="first_name" value="<?php echo $_SESSION['first_name'];?>" readonly placeholder="First Name" required>
                        <input type="text" id="last-name" name="last_name" value="<?php echo $_SESSION['last_name']; ?>" readonly placeholder="Last Name" required>
                    </div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $_SESSION['email']; ?>" readonly placeholder="Email Address" required>
                    <label for="message">Send us a message</label>
                    <textarea id="message" name="message" placeholder="Type a message here" ></textarea>
                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>
        </div>
    </section>
   

    <div class="contact-container" id="second-contact-container">
        <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <p><strong>Address:</strong> Phase 1 Block 37 Lot 2-A Mary Cris Complex Pasong Camachile 2 General Trias, Cavite</p>
        </div>

        <div class="contact-item">
            <i class="fas fa-phone-alt"></i>
            <p><strong>Mildred Peña:</strong> 09175281760</p>
            <p><strong>Joy Baltazar:</strong> 09176195984</p>
            <p><strong>Nel Liongson:</strong> 09454801515</p>
        </div>

        <div class="contact-item">
            <i class="fas fa-paper-plane"></i>
            <p><strong>Email:</strong>k8_fcs@yahoo.com</a></p>
        </div>

        <div class="contact-item">
            <i class="fas fa-globe"></i>
            <p><strong>Facebook:</strong> <a href="https://www.facebook.com/k8fcscarloan">K8 FCS</a></p>
        </div>
    </div>

    <?php include '../required/footer.php'; ?>
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

        function confirmSubmission(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Confirm Submission',
                text: "Are you sure you want to submit this form?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit it!',
                scrollbarPadding: false,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Submitted!',
                        text: 'Please wait on your email for our response. Thank you!',
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        // Show loading spinner and overlay again
                        document.querySelector('.loading-overlay').style.display = 'block';
                        document.querySelector('.loading-spinner').style.display = 'block';

                        event.target.submit();
                    });
                }
            });
        }
    </script>
</body>

</html>