<div class="fetchdata-container">
    
    <div class="profile-details-grid">
        <div class="profile-picture-container">
            <img aria-hidden="true" alt="user-icon" src="<?php echo htmlspecialchars($user['profile_picture'] ?: '../assets/images/profile/user.jpg'); ?>" class="profile-picture" />
        </div>
        
        <div class="profile-name-column">
            <p>
                <strong><?php echo htmlspecialchars($user['first_name']); ?></strong>
                <strong> <?php echo htmlspecialchars($user['last_name']); ?></strong>
            </p>
            <p id="sub-profile-details"><?php echo htmlspecialchars($user['address']); ?></p>
            <div class="profile-details-column">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Date of Birth:</strong>
                    <?php
                    $dob = new DateTime($user['dob']);
                    echo htmlspecialchars($dob->format('F j, Y'));
                    ?>
                </p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
                <p><strong>Account Created:</strong>
                    <?php
                    $date = new DateTime($user['created_at']);
                    echo htmlspecialchars($date->format('F j, Y h:i:sA'));
                    ?>
                </p>
            </div>
        </div>
        <div class="edit-button-container">
            <button class="edit-profile-btn" onclick="document.getElementById('editProfileModal').style.display='block'">Edit Profile</button>
        </div>
    </div>
</div>