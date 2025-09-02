<?php
session_start();
require_once "../lib/config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "waiter") {
    header("Location: ../public/login.php");
    exit;
}

$waiterId = $_SESSION["user_id"];
$message = "";

// Update status to completed
if (isset($_POST["complete_order"])) {
    $orderId = $_POST["order_id"];
    $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND waiter_id = ?");
    $stmt->bind_param("ii", $orderId, $waiterId);
    if ($stmt->execute()) {
        $message = "✅ Order marked as completed!";
    }
}

// Fetch orders assigned to this waiter
$stmt = $conn->prepare("SELECT o.*, u.name AS customer_name
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        WHERE o.waiter_id = ?
                        ORDER BY o.created_at DESC");
$stmt->bind_param("i", $waiterId);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Waiter Dashboard - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">🌙 Moonlight Waiter</div>
    <nav class="nav">
      <a href="waiter.php" class="active">My Orders</a>
      <a href="../public/logout.php">Logout</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>Orders Assigned to Me</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>

    <?php if ($orders->num_rows == 0): ?>
      <p class="msg">No orders assigned yet.</p>
    <?php else: ?>
      <?php while ($order = $orders->fetch_assoc()): ?>
        <div class="order-card">
          <h3>Order #<?= $order["id"] ?> - <?= ucfirst($order["status"]) ?></h3>
          <p><strong>Customer:</strong> <?= $order["customer_name"] ?></p>
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

          <?php if ($order["status"] != "completed"): ?>
            <form method="post" style="margin-top:10px;">
              <input type="hidden" name="order_id" value="<?= $order["id"] ?>">
              <button type="submit" name="complete_order" class="btn neon-btn">Mark as Completed</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</body>
</html>