<?php
date_default_timezone_set('Asia/Manila');

include '../settings/config.php';
include '../settings/authenticate.php';
checkUserRole(['Client']); 

if (!isset($_SESSION['email'])) {
  header('Location: ../php/login.php');
  exit;
}

$user_email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT first_name, last_name, dob, address, phone, gender, email, profile_picture, created_at FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check for change password message
$change_password_message = '';
if (isset($_SESSION['change_password_message'])) {
  $change_password_message = $_SESSION['change_password_message'];
  unset($_SESSION['change_password_message']);
}

// Check for upload message
$upload_message = '';
if (isset($_SESSION['upload_message'])) {
  $upload_message = $_SESSION['upload_message'];
  unset($_SESSION['upload_message']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($address) || empty($phone) || empty($gender)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'All fields are required.'
                    });
                });
              </script>";
    } elseif (!preg_match('/^\d{11}$/', $phone)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Contact number must be 11 digits.'
                    });
                });
              </script>";
    } else {
        // Handle profile picture upload
        $profile_picture_changed = false;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/profile_picture/' . $user_email . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $upload_file = $upload_dir . basename($_FILES['profile_picture']['name']);
            $image_info = getimagesize($_FILES['profile_picture']['tmp_name']);
            $image_type = $image_info[2];

            if ($image_type == IMAGETYPE_JPEG) {
                $image = imagecreatefromjpeg($_FILES['profile_picture']['tmp_name']);
            } elseif ($image_type == IMAGETYPE_PNG) {
                $image = imagecreatefrompng($_FILES['profile_picture']['tmp_name']);
            } else {
                $image = null;
            }

            if ($image) {
                $webp_file = $upload_dir . pathinfo($_FILES['profile_picture']['name'], PATHINFO_FILENAME) . '.webp';
                imagewebp($image, $webp_file);
                imagedestroy($image);
                $profile_picture = $webp_file;
                $profile_picture_changed = true;
            } else {
              $profile_picture = $user['profile_picture'];
            }
          } else {
            $profile_picture = $user['profile_picture'];
          }

          $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, address = ?, phone = ?, gender = ?, profile_picture = ? WHERE email = ?");
          $stmt->bind_param("sssssss", $first_name, $last_name, $address, $phone, $gender, $profile_picture, $user_email);
          if ($stmt->execute()) {
            // Determine the action and file name for the activity log
            if ($profile_picture_changed && ($first_name != $user['first_name'] || $last_name != $user['last_name'] || $address != $user['address'] || $phone != $user['phone'] || $gender != $user['gender'])) {
                $action = "Profile Information and Profile Picture is Changed";
                $file_name = basename($profile_picture);
            } elseif ($profile_picture_changed) {
                $action = "Profile Picture Changed";
                $file_name = basename($profile_picture);
            } else {
                $action = "Information Changed";
                $file_name = "N/A";
            }

            $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user_email, $action, $file_name);
            if (!$stmt->execute()) {
              error_log("Failed to insert into activity log: " . $stmt->error);
            }
        }
        $stmt->close();

        header('Location: profile.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<?php include '../required/headerOnLogin.php' ?>
  <title>Profile</title>
  <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
  <link rel="stylesheet" href="../assets/css/profile.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../assets/css/loader.css"><!-- Style for loader -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><!-- JQuery for loader -->
  <?php include 'security/idleLogout.php' ?>
</head>
<body>
  <!-- LOADER -->
  <div class="loader-wrapper">
    <div class="loader"></div>
  </div>
  <?php require '../required/navbarOnLogin.php' ?>
  <div class="dboard-container">
          <?php include 'user-profile/fetchData.php' ?>
          
    <div class="dashboard-trackerfiles-container">
        <div class="tabs">
            <button class="tab-button" onclick="openTab(event, 'Files')">Files</button>
            <button class="tab-button" onclick="openTab(event, 'StatusUpdates')">Status Updates</button>
            <button class="tab-button" onclick="openTab(event, 'ChangePassword')">Change Password</button>
            <button class="tab-button" onclick="openTab(event, 'TransactionHistory')">Transaction History</button>
            <button class="tab-button" onclick="openTab(event, 'ActivityLog')">Activity Log</button>
        </div>
        <div id="Files" class="tab-content">
            <div class="dashboard-card" id="dashboard-card-files">
                <div class="file-container">
                    <?php include 'document-files/fetchFiles.php' ?>
                </div>
            </div>
        </div>
        <div id="StatusUpdates" class="tab-content" style="display:none;">
            <div class="dashboard-card" id="dashboard-card-tracker">
                <?php include 'document-tracker/document-tracker.php'; ?>
            </div>
        </div>
        <div id="ChangePassword" class="tab-content" style="display:none;">
            <div class="dashboard-card" id="dashboard-card-password">
                <?php include 'change-password/changepass-client.php'; ?>
            </div>
        </div>
        <div id="TransactionHistory" class="tab-content" style="display:none;">
            <div class="dashboard-card" id="dashboard-card-password">
                <?php include 'transaction-history/transaction-history.php'; ?>
            </div>
        </div>
        <div id="ActivityLog" class="tab-content" style="display:none;">
            <div class="dashboard-card" id="dashboard-card-activity-log">
                <?php include 'activity-log/fetchActivityLog.php'; ?>
            </div>
        </div>
      </div>
  </div>

  <div id="editProfileModal" class="modal">
    <div class="modal-content">
      <span onclick="document.getElementById('editProfileModal').style.display='none'" class="close">&times;</span>
      <form method="POST" action="profile.php" enctype="multipart/form-data">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
          <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
          <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
          <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
        </select>
        <label for="profile_picture">Profile Picture:</label>
        <input type="file" id="profile_picture" name="profile_picture">
        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>

  <footer id="footerContainer">
    <?php include '../required/footerOnLogin.php' ?>
  </footer>

    <script src="../assets/js/loader.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.popup-button').addEventListener('click', function () {
          document.querySelector('.upload-container').classList.toggle('hidden');
          this.innerText = this.innerText === 'Upload' ? 'Close' : 'Upload';
        });
      });

      function openTab(evt, tabName) {
    var i, tabcontent, tabbuttons;

    // Hide all tab contents
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Remove the active class from all tab buttons
    tabbuttons = document.getElementsByClassName("tab-button");
    for (i = 0; i < tabbuttons.length; i++) {
        tabbuttons[i].className = tabbuttons[i].className.replace(" active", "");
    }

    // Show the current tab and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

    // Set the default tab to be open
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".tab-button").click();
    });

    window.onclick = function(event) {
      var modal = document.getElementById('editProfileModal');
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
    </script>
  

</body>

</html>