<?php
session_start();
require_once "../lib/helpers.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit;
}

$message = "";

// build menu with today's prices only
$menu = [];
$q = $conn->query("SELECT id, name, category FROM menu_items WHERE is_active=1 ORDER BY category,name");
while ($row = $q->fetch_assoc()) {
  $p = price_today($row['id']);
  if ($p !== null) { // only show items that have a price today
    $menu[] = ['id'=>$row['id'], 'name'=>$row['name'], 'price'=>$p, 'category'=>$row['category']];
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["order"])) {
    $total = 0;
    $items = [];

    foreach ($_POST["qty"] as $index => $qty) {
        if ($qty > 0) {
            $itemName = $menu[$index]["name"];
            $price = $menu[$index]["price"];
            $lineTotal = $price * $qty;
            $total += $lineTotal;
            $items[] = ["name" => $itemName, "price" => $price, "qty" => $qty];
        }
    }

    if ($total > 0) {
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
        $stmt->bind_param("id", $_SESSION["user_id"], $total);
        $stmt->execute();
        $orderId = $stmt->insert_id;

        // Insert order items
        foreach ($items as $item) {
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, quantity) VALUES (?, ?, ?, ?)");
            $stmtItem->bind_param("isdi", $orderId, $item["name"], $item["price"], $item["qty"]);
            $stmtItem->execute();
        }

        $message = "✅ Order placed successfully! Total: $" . number_format($total, 2);
    } else {
        $message = "⚠️ Please select at least one item.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Place Order - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">🌙 Moonlight</div>
    <nav class="nav">
      <a href="index.php">Home</a>
      <a href="orders.php" class="active">Order</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>Place Your Order</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>

    <form method="post">
      <table class="order-table">
        <tr><th>Item</th><th>Price ($)</th><th>Qty</th></tr>
        <?php foreach ($menu as $i => $item): ?>
          <tr>
            <td><?= $item["name"] ?></td>
            <td><?= number_format($item["price"], 2) ?></td>
            <td><input type="number" name="qty[<?= $i ?>]" min="0" value="0"></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <button type="submit" name="order" class="btn neon-btn">Confirm Order</button>
    </form>
  </div>
</body>
</html>