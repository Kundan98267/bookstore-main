<?php
include 'config.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if(!$user_id){
   header('location:login.php');
}

if(isset($_POST['send'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $number = $_POST['number'];
   $msg = mysqli_real_escape_string($conn, $_POST['message']);

   $select_message = mysqli_query($conn, "SELECT * FROM `message` WHERE name='$name' AND email='$email' AND number='$number' AND message='$msg'") or die('query failed');

   if(mysqli_num_rows($select_message) > 0){
      $message[] = 'Message already sent!';
   }else{
      mysqli_query($conn, "INSERT INTO `message`(user_id, name, email, number, message) VALUES('$user_id','$name','$email','$number','$msg')") or die('query failed');
      $message[] = 'Message sent successfully!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contact Us | Book Store</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="style.css">
   <style>
      /* .heading {
         background: #222;
         padding: 30px 20px;
         text-align: center;
         color: #fff;
      }
      .heading h3 {
         font-size: 32px;
         margin-bottom: 8px;
      }
      .heading p {
         font-size: 16px;
         color: #ddd;
      }
      .heading p a {
         color: #c02736;
         text-decoration: none;
      } */

      .contact {
         display: flex;
         align-items: center;
         justify-content: center;
         flex-wrap: wrap;
         padding: 50px 20px;
         max-width: 1200px;
         margin: auto;
         gap: 40px;
      }
      .contact .image {
         flex: 1 1 45%;
         text-align: center;
      }
      .contact .image img {
         max-width: 100%;
      }
      .contact form {
         flex: 1 1 45%;
         background: #fff;
         padding: 25px;
         border: 1px solid #ddd;
         border-radius: 8px;
         box-shadow: 0px 6px 12px rgba(0,0,0,0.1);
      }
      .contact form h3 {
         text-align: center;
         margin-bottom: 15px;
         font-size: 22px;
         font-weight: bold;
         color: #333;
      }
      .contact form .box {
         width: 100%;
         padding: 12px;
         border: 1px solid #ccc;
         border-radius: 6px;
         margin: 10px 0;
         font-size: 16px;
      }
      .contact form textarea {
         resize: none;
         height: 120px;
      }
      .contact form .btn {
         display: block;
         width: 100%;
         padding: 12px;
         background: #c02736;
         color: #fff;
         border: none;
         font-weight: bold;
         border-radius: 6px;
         cursor: pointer;
         transition: 0.3s;
      }
      .contact form .btn:hover {
         background: #fff;
         color: #c02736;
         border: 2px solid #c02736;
      }
   </style>
</head>
<body>

<?php include 'user_header.php'; ?>

<!-- <div class="heading">
   <h3>Contact Us</h3>
   <p><a href="home.php">Home</a> / Contact</p>
</div> -->

<section class="contact">

   <div class="image">
      <img src="uploaded_img/contact-img.svg" alt="Contact Illustration">
   </div>

   <form action="" method="post">
      <h3>Tell Us Something!</h3>
      <input type="text" name="name" maxlength="50" class="box" placeholder="Enter your name" required>
      <input type="text" name="number" maxlength="10" class="box" placeholder="Enter your number" required>
      <input type="email" name="email" maxlength="50" class="box" placeholder="Enter your email" required>
      <textarea name="message" class="box" required placeholder="Enter your message" maxlength="500"></textarea>
      <input type="submit" value="Send Message" name="send" class="btn">
   </form>

</section>

<?php include 'footer.php'; ?>

<script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
</body>
</html>
