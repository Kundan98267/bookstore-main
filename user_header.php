<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
?>

<header class="user_header">
  <div class="header_1">
    <div class="user_flex">
      
      <!-- Logo -->
      <a href="home.php" class="logo">
          <img src="book_logo.png" alt="Book Store Logo" style="height:55px;">
          <span class="logo-text">𝕭𝖔𝖔k 𝕾𝖙𝖔𝖗𝖊</span>
      </a>

      <!-- Navbar -->
      <nav class="navbar">
        <a href="home.php">Home</a>
        <a href="about.php">About</a>
        <a href="shop.php">Shop</a>
        <a href="orders.php">Orders</a>
        <a href="contact.php">Contact</a>
      </nav>

      <!-- Right Side -->
      <div class="last_part">
        <div class="icons">
          <a class="fa-solid fa-magnifying-glass" href="search_page.php"></a>

          <?php
          $cart_count = 0;
          if ($user_id != 0) {
              $stmt = $conn->prepare("SELECT COUNT(*) as total FROM cart WHERE user_id=?");
              $stmt->bind_param("i", $user_id);
              $stmt->execute();
              $result = $stmt->get_result();
              $row = $result->fetch_assoc();
              $cart_count = $row['total'] ?? 0;
              $stmt->close();
          }
          ?>
          <a href="cart.php">
            <i class="fas fa-shopping-cart"></i>
            <span class="quantity">(<?php echo $cart_count; ?>)</span>
          </a>

          <!-- Profile Button -->
          <div class="fas fa-user" id="user_btn"></div>
          <div class="fas fa-bars" id="user_menu_btn"></div>
        </div>
      </div>

      <!-- Profile Box -->
      <div class="profile-box" id="profile_box">
        <?php if ($user_id != 0): ?>
          <?php
            $stmt = $conn->prepare("SELECT name,email FROM users WHERE id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $userData = $result->fetch_assoc();
            $stmt->close();
          ?>
          <p class="name">User name : <span><?= htmlspecialchars($userData['name']); ?></span></p>
          
          <!-- ✅ Profile + Logout buttons -->
          <div class="action-row">
            <a href="profile.php" class="product_btn">Profile</a>
            <a href="logout.php" class="product_btn" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
          </div>

        <?php else: ?>
          <!-- Guest Box -->
          <div class="guest-box">
            <p class="guest-msg"> Please login first!</p>
            <div class="action-row">
              <a href="login.php" class="product_btn">Login</a>
              <a href="register.php" class="product_btn">Register</a>
            </div>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</header>

<!-- Custom CSS -->
<style>
    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }
    .logo-text {
        font-weight: bold;
        font-size: 32px;
        color: #d32f2f;
        position: relative;
        overflow: hidden;
    }
    .logo-text::after {
        content: '';
        position: absolute;
        top: 0;
        left: -150%;
        width: 50%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255,255,255,0.6), transparent);
        animation: shine 2s linear infinite;
    }
    @keyframes shine {
        0% { left: -150%; }
        50% { left: 150%; }
        100% { left: 150%; }
    }

    /* Profile Box Styling */
    .profile-box {
        display: none;
        position: absolute;
        top: 80px;
        right: 20px;
        background: #fff;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        width: 250px;
        z-index: 999;
        color: #000;
    }
    .profile-box.active { display: block; }
    .profile-box p { margin: 8px 0; }
    .profile-box .name span {
        font-weight: bold;
        color: #333;
    }

    /* ✅ Reuse Add-to-Cart button style */
    .action-row {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
    }
    .product_btn {
      padding: 10px 22px;
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

    .guest-msg {
        color: #d32f2f;
        font-weight: bold;
        font-size: 15px;
        text-align: center;
    }
</style>

<script>
  // toggle profile box on user icon click
  document.addEventListener("DOMContentLoaded", function() {
    const userBtn = document.getElementById("user_btn");
    const profileBox = document.getElementById("profile_box");
    userBtn.addEventListener("click", function() {
      profileBox.classList.toggle("active");
    });
  });
</script>
