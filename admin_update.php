<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
  header('location:login.php');
  exit;
}

// Initialize message variable
if (!isset($_SESSION['update_message'])) $_SESSION['update_message'] = "";

// Fetch admin data (do this at very start)
$select_admin = mysqli_query($conn, "SELECT * FROM `register` WHERE id = '$admin_id' AND user_type = 'admin'") or die('Select query failed: ' . mysqli_error($conn));
if (mysqli_num_rows($select_admin) > 0) {
  $fetch_admin = mysqli_fetch_assoc($select_admin);
} else {
  echo "Admin not found!";
  exit;
}

// Update profile logic
if (isset($_POST['update_profile'])) {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $updated = false;

  mysqli_query($conn, "UPDATE `register` SET name = '$name', email = '$email' WHERE id = '$admin_id'") or die('Update query failed: ' . mysqli_error($conn));
  $updated = true;

  // Update SESSION values for immediate header/sidebar update
  $_SESSION['admin_name'] = $name;
  $_SESSION['admin_email'] = $email;

  // Password update (optional)
  if (!empty($_POST['old_pass']) || !empty($_POST['new_pass']) || !empty($_POST['confirm_pass'])) {
    // Always get current password after name/email update
    $select_admin = mysqli_query($conn, "SELECT * FROM `register` WHERE id = '$admin_id'") or die('Query failed');
    $fetch_admin = mysqli_fetch_assoc($select_admin);

    // Correct validation for bcrypt hashed passwords
    if (!password_verify($_POST['old_pass'], $fetch_admin['password'])) {
      $_SESSION['update_message'] = 'Old password does not match!';
    } elseif ($_POST['new_pass'] !== $_POST['confirm_pass']) {
      $_SESSION['update_message'] = 'Confirm password does not match!';
    } else {
      // Hash new password with bcrypt
      $new_hashed = password_hash($_POST['confirm_pass'], PASSWORD_BCRYPT);
      mysqli_query($conn, "UPDATE `register` SET password = '$new_hashed' WHERE id = '$admin_id'") or die('Password update failed: ' . mysqli_error($conn));
      $_SESSION['update_message'] = 'Password updated successfully!';
    }
  } else {
    if ($updated) $_SESSION['update_message'] = 'Profile updated successfully!';
  }

  // After any update, redirect to same page (so form shows current value)
  header("Location: admin_update.php");
  exit();
}

// Fetch latest admin data every time page loads
$select_admin = mysqli_query($conn, "SELECT * FROM `register` WHERE id = '$admin_id' AND user_type = 'admin'") or die('Select query failed: ' . mysqli_error($conn));
$fetch_admin = mysqli_fetch_assoc($select_admin);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Update Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="admin.css" />
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      background: #f5f5f5;
    }
    .update-profile {
      padding: 100px 20px 50px;
      text-align: center;
    }
    .update-profile h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 20px;
      color: #222;
      text-transform: uppercase;
    }
    form {
      background: #fff;
      border: 2px solid #000;
      max-width: 450px;
      margin: 0 auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    form .box {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: 1px solid #aaa;
    }
    form .btn {
      background: #c02736;
      color: #fff;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
      border: none;
    }
    form .btn:hover {
      background: #fff;
      color: #c02736;
      border: 2px solid #c02736;
    }
    .alert-message {
      margin-bottom: 15px;
      font-weight: 600;
      color: green;
      background: #e7ffe7;
      border: 1px solid #70db70;
      padding: 12px 38px 12px 12px;
      border-radius: 5px;
      max-width: 450px;
      margin-left: auto;
      margin-right: auto;
      position: relative;
      text-align: left;
      transition: opacity 0.5s;
    }
    .alert-message .close-alert {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #c02736;
      font-size: 20px;
      background: none;
      border: none;
      cursor: pointer;
      font-weight: bold;
      outline: none;
      padding: 0;
    }
  </style>
</head>
<body>
  <?php include 'admin_header.php'; ?>

  <section class="update-profile">
    <h1>Update Profile</h1>

    <?php if (!empty($_SESSION['update_message'])) : ?>
      <div class="alert-message" id="alertMessage">
        <?= htmlspecialchars($_SESSION['update_message']) ?>
        <button class="close-alert" onclick="closeAlert()">&times;</button>
      </div>
      <?php $_SESSION['update_message'] = ""; // show once only ?>
    <?php endif; ?>

    <form action="" method="post" autocomplete="off">
      <input type="text" name="name" class="box" placeholder="Enter new name" value="<?= htmlspecialchars($fetch_admin['name']) ?>" />
      <input type="email" name="email" class="box" placeholder="Enter new email" value="<?= htmlspecialchars($fetch_admin['email']) ?>" />
      <input type="password" name="old_pass" class="box" placeholder="Enter old password" />
      <input type="password" name="new_pass" class="box" placeholder="Enter new password" />
      <input type="password" name="confirm_pass" class="box" placeholder="Confirm new password" />
      <input type="submit" name="update_profile" value="Update Profile" class="btn" />
    </form>
  </section>

  <script src="admin_js.js"></script>
  <script>
    // Auto-hide the message after 5 seconds
    window.addEventListener("DOMContentLoaded", function () {
      setTimeout(function () {
        const message = document.getElementById("alertMessage");
        if (message) {
          message.style.opacity = "0";
          setTimeout(() => (message.style.display = "none"), 500);
        }
      }, 5000);
    });

    // Manual close of message
    function closeAlert() {
      var msg = document.getElementById("alertMessage");
      if (msg) {
        msg.style.display = "none";
      }
    }
  </script>
</body>
</html>
