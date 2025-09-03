<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])) { header("Location: ../public/login.php"); exit; }

$msg='';

// Add new menu item
if (isset($_POST['add_item'])) {
  $name = trim($_POST['name']);
  $desc = trim($_POST['description']);
  $price = $_POST['price'];
  $cat = $_POST['category_id'];
  $imgPath = null;

  if (!empty($_FILES['image']['name'])) {
    $target_dir = "../assets/menu/";
    if (!is_dir($target_dir)) mkdir($target_dir,0777,true);
    $filename = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $filename;
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
      $imgPath = "assets/menu/" . $filename;
    }
  }

  $st = $pdo->prepare("INSERT INTO menu_items (category_id,name,description,price,image_url) VALUES (?,?,?,?,?)");
  $st->execute([$cat,$name,$desc,$price,$imgPath]);
  $msg = "✅ Item added!";
}

// Fetch categories & items
$cats = $pdo->query("SELECT * FROM menu_categories ORDER BY name ASC")->fetchAll();
$items = $pdo->query("SELECT m.*,c.name AS cat FROM menu_items m LEFT JOIN menu_categories c ON c.id=m.category_id ORDER BY m.created_at DESC")->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Menu Manager</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight Admin</div>
  <nav class="nav">
    <a href="index.php">Dashboard</a>
    <a href="menu.php">Menu</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<div class="container" style="padding:22px">
  <h2>Menu Manager</h2>
  <?php if($msg): ?><div class="card" style="background:#1a4220"><?= $msg ?></div><?php endif; ?>

  <div class="card">
    <h3>Add Menu Item</h3>
    <form method="post" enctype="multipart/form-data">
      <label>Name<input type="text" name="name" required></label>
      <label>Description<textarea name="description"></textarea></label>
      <label>Price<input type="number" step="0.01" name="price" required></label>
      <label>Category
        <select name="category_id" required>
          <?php foreach($cats as $c): ?>
            <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Image<input type="file" name="image"></label>
      <p><button class="btn" name="add_item">Add Item</button></p>
    </form>
  </div>

  <div class="card" style="margin-top:20px">
    <h3>Menu Items</h3>
    <table class="table">
      <tr><th>Name</th><th>Category</th><th>Price</th><th>Image</th></tr>
      <?php foreach($items as $i): ?>
        <tr>
          <td><?= e($i['name']) ?></td>
          <td><?= e($i['cat']) ?></td>
          <td>$<?= number_format($i['price'],2) ?></td>
          <td><?php if($i['image_url']): ?><img src="../<?= $i['image_url'] ?>" width="60"><?php endif; ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
