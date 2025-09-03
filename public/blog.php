<?php
require_once __DIR__ . '/../lib/config.php';
$posts = $pdo->query("SELECT p.*,u.name AS author FROM blog_posts p LEFT JOIN users u ON u.id=p.author_id ORDER BY p.created_at DESC")->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Blog — Moonlight</title>
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
  <h2>Moonlight Blog & Events</h2>
  <?php foreach($posts as $p): ?>
    <div class="card" style="margin-bottom:20px">
      <?php if($p['image_url']): ?>
        <img src="../<?= $p['image_url'] ?>" style="width:100%;max-height:200px;object-fit:cover">
      <?php endif; ?>
      <h3><?= e($p['title']) ?></h3>
      <p class="muted">By <?= e($p['author'] ?? 'Admin') ?> on <?= $p['created_at'] ?></p>
      <p><?= nl2br(e(substr($p['content'],0,200))) ?>...</p>
      <a href="post.php?id=<?= $p['id'] ?>" class="btn">Read More</a>
    </div>
  <?php endforeach; ?>
</div>
</body></html>
