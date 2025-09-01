<?php
include __DIR__ . '/_partials_header.php';

try {
  $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
  $st = $pdo->query("SELECT title, slug, content, published_at 
                     FROM blog_posts 
                     WHERE published_at IS NOT NULL 
                     ORDER BY published_at DESC LIMIT 12");
  $posts = $st->fetchAll();
} catch(Exception $e){ $posts = []; }
?>
<h2>Events & Blog</h2>
<p class="muted">Updates from Moonlight — events, highlights and announcements.</p>

<div class="grid grid-3">
  <?php if(!$posts): ?>
    <div class="card">
      <h3>No posts yet</h3>
      <p class="muted">Use Admin → Blog to publish your first post.</p>
    </div>
  <?php else: foreach($posts as $p): ?>
    <div class="card">
      <h3><?= htmlspecialchars($p['title']) ?></h3>
      <p class="muted"><?= date('M j, Y', strtotime($p['published_at'])) ?></p>
      <p><?= nl2br(htmlspecialchars(mb_strimwidth(strip_tags($p['content']),0,160,'…'))) ?></p>
    </div>
  <?php endforeach; endif; ?>
</div>
<?php include __DIR__ . '/_partials_footer.php'; ?>
