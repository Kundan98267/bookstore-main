<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $select_users = mysqli_query($conn, "SELECT * FROM `register` WHERE email='$email' LIMIT 1") or die('query failed');

    if (mysqli_num_rows($select_users) > 0) {
        $row = mysqli_fetch_assoc($select_users);

        if (password_verify($password, $row['password'])) {
            if ($row['user_type'] == 'admin') {
                $_SESSION['admin_name']  = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_id']    = $row['id'];
                header('location:admin_page.php');
                exit();
            } elseif ($row['user_type'] == 'user') {
                $_SESSION['user_name']  = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_id']    = $row['id'];
                header('location:home.php');
                exit();
            }
        } else {
            $message[] = 'Incorrect password!';
        }
    } else {
        $message[] = 'No account found with this email!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="style.css"> <!-- common css -->
   <style>
      .login-section{
         display: flex;
         justify-content: center;
         align-items: center;
         flex-direction: column;
         padding: 60px 20px;
      }
      .login-box{
         width: 100%;
         max-width: 400px;
         border: 1px solid #ddd;
         padding: 30px;
         text-align: center;
         border-radius: 8px;
         background: #fff;
         box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }
      .login-box h2{
         font-size: 22px;
         margin-bottom: 20px;
         font-weight: bold;
      }
      .login-box .inputbox{
         margin-bottom: 15px;
         text-align: left;
      }
      .login-box .inputbox input{
         width: 100%;
         padding: 10px;
         border: 1px solid #ccc;
         border-radius: 5px;
         outline: none;
      }
      .login-box .links{
         display: flex;
         justify-content: space-between;
         font-size: 14px;
         margin-bottom: 20px;
      }
      /* Button style */
      .product_btn {
         width: 100%;
         padding: 12px;
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
      /* Message style */
      .message {
         position: relative;
         max-width: 400px;
         margin: 15px auto;
         padding: 15px 45px 15px 15px;
         border-radius: 5px;
         border: 1px solid #f44336;
         background-color: #ffcdd2;
         color: #b71c1c;
         font-weight: bold;
         text-align: left;
      }
      .message .close-btn {
         position: absolute;
         right: 10px;
         top: 50%;
         transform: translateY(-50%);
         color: #b71c1c;
         font-size: 20px;
         font-weight: bold;
         cursor: pointer;
         user-select: none;
      }
   </style>
</head>
<body>
   <?php include 'user_header.php'; ?>

   <section class="login-section">
      <?php
      if (isset($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">
                     '.$msg.'
                     <span class="close-btn" onclick="this.parentElement.style.display=\'none\'">&times;</span>
                  </div>';
         }
      }
      ?>
      <div class="login-box">
         <h2>LOGIN NOW</h2>
         <form action="" method="post">
            <div class="inputbox">
               <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="inputbox">
               <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="links">
               <a href="register.php">Create account</a>
               <a href="forgot_password.php">Forgot password?</a>
            </div>
            <input type="submit" value="Login" name="submit" class="product_btn" />
         </form>
      </div>
   </section>

   <?php include 'footer.php'; ?>

   <script>
      // Auto-hide messages after 5 seconds
      window.addEventListener('DOMContentLoaded', function() {
         setTimeout(() => {
            document.querySelectorAll('.message').forEach(msg => {
               msg.style.transition = 'opacity 0.5s';
               msg.style.opacity = '0';
               setTimeout(() => msg.style.display = 'none', 500);
            });
         }, 5000);
      });
   </script>
</body>
</html>
