<?php
require_once __DIR__ . '/../lib/config.php';
$id = $_GET['id'] ?? 0;
$st = $pdo->prepare("SELECT p.*,u.name AS author FROM blog_posts p LEFT JOIN users u ON u.id=p.author_id WHERE p.id=?");
$st->execute([$id]);
$p = $st->fetch();
if(!$p) die("Post not found");
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title><?= e($p['title']) ?> — Moonlight</title>
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
  <div class="card">
    <?php if($p['image_url']): ?>
      <img src="../<?= $p['image_url'] ?>" style="width:100%;max-height:250px;object-fit:cover">
    <?php endif; ?>
    <h2><?= e($p['title']) ?></h2>
    <p class="muted">By <?= e($p['author'] ?? 'Admin') ?> on <?= $p['created_at'] ?></p>
    <p><?= nl2br(e($p['content'])) ?></p>
  </div>
</div>
</body></html>
