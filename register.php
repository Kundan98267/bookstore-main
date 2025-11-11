<?php
include 'config.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer autoload

if (isset($_POST['submit'])) {
    $name      = mysqli_real_escape_string($conn, $_POST['name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $number    = mysqli_real_escape_string($conn, $_POST['number']);
    $password  = mysqli_real_escape_string($conn, password_hash($_POST['password'], PASSWORD_BCRYPT));
    $cpassword = $_POST['cpassword'];
    $user_type = $_POST['user_type'];

    $select_users = mysqli_query($conn, "SELECT * FROM `register` WHERE email='$email' OR number='$number'") or die(mysqli_error($conn));

    if (mysqli_num_rows($select_users) > 0) {
        echo "<script>alert('Email or Phone number already exists!');</script>";
    } else {
        if ($_POST['password'] !== $cpassword) {
            echo "<script>alert('Confirm password not matched!');</script>";
        } else {
            if ($user_type === 'admin') {
                // ✅ Check if there are already 2 admins
                $check_admins = mysqli_query($conn, "SELECT COUNT(*) AS total_admins FROM `register` WHERE user_type='admin'");
                $admin_data = mysqli_fetch_assoc($check_admins);

                if ($admin_data['total_admins'] >= 2) {
                    echo "<script>alert('Admin limit reached! Only 2 admins are allowed.');</script>";
                } else {
                    $insert = mysqli_query($conn, "INSERT INTO `register`(name,email,number,password,user_type) 
                        VALUES('$name','$email','$number','$password','$user_type')") or die(mysqli_error($conn));

                    if ($insert) {
                        sendWelcomeMail($email, $name);
                        $_SESSION['user_name']  = $name;
                        $_SESSION['user_email'] = $email;
                        header("Location: login.php");
                        exit();
                    } else {
                        echo "<script>alert('Something went wrong while registering!');</script>";
                    }
                }
            } else {
                // ✅ Normal user registration (no limit)
                $insert = mysqli_query($conn, "INSERT INTO `register`(name,email,number,password,user_type) 
                    VALUES('$name','$email','$number','$password','$user_type')") or die(mysqli_error($conn));

                if ($insert) {
                    sendWelcomeMail($email, $name);
                    $_SESSION['user_name']  = $name;
                    $_SESSION['user_email'] = $email;
                    header("Location: login.php");
                    exit();
                } else {
                    echo "<script>alert('Something went wrong while registering!');</script>";
                }
            }
        }
    }
}

// ✅ Email sending function
function sendWelcomeMail($email, $name) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'onlinebookstore2025@gmail.com';
        $mail->Password   = 'tjdl qbih uvza uivx'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Localhost SSL Fix
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('onlinebookstore2025@gmail.com', 'Bookstore');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Welcome to Bookstore, $name!";
        $mail->Body    = "Hello <b>$name</b>,<br><br>
                          Thank you for registering at <b>Bookstore</b>!<br>
                          We are excited to have you with us.<br><br>
                          <b>Happy Reading!</b><br>
                          - Bookstore Team";
        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>Register | Book Store</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
   <link rel="stylesheet" href="style.css" />
   <style>
      .register-section{
         display: flex;
         justify-content: center;
         align-items: center;
         flex-direction: column;
         padding: 60px 20px;
      }
      .register-box{
         width: 100%;
         max-width: 400px;
         border: 1px solid #ddd;
         padding: 30px;
         text-align: center;
         border-radius: 8px;
         background: #fff;
         box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }
      .register-box h2{
         font-size: 22px;
         margin-bottom: 20px;
         font-weight: bold;
      }
      .register-box .inputbox{
         margin-bottom: 15px;
         text-align: left;
      }
      .register-box .inputbox input,
      .register-box .inputbox select{
         width: 100%;
         padding: 10px;
         border: 1px solid #ccc;
         border-radius: 5px;
         outline: none;
      }
      .register-box input[type="submit"] {
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
         transition:0.3s;
      }
      .register-box input[type="submit"]:hover {
         background:#b71c1c;
         color:#fff;
      }
      .links{
         margin-top: 15px;
         font-size: 14px;
      }
      .links a{
         font-weight: bold;
         text-decoration: none;
         margin-left: 5px;
         color: #000;
      }
   </style>
</head>
<body>

   <?php include 'user_header.php'; ?>

   <section class="register-section">
      <div class="register-box">
         <h2>REGISTER NOW</h2>
         <form id="registerForm" action="" method="post" onsubmit="return validateForm();">
            <div class="inputbox">
               <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="inputbox">
               <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="inputbox">
               <input type="number" id="number" name="number" placeholder="Enter your phone number" required>
            </div>
            <div class="inputbox">
               <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="inputbox">
               <input type="password" id="cpassword" name="cpassword" placeholder="Confirm your password" required>
            </div>
            <div class="inputbox">
               <select id="user_type" name="user_type" required>
                  <option value="">Select user type</option>
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
               </select>
            </div>
            <input type="submit" value="Register Now" name="submit" />
            <div class="links">
               Already have an account?
               <a href="login.php">Login now</a>
            </div>
         </form>
      </div>
   </section>

   <?php include 'footer.php'; ?>

   <script>
    function validateForm() {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const number = document.getElementById('number').value.trim();
        const password = document.getElementById('password').value;
        const cpassword = document.getElementById('cpassword').value;
        const user_type = document.getElementById('user_type').value;

        if (!/^[a-zA-Z\s]{3,}$/.test(name)) {
            alert("Name must be at least 3 characters and contain only letters and spaces!");
            return false;
        }
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com)$/.test(email)) {
            alert("Email must be a valid .com address!");
            return false;
        }
        if (!/^\d{10}$/.test(number)) {
            alert("Phone number must be exactly 10 digits!");
            return false;
        }
        if (password !== cpassword) {
            alert("Passwords do not match");
            return false;
        }
        if (user_type === "") {
            alert("Please select user type");
            return false;
        }
        return true;
    }
   </script>

</body>
</html>
