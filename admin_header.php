<?php
if (isset($message) && is_array($message)) {
  foreach ($message as $msg) {
    echo '
      <div class="message">
        <span>' . htmlspecialchars($msg) . '</span>
        <i class="fa-solid fa-xmark" onclick="this.parentElement.remove();"></i>
      </div>
    ';
  }
}
?>


<header class="admin_header">
  <div class="header_navigation">
    <a href="admin_page.php" class="header_logo">Admin <span>Dashboard</span></a>

    <nav class="header_navbar">
      <a href="admin_page.php">Home</a>
      <a href="admin_products.php">Products</a>
      <a href="admin_orders.php">Orders</a>
      <a href="admin_users.php">Users</a>
      <a href="admin_messages.php">Messages</a>
    </nav>

    <div class="header_icons">
      <div id="menu_btn" class="fas fa-bars"></div>
      <div id="user_btn" class="fas fa-user"></div>
    </div>

    <div class="header_acc_box">
      <p>Username : <span><?php echo $_SESSION['admin_name']; ?></span></p>
      <p>Email : <span><?php echo $_SESSION['admin_email']; ?></span></p>
      
      <div class="header_buttons">
        <!-- ✅ Redirects to admin_update_profile.php -->
        <a href="admin_update.php" class="btn">Update Profile</a>
        <a href="logout.php" class="btn">Logout</a>
      </div>
    </div>
  </div>
</header>

<style>
  .header_acc_box {
    position: absolute;
    top: 110%;
    right: 2rem;
    background: #fff;
    border: 2px solid #000;
    border-radius: 10px;
    padding: 20px;
    width: 250px;
    display: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    text-align: left;
    z-index: 1000;
  }

  .header_acc_box.active {
    display: block;
  }

  .header_acc_box p {
    font-size: 15px;
    margin-bottom: 8px;
  }

  .header_acc_box span {
    color: #c02736;
    font-weight: 600;
  }

  /* Same button style as “View Users” */
  .header_buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 15px;
  }

  .header_buttons .btn {
    display: inline-block;
    background: #c02736;
    color: #fff;
    font-weight: 600;
    text-align: center;
    padding: 10px 15px;
    border-radius: 6px;
    transition: all 0.3s ease;
    text-decoration: none;
    border: 2px solid #c02736;
  }

  .header_buttons .btn:hover {
    background: #fff;
    color: #c02736;
  }
</style>

<script>
  // Toggle user box
  document.querySelector('#user_btn').onclick = () => {
    document.querySelector('.header_acc_box').classList.toggle('active');
  };
</script>
