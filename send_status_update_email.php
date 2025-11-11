<?php
include 'config.php';
session_start();

/* 🔧 FIXED AUTOLOAD PATH */
$autoloadPath1 = __DIR__ . '/vendor/autoload.php'; // If admin_orders.php is in root folder
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php'; // If it's inside admin/ folder

if (file_exists($autoloadPath1)) {
    require $autoloadPath1;
} elseif (file_exists($autoloadPath2)) {
    require $autoloadPath2;
} else {
    die('❌ PHPMailer not found. Please run: composer install');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* 📩 EMAIL FUNCTION */
function sendStatusUpdateEmail($toEmail, $toName, $type, $status) {
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

        // ✅ SENDER & RECIPIENT
        $mail->setFrom('onlinebookstore2025@gmail.com', 'Online Bookstore');
        $mail->addAddress($toEmail, $toName);

        // ✅ EMAIL CONTENT
        $mail->isHTML(true);
        $mail->Subject = "$type Update - Online Bookstore";
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background:#f2f2f2; padding:20px;'>
            <div style='max-width:600px; margin:auto; background:white; padding:25px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);'>
                <h2 style='color:#c02736; text-align:center;'>📚 Online Bookstore Update</h2>
                <p style='font-size:16px;'>Hello <strong>$toName</strong>,</p>
                <p style='font-size:16px;'>Your <strong>$type</strong> status has been updated to:</p>
                <p style='font-size:22px; font-weight:bold; color:#28a745; text-align:center;'>$status</p>
                <hr style='margin:25px 0; border:none; border-top:1px solid #ddd;'>
                <p style='font-size:15px; color:#555;'>Thank you for shopping with <strong>Online Bookstore</strong>! We appreciate your support and hope you enjoy your reading journey. 📖</p>
                <p style='font-size:14px; color:#777;'>— The Online Bookstore Team</p>
            </div>
        </div>";
        $mail->AltBody = "Hello $toName,\n\nYour $type status has been updated to: $status.\n\nThank you for choosing Online Bookstore!";

        // ✅ SEND EMAIL
        $mail->send();
        return "success";
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        return "error: " . $mail->ErrorInfo;
    }
}

/* 🔒 SESSION CHECK */
if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit();
}

/* 💳 UPDATE PAYMENT + SEND EMAIL */
if (isset($_POST['update_payment_btn'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['update_payment'];

    mysqli_query($conn, "UPDATE `orders` SET payment_status='$status' WHERE id='$order_id'") or die('query failed');

    // Fetch order info for email
    $result = mysqli_query($conn, "SELECT name, email FROM `orders` WHERE id='$order_id' LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        sendStatusUpdateEmail($order['email'], $order['name'], "Payment", ucfirst($status));
    }
}

/* 🚚 UPDATE DELIVERY + SEND EMAIL */
if (isset($_POST['update_delivery_btn'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['update_delivery'];

    mysqli_query($conn, "UPDATE `orders` SET delivery_status='$status' WHERE id='$order_id'") or die('query failed');

    // Fetch order info for email
    $result = mysqli_query($conn, "SELECT name, email FROM `orders` WHERE id='$order_id' LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        sendStatusUpdateEmail($order['email'], $order['name'], "Delivery", ucfirst($status));
    }
}

/* ❌ DELETE ORDER */
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `orders` WHERE id='$delete_id'") or die('query failed');
}
?>
