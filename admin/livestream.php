<?php
require_once __DIR__ . '/_header.php';
require_role(['Admin','Manager']);

$pdo = db();
if(isset($_POST['add_stream'])){
  $st=$pdo->prepare("INSERT INTO live_streams(platform,url,is_active) VALUES (?,?,?)");
  $st->execute([$_POST['platform'], trim($_POST['url']), isset($_POST['is_active'])?1:0]);
  flash("Stream saved."); header("Location: livestream.php"); exit;
}
$streams = $pdo->query("SELECT * FROM live_streams ORDER BY id DESC")->fetchAll();
?>
<h2>Livestream</h2>
<div class="grid grid-2">
  <div class="card">
    <h3>Add Stream</h3>
    <form method="post">
      <label>Platform
        <select name="platform">
          <option value="youtube">YouTube</option>
          <option value="facebook">Facebook</option>
          <option value="instagram">Instagram</option>
        </select>
      </label>
      <label>URL <input name="url" placeholder="https://..."></label>
      <label><input type="checkbox" name="is_active" value="1"> Active</label>
      <p><button class="btn" name="add_stream">Save</button></p>
    </form>
  </div>
  <div class="card">
    <h3>Existing</h3>
    <table class="table">
      <tr><th>Platform</th><th>URL</th><th>Active</th></tr>
      <?php foreach($streams as $s): ?>
        <tr><td><?= htmlspecialchars($s['platform']) ?></td><td><?= htmlspecialchars($s['url']) ?></td><td><?= $s['is_active']?'Yes':'No' ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
