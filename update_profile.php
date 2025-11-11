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

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    $messages = [];

    // Update Name
    if (!empty($name)) {
        mysqli_query($conn, "UPDATE `register` SET name='$name' WHERE id='$user_id'") or die(mysqli_error($conn));
    }

    // Update Email
    if (!empty($email)) {
        $check_email = mysqli_query($conn, "SELECT * FROM `register` WHERE email='$email' AND id!='$user_id'");
        if (mysqli_num_rows($check_email) > 0) {
            $messages[] = 'Email already taken!';
        } else {
            mysqli_query($conn, "UPDATE `register` SET email='$email' WHERE id='$user_id'");
        }
    }

    // Update Number
    if (!empty($number)) {
        $check_number = mysqli_query($conn, "SELECT * FROM `register` WHERE number='$number' AND id!='$user_id'");
        if (mysqli_num_rows($check_number) > 0) {
            $messages[] = 'Number already taken!';
        } else {
            mysqli_query($conn, "UPDATE `register` SET number='$number' WHERE id='$user_id'");
        }
    }

    // Update Password
    if (!empty($old_pass) && !empty($new_pass) && !empty($confirm_pass)) {
        $admin_res = mysqli_query($conn, "SELECT * FROM `register` WHERE id='$user_id'");
        $admin_data = mysqli_fetch_assoc($admin_res);
        if (!password_verify($old_pass, $admin_data['password'])) {
            $messages[] = 'Old password not matched!';
        } elseif ($new_pass !== $confirm_pass) {
            $messages[] = 'Confirm password not matched!';
        } else {
            $hashed_pass = password_hash($confirm_pass, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE `register` SET password='$hashed_pass' WHERE id='$user_id'");
            $messages[] = 'Password updated successfully!';
        }
    } else {
        if (empty($messages)) {
            $messages[] = 'Profile updated successfully!';
        }
    }

    $_SESSION['messages'] = $messages;
    header('Location: home.php'); // Redirect to refresh data and avoid resubmission
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Update Profile|BookStore </title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="style.css" />
<style>
    /* Your existing styles for form, button, etc. */
    .update-section {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 60px 20px;
    }
    .update-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow:0 4px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    .update-card h3 {
        margin-bottom:20px;
        font-size:22px;
        font-weight:bold;
    }
    .update-card .box {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
    }
    /* Style for "Update Now" button similar to login button */
    .update-btn {
        width: 100%;
        padding: 12px;
        background:#c02736ff;
        color:#fff;
        border:none;
        border-radius:6px;
        cursor:pointer;
        font-weight:600;
        transition: all 0.3s ease;
    }
    .update-btn:hover {
        background:#fff;
        color:#c02736ff;
        border: 2px solid #c02736ff;
    }
    /* Message box styles with close button and fade out animation */
    .message {
        position: relative;
        max-width: 400px;
        margin:15px auto;
        padding:12px 45px 12px 12px;
        border:1px solid red;
        background:#ffe5e5;
        color:red;
        border-radius:5px;
        font-weight:600;
        font-size:14px;
    }
    .message .close-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: bold;
        font-size: 16px;
        color:red;
        cursor:pointer;
    }
</style>
</head>
<body>

<?php include 'user_header.php'; ?>

<section class="update-section">
<div class="update-card">
<h3>Update Profile</h3>

<!-- Show messages -->
<?php
if (isset($_SESSION['messages'])) {
    foreach ($_SESSION['messages'] as $msg) {
        echo '<div class="message">
                '.$msg.'
                <span class="close-btn" onclick="this.parentElement.style.display=\'none\'">&times;</span>
              </div>';
    }
    unset($_SESSION['messages']);
}
?>

<form action="" method="post" onsubmit="return validateForm();">
<input type="text" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($fetch_profile['name']) ?>" class="box" required>
<input type="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($fetch_profile['email']) ?>" class="box" required>
<input type="number" name="number" placeholder="Enter your number" value="<?= htmlspecialchars($fetch_profile['number']) ?>" class="box" required>
<input type="password" name="old_pass" placeholder="Enter old password" class="box" required>
<input type="password" name="new_pass" placeholder="Enter new password" class="box" required>
<input type="password" name="confirm_pass" placeholder="Confirm new password" class="box" required>
<button type="submit" class="update-btn" name="submit">Update Now</button>
</form>
</div>
</section>

<script>
  function validateForm() {
    // Validation code similar to previous instructions:
    const name = document.querySelector('input[name="name"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const number = document.querySelector('input[name="number"]').value.trim();
    const oldPass = document.querySelector('input[name="old_pass"]').value;
    const newPass = document.querySelector('input[name="new_pass"]').value;
    const confirmPass = document.querySelector('input[name="confirm_pass"]').value;
    
    if (!/^[a-zA-Z\s]{3,}$/.test(name)) { alert("Name must contain only alphabets and be at least 3 characters!"); return false; }
    if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com)$/.test(email)) { alert("Enter a valid .com email!"); return false; }
    if (!/^\d{10}$/.test(number)) { alert("Number must be 10 digits!"); return false; }
    if (newPass !== confirmPass) { alert("Passwords do not match!"); return false; }
    return true;
  }

  // Optional: auto hide messages
  setTimeout(()=> {
    document.querySelectorAll('.message').forEach(m => m.style.display='none');
  }, 5000);
</script>

  <?php include 'footer.php'; ?>


</body>
</html>
