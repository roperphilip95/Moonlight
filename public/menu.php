<?php
require_once __DIR__ . '/../lib/config.php';
$cats = $pdo->query("SELECT * FROM menu_categories")->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Menu — Moonlight</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight</div>
  <nav class="nav">
    <a href="index.php">Home</a>
    <a href="menu.php">Menu</a>
    <a href="blog.php">Blog</a>
    <a href="contact.php">Contact</a>
    <a href="login.php">Login</a>
  </nav>
</header>

<div class="container" style="padding:22px">
  <h2>Our Menu</h2>
  <?php foreach($cats as $c): 
    $items = $pdo->prepare("SELECT * FROM menu_items WHERE category_id=? ORDER BY created_at DESC");
    $items->execute([$c['id']]);
    $list = $items->fetchAll();
    if (!$list) continue;
  ?>
  <h3><?= e($c['name']) ?></h3>
  <div style="display:flex;flex-wrap:wrap;gap:20px">
    <?php foreach($list as $i): ?>
      <div class="card" style="width:200px">
        <?php if($i['image_url']): ?><img src="../<?= $i['image_url'] ?>" style="width:100%;height:120px;object-fit:cover"><?php endif; ?>
        <h4><?= e($i['name']) ?></h4>
        <p class="muted"><?= e($i['description']) ?></p>
        <p><b>$<?= number_format($i['price'],2) ?></b></p>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endforeach; ?>
</div>
</body></html>
