<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])) { header("Location: ../public/login.php"); exit; }

$msg='';

// Handle new blog post
if (isset($_POST['add_post'])) {
  $title = trim($_POST['title']);
  $content = trim($_POST['content']);
  $author_id = $_SESSION['user_id'];
  $imgPath = null;

  if (!empty($_FILES['image']['name'])) {
    $target_dir = "../assets/blog/";
    if (!is_dir($target_dir)) mkdir($target_dir,0777,true);
    $filename = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $filename;
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
      $imgPath = "assets/blog/" . $filename;
    }
  }

  $st = $pdo->prepare("INSERT INTO blog_posts (title,content,image_url,author_id) VALUES (?,?,?,?)");
  $st->execute([$title,$content,$imgPath,$author_id]);
  $msg = "✅ Blog post added!";
}

// Fetch posts
$posts = $pdo->query("SELECT p.*,u.name AS author FROM blog_posts p LEFT JOIN users u ON u.id=p.author_id ORDER BY p.created_at DESC")->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Blog Manager</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight Admin</div>
  <nav class="nav">
    <a href="index.php">Dashboard</a>
    <a href="menu.php">Menu</a>
    <a href="blog.php">Blog</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<div class="container" style="padding:22px">
  <h2>Blog Manager</h2>
  <?php if($msg): ?><div class="card" style="background:#1a4220"><?= $msg ?></div><?php endif; ?>

  <div class="card">
    <h3>Add Blog Post</h3>
    <form method="post" enctype="multipart/form-data">
      <label>Title<input type="text" name="title" required></label>
      <label>Content<textarea name="content" required></textarea></label>
      <label>Image<input type="file" name="image"></label>
      <p><button class="btn" name="add_post">Publish</button></p>
    </form>
  </div>

  <div class="card" style="margin-top:20px">
    <h3>All Posts</h3>
    <table class="table">
      <tr><th>Title</th><th>Author</th><th>Date</th></tr>
      <?php foreach($posts as $p): ?>
        <tr>
          <td><?= e($p['title']) ?></td>
          <td><?= e($p['author'] ?? 'Unknown') ?></td>
          <td><?= $p['created_at'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
