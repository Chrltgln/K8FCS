<h2 class="card-title">Change Password</h2>
<?php if ($change_password_message): ?>
  <p class="message"><?php echo htmlspecialchars($change_password_message); ?></p>
<?php endif; ?>

<?php if ($change_password_message): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        title: '<?php echo htmlspecialchars($change_password_message); ?>',
        icon: '<?php echo strpos($change_password_message, 'successfully') !== false ? 'success' : 'error'; ?>',
        confirmButtonText: 'OK'
      }).then(() => {
        if (window.shouldShowLoader) {
          document.querySelector('.loader-wrapper').style.display = 'block';
        }
      });
    });
  </script>
<?php endif; ?>
<div class="change-password-container">
  <form method="POST" action="../clientside/changePassword.php">
    <div class="form-group">
      <label class="form-label" for="old_password"><strong>Old Password:</strong></label>
      <input class="form-input" type="password" id="old_password" name="old_password" placeholder="Enter Old Password" required />
    </div>
    <div class="form-group">
      <label class="form-label" for="new_password"><strong>New Password:</strong></label>
      <input class="form-input" type="password" id="new_password" name="new_password" placeholder="Enter New Password" required />
    </div>
    <div class="form-group">
      <label class="form-label" for="confirm_password"><strong>Confirm Password:</strong></label>
      <input class="form-input" type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required />
    </div>
    <div class="button-container">
      <button class="submit-button" type="submit" name="change_password">Save</button>
    </div>
  </form>
</div>