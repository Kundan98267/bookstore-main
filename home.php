<?php
include 'config.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (isset($_POST['add_to_cart'])) {
  if ($user_id) {
      $pro_name     = $_POST['product_name'];
      $pro_price    = $_POST['product_price'];
      $pro_quantity = $_POST['product_quantity'];
      $pro_image    = $_POST['product_image'];

      $check = mysqli_query($conn, "SELECT * FROM `cart` WHERE name='$pro_name' AND user_id='$user_id'") or die('query failed');

      if (mysqli_num_rows($check) > 0) {
          $message[] = 'Already added to cart!';
      } else {
          mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) 
                               VALUES ('$user_id','$pro_name','$pro_price','$pro_quantity','$pro_image')") or die('query2 failed');
          $message[] = 'Product added to cart!';
      }
  } else {
      $message[] = 'Please login to add products to cart!';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bookstore - Home</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="home.css">

  <style>
    .msg-box {
      margin: 20px auto;
      padding: 12px 20px;
      border: 1px solid #c02736ff;
      background: #eafaf1;
      color: #c02736ff;
      text-align: center;
      font-weight: bold;
      border-radius: 6px;
      max-width: 500px;
      animation: fadeOut 4s forwards;
    }
    @keyframes fadeOut {
      0% { opacity: 1; }
      70% { opacity: 1; }
      100% { opacity: 0; display: none; }
    }

    h2.section-title {
      text-align: center;
      font-size: 36px;
      font-weight: 900;
      margin: 0 auto 30px;
      text-transform: uppercase;
      border-bottom: 3px solid #111;
      width: fit-content;
      padding-bottom: 6px;
    }

    /* Category grid */
    .category-grid {
      max-width: 1200px;
      margin: auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      padding: 0 16px 50px;
    }
    .category-card {
      border: 2px solid #111;
      background: #fff;
      padding: 28px 18px;
      text-align: center;
      text-decoration: none;
      border-radius: 8px;
      transition: 0.2s;
      color: #111;
    }
    .category-card i {
      font-size: 50px;
      margin-bottom: 12px;
    }
    .category-card h3 {
      font-size: 20px;
      font-weight: 700;
    }
    .category-card:hover {
      background: #000;
      color: #fff;
    }
    .category-card:hover i,
    .category-card:hover h3 {
      color: #fff;
    }

    /* Latest Books */
    .books-grid {
      max-width: 1200px;
      margin: auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
      padding: 0 16px 50px;
    }
    .book-card {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      transition: 0.3s;
    }
    .book-card:hover {
      transform: translateY(-5px);
      box-shadow: 0px 6px 15px rgba(0,0,0,0.15);
    }
    .book-card img {
      width: 100%;
      height: 220px;
      object-fit: contain;
      margin-bottom: 10px;
    }
    .book-card h3 {
      font-size: 20px;
      font-weight: 600;
      margin: 10px 0;
      color: #111;
    }

    /* Category display under book */
    .book-card .category {
      font-size: 15px;
      color: #555;
      margin-top: -5px;
      margin-bottom: 8px;
    }
    .book-card .category span {
      font-weight: 600;
      color: #c02736ff;
      text-transform: capitalize;
    }

    .book-card .price {
      font-size: 18px;
      font-weight: bold;
      color: #e67e22;
      margin-bottom: 15px;
    }
    .book-card input[type=number] {
      width: 70px;
      padding: 6px;
      margin-bottom: 12px;
      text-align: center;
      border: 2px solid #c02736ff;
      border-radius: 6px;
      font-weight: 600;
    }

    /* Unified button style */
    .btn, .product_btn, .read-btn, .contact-btn {
      padding: 10px 22px;
      background: #c02736ff;
      color: #fff;
      border: 2px solid #c02736ff;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      white-space: nowrap;
      display: inline-block;
      text-decoration: none;
    }
    .btn:hover, .product_btn:hover, .read-btn:hover, .contact-btn:hover {
      background: #fff;
      color: #c02736ff;
      border: 2px solid #c02736ff;
    }

    /* About / Contact */
    .about_cont, .questions_cont {
      max-width: 1100px;
      margin: 60px auto;
      display: flex;
      align-items: center;
      gap: 30px;
      padding: 0 16px;
    }
    .about_cont img {
      width: 50%;
      border-radius: 8px;
    }
    .about_descript h2, .questions h2 {
      font-size: 28px;
      font-weight: 800;
      margin-bottom: 12px;
    }
    .about_descript p, .questions p {
      color: #555;
      line-height: 1.6;
      margin-bottom: 18px;
    }

    @media(max-width: 768px){
      .about_cont, .questions_cont { flex-direction: column; text-align: center; }
      .about_cont img { width: 100%; }
    }
  </style>
</head>
<body>

<?php include 'user_header.php'; ?>

<?php if (isset($message)) { foreach ($message as $msg) { echo "<div class='msg-box'>$msg</div>"; } } ?>

<!-- Hero Section -->
<section class="home_cont">
  <div class="main_descrip">
    <h1>The Bookshelf</h1>
    <p>Explore, Discover, and Buy Your Favorite Books</p>
    <button class="product_btn" onclick="window.location.href='shop.php';">Discover More</button>
  </div>
</section>

<!-- BOOK CATEGORY -->
<section>
  <h2 class="section-title">BOOK CATEGORY</h2>
  <div class="category-grid">
    <a class="category-card" href="shop.php?category=Fiction"><i class="fa-solid fa-book-open"></i><h3>Fiction</h3></a>
    <a class="category-card" href="shop.php?category=Non-Fiction"><i class="fa-solid fa-feather-pointed"></i><h3>Non-Fiction</h3></a>
    <a class="category-card" href="shop.php?category=Children"><i class="fa-solid fa-child"></i><h3>Children</h3></a>
    <a class="category-card" href="shop.php?category=Textbooks"><i class="fa-solid fa-user-graduate"></i><h3>Textbooks</h3></a>
  </div>
</section>

<!-- LATEST BOOKS -->
<section>
  <h2 class="section-title">LATEST BOOKS</h2>
  <div class="books-grid">
    <?php
      $select_products = mysqli_query($conn, "SELECT * FROM `products` ORDER BY id DESC LIMIT 3") or die('query failed');
      if (mysqli_num_rows($select_products) > 0) {
        while ($fetch_products = mysqli_fetch_assoc($select_products)) {
    ?>
    <form action="" method="post" class="book-card">
      <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
      <h3><?php echo $fetch_products['name']; ?></h3>
      <p class="category">Category: <span><?php echo $fetch_products['category']; ?></span></p>
      <div class="price">₹<?php echo $fetch_products['price']; ?>/-</div>
      <input type="number" name="product_quantity" min="1" value="1">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
      <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
    </form>
    <?php } } else { echo '<p class="empty">No Books Added Yet!</p>'; } ?>
  </div>
</section>

<!-- ABOUT -->
<section class="about_cont">
  <img src="uploaded_img/about.jpg" alt="">
  <div class="about_descript">
    <h2>Discover Our Story</h2>
    <p>At Book Store, we are passionate about connecting readers with captivating stories, inspiring ideas, and a world of knowledge. Our bookstore is more than just a place to buy books; it's a haven for book enthusiasts, where the love for literature thrives.</p>
    <a href="about.php" class="read-btn">Read More</a>
  </div>
</section>

<!-- CONTACT -->
<section class="questions_cont">
  <div class="questions">
    <h2>Have Any Queries?</h2>
    <p>At Bookiee, we value your satisfaction and strive to provide exceptional customer service. If you have any questions, concerns, or inquiries, our dedicated team is here to assist you every step of the way.</p>
    <a href="contact.php" class="contact-btn">Contact Us</a>
  </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
