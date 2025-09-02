<?php
session_start();
require_once "../lib/config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];

// Fetch user orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">🌙 Moonlight</div>
    <nav class="nav">
      <a href="index.php">Home</a>
      <a href="orders.php">Order</a>
      <a href="my_orders.php" class="active">My Orders</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>My Orders</h2>
    <?php if ($orders->num_rows == 0): ?>
      <p class="msg">You have not placed any orders yet.</p>
    <?php else: ?>
      <?php while ($order = $orders->fetch_assoc()): ?>
        <div class="order-card">
          <h3>Order #<?= $order["id"] ?> - <?= ucfirst($order["status"]) ?></h3>
          <p><strong>Total:</strong> $<?= number_format($order["total"], 2) ?></p>
          <p><strong>Date:</strong> <?= $order["created_at"] ?></p>
          
          <table class="order-table">
            <tr><th>Item</th><th>Qty</th><th>Price</th></tr>
            <?php
              $stmtItems = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
              $stmtItems->bind_param("i", $order["id"]);
              $stmtItems->execute();
              $items = $stmtItems->get_result();
              while ($item = $items->fetch_assoc()):
            ?>
              <tr>
                <td><?= $item["item_name"] ?></td>
                <td><?= $item["quantity"] ?></td>
                <td>$<?= number_format($item["price"] * $item["quantity"], 2) ?></td>
              </tr>
            <?php endwhile; ?>
          </table>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</body>
</html>