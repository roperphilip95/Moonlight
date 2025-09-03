<?php
require_once __DIR__ . '/../lib/config.php';
$images = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Gallery — Moonlight</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="../assets/style.css">
<script defer src="../assets/app.js"></script>
<style>
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}
.gallery-item{position:relative;overflow:hidden;border-radius:10px}
.gallery-item img{width:100%;height:240px;object-fit:cover;display:block;transition:transform .4s}
.gallery-item:hover img{transform:scale(1.05)}
.gallery-caption{position:absolute;left:0;right:0;bottom:0;padding:10px;background:linear-gradient(180deg,transparent,rgba(0,0,0,0.6));color:#fff;font-size:14px}
</style>
</head>
<body>
<?php include __DIR__ . '/_partials_header.php'; ?>

<div class="container" style="padding:22px">
  <h2>Gallery</h2>
  <?php if (empty($images)): ?>
    <div class="card muted">No images yet. Admin can upload via Media Manager.</div>
  <?php else: ?>
    <div class="gallery-grid">
      <?php foreach($images as $img): ?>
        <div class="gallery-item card">
          <img 
  src="../<?= e($img['image_url']) ?>" 
  alt="<?= e($img['caption']) ?>"
  data-lightbox="../<?= e($img['image_url']) ?>"
  data-caption="<?= e($img['caption']) ?>">
</div><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/_partials_footer.php'; ?>
</body>
</html>
