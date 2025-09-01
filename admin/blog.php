<?php
require_once __DIR__ . '/_header.php';
require_role(['Admin','Manager']);

$pdo = db();

if(isset($_POST['add_post'])){
  $title = trim($_POST['title']);
  $slug  = strtolower(preg_replace('~[^a-z0-9]+~','-', $title)).'-'.substr(sha1(microtime()),0,6);
  $content = trim($_POST['content']);
  $pub = isset($_POST['publish']) ? date('Y-m-d H:i:s') : null;
  $st=$pdo->prepare("INSERT INTO blog_posts(title,slug,content,author_id,published_at) VALUES (?,?,?,?,?)");
  $st->execute([$title,$slug,$content,$_SESSION['uid'],$pub]);
  flash("Post saved."); header("Location: blog.php"); exit;
}

$posts = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 20")->fetchAll();
?>
<h2>Blog / Events</h2>
<div class="card">
  <h3>New Post</h3>
  <form method="post">
    <label>Title<input name="title" required></label>
    <label>Content<textarea name="content" rows="6" required></textarea></label>
    <label><input type="checkbox" name="publish" value="1"> Publish now</label>
    <p><button class="btn" name="add_post">Save</button></p>
  </form>
</div>

<div class="card">
  <h3>Recent Posts</h3>
  <table class="table">
    <tr><th>Title</th><th>Slug</th><th>Status</th><th>Created</th></tr>
    <?php foreach($posts as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['title']) ?></td>
        <td><?= htmlspecialchars($p['slug']) ?></td>
        <td><?= $p['published_at'] ? 'Published' : 'Draft' ?></td>
        <td><?= htmlspecialchars($p['created_at']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
