<?php
include 'config.php';
session_start();

/* ✅ SECURE SESSION CHECK */
if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit();
}

/* ✅ AUTOLOAD FILE CHECK FOR PHPMailer */
$autoloadPath1 = __DIR__ . '/vendor/autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require $autoloadPath1;
} elseif (file_exists($autoloadPath2)) {
    require $autoloadPath2;
} else {
    die('❌ PHPMailer not found. Please run: composer install');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ✅ EMAIL FUNCTION */
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

        // Sender & Recipient
        $mail->setFrom('onlinebookstore2025@gmail.com', 'Online Bookstore');
        $mail->addAddress($toEmail, $toName);

        // ✅ Email Content
        $mail->isHTML(true);
        $mail->Subject = "$type Update - Online Bookstore";
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background:#f9f9f9; padding:20px;'>
            <div style='max-width:600px; margin:auto; background:white; padding:25px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);'>
                <h2 style='color:#c02736; text-align:center;'>📚 Bookstore Update</h2>
                <p style='font-size:16px;'>Hello <strong>$toName</strong>,</p>
                <p style='font-size:16px;'>Your <strong>$type</strong> status has been updated to:</p>
                <p style='font-size:22px; font-weight:bold; color:#28a745; text-align:center;'>$status</p>
                <hr style='margin:25px 0; border:none; border-top:1px solid #ddd;'>
                <p style='font-size:15px; color:#555;'>Thank you for shopping with <strong>Online Bookstore</strong>! We appreciate your support and hope you enjoy your reading journey. 📖</p>
                <p style='font-size:14px; color:#777;'>— The Online Bookstore Team</p>
            </div>
        </div>";
        $mail->AltBody = "Hello $toName,\n\nYour $type status has been updated to: $status.\n\nThank you for choosing Online Bookstore!";

        // Send email
        $mail->send();
        return true;

    } catch (Exception $e) {
        // ✅ Show and log error for debugging
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

/* ✅ HANDLE STATUS UPDATES */
$message = '';

if (isset($_POST['update_payment_btn'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['update_payment']);

    mysqli_query($conn, "UPDATE `orders` SET payment_status='$status' WHERE id='$order_id'") or die('Query failed');
    $result = mysqli_query($conn, "SELECT name, email FROM `orders` WHERE id='$order_id' LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        $sent = sendStatusUpdateEmail($order['email'], $order['name'], "Payment", ucfirst($status));
        $message = $sent ? "✅ Payment status updated and email sent!" : "⚠ Payment updated but email failed!";
    }
}

if (isset($_POST['update_delivery_btn'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['update_delivery']);

    mysqli_query($conn, "UPDATE `orders` SET delivery_status='$status' WHERE id='$order_id'") or die('Query failed');
    $result = mysqli_query($conn, "SELECT name, email FROM `orders` WHERE id='$order_id' LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        $sent = sendStatusUpdateEmail($order['email'], $order['name'], "Delivery", ucfirst($status));
        $message = $sent ? "✅ Delivery status updated and email sent!" : "⚠ Delivery updated but email failed!";
    }
}

if (isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM `orders` WHERE id='$delete_id'") or die('Query failed');
    $message = "🗑️ Order deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Orders</title>
   <link rel="stylesheet" href="admin.css">
   <link rel="stylesheet" href="style.css">
   <script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
   <style>
      .admin-orders { margin-top: 130px; }
      .orders-container {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
         gap: 15px;
         margin: 20px;
      }
      .order-card {
         border: 1px solid #ccc;
         border-radius: 10px;
         padding: 15px;
         background: #fff;
         box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
      }
      .order-card p span { font-weight: bold; color: #333; }
      .status-bar { display: flex; justify-content: space-between; margin: 15px 0; }
      .status-bar div { text-align: center; flex: 1; }
      .status-bar i { font-size: 22px; margin-bottom: 5px; }
      .order-actions { margin-top: 10px; }
      .order-actions select, .order-actions input[type="submit"] {
         margin: 5px 0; padding: 8px; border-radius: 5px;
         border: 1px solid #aaa; width: 100%;
      }
      .btn-invoice, .delete-btn {
         display: block; margin-top: 10px; padding: 12px;
         background: #c02736; color: #fff; border: 2px solid #c02736;
         border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 16px;
         text-align: center; text-decoration: none; transition: all 0.3s ease;
      }
      .btn-invoice:hover, .delete-btn:hover {
         background: #fff; color: #c02736; border: 2px solid #c02736;
      }
      .empty { grid-column: 1/-1; font-size: 20px; text-align: center; color: #888; margin: 30px 0; }
      .alert {
         position: fixed;
         top: 15px;
         left: 50%;
         transform: translateX(-50%);
         background: #d4edda;
         color: #155724;
         border: 2px solid #c3e6cb;
         border-radius: 10px;
         padding: 14px 25px;
         font-weight: 600;
         font-size: 16px;
         text-align: center;
         z-index: 9999;
         box-shadow: 0 5px 15px rgba(0,0,0,0.2);
         animation: fadeInOut 4s ease forwards;
      }
      @keyframes fadeInOut {
         0% { opacity: 0; transform: translate(-50%, -20px); }
         10%, 90% { opacity: 1; transform: translate(-50%, 0); }
         100% { opacity: 0; transform: translate(-50%, -20px); }
      }
   </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<?php if ($message): ?>
<div class="alert"><?= htmlspecialchars($message) ?></div>
<script>
   setTimeout(()=>{ 
       const alertBox = document.querySelector('.alert'); 
       if(alertBox) alertBox.remove(); 
   }, 4000);
</script>
<?php endif; ?>

<section class="admin-orders">
   <div class="orders-container">
   <?php
      $select_orders = mysqli_query($conn, "SELECT * FROM `orders` ORDER BY id DESC") or die('Query failed');
      if (mysqli_num_rows($select_orders) > 0) {
         while ($order = mysqli_fetch_assoc($select_orders)) {
   ?>
   <div class="order-card">
      <p>Placed On : <span><?= htmlspecialchars($order['placed_on']); ?></span></p>
      <p>Name : <span><?= htmlspecialchars($order['name']); ?></span></p>
      <p>Email : <span><?= htmlspecialchars($order['email']); ?></span></p>
      <p>Number : <span><?= htmlspecialchars($order['number']); ?></span></p>
      <p>Address : <span><?= htmlspecialchars($order['address']); ?></span></p>
      <p>Your Books : <span><?= htmlspecialchars($order['total_products']); ?></span></p>
      <p>Total Price : <span>₹<?= htmlspecialchars($order['total_price']); ?>/-</span></p>
      <p>Payment Method : <span><?= htmlspecialchars($order['method']); ?></span></p>
      <p>Payment Status : <span><?= ucfirst(htmlspecialchars($order['payment_status'])); ?></span></p>
      <p>Delivery Status : <span><?= ucfirst(htmlspecialchars($order['delivery_status'])); ?></span></p>

      <div class="status-bar">
         <div><i class="fas fa-check-circle" style="color:green"></i><br>Order Placed</div>
         <div><i class="fas fa-shipping-fast" style="color:orange"></i><br>On the Way</div>
         <div><i class="fas fa-box" style="color:blue"></i><br>Delivered</div>
      </div>

      <form action="" method="post" class="order-actions">
         <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
         <select name="update_payment">
            <option value="pending" <?= ($order['payment_status']=="pending"?"selected":"") ?>>Pending</option>
            <option value="completed" <?= ($order['payment_status']=="completed"?"selected":"") ?>>Completed</option>
            <option value="cancelled,Amount Refund" <?= ($order['payment_status']=="cancelled,Amount Refund"?"selected":"") ?>>Cancelled, Amount Refund</option>
         </select>
         <input type="submit" name="update_payment_btn" value="Update Payment">
      </form>

      <form action="" method="post" class="order-actions">
         <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
         <select name="update_delivery">
            <option value="processing" <?= ($order['delivery_status']=="processing"?"selected":"") ?>>Processing</option>
            <option value="on the way" <?= ($order['delivery_status']=="on the way"?"selected":"") ?>>On the Way</option>
            <option value="delivered" <?= ($order['delivery_status']=="delivered"?"selected":"") ?>>Delivered</option>
            <option value="cancelled" <?= ($order['delivery_status']=="cancelled"?"selected":"") ?>>🚫 Cancelled</option>
         </select>
         <input type="submit" name="update_delivery_btn" value="Update Delivery">
      </form>

      <a href="invoice.php?order_id=<?= $order['id']; ?>" class="btn-invoice">Download Invoice</a>
      <a href="admin_orders.php?delete=<?= $order['id']; ?>" onclick="return confirm('Delete this order?');" class="delete-btn">Delete</a>
   </div>
   <?php } } else { echo '<p class="empty">No orders placed yet!</p>'; } ?>
   </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const userBtn = document.getElementById("user_btn");
    const accBox = document.querySelector(".header_acc_box");
    if (userBtn && accBox) {
        userBtn.addEventListener("click", () => accBox.classList.toggle("active"));
    }
});
</script>
</body>
</html>
