<?php
include '../settings/config.php'; // Adjust the path as necessary

// Query to get the number of pending appointments
$query_pending = "SELECT COUNT(*) as pending_count FROM appointments WHERE status = 'Processing'";
$result_pending = $conn->query($query_pending);
$row_pending = $result_pending->fetch_assoc();
$pending_count = $row_pending['pending_count'];

// Query to get the number of pending approvals
$query_approval = "SELECT COUNT(*) as approval_count FROM appointments WHERE status = 'Accepted' AND archived = 0 AND (form_type = 'sangla-orcr' OR form_type = 'brand-new' OR form_type = 'second-hand')";
$result_approval = $conn->query($query_approval);
$row_approval = $result_approval->fetch_assoc();
$approval_count = $row_approval['approval_count'];

// Query to get the number of pending payments
$query_payment = "SELECT COUNT(*) as payment_count FROM appointments WHERE status = 'Approved' AND archived = 0";
$result_payment = $conn->query($query_payment);
$row_payment = $result_payment->fetch_assoc();
$payment_count = $row_payment['payment_count'];

// Query to get the number of unread notifications
$query_unread_notifications = "SELECT COUNT(*) as unread_count FROM appointments WHERE status = 'Processing' AND mark_as_read IS NULL";
$result_unread_notifications = $conn->query($query_unread_notifications);
$row_unread_notifications = $result_unread_notifications->fetch_assoc();
$total_notifications = $row_unread_notifications['unread_count'];

// Query to get all notifications ordered by recieve_at
$query_notifications = "SELECT a.*, u.profile_picture FROM appointments a JOIN users u ON a.email = u.email WHERE a.status = 'Processing' AND a.archived = 0 ORDER BY a.recieve_at DESC";
$result_notifications = $conn->query($query_notifications);
$notifications = [];
while ($row = $result_notifications->fetch_assoc()) {
    $notifications[] = $row;
}
?>
<style>
    /* Adjust the CSS block to style the badge */
    .badge {
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 14px;
        position: absolute;
        top: -8px;
        right: -15px;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #sidebar-badge {
        display: none;
    }

    @media screen and (max-width: 860px) {
        .badge {
            display: none;
        }

        #sidebar-badge {
            display: inline-flex;
            position: relative;
            top: 0;
            right: 0;
            margin-left: 10px;
        }

        .sidebar-links li {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-links li a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 90%;
        }
    }

    .auth-links {
        display: flex;
        align-items: center;
    }

    .notification-bell {
        position: relative;
        margin-right: 5px;
        cursor: pointer;
    }

    .notification-bell .bell-icon {
        font-size: 16px;
        color: #fff;
    }

    .notification-bell .badge {
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 14px;
        position: absolute;
        top: -8px;
        right: -10px;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-dropdown-container {
        position: absolute;
        top: 55px;
        right: -130px;
        background-color: white; /* Change background color to white */
        color: black;
        width: 430px;
        z-index: -1000;
        display: none;
        flex-direction: column;
        max-height: 300px;
        overflow-y: auto;
        border-radius: 10px; /* Add border radius */
        border: 1px solid #ccc; /* Add border color */
    }

    /* Customize scrollbar */
    .notification-dropdown-container::-webkit-scrollbar {
        width: 10px;
    }

    .notification-dropdown-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .notification-dropdown-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .notification-dropdown-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .notification-dropdown-container.active {
        display: flex;
    }

    .notification-item {
        position: relative; /* Add relative positioning to the notification item */
        padding: 10px;
        border-bottom: 1px solid #ddd;
        font-weight: bold; /* Make notification items bold by default */
        display: flex;
        flex-direction: column; /* Change to column to align items vertically */
        justify-content: space-between;
        align-items: flex-start; /* Align items to the start */
    }

    .notification-item.read {
        font-weight: normal; /* Regular font weight for read notifications */
    }

    .mark-all-read-link {
        background-color:rgb(233, 233, 233);
        color:rgb(0, 0, 0);
        padding: 10px;
        cursor: pointer;
        width: 100%;
        text-align: center;
        position: sticky;
        bottom: 0;
        text-decoration: none;
    }
    .mark-all-read-link:hover {
        text-decoration: underline;
        }
    .remove-notification {
        color: red;
        cursor: pointer;
        margin-left: auto; /* Move to the very right */
        font-size: 18px;
    }
    .notification-item .form-type {
        color: green; /* Set form_type color to green */
        font-size: 0.9em; /* Make form_type font size smaller */
        margin-top: -5px; /* Reduce the distance between form-type and clientname */
    }
    .notification-item .transaction-id {
        font-size: 0.9em; /* Ensure transaction_id is the same size as form_type */
    }
    .notification-item .transaction-date {
        font-size: 0.9em; /* Ensure transaction_date is the same size as form_type */
        align-self: flex-end; /* Align to the right side */
        color: gray;
        margin-right: 14px;
    }
    .notification-item .profile-picture {
        width: 50px; /* Make profile picture bigger */
        height: 50px; /* Make profile picture bigger */
        border-radius: 50%;
        margin-right: 10px;
    }
    .notification-item .content {
        line-height: 1.2; /* Reduce line spacing */
    }
    .notification-item .remove-notification {
        color: red;
        cursor: pointer;
        font-size: 18px;
        position: absolute; /* Position absolutely within the notification item */
        right: 10px; /* Align to the right side */
        top: 10px; /* Adjust top position as needed */
    }
    .notification-header {
        background-color:rgb(233, 233, 233); /* Make background color darker */
        padding: 10px; /* Add padding */
        font-weight: bold;
        border: 1px solid #ccc; /* Add black border around the div */
    }

    @media screen and (max-width: 450px) {
        .notification-dropdown-container {
            position: fixed; /* Change to fixed position */
            top: 80px;
            left: 3%; /* Center the dropdown */
            width: 95%; /* Set width to 90% */
            right: 5%;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bellIcon = document.querySelector('.notification-bell');
        const dropdownContainer = document.querySelector('.notification-dropdown-container');
        const notificationItems = document.querySelectorAll('.notification-item');
        const badge = bellIcon.querySelector('.badge');
        const markAllAsReadLink = document.createElement('div');

        markAllAsReadLink.textContent = 'Mark all read';
        markAllAsReadLink.classList.add('mark-all-read-link');
        dropdownContainer.appendChild(markAllAsReadLink);

        bellIcon.addEventListener('click', function () {
            dropdownContainer.classList.toggle('active');
        });

        document.addEventListener('click', function (event) {
            if (!bellIcon.contains(event.target) && !dropdownContainer.contains(event.target)) {
                dropdownContainer.classList.remove('active');
            }
        });

        notificationItems.forEach(item => {
            const removeLink = document.createElement('span');
            removeLink.innerHTML = '<i class="fas fa-times"></i>'; // Use FontAwesome times icon
            removeLink.classList.add('remove-notification');
            // Append removeLink to the right of "New Application:" line
            const strongElement = item.querySelector('strong');
            strongElement.style.display = 'flex';
            strongElement.style.justifyContent = 'space-between';
            strongElement.style.width = '100%';
            strongElement.appendChild(removeLink);

            item.addEventListener('click', function () {
                const appointmentId = this.getAttribute('data-id'); // Get appointment ID from data attribute

                // AJAX request to update mark_as_read column
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'includes/update_notification_status.php', true); // Ensure the path is correct
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            const currentCount = parseInt(badge.textContent);
                            if (currentCount > 1) {
                                badge.textContent = currentCount - 1;
                            } else {
                                badge.remove();
                            }
                            // Redirect to pendingAppointment.php with client filter after a slight delay
                            setTimeout(function() {
                                window.location.href = 'pendingAppointment.php?client_id=' + appointmentId;
                            }, 100);
                        } else {
                            console.error('Error updating notification status:', xhr.responseText);
                            // Redirect to pendingAppointment.php with client filter even if there's an error
                            window.location.href = 'pendingAppointment.php?client_id=' + appointmentId;
                        }
                    }
                };
                xhr.send('id=' + appointmentId);

                // Redirect to pendingAppointment.php with client filter immediately
                window.location.href = 'pendingAppointment.php?client_id=' + appointmentId;
            });

            removeLink.addEventListener('click', function (event) {
                event.stopPropagation();
                const appointmentId = item.getAttribute('data-id'); // Get appointment ID from data attribute

                // AJAX request to mark notification as removed
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'includes/update_notification_status.php', true); // Ensure the path is correct
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        item.remove();
                        const currentCount = parseInt(badge.textContent);
                        if (currentCount > 1) {
                            badge.textContent = currentCount - 1;
                        } else {
                            badge.remove();
                        }
                    }
                };
                xhr.send('id=' + appointmentId + '&remove=true');
            });
        });

        markAllAsReadLink.addEventListener('click', function () {
            // AJAX request to mark all notifications as read
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'includes/update_notification_status.php', true); // Ensure the path is correct
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    badge.remove();
                    notificationItems.forEach(item => {
                        item.classList.add('read');
                    });
                }
            };
            xhr.send();
        });
    });
</script>
<nav class="navbar">
    <div class="hamburger">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>
    <a href="homepage.php">
        <div class="logo-container">
            <img src="../assets/images/updated-logo.webp" alt="K8 FCS logo" class="logo">
            <span class="title">K8 FCS</span>
        </div>
    </a>

    <ul class="nav-links">
        <li class="nav-item"><a href="homepage" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="pendingAppointment" class="nav-link">Pending
                <?php if ($pending_count > 0) { ?><span class="badge"><?php echo $pending_count; ?></span><?php } ?></a>
        </li>
        <li class="nav-item"><a href="acceptedAppointment" class="nav-link">Approval
                <?php if ($approval_count > 0) { ?><span
                        class="badge"><?php echo $approval_count; ?></span><?php } ?></a></li>
        <li class="nav-item"><a href="accept-payment" class="nav-link">Payment <?php if ($payment_count > 0) { ?><span
                        class="badge"><?php echo $payment_count; ?></span><?php } ?></a></li>
        <li class="nav-item"><a href="archives" class="nav-link">Archives</a></li>
        <li class="nav-item"><a href="https://dashboard.paymongo.com/payments" class="nav-link"
                target="_blank">PayMongo</a></li>
    </ul>
    <div class="auth-links">
        <div class="notification-bell">
            <i class="fas fa-bell bell-icon"></i>
            <?php if ($total_notifications > 0) { ?><span class="badge"><?php echo $total_notifications; ?></span><?php } ?>
            <div class="notification-dropdown-container">
                <?php if (count($notifications) > 0) { ?>
                    <div class="notification-header">
                        <strong>New Applications:</strong>
                    </div>
                    <?php foreach ($notifications as $notification) { ?>
                        <div class="notification-item <?php echo $notification['mark_as_read'] ? 'read' : ''; ?>" data-id="<?php echo $notification['id']; ?>">
                            <?php
                            $notification_form_type = str_replace('-', ' ', $notification['form_type']);
                            $notification_form_type = preg_replace_callback('/\b\w+\b/', function ($matches) {
                                return (strtolower($matches[0]) === 'orcr') ? strtoupper($matches[0]) : ucfirst($matches[0]);
                            }, $notification_form_type); // Capitalize only the word "ORCR" and the first letter of all words
                            $formatted_date = date('M. d, Y g:iA', strtotime($notification['recieve_at'])); // Format the date
                            echo "<div style='display: flex; align-items: center;' class='content'>";
                            $profile_picture = !empty($notification['profile_picture']) ? "../clientside/" . htmlspecialchars($notification['profile_picture']) : "../assets/images/profile/user.jpg";
                            echo "<img src='" . $profile_picture . "' alt='Profile Picture' class='profile-picture'>"; // Display profile picture or default
                            echo "<div style='flex-grow: 1;'>";
                            echo "<strong style='display: flex; align-items: center;'>" . htmlspecialchars($notification['clientname']) . "</strong>"; // Make clientname bold
                            echo "<span class='form-type'>" . htmlspecialchars($notification_form_type) . "</span><br>"; // Make form_type green
                            echo "<span class='transaction-id'>" . htmlspecialchars($notification['transaction_id']) . "</span><br>"; // Ensure transaction_id is the same size as form_type
                            echo "</div>";
                            echo "<span class='remove-notification'><i class='fas fa-times'></i></span>"; // Move remove button to the right side
                            echo "</div>";
                            echo "<span class='transaction-date'>" . htmlspecialchars($formatted_date) . "</span>"; // Add formatted date and time of transaction
                            ?>
                        </div>
                    <?php }
                } else { ?>
                    <div class="notification-item">No new notifications</div>
                <?php } ?>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="nav-link dropbtn" id="userDropdownBtn">
                <i class="fas fa-user"></i>
                <?php echo htmlspecialchars($_SESSION['first_name']); ?>
            </a>
            <div id="userDropdown" class="dropdown-content">
                <a href="../employeeside/my-files" class="dropdown-item">My Files</a>
                <a href="../php/logout.php" class="dropdown-item">Logout</a>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-mobile">
            <img src="../assets/images/updated-logo.webp" alt="K8 FCS logo" class="logo">
            <span class="title">K8 FCS</span>
        </div>
        <ul class="sidebar-links">
            <li><a href="homepage">Home</a></li>
            <li><a href="pendingAppointment">Pending Application
                    <?php if ($pending_count > 0) { ?><span class="badge"
                            id="sidebar-badge"><?php echo $pending_count; ?></span><?php } ?></a></li>
            <li><a href="acceptedAppointment">Approval
                    <?php if ($approval_count > 0) { ?><span class="badge"
                            id="sidebar-badge"><?php echo $approval_count; ?></span><?php } ?></a></li>
            <li><a href="accept-payment">Payment
                    <?php if ($payment_count > 0) { ?><span class="badge"
                            id="sidebar-badge"><?php echo $payment_count; ?></span><?php } ?></a></li>
            <li><a href="archives">Archives</a></li>
            <li><a href="https://dashboard.paymongo.com/payments" target="_blank">PayMongo</a></li>
        </ul>
    </div>
</nav>