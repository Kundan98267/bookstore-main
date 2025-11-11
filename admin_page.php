<?php
include 'config.php';
session_start();

// ✅ Ensure only admin can access
if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// ✅ Fetch Admin Profile
$fetch_profile = null;
$select_profile = mysqli_query($conn, "SELECT * FROM `register` WHERE id = '$admin_id' AND user_type = 'admin' LIMIT 1") or die('query failed');
if (mysqli_num_rows($select_profile) > 0) {
    $fetch_profile = mysqli_fetch_assoc($select_profile);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="style.css">

  <style>
    body {
      background: #f5f5f5;
      font-family: 'Poppins', sans-serif;
    }
    .dashboard {
      padding: 100px 20px 50px;
      text-align: center;
    }
    .dashboard h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 30px;
      text-transform: uppercase;
      color: #222;
    }
    .box-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      justify-content: center;
      align-items: stretch;
      max-width: 1200px;
      margin: 0 auto;
    }
    .box {
      background: #fff;
      border: 2px solid #000;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .box:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }
    .box h3 {
      font-size: 22px;
      color: #c02736;
      margin-bottom: 10px;
    }
    .box p {
      font-size: 15px;
      color: #444;
      margin-bottom: 15px;
    }
    .box a.btn {
      display: inline-block;
      padding: 8px 16px;
      background: #c02736;
      color: #fff;
      font-weight: 600;
      border-radius: 6px;
      transition: 0.3s;
      text-decoration: none;
    }
    .box a.btn:hover {
      background: #fff;
      color: #c02736;
      border: 2px solid #c02736;
    }
  </style>
</head>

<body>

  <?php include 'admin_header.php'; ?>

  <section class="dashboard">
    <h1>Admin Dashboard</h1>

    <div class="box-container">

      <!-- ✅ Admin Profile -->
      <div class="box">
        <h3>Welcome!</h3>
        <?php if ($fetch_profile): ?>
          <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
        <?php else: ?>
          <p>Profile not found</p>
        <?php endif; ?>
        <a href="admin_update.php" class="btn">Update Profile</a>
      </div>

      <!-- ✅ Pending Payments -->
      <div class="box">
        <?php
          $total_pendings = 0;
          $select_pending = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'pending'") or die('query failed');
          while ($row = mysqli_fetch_assoc($select_pending)) {
              $total_pendings += $row['total_price'];
          }
        ?>
        <h3>₹<?= number_format($total_pendings); ?></h3>
        <p>Total Payments Pending</p>
        <a href="admin_orders.php" class="btn">View Orders</a>
      </div>

      <!-- ✅ Completed Payments -->
      <div class="box">
        <?php
          $total_completed = 0;
          $select_completed = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'completed'") or die('query failed');
          while ($row = mysqli_fetch_assoc($select_completed)) {
              $total_completed += $row['total_price'];
          }
        ?>
        <h3>₹<?= number_format($total_completed); ?></h3>
        <p>Completed Payments</p>
        <a href="admin_orders.php" class="btn">View Orders</a>
      </div>

      <!-- ✅ Orders -->
      <div class="box">
        <?php
          $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
          $number_of_orders = mysqli_num_rows($select_orders);
        ?>
        <h3><?= $number_of_orders; ?></h3>
        <p>Orders Placed</p>
        <a href="admin_orders.php" class="btn">View Orders</a>
      </div>

      <!-- ✅ Products -->
      <div class="box">
        <?php
          $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
          $number_of_products = mysqli_num_rows($select_products);
        ?>
        <h3><?= $number_of_products; ?></h3>
        <p>Products Added</p>
        <a href="admin_products.php" class="btn">View Products</a>
      </div>

      <!-- ✅ Registered Users -->
      <div class="box">
        <?php
          $select_users = mysqli_query($conn, "SELECT * FROM `register` WHERE user_type = 'user'") or die('query failed');
          $number_of_users = mysqli_num_rows($select_users);
        ?>
        <h3><?= $number_of_users; ?></h3>
        <p>Registered Users</p>
        <a href="admin_users.php?type=user" class="btn">View Users</a>
      </div>

      <!-- ✅ Registered Admins -->
      <div class="box">
        <?php
          $select_admins = mysqli_query($conn, "SELECT * FROM `register` WHERE user_type = 'admin'") or die('query failed');
          $number_of_admins = mysqli_num_rows($select_admins);
        ?>
        <h3><?= $number_of_admins; ?></h3>
        <p>Registered Admins</p>
        <a href="admin_users.php?type=admin" class="btn">View Admins</a>
      </div>

      <!-- ✅ Messages -->
      <div class="box">
        <?php
          $select_messages = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
          $number_of_messages = mysqli_num_rows($select_messages);
        ?>
        <h3><?= $number_of_messages; ?></h3>
        <p>New Messages</p>
        <a href="admin_messages.php" class="btn">View Messages</a>
      </div>

    </div>
  </section>

  <script src="admin_js.js"></script>
  <script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
</body>
</html>
