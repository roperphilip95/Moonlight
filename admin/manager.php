<?php
session_start();
require_once "../lib/config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "manager") {
    header("Location: ../public/login.php");
    exit;
}

$message = "";

// Assign waiter
if (isset($_POST["assign_waiter"])) {
    $orderId = $_POST["order_id"];
    $waiterId = $_POST["waiter_id"];
    $stmt = $conn->prepare("UPDATE orders SET waiter_id = ?, status = 'processing' WHERE id = ?");
    $stmt->bind_param("ii", $waiterId, $orderId);
    if ($stmt->execute()) {
        $message = "✅ Order assigned to waiter!";
    }
}

// Update order status
if (isset($_POST["update_status"])) {
    $orderId = $_POST["order_id"];
    $status = $_POST["status"];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    if ($stmt->execute()) {
        $message = "✅ Order status updated!";
    }
}

// Fetch all orders
$orders = $conn->query("SELECT o.*, u.name AS customer_name, w.name AS waiter_name 
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        LEFT JOIN users w ON o.waiter_id = w.id
                        ORDER BY o.created_at DESC");

// Fetch waiters
$waiters = $conn->query("SELECT id, name FROM users WHERE role = 'waiter'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manager Dashboard - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">🌙 Moonlight Manager</div>
    <nav class="nav">
      <a href="manager.php" class="active">Orders</a>
      <a href="../public/logout.php">Logout</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>Manage Orders</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>

    <?php while ($order = $orders->fetch_assoc()): ?>
      <div class="order-card">
        <h3>Order #<?= $order["id"] ?> - <?= ucfirst($order["status"]) ?></h3>
        <p><strong>Customer:</strong> <?= $order["customer_name"] ?></p>
        <p><strong>Total:</strong> $<?= number_format($order["total"], 2) ?></p>
        <p><strong>Date:</strong> <?= $order["created_at"] ?></p>
        <p><strong>Assigned Waiter:</strong> <?= $order["waiter_name"] ?? "Not Assigned" ?></p>

        <!-- Assign Waiter -->
        <form method="post" style="margin-top:10px;">
          <input type="hidden" name="order_id" value="<?= $order["id"] ?>">
          <select name="waiter_id" required>
            <option value="">-- Select Waiter --</option>
            <?php while ($w = $waiters->fetch_assoc()): ?>
              <option value="<?= $w["id"] ?>"><?= $w["name"] ?></option>
            <?php endwhile; ?>
          </select>
          <button type="submit" name="assign_waiter" class="btn neon-btn">Assign</button>
        </form>

        <!-- Update Status -->
        <form method="post" style="margin-top:10px;">
          <input type="hidden" name="order_id" value="<?= $order["id"] ?>">
          <select name="status" required>
            <option value="pending" <?= $order["status"]=="pending"?"selected":"" ?>>Pending</option>
            <option value="processing" <?= $order["status"]=="processing"?"selected":"" ?>>Processing</option>
            <option value="completed" <?= $order["status"]=="completed"?"selected":"" ?>>Completed</option>
            <option value="cancelled" <?= $order["status"]=="cancelled"?"selected":"" ?>>Cancelled</option>
          </select>
          <button type="submit" name="update_status" class="btn">Update</button>
        </form>
      </div>
    <?php endwhile; ?>
  </div>
</body>
</html>