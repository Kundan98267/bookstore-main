<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

include 'config.php';
require_once 'send_email_invoice.php'; // for email+invoice (mysqli version)

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// ✅ Fetch profile
$profile_stmt = $conn->prepare("SELECT id, name, email, number, address FROM `register` WHERE id = ?");
$profile_stmt->bind_param("i", $user_id);
$profile_stmt->execute();
$profile_res = $profile_stmt->get_result();
$fetch_profile = $profile_res->fetch_assoc() ?: [
    'name' => '',
    'email' => '',
    'number' => '',
    'address' => ''
];

// ✅ Place order
if (isset($_POST['order_btn'])) {
    $name     = trim($_POST['name']);
    $number   = trim($_POST['number']);
    $email    = trim($_POST['email']);
    $method   = trim($_POST['method']);
    $address  = trim($_POST['address']);
    $placed_on = date('Y-m-d H:i:s');

    // check cart
    $check_cart = $conn->prepare("SELECT name, price, quantity FROM `cart` WHERE user_id = ?");
    $check_cart->bind_param("i", $user_id);
    $check_cart->execute();
    $cart_result = $check_cart->get_result();

    if ($cart_result->num_rows > 0) {
        if (empty($address)) {
            echo "<script>alert('Please add your address before placing an order!');</script>";
        } else {
            // collect cart items
            $cart_items = [];
            $grand_total = 0;
            while ($r = $cart_result->fetch_assoc()) {
                $cart_items[] = $r['name'] . ' (' . $r['quantity'] . ')';
                $grand_total += ((float)$r['price'] * (int)$r['quantity']);
            }
            $total_products = implode(', ', $cart_items);

            // insert order
            $insert_order = $conn->prepare("
                INSERT INTO `orders`
                (user_id, name, number, email, method, address, total_products, total_price, placed_on)
                VALUES (?,?,?,?,?,?,?,?,?)
            ");
            $insert_order->bind_param(
                "issssssss",
                $user_id,
                $name,
                $number,
                $email,
                $method,
                $address,
                $total_products,
                $grand_total,
                $placed_on
            );

            if ($insert_order->execute()) {
                $last_order_id = $insert_order->insert_id;

                // clear cart
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
                $delete_cart->bind_param("i", $user_id);
                $delete_cart->execute();

                // update latest address
                $update_profile = $conn->prepare("UPDATE `register` SET address = ? WHERE id = ?");
                $update_profile->bind_param("si", $address, $user_id);
                $update_profile->execute();

                // send email + invoice
                sendEmailInvoice($last_order_id, $conn);

                // ✅ success alert + redirect to invoice.php
                echo "<script>
                    alert('🎉 Your order has been placed successfully!');
                    window.location.href='invoice.php?order_id={$last_order_id}';
                </script>";
                exit;
            } else {
                echo "<script>alert('Something went wrong while placing the order.');</script>";
            }
        }
    } else {
        echo "<script>alert('Your cart is empty!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout Page</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="home.css">
</head>
<body>

<?php include 'user_header.php'; ?>

<section class="display_order">
  <h2>Ordered Products</h2>
  <?php
    $grand_total = 0;
    $select_cart = $conn->prepare("SELECT name, price, quantity, image FROM `cart` WHERE user_id = ?");
    $select_cart->bind_param("i", $user_id);
    $select_cart->execute();
    $cart_result = $select_cart->get_result();

    if ($cart_result->num_rows > 0) {
        while ($fetch_cart = $cart_result->fetch_assoc()) {
            $total_price = ((float)$fetch_cart['price'] * (int)$fetch_cart['quantity']);
            $grand_total += $total_price;
  ?>
  <div class="single_order_product">
    <img src="./uploaded_img/<?= htmlspecialchars($fetch_cart['image']); ?>" alt="">
    <div class="single_des">
      <h3><?= htmlspecialchars($fetch_cart['name']); ?></h3>
      <p>Rs. <?= htmlspecialchars($fetch_cart['price']); ?></p>
      <p>Quantity : <?= (int)$fetch_cart['quantity']; ?></p>
    </div>
  </div>
  <?php
        }
    } else {
        echo '<p class="empty">Your cart is empty</p>';
    }
  ?>
  <div class="checkout_grand_total">
    GRAND TOTAL : <span>Rs. <?= number_format((float)$grand_total, 2); ?>/-</span>
  </div>
</section>

<section class="contact_us">
<form action="" method="post">
   <h2>Add Your Details</h2>
   <input type="text" name="name" required value="<?= htmlspecialchars($fetch_profile['name']); ?>" placeholder="Enter your name">
   <input type="tel" name="number" required value="<?= htmlspecialchars($fetch_profile['number']); ?>" placeholder="Enter your number">
   <input type="email" name="email" required value="<?= htmlspecialchars($fetch_profile['email']); ?>" placeholder="Enter your email">
   <select name="method" required>
      <option value="" disabled selected>Select payment method --</option>
      <option value="cash on delivery">Cash on Delivery</option>
      <option value="gpay">GPay</option>
      <option value="paytm">Paytm</option>
      <option value="phonepe">PhonePe</option>
      <option value="paypal">Paypal</option>
   </select>
   <textarea name="address" placeholder="Enter your address" cols="30" rows="5"><?= htmlspecialchars($fetch_profile['address']); ?></textarea>
   <input type="submit" value="Place Your Order" name="order_btn" class="product_btn">
</form>
</section>

<?php include 'footer.php'; ?>
<script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
<script src="script.js"></script>
</body>
</html>
