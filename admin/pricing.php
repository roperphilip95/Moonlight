<?php
require_once "../lib/config.php";
require_once "../lib/auth.php";
require_role(['admin','manager']); // managers and admin can set prices
require_once "../lib/helpers.php";

$msg = "";

/* Add menu item */
if (isset($_POST['add_item'])) {
  $name = trim($_POST['name']);
  $cat  = $_POST['category'];
  $stmt = $conn->prepare("INSERT INTO menu_items (name, category) VALUES (?,?)");
  $stmt->bind_param("ss", $name, $cat);
  if ($stmt->execute()) { $msg = "✅ Item added"; audit('MENU_ITEM_ADD', $name); }
}

/* Set/Update today's price */
if (isset($_POST['set_price'])) {
  $item_id = (int)$_POST['item_id'];
  $price   = (float)$_POST['price'];
  $date    = date('Y-m-d');
  $stmt = $conn->prepare("INSERT INTO item_prices (item_id, price, effective_date)
      VALUES (?,?,?) ON DUPLICATE KEY UPDATE price=VALUES(price)");
  $stmt->bind_param("ids", $item_id, $price, $date);
  if ($stmt->execute()) { $msg = "✅ Today's price updated"; audit('PRICE_SET', json_encode(['item'=>$item_id,'price'=>$price])); }
}

/* Data */
$items = $conn->query("SELECT * FROM menu_items WHERE is_active=1 ORDER BY category, name");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Pricing - Moonlight</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header class="site-header">
  <div class="logo">🌙 Pricing</div>
  <nav class="nav">
    <a href="dashboard.php">Admin</a>
    <a class="active" href="pricing.php">Prices</a>
    <a href="../public/logout.php">Logout</a>
  </nav>
</header>

<div class="form-container">
  <h2>Daily Price List</h2>
  <?php if ($msg) echo "<p class='msg'>$msg</p>"; ?>

  <h3>Add Menu Item</h3>
  <form method="post">
    <input type="text" name="name" placeholder="Item name" required>
    <select name="category" required>
      <option value="drink">Drink</option>
      <option value="food">Food</option>
      <option value="hookah">Hookah</option>
      <option value="other">Other</option>
    </select>
    <button class="btn" name="add_item" type="submit">Add</button>
  </form>

  <h3>Set Today’s Price</h3>
  <form method="post">
    <select name="item_id" required>
      <option value="">-- Item --</option>
      <?php while($i = $items->fetch_assoc()): ?>
        <option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['name']) ?> (<?= $i['category'] ?>)</option>
      <?php endwhile; ?>
    </select>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <button class="btn neon-btn" name="set_price" type="submit">Save Price</button>
  </form>
</div>
</body>
</html>