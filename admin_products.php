<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
  header('location:login.php');
  exit();
}

// ✅ Add product
if (isset($_POST['add_products_btn'])) {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $price = $_POST['price'];
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $image = $_FILES['image']['name'];
  $image_size = $_FILES['image']['size'];
  $image_tmp_name = $_FILES['image']['tmp_name'];
  $image_folder = "uploaded_img/" . $image;

  $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name='$name'") or die('query failed');

  if (mysqli_num_rows($select_product_name) > 0) {
    $message[] = 'The given product is already added';
  } else {
    $add_product_query = mysqli_query($conn, "INSERT INTO `products`(name,price,image,category) 
      VALUES ('$name','$price','$image','$category')") or die('query2 failed');

    if ($add_product_query) {
      if ($image_size > 2000000) {
        $message[] = 'Image size is too large';
      } else {
        move_uploaded_file($image_tmp_name, $image_folder);
        $message[] = "Product added successfully!";
      }
    } else {
      $message[] = "Product failed to be added!";
    }
  }
}

// ✅ Delete product
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];

  $delete_img_query = mysqli_query($conn, "SELECT image FROM `products` WHERE id='$delete_id'") or die('query failed');
  $fetch_del_img = mysqli_fetch_assoc($delete_img_query);
  unlink('./uploaded_img/' . $fetch_del_img['image']);

  mysqli_query($conn, "DELETE FROM `products` WHERE id='$delete_id'") or die('query failed');
  header('location:admin_products.php');
}

// ✅ Update product
if (isset($_POST['update_product'])) {
  $update_p_id = $_POST['update_p_id'];
  $update_name = $_POST['update_name'];
  $update_price = $_POST['update_price'];
  $update_category = $_POST['update_category'];

  mysqli_query($conn, "UPDATE `products` SET name='$update_name', price='$update_price', category='$update_category' WHERE id='$update_p_id'") or die('query failed');

  $update_image = $_FILES['update_image']['name'];
  $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
  $update_image_size = $_FILES['update_image']['size'];
  $update_folder = './uploaded_img/' . $update_image;
  $old_image = $_POST['update_old_img'];

  if (!empty($update_image)) {
    if ($update_image_size > 2000000) {
      $message[] = 'Image size is too large';
    } else {
      mysqli_query($conn, "UPDATE `products` SET image='$update_image' WHERE id='$update_p_id'") or die('query failed');
      move_uploaded_file($update_image_tmp_name, $update_folder);
      unlink('./uploaded_img/' . $old_image);
      $message[] = "Product updated successfully!";
    }
  }
  header('location:admin_products.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<!-- ✅ Add Product Form -->
<section class="admin_add_products">
  <form action="" method="post" enctype="multipart/form-data">
    <h3>Add Product</h3>
    <input type="text" name="name" class="admin_input" placeholder="Enter Product Name" required>
    <input type="number" min="0" name="price" class="admin_input" placeholder="Enter Product Price" required>

    <!-- Category Select -->
    <select name="category" class="admin_input" required>
      <option value="General">General</option>
      <option value="Fiction">Fiction</option>
      <option value="Non Fiction">Non Fiction</option>
      <option value="Children">Children</option>
      <option value="Textbooks">Textbooks</option>
    </select>

    <input type="file" name="image" class="admin_input" accept="image/jpg, image/jpeg, image/png" required>
    <input type="submit" name="add_products_btn" class="admin_input" value="Add Product">
  </form>
</section>

<!-- ✅ Show Products -->
<section class="show_products">
  <div class="product_box_cont">
    <?php
    $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
    if (mysqli_num_rows($select_products) > 0) {
      while ($fetch_products = mysqli_fetch_assoc($select_products)) {
    ?>
    <div class="product_box">
      <img src="./uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
      <div class="product_name"><?php echo $fetch_products['name']; ?></div>
      <div class="product_price">Rs. <?php echo $fetch_products['price']; ?> /-</div>
      <div class="product_cat">Category: <b><?php echo $fetch_products['category']; ?></b></div>

      <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="product_btn">Update</a>
      <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="product_btn product_del_btn" onclick="return confirm('Are you sure you want to delete this product ?');">Delete</a>
    </div>
    <?php
      }
    } else {
      echo '<p class="empty">No Product added yet!</p>';
    }
    ?>
  </div>
</section>

<!-- ✅ Edit Product Form -->
<section class="edit_product_form">
  <?php
  if (isset($_GET['update'])) {
    $update_id = $_GET['update'];
    $update_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id='$update_id'") or die('query failed');
    if (mysqli_num_rows($update_query) > 0) {
      while ($fetch_update = mysqli_fetch_assoc($update_query)) {
  ?>
  <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
    <input type="hidden" name="update_old_img" value="<?php echo $fetch_update['image']; ?>">

    <img src="./uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">

    <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="admin_input update_box" required placeholder="Enter Product Name">
    <input type="number" name="update_price" min="0" value="<?php echo $fetch_update['price']; ?>" class="admin_input update_box" required placeholder="Enter Product Price">

    <!-- ✅ Update Category -->
    <select name="update_category" class="admin_input update_box" required>
      <option value="General" <?php if ($fetch_update['category'] == "General") echo "selected"; ?>>General</option>
      <option value="Fiction" <?php if ($fetch_update['category'] == "Fiction") echo "selected"; ?>>Fiction</option>
      <option value="Non Fiction" <?php if ($fetch_update['category'] == "Non Fiction") echo "selected"; ?>>Non Fiction</option>
      <option value="Children" <?php if ($fetch_update['category'] == "Children") echo "selected"; ?>>Children</option>
      <option value="Textbooks" <?php if ($fetch_update['category'] == "Textbooks") echo "selected"; ?>>Textbooks</option>
    </select>

    <input type="file" name="update_image" class="admin_input update_box" accept="image/jpg, image/jpeg, image/png">
    <input type="submit" value="update" name="update_product" class="product_btn">
    <input type="reset" value="cancel" id="close_update" class="product_btn product_del_btn">
  </form>
  <?php
      }
    }
  } else {
    echo "<script>document.querySelector('.edit_product_form').style.display='none';</script>";
  }
  ?>
</section>

<script src="admin_js.js"></script>
<script src="https://kit.fontawesome.com/eedbcd0c96.js" crossorigin="anonymous"></script>
</body>
</html>
