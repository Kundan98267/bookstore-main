<?php 
include 'config.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$message = [];

if (isset($_POST['send_link'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists
    $check_user = mysqli_query($conn, "SELECT * FROM `register` WHERE email = '$email'");

    if (!$check_user) {
        die("❌ Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($check_user) > 0) {
        $user = mysqli_fetch_assoc($check_user);
        $username = $user['name'];

        // Generate reset token
        $token = bin2hex(random_bytes(16));
        $expire = date("Y-m-d H:i:s", time() + 3600);

        // Update DB
        $update = mysqli_query($conn, "UPDATE `register` 
                SET reset_token='$token', reset_expire='$expire' 
                WHERE email='$email'");

        if (!$update) {
            die("❌ Query update failed: " . mysqli_error($conn));
        }

        // Reset link
        $reset_link = "http://localhost/bookstore-main/reset_password.php?token=$token";

        $mail = new PHPMailer(true);

        try {
        // ✅ SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'onlinebookstore2025@gmail.com'; // your Gmail
        $mail->Password   = 'tjdl qbih uvza uivx';        // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;     // Use SSL
        $mail->Port       = 465;

        // ✅ Fix SSL Verification for Localhost (XAMPP)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

            $mail->setFrom('onlinebookstore2025@gmail.com', 'Book Store');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - Book Store';
            $mail->Body    = "
                <p>Hello, <b>$username</b></p>
                <p>We received a request to reset your password. Click below within the next hour:</p>
                <p><a href='$reset_link'>Reset Your Password</a></p>
                <p>If you didn’t request this, please ignore this email.</p>
            ";

            $mail->send();
            $message[] = "✅ A reset link has been sent to your email.";
        } catch (Exception $e) {
            $message[] = "⚠️ Failed to send reset email. Error: {$mail->ErrorInfo}";
        }
    } else {
        $message[] = "⚠️ No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Forgot Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" /> <!-- common css -->
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
    .btn {
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
    .btn:hover {
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
      <h3>FORGOT PASSWORD</h3>
      <?php 
        if (!empty($message)) {
          foreach ($message as $msg) {
            echo "<p class='msg'>$msg</p>";
          }
        }
      ?>
      <input type="email" name="email" required placeholder="Enter your email" class="box" />
      <input type="submit" name="send_link" value="Send Reset Link" class="btn" />
      <p><a href="login.php">Back to login</a></p>
    </form>
  </section>

  <?php include 'footer.php'; ?>
</body>

</html>
