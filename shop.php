<?php
include 'config.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// ✅ Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    if (!$user_id) {
        header('Location: login.php');
        exit();
    }

    $pro_name     = $_POST['product_name'];
    $pro_price    = $_POST['product_price'];
    $pro_quantity = $_POST['product_quantity'];
    $pro_image    = $_POST['product_image'];

    $check = mysqli_query($conn, "SELECT * FROM `cart` WHERE name='$pro_name' AND user_id='$user_id'") or die('query failed');

    if (mysqli_num_rows($check) > 0) {
        $message[] = 'Already added to cart!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) 
                             VALUES ('$user_id', '$pro_name', '$pro_price', '$pro_quantity', '$pro_image')") 
                             or die('query2 failed');
        $message[] = 'Product added to cart!';
    }
}

// ✅ Category Filter
$categoryFilter = '';
if (isset($_GET['category']) && $_GET['category'] !== '') {
    $categoryFilter = mysqli_real_escape_string($conn, $_GET['category']);
}

if ($categoryFilter !== '') {
    $select_products = mysqli_query($conn,
        "SELECT * FROM `products` WHERE category='$categoryFilter' ORDER BY id DESC"
    ) or die('query failed');
} else {
    $select_products = mysqli_query($conn,
        "SELECT * FROM `products` ORDER BY id DESC"
    ) or die('query failed');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop Page</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="home.css">
  <style>
    .msg-box {
        margin: 15px auto;
        max-width: 600px;
        padding: 12px;
        border-radius: 6px;
        text-align: center;
        font-weight: bold;
        animation: fadeOut 5s forwards;
    }
    .msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .msg-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    @keyframes fadeOut {
      0% { opacity: 1; }
      80% { opacity: 1; }
      100% { opacity: 0; display: none; }
    }

    .category-bar {
        max-width: 1200px;
        margin: 20px auto;
        padding: 12px 16px;
        border: 1px solid #111;
        background: #fafafa;
        font-size: 16px;
    }
    .category-bar strong { color: #111; }
    .category-bar a { text-decoration: underline; color: #721c24; margin-left: 10px; }

    /* ✅ Product Grid */
    .products_cont {
      max-width: 1200px;
      margin: auto;
      padding: 30px 16px;
    }
    .pro_box_cont {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }
    .pro_box {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      transition: 0.3s;
      position: relative;
    }
    .pro_box:hover {
      transform: translateY(-5px);
      box-shadow: 0px 6px 15px rgba(0,0,0,0.15);
    }
    .pro_box img {
      width: 100%;
      height: 220px;
      object-fit: contain;
      margin-bottom: 10px;
    }
    .pro_box h3 {
      font-size: 20px;
      font-weight: 600;
      margin: 10px 0 4px;
      color: #111;
    }
    .pro_box .category {
      font-size: 14px;
      color: #666;
      margin-bottom: 10px;
    }
    .price-tag {
      position: absolute;
      top: 10px;
      left: 10px;
      background: #c02736ff;
      color: #fff;
      padding: 5px 10px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 15px;
    }

    /* ✅ Quantity & Button in one line */
    .action-row {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }
    .action-row input[type=number] {
      width: 70px;
      padding: 6px;
      text-align: center;
      border: 2px solid #c02736ff;
      border-radius: 6px;
      font-weight: 600;
    }
    .action-row .product_btn {
      padding: 10px 22px;
      background: #c02736ff;
      color: #fff;
      border: 2px solid #c02736ff;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      white-space: nowrap;
    }
    .action-row .product_btn:hover {
      background: #fff;
      color: #c02736ff;
      border: 2px solid #c02736ff;
    }
  </style>
</head>
<body>
  
<?php include 'user_header.php'; ?>

<!-- ✅ Message Show -->
<?php
if (isset($message)) {
    foreach ($message as $msg) {
        if ($msg == "Already added to cart!") {
            echo '<div class="msg-box msg-error">'.$msg.'</div>';
        } else {
            echo '<div class="msg-box msg-success">'.$msg.'</div>';
        }
    }
}
?>

<!-- ✅ Category Filter Bar -->
<?php if ($categoryFilter !== ''): ?>
  <div class="category-bar">
    Showing category: <strong><?php echo htmlspecialchars($categoryFilter); ?></strong>
    <a href="shop.php">Clear</a>
  </div>
<?php endif; ?>

<!-- ✅ Products Section -->
<section class="products_cont">
  <div class="pro_box_cont">
    <?php
    if (mysqli_num_rows($select_products) > 0) {
        while ($fetch_products = mysqli_fetch_assoc($select_products)) {
    ?>
      <form action="" method="post" class="pro_box">
        <span class="price-tag">₹<?php echo $fetch_products['price']; ?>/-</span>
        <img src="./uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
        <h3><?php echo $fetch_products['name']; ?></h3>
        <div class="category">Category: <?php echo ucfirst($fetch_products['category']); ?></div>

        <div class="action-row">
          <input type="number" name="product_quantity" min="1" value="1">
          <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
          <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
          <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
          <input type="submit" value="Add to Cart" name="add_to_cart" class="product_btn">
        </div>
      </form>
    <?php
        }
    } else {
        echo '<p class="empty">No Products Found!</p>';
    }
    ?>
  </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
<script src="script.js"></script>

</body>
</html>
