<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
   header('location:login.php');
   exit;
}

$user_id = (int)$_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM `register` WHERE id = $user_id");
$fetch_profile = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Profile</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="style.css"> <!-- common css -->
   <style>
      .profile-section{
         display: flex;
         justify-content: center;
         align-items: center;
         flex-direction: column;
         padding: 60px 20px;
      }
      .profile-card{
         width: 100%;
         max-width: 400px;
         border: 1px solid #ddd;
         padding: 30px;
         text-align: center;
         border-radius: 8px;
         background: #fff;
         box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }
      .profile-card img{
         width:120px;
         height:120px;
         border-radius:50%;
         margin-bottom:15px;
      }
      .profile-card p{
         margin:10px 0;
         font-size:15px;
         color:#333;
      }
      .profile-card i{ margin-right:8px; color:#555; }
      .profile-card .btn{
         display:block;
         width:100%;
         margin:10px 0;
         padding:12px;
         background:#d32f2f;
         color:#000;
         border:none;
         border-radius:5px;
         font-weight:bold;
         cursor:pointer;
         text-decoration:none;
      }
      .profile-card .btn:hover{ background:#b71c1c; color:#fff; }
   </style>
</head>
<body>

   <?php include 'user_header.php'; ?>

   <section class="profile-section">
      <div class="profile-card">
         <img src="uploaded_img/user-icon.png" alt="User">
         <p><i class="fas fa-user"></i> <?= htmlspecialchars($fetch_profile['name']); ?></p>
          <p><i class="fas fa-phone"></i> <?= htmlspecialchars($fetch_profile['number']); ?></p>
         <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($fetch_profile['email']); ?></p>

         <a href="update_profile.php" class="btn">Update Info</a>

         <p><i class="fas fa-map-marker-alt"></i>
         <?= empty($fetch_profile['address']) ? 'Please enter your address' : htmlspecialchars($fetch_profile['address']); ?>
         </p>
         <a href="update_address.php" class="btn">Update Address</a>
      </div>
   </section>

   <?php include 'footer.php'; ?>
</body>
</html>
