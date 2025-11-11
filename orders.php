<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
date_default_timezone_set('Asia/Kolkata');

// Handle cancel request
if (isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);

    // Check status before cancelling
    $check_status = mysqli_query($conn, "SELECT delivery_status FROM `orders` WHERE id='$order_id' AND user_id='$user_id'") or die('query failed');
    if (mysqli_num_rows($check_status) > 0) {
        $order_data = mysqli_fetch_assoc($check_status);
        if ($order_data['delivery_status'] != 'delivered' && $order_data['delivery_status'] != 'cancelled') {
            mysqli_query($conn, "UPDATE `orders` SET delivery_status='cancelled' WHERE id='$order_id'") or die('query failed');
            echo "<script>alert('Your order has been cancelled.'); window.location.href='orders.php';</script>";
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Orders</title>

  <!-- keep same css links as 1st code so cart/profile show correctly -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="home.css">

  <style>
      /* Orders Page Scoped CSS */
      .orders-page {
         max-width: 1000px;
         margin: 40px auto;
         padding: 0 16px;
      }
      .orders-page h1.title {
         text-align: center;
         font-size: 26px;
         margin-bottom: 25px;
         font-weight: bold;
      }
      .orders-page .order-box {
         background: #fff;
         border: 1px solid #ddd;
         border-radius: 10px;
         padding: 20px;
         margin-bottom: 25px;
         box-shadow: 0 3px 8px rgba(0,0,0,0.08);
         overflow: hidden;
      }
      .orders-page .order-box p { margin: 6px 0; font-size: 15px; }
      .orders-page .order-box span { font-weight: bold; }

      /* Timeline */
      .orders-page .order-timeline {
         position: relative;
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin: 30px 10px;
      }
      .orders-page .order-timeline::before {
         content: "";
         position: absolute;
         top: 20px;
         left: 0;
         right: 0;
         height: 4px;
         background: #ccc;
         z-index: 1;
      }
      .orders-page .order-progress {
         position: absolute;
         top: 20px;
         left: 0;
         height: 4px;
         background: #4CAF50;
         z-index: 2;
         transition: width 0.4s ease;
      }
      .orders-page .order-step {
         position: relative;
         text-align: center;
         flex: 1;
         z-index: 3;
      }
      .orders-page .order-circle {
         width: 40px;
         height: 40px;
         line-height: 40px;
         border-radius: 50%;
         background: #ccc;
         margin: 0 auto;
         font-size: 18px;
         display: flex;
         justify-content: center;
         align-items: center;
      }
      .orders-page .order-step.active .order-circle { background: #4CAF50; color: #fff; }
      .orders-page .order-label { margin-top: 8px; font-size: 14px; color: #444; }
      .orders-page .order-step.active .order-label { color: #4CAF50; }

      /* Vehicle */
      .orders-page .vehicle img {
         width: 30px;
         height: 30px;
      }
      .orders-page .vehicle.moving img {
         animation: moveVehicle 0.8s ease-in-out infinite alternate;
      }
      @keyframes moveVehicle {
         0% { transform: translateX(-5px); }
         100% { transform: translateX(5px); }
      }

      /* Cancelled order */
      .orders-page .cancelled .order-circle { background-color: red; color: white; }
      .orders-page .cancelled .order-label { color: red; }

      /* Buttons */
      .orders-page .order-btn {
         display: inline-block;
         margin-top: 15px;
         padding: 10px 20px;
         background: #c02736;
         color: #fff;
         border-radius: 6px;
         text-decoration: none;
         font-weight: bold;
         border: none;
         cursor: pointer;
      }
      .orders-page .order-btn:hover { background: #fff; color: #c02736; border: 2px solid #c02736; }
  </style>
</head>
<body>
  
<?php include 'user_header.php'; ?>

<section class="orders-page">
   <h1 class="title">Your Orders</h1>
   <?php
      $select_orders = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id='$user_id' ORDER BY id DESC") or die('query failed');
      if(mysqli_num_rows($select_orders) > 0){
         while($order = mysqli_fetch_assoc($select_orders)){
            $status = $order['delivery_status'];
            $progressWidth = 0;
            if ($status == 'on the way') $progressWidth = 50;
            if ($status == 'delivered') $progressWidth = 100;
   ?>
   <div class="order-box">
      <p>Placed on : <span><?= $order['placed_on']; ?></span></p>
      <p>Name : <span><?= $order['name']; ?></span></p>
      <p>Email : <span><?= $order['email']; ?></span></p>
      <p>Number : <span><?= $order['number']; ?></span></p>
      <p>Address : <span><?= $order['address']; ?></span></p>
      <p>Payment method : <span><?= $order['method']; ?></span></p>
      <p>Your orders : <span><?= $order['total_products']; ?></span></p>
      <p>Total price : <span>₹<?= $order['total_price']; ?>/-</span></p>
      <p>Payment status : 
         <span style="color:<?= $order['payment_status'] == 'pending' ? 'red' : 'green'; ?>">
            <?= ucfirst($order['payment_status']); ?>
         </span>
      </p>
      <p>Delivery status : 
         <span style="color:
            <?= $status == 'delivered' ? 'green' : 
               ($status == 'on the way' ? 'orange' : 
               ($status == 'cancelled' ? 'red' : 'gray')); ?>">
            <?= ucfirst($status); ?>
         </span>
      </p>

      <!-- Timeline -->
      <?php if ($status == 'cancelled'): ?>
         <div class="order-timeline">
            <div class="order-step cancelled">
               <div class="order-circle">❌</div>
               <div class="order-label">Cancelled</div>
            </div>
         </div>
      <?php else: ?>
         <div class="order-timeline">
            <div class="order-progress" style="width: <?= $progressWidth; ?>%;"></div>

            <!-- Step 1 -->
            <div class="order-step active">
               <div class="order-circle">✅</div>
               <div class="order-label">Order Placed</div>
            </div>

            <!-- Step 2 -->
            <div class="order-step <?= in_array($status, ['on the way','delivered']) ? 'active' : '' ?>">
               <div class="order-circle vehicle <?= $status == 'on the way' ? 'moving' : '' ?>">
                  <img src="uploaded_img/fast.png" alt="On the way">
               </div>
               <div class="order-label">On the Way</div>
            </div>

            <!-- Step 3 -->
            <div class="order-step <?= $status == 'delivered' ? 'active' : '' ?>">
               <div class="order-circle">📦</div>
               <div class="order-label">Delivered</div>
            </div>
         </div>
      <?php endif; ?>

      <!-- Cancel Button -->
      <?php if ($status != 'delivered' && $status != 'cancelled'): ?>
         <form action="" method="post">
            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
            <button type="submit" name="cancel_order" class="order-btn" onclick="return confirm('Are you sure you want to cancel this order?');">
               Cancel Order
            </button>
         </form>
      <?php endif; ?>

      <a href="invoice.php?order_id=<?= $order['id']; ?>" class="order-btn" target="_blank">Download Invoice</a>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">No orders placed yet!</p>';
      }
   ?>
</section>

<?php include 'footer.php'; ?>
<script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
<script src="script.js"></script>
</body>
</html>
