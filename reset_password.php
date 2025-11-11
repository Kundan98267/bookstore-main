<?php
include 'config.php';
session_start();

$message = [];

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    if (isset($_POST['submit'])) {
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // update password where token matches
            $update_query = "UPDATE register SET password='$hashed_password', reset_token=NULL, reset_expire=NULL WHERE reset_token='$token'";
            if (mysqli_query($conn, $update_query)) {
                echo "<script>alert('Password updated successfully! You can now login.'); window.location.href='login.php';</script>";
                exit();
            } else {
                $message[] = "Error updating password. Try again.";
            }
        } else {
            $message[] = "Passwords do not match!";
        }
    }
} else {
    echo "<script>alert('Invalid or expired token.'); window.location.href='forgot_password.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reset Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
  <style>
    .form-container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 60px 20px;
    }
    .form-box {
      max-width: 400px;
      width: 100%;
      border: 1px solid #ddd;
      padding: 30px;
      text-align: center;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .form-box h3 {
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .form-box .msg {
      font-size: 14px;
      margin-bottom: 15px;
      color: #d9534f;
    }
    .form-box input.box {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      font-size: 15px;
      border-radius: 5px;
      outline: none;
    }
    .product_btn {
      width: 100%;
      padding: 12px;
      background: #c02736ff;
      color: #fff;
      border: 2px solid #c02736ff;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      white-space: nowrap;
      text-decoration: none;
      text-align: center;
    }
    .product_btn:hover {
      background: #fff;
      color: #c02736ff;
      border: 2px solid #c02736ff;
    }
    .form-box a {
      color: #000;
      font-size: 14px;
      text-decoration: none;
    }
    .form-box a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <?php include 'user_header.php'; ?>

  <section class="form-container">
    <form class="form-box" method="post">
      <h3>RESET PASSWORD</h3>
      <?php 
        if (!empty($message)) {
          foreach ($message as $msg) {
            echo "<p class='msg'>$msg</p>";
          }
        }
      ?>
      <input type="password" name="new_password" required placeholder="Enter new password" class="box">
      <input type="password" name="confirm_password" required placeholder="Confirm new password" class="box">

      <input type="submit" name="submit" value="Reset Password" class="product_btn">
      <p><a href="login.php">Back to login</a></p>
    </form>
  </section>

  <?php include 'footer.php'; ?>
</body>
</html>
