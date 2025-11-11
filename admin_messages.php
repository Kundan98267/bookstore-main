<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `message` WHERE id='$delete_id'");
    $message[] = '1 message has been deleted';
    header("location:admin_messages.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Messages</title>
  <link rel="stylesheet" href="admin.css" />
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Unified animated button style for reply and delete buttons */
    .animated-btn {
      display: inline-block;
      padding: 8px 18px;
      margin-right: 8px;
      color: #fff;
      border: 2px solid transparent;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 16px;
      text-align: center;
      text-decoration: none;
      transition: all 0.3s ease;
      white-space: nowrap;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    /* Reply button styles */
    .reply-btn {
      background-color: #c6bd13ff;
      border-color: #c6bd13ff;
    }

    .reply-btn:hover {
      background-color: #fff;
      color: #c6bd13ff;
      border-color: #c6bd13ff;
      letter-spacing: 1px;
      box-shadow: 0 6px 24px rgba(198,189,19,0.3);
      transform: translateY(-2px) scale(1.04);
    }

    /* Delete button styles */
    .delete-btn {
      background-color: #de4839;
      border-color: #de4839;
    }

    .delete-btn:hover {
      background-color: #fff;
      color: #de4839;
      border-color: #de4839;
      letter-spacing: 1px;
      box-shadow: 0 6px 24px rgba(222,72,57,0.3);
      transform: translateY(-2px) scale(1.04);
    }
  </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="admin_messages">
  <div class="admin_box_container">
    <?php
      $select_msgs = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
      if (mysqli_num_rows($select_msgs) > 0) {
        while ($fetch_msgs = mysqli_fetch_assoc($select_msgs)) {
    ?>
    <div class="admin_box">
      <p>Name : <span><?php echo $fetch_msgs['name']; ?></span></p>
      <p>Number : <span><?php echo $fetch_msgs['number']; ?></span></p>
      <p>Email : <span><?php echo $fetch_msgs['email']; ?></span></p>
      <p>Message : <span><?php echo $fetch_msgs['message']; ?></span></p>
      <a href="reply_message.php?id=<?php echo $fetch_msgs['id']; ?>" class="animated-btn reply-btn">Reply</a>
      <a href="admin_messages.php?delete=<?php echo $fetch_msgs['id']; ?>" onclick="return confirm('Are you sure you want to delete this message?');" class="animated-btn delete-btn">Delete</a>
    </div>
    <?php
        }
      } else {
        echo '<p class="empty">You Have No Messages!</p>';
      }
    ?>
  </div>
</section>

<script src="admin_js.js"></script>
<script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
</body>
</html>
