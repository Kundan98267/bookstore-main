<?php
include 'config.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$admin_id = $_SESSION['admin_id'] ?? '';

if (!$admin_id) {
    header('location:login.php');
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('location:admin_messages.php');
    exit;
}

$message_id = $_GET['id'];

// ✅ Fetch the message details safely
$select_message = mysqli_query($conn, "SELECT * FROM `message` WHERE id = '$message_id'") or die('Query failed');
if (mysqli_num_rows($select_message) == 0) {
    header('location:admin_messages.php');
    exit;
}
$message_data = mysqli_fetch_assoc($select_message);

if (isset($_POST['send_reply'])) {
    $reply = mysqli_real_escape_string($conn, $_POST['reply']);
    $user_email = $message_data['email'];
    $user_name = $message_data['name'];
    $user_message = $message_data['message'];

    $mail = new PHPMailer(true);

    try {
        // ✅ Server Settings (Corrected)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'onlinebookstore2025@gmail.com'; // your Gmail
        $mail->Password   = 'yity rhwt tzly wttn'; // Gmail App Password (not your login)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // ✅ proper constant for TLS
        $mail->Port       = 587;

        // ✅ Fix SSL certificate issue on localhost
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // ✅ Sender & Recipient
        $mail->setFrom('onlinebookstore2025@gmail.com', 'Bookstore Admin');
        $mail->addAddress($user_email, $user_name);

        // ✅ Email content
        $mail->isHTML(true);
        $mail->Subject = 'Reply from Bookstore Admin';
        $mail->Body = "
            <p>Dear <b>$user_name</b>,</p>
            <p>We received your message:</p>
            <blockquote style='border-left:3px solid #c02736; padding-left:10px; color:#555;'>$user_message</blockquote>
            <p><b>Admin Reply:</b></p>
            <p style='color:#333;'>$reply</p>
            <br>
            <p>Best Regards,<br><b>Bookstore Support Team</b></p>
        ";

        $mail->send();

        // ✅ Optional: save reply in DB
        mysqli_query($conn, "INSERT INTO `replies`(message_id, reply_text, admin_id) VALUES('$message_id', '$reply', '$admin_id')");

        $success_msg = "✅ Reply sent successfully to $user_email!";
    } catch (Exception $e) {
        $error_msg = "❌ Message could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reply to Message</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: #f9f9f9;
    }
    .form-container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 60px 20px;
      margin-top: 120px;
    }
    .form-box {
      width: 100%;
      max-width: 600px;
      border: 2px solid #000;
      padding: 30px 40px;
      background: #fff;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .form-box h3 {
      margin-bottom: 15px;
      font-size: 22px;
      font-weight: bold;
      color: #222;
    }
    .form-box p {
      font-size: 16px;
      margin: 10px 0;
      text-align: left;
    }
    .form-box textarea {
      width: 90%;
      height: 120px;
      border: 2px solid #000;
      border-radius: 5px;
      padding: 10px;
      resize: none;
      font-size: 15px;
      display: block;
      margin: 15px auto;
    }
    .btn, .back-btn {
      display: inline-block;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      cursor: pointer;
      transition: 0.3s;
      margin: 10px;
    }
    .btn {
      background: #c02736;
      color: #fff;
      border: none;
    }
    .btn:hover {
      background: #fff;
      color: #c02736;
      border: 2px solid #c02736;
    }
    .back-btn {
      background: #000;
      color: #fff;
    }
    .back-btn:hover {
      background: #333;
    }
    .msg {
      margin-top: 15px;
      font-weight: 600;
      color: green;
      text-align: center;
    }
    .error {
      color: red;
      font-weight: 600;
      text-align: center;
    }
  </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="form-container">
  <form class="form-box" method="post">
    <h3>Reply to: <?= htmlspecialchars($message_data['name']); ?></h3>
    <h3><b>Message:</b> <?= nl2br(htmlspecialchars($message_data['message'])); ?></h3>

    <textarea name="reply" placeholder="Type your reply here..." required></textarea>

    <div>
      <input type="submit" name="send_reply" value="Send Reply" class="btn">
      <a href="admin_messages.php" class="back-btn">Back</a>
    </div>

    <?php if (!empty($success_msg)) echo "<p class='msg'>$success_msg</p>"; ?>
    <?php if (!empty($error_msg)) echo "<p class='error'>$error_msg</p>"; ?>
  </form>
</section>

<script src="admin_js.js"></script>
<script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
</body>
</html>
