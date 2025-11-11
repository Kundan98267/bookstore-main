<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM `register` WHERE id = $user_id");
$fetch_user = mysqli_fetch_assoc($result);

$messages = [];

if (isset($_POST['submit'])) {
    $flat = mysqli_real_escape_string($conn, $_POST['flat']);
    $building = mysqli_real_escape_string($conn, $_POST['building']);
    $area = mysqli_real_escape_string($conn, $_POST['area']);
    $town = mysqli_real_escape_string($conn, $_POST['town']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $pin_code = mysqli_real_escape_string($conn, $_POST['pin_code']);

    // Basic validation
    if (!preg_match('/^\d+$/', $flat) || !preg_match('/^\d+$/', $building)) {
        $messages[] = 'Flat and building number must be digits only!';
    } elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', $area)) {
        $messages[] = 'Area name should contain only letters and numbers!';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $town) || !preg_match('/^[a-zA-Z\s]+$/', $city) ||
              !preg_match('/^[a-zA-Z\s]+$/', $state) || !preg_match('/^[a-zA-Z\s]+$/', $country)) {
        $messages[] = 'Town, city, state, and country should contain only letters!';
    } elseif (!preg_match('/^\d{6}$/', $pin_code)) {
        $messages[] = 'Pin code must be exactly 6 digits!';
    } else {
        $address = "$flat, $building, $area, $town, $city, $state, $country - $pin_code";
        mysqli_query($conn, "UPDATE `register` SET address='$address' WHERE id='$user_id'") or die(mysqli_error($conn));
        $messages[] = '✅ Address updated successfully!';
    }

    $_SESSION['messages'] = $messages;
    header("Location: update_address.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Update Address | BookStore</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="style.css">
<style>
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
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    text-align: center;
}
.update-card h3 {
    margin-bottom: 20px;
    font-size: 22px;
    font-weight: bold;
}
.update-card .box {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.update-btn {
    width: 100%;
    padding: 12px;
    background: #ffc107;
    color: #000;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}
.update-btn:hover {
    background: #e0a800;
}
.message {
    position: relative;
    max-width: 400px;
    margin: 15px auto;
    padding: 12px 45px 12px 12px;
    border: 1px solid #28a745;
    background: #d4edda;
    color: #155724;
    border-radius: 5px;
    font-weight: 600;
    font-size: 14px;
}
.message .close-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-weight: bold;
    font-size: 16px;
    color: #155724;
    cursor: pointer;
}
</style>
</head>
<body>

<?php include 'user_header.php'; ?>

<section class="update-section">
<div class="update-card">
<h3>Update Address</h3>

<!-- ✅ Message Section -->
<?php
if (isset($_SESSION['messages'])) {
    foreach ($_SESSION['messages'] as $msg) {
        echo '<div class="message">'.$msg.'
              <span class="close-btn" onclick="this.parentElement.style.display=\'none\'">&times;</span>
              </div>';
    }
    unset($_SESSION['messages']);
}
?>

<form action="" method="post" onsubmit="return validateForm();">
    <input type="text" name="flat" placeholder="Flat no." class="box" required pattern="\d+" title="Only digits allowed">
    <input type="text" name="building" placeholder="Building no." class="box" required pattern="\d+" title="Only digits allowed">
    <input type="text" name="area" placeholder="Area name" class="box" required pattern="[A-Za-z0-9\s]+" title="Letters and digits allowed">
    <input type="text" name="town" placeholder="Town name" class="box" required pattern="[A-Za-z\s]+" title="Only letters allowed">
    <input type="text" name="city" placeholder="City name" class="box" required pattern="[A-Za-z\s]+" title="Only letters allowed">
    <input type="text" name="state" placeholder="State name" class="box" required pattern="[A-Za-z\s]+" title="Only letters allowed">
    <input type="text" name="country" placeholder="Country name" class="box" required pattern="[A-Za-z\s]+" title="Only letters allowed">
    <input type="text" name="pin_code" placeholder="Pin code" class="box" required pattern="\d{6}" maxlength="6" title="Pin code must be exactly 6 digits">
    <button type="submit" name="submit" class="update-btn">Update Address</button>
</form>
</div>
</section>

<script>
function validateForm() {
    const flat = document.querySelector('[name="flat"]').value.trim();
    const building = document.querySelector('[name="building"]').value.trim();
    const pinCode = document.querySelector('[name="pin_code"]').value.trim();

    if (!/^\d+$/.test(flat) || !/^\d+$/.test(building)) {
        alert("Flat and building numbers must be digits only!");
        return false;
    }
    if (!/^\d{6}$/.test(pinCode)) {
        alert("Pin code must be exactly 6 digits!");
        return false;
    }
    return true;
}

setTimeout(() => {
    document.querySelectorAll('.message').forEach(m => m.style.display='none');
}, 4000);
</script>

<?php include 'footer.php'; ?>
</body>
</html>
