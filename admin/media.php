<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/../lib/upload.php';

// simple auth guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

$msg = '';

// Handle gallery upload
if (isset($_POST['upload_gallery'])) {
    $res = handle_image_upload('gallery_image', __DIR__ . '/../assets/gallery', 4*1024*1024);
    if ($res['ok']) {
        $path = $res['path'];
        $cap = trim($_POST['caption'] ?? '');
        $st = $pdo->prepare("INSERT INTO gallery (image_url, caption) VALUES (?,?)");
        $st->execute([$path, $cap]);
        $msg = "✅ Gallery image uploaded.";
    } else {
        $msg = "❌ " . $res['msg'];
    }
}

// Handle slider upload
if (isset($_POST['upload_slider'])) {
    $res = handle_image_upload('slider_image', __DIR__ . '/../assets/slider', 4*1024*1024);
    if ($res['ok']) {
        $path = $res['path'];
        $cap = trim($_POST['caption'] ?? '');
        $st = $pdo->prepare("INSERT INTO slider (image_url, caption) VALUES (?,?)");
        $st->execute([$path, $cap]);
        $msg = "✅ Slider image uploaded.";
    } else {
        $msg = "❌ " . $res['msg'];
    }
}

// Delete gallery
if (isset($_POST['delete_gallery'])) {
    $id = (int)$_POST['gallery_id'];
    $row = $pdo->prepare("SELECT image_url FROM gallery WHERE id=? LIMIT 1");
    $row->execute([$id]); $r = $row->fetch();
    if ($r) {
        $file = __DIR__ . '/../' . $r['image_url'];
        if (file_exists($file)) @unlink($file);
        $pdo->prepare("DELETE FROM gallery WHERE id=?")->execute([$id]);
        $msg = "🗑️ Gallery image deleted.";
    }
}

// Delete slider
if (isset($_POST['delete_slider'])) {
    $id = (int)$_POST['slider_id'];
    $row = $pdo->prepare("SELECT image_url FROM slider WHERE id=? LIMIT 1");
    $row->execute([$id]); $r = $row->fetch();
    if ($r) {
        $file = __DIR__ . '/../' . $r['image_url'];
        if (file_exists($file)) @unlink($file);
        $pdo->prepare("DELETE FROM slider WHERE id=?")->execute([$id]);
        $msg = "🗑️ Slider image deleted.";
    }
}

// Fetch previews
$gallery = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 50")->fetchAll();
$slider  = $pdo->query("SELECT * FROM slider ORDER BY created_at DESC LIMIT 10")->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Media Manager — Moonlight</title>
<link rel="stylesheet" href="../assets/style.css">
<style>
.media-grid{display:flex;flex-wrap:wrap;gap:12px}
.media-item{width:140px;text-align:center}
.media-item img{width:140px;height:90px;object-fit:cover;border-radius:6px}
.small-form{display:flex;gap:8px;align-items:center}
.small-form input[type="text"]{flex:1;padding:6px;border-radius:6px;border:1px solid #222;background:#0f0f1a;color:#fff}
.small-form button{padding:6px 10px}
</style>
</head>
<body>
<?php include __DIR__ . '/../public/_partials_header.php'; ?>
<div class="container" style="padding:18px">
  <h2>Media Manager</h2>
  <?php if ($msg): ?><div class="card"><?= e($msg) ?></div><?php endif; ?>

  <div class="card" style="margin-top:12px">
    <h3>Upload Gallery Image</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="file" name="gallery_image" accept="image/*" required>
      <input type="text" name="caption" placeholder="Caption (optional)">
      <p><button class="btn" name="upload_gallery">Upload Gallery</button></p>
    </form>
  </div>

  <div class="card" style="margin-top:12px">
    <h3>Upload Slider Image</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="file" name="slider_image" accept="image/*" required>
      <input type="text" name="caption" placeholder="Caption (optional)">
      <p><button class="btn" name="upload_slider">Upload Slider</button></p>
    </form>
  </div>

  <div class="card" style="margin-top:12px">
    <h3>Gallery Preview</h3>
    <div class="media-grid">
      <?php foreach($gallery as $g): ?>
        <div class="media-item card">
          <img src="../<?= e($g['image_url']) ?>" alt="<?= e($g['caption']) ?>">
          <div style="font-size:12px;color:#aaa"><?= e($g['caption']) ?></div>
          <form method="post" onsubmit="return confirm('Delete image?');">
            <input type="hidden" name="gallery_id" value="<?= $g['id'] ?>">
            <button class="btn" name="delete_gallery">Delete</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card" style="margin-top:12px">
    <h3>Slider Preview</h3>
    <div class="media-grid">
      <?php foreach($slider as $s): ?>
        <div class="media-item card">
          <img src="../<?= e($s['image_url']) ?>" alt="<?= e($s['caption']) ?>">
          <div style="font-size:12px;color:#aaa"><?= e($s['caption']) ?></div>
          <form method="post" onsubmit="return confirm('Delete slide?');">
            <input type="hidden" name="slider_id" value="<?= $s['id'] ?>">
            <button class="btn" name="delete_slider">Delete</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>
</body>
</html>
