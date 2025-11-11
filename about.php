<?php
include 'config.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us | Online Book Store</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="style.css">

   <style>
      /* --- General Styling --- */
      .about {
         padding: 60px 20px;
         max-width: 1200px;
         margin: auto;
      }
      .about .row {
         display: flex;
         align-items: center;
         flex-wrap: wrap;
         gap: 40px;
      }
      .about .row .image {
         flex: 1 1 45%;
         text-align: center;
      }
      .about .row .image img {
         max-width: 100%;
         border-radius: 12px;
         box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      }
      .about .row .content {
         flex: 1 1 45%;
      }
      .about .row .content h3 {
         font-size: 28px;
         margin-bottom: 15px;
         font-weight: bold;
         color: #333;
      }
      .about .row .content p {
         margin-bottom: 10px;
         font-size: 16px;
         color: #444;
         line-height: 1.6;
      }
      .about .btn {
         display: inline-block;
         margin-top: 15px;
         padding: 10px 20px;
         background: #c02736;
         color: #fff;
         border-radius: 6px;
         text-decoration: none;
         transition: 0.3s;
      }
      .about .btn:hover {
         background: #fff;
         color: #c02736;
         border: 2px solid #c02736;
      }

      /* --- Simple Steps Section --- */
      .steps {
         padding: 60px 20px;
         max-width: 1200px;
         margin: auto;
         text-align: center;
      }
      .steps h1 {
         font-size: 28px;
         margin-bottom: 30px;
         font-weight: bold;
         text-transform: uppercase;
      }
      .steps .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
         gap: 25px;
      }
      .steps .box {
         background: #fff;
         padding: 25px;
         border: 1px solid #ddd;
         border-radius: 10px;
         box-shadow: 0 6px 12px rgba(0,0,0,0.1);
         transition: 0.4s ease;
         position: relative;
         overflow: hidden;
      }
      .steps .box:hover {
         transform: translateY(-8px);
         box-shadow: 0 10px 18px rgba(0,0,0,0.15);
      }
      .icon-container {
         width: 100%;
         height: 120px;
         display: flex;
         align-items: center;
         justify-content: center;
         margin-bottom: 15px;
         position: relative;
      }
      /* Floating animation (used in Choose & Enjoy) */
      .floating {
         height: 100px;
         transition: transform 0.3s ease, filter 0.3s ease;
         animation: floatUpDown 3s ease-in-out infinite;
      }
      @keyframes floatUpDown {
         0%, 100% { transform: translateY(0); }
         50% { transform: translateY(-10px); }
      }
      .steps .box:hover .floating {
         transform: scale(1.1) rotate(3deg);
         filter: drop-shadow(0 5px 8px rgba(0,0,0,0.15));
      }

      /* Bike Animation */
      .bike {
         height: 100px;
         animation: moveBike 3s linear infinite;
      }
      @keyframes moveBike {
         0% { transform: translateX(-20px); }
         50% { transform: translateX(20px); }
         100% { transform: translateX(-20px); }
      }

      /* --- Reviews Section --- */
      .reviews {
         padding: 60px 20px;
         max-width: 1200px;
         margin: auto;
         text-align: center;
      }
      .reviews h1 {
         font-size: 28px;
         margin-bottom: 30px;
         font-weight: bold;
         text-transform: uppercase;
      }
      .reviews .slide {
         background: #fff;
         padding: 20px;
         border: 1px solid #ddd;
         border-radius: 8px;
         box-shadow: 0 6px 12px rgba(0,0,0,0.1);
      }
      .reviews .slide img {
         width: 80px;
         height: 80px;
         border-radius: 50%;
         margin-bottom: 15px;
      }
      .reviews .stars {
         color: #ffcc00;
         margin-top: 8px;
      }
   </style>
</head>
<body>

<?php include 'user_header.php'; ?>

<!-- About Section -->
<section class="about">
   <div class="row">
      <div class="image" data-aos="fade-right">
         <img src="uploaded_img/about1.jpg" alt="About Illustration">
      </div>
      <div class="content" data-aos="fade-left">
         <h3>Why Choose Us?</h3>
         <p>Welcome to <strong>Online Book Store</strong> — your one-stop destination for exploring, discovering, and purchasing books. We are passionate about spreading knowledge and joy through books.</p>
         
         <div class="features">
            <p><strong>📚 Vast Collection:</strong> Choose from a wide range of genres and authors.</p>
            <p><strong>⚡ Fast Delivery:</strong> Get your favorite books delivered quickly and safely.</p>
            <p><strong>🤝 Customer Support:</strong> Friendly service for a smooth book-buying experience.</p>
            <p><strong>💻 Easy Access:</strong> Browse, order, and enjoy books from the comfort of your home.</p>
         </div>

         <p>At Online Book Store, we believe every book opens up a new world. Start your reading journey with us today!</p>

         <a href="shop.php" class="btn">Browse Books</a>
      </div>
   </div>
</section>

<!-- ✨ Animated Simple Steps Section -->
<section class="steps">
   <h1>Simple Steps</h1>
   <div class="box-container">
      
      <!-- 📚 Choose Your Book -->
      <div class="box choose">
         <div class="icon-container">
            <img src="uploaded_img/choose book.png" alt="Choose Book" class="floating">
         </div>
         <h3>Choose Your Book</h3>
         <p>Browse our collection and pick your favorite books with ease.</p>
      </div>

      <!-- 🚲 Fast Delivery -->
      <div class="box">
         <div class="icon-container">
            <img src="uploaded_img/fast.png" alt="Fast Delivery" class="bike">
         </div>
         <h3>Fast Delivery</h3>
         <p>Your selected books are packed with care and delivered safely.</p>
      </div>

      <!-- 📖 Enjoy Reading -->
      <div class="box enjoy">
         <div class="icon-container">
            <img src="uploaded_img/Enjoy reading.png" alt="Enjoy Reading" class="floating">
         </div>
         <h3>Enjoy Reading</h3>
         <p>Relax with your books and dive into a world of imagination.</p>
      </div>

   </div>
</section>

<!-- ✨ Customer Reviews Section -->
<section class="reviews">
   <h1>Customer Reviews</h1>
   <div class="swiper reviews-slider">
      <div class="swiper-wrapper">
         <div class="swiper-slide slide">
            <img src="uploaded_img/pic-1.png" alt="">
            <p>This bookstore has an amazing collection! I always find what I need.</p>
            <div class="stars">
               <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Rahul Sharma</h3>
         </div>
         <div class="swiper-slide slide">
            <img src="uploaded_img/pic-5.png" alt="">
            <p>Fast delivery and excellent quality of books. Highly recommended!</p>
            <div class="stars">
               <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <h3>Bablu Pandit</h3>
         </div>
         <div class="swiper-slide slide">
            <img src="uploaded_img/pic-2.png" alt="">
            <p>I love the user-friendly website. Buying books has never been easier.</p>
            <div class="stars">
               <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <h3>Priya Verma</h3>
         </div>
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script>
var swiper = new Swiper(".reviews-slider", {
   loop:true,
   grabCursor: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: { slidesPerView: 1 },
      700: { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
   },
});
</script>
</body>
</html>
