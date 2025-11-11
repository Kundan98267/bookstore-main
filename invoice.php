<?php
include 'config.php';

if(!isset($_GET['order_id'])) {
    die("Invalid Order");
}

$order_id = intval($_GET['order_id']);
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if(!$order) {
    die("Order not found!");
}

$placed_on_ts = strtotime($order['placed_on']);
$formatted_date = ($placed_on_ts && $placed_on_ts > 0) ? date("d M Y, h:i A", $placed_on_ts) : "Unknown";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice - Bookstore</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background: #f8f9fa; }
    .invoice-container {
        max-width: 800px; margin: auto; background: #fff;
        padding: 30px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }
    .invoice-header {
        text-align: center; border-bottom: 2px solid #eee;
        margin-bottom: 20px; padding-bottom: 15px;
    }
    .invoice-header img { height: 60px; margin-bottom: 10px; }
    .thank-box {
        background: #d4edda; border: 1px solid #c3e6cb;
        color: #155724; padding: 15px; border-radius: 8px;
        font-weight: bold; margin-bottom: 20px; text-align: center; font-size: 18px;
    }
    h5 { border-bottom: 2px solid #eee; padding-bottom: 6px; margin-top: 25px; color: #34495e; }
    .btn-print { background: #007bff; color: #fff; border-radius: 8px; padding: 10px 20px; }
    .btn-home { background: #28a745; color: #fff; border-radius: 8px; padding: 10px 20px; }
    /* ✅ Hide buttons when printing */
    @media print {
        .no-print { display: none !important; }
    }
</style>
</head>
<body>

<div class="invoice-container">
    <div class="invoice-header">
        <img src="book_logo.png" alt="Bookstore Logo"> 
        <h2>Bookstore Invoice</h2>
        <p>Order #<?= $order['id'] ?> | Printed on <span id="printTime"></span></p>
    </div>

    <div class="thank-box">
        🎉 Thanks for choosing <strong>Bookstore</strong> 📚 
    </div>

    <h5>Customer Details</h5>
    <p><strong>Name:</strong> <?= htmlspecialchars($order['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($order['number']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>

    <h5>Order Summary</h5>
    <p><strong>Products:</strong> <?= htmlspecialchars($order['total_products']) ?></p>
    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['method']) ?></p>
    <p><strong>Total Price:</strong> ₹<?= htmlspecialchars($order['total_price']) ?></p>
    <p><strong>Order Placed On:</strong> <?= $formatted_date ?></p>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-print me-2">🖨 Print Invoice</button>
        <a href="home.php" class="btn btn-home">🏠 Back to Home</a>
    </div>
</div>

<script>
    // ✅ Show real-time print time
    document.getElementById("printTime").innerText = new Date().toLocaleString();

    // ✅ 30 sec redirect
    setTimeout(function() {
        window.location.href = "home.php";
    }, 30000);
</script>

</body>
</html>
