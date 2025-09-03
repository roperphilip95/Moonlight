<?php
require_once __DIR__ . '/../lib/config.php';

// Optionally pull embed URLs or channel IDs from site_settings
$get = $pdo->prepare("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('yt_channel','fb_video_url','ig_post_url')");
$get->execute();
$settings = $get->fetchAll(PDO::FETCH_KEY_PAIR);

// helper: produce iframe for youtube channel (live_stream)
$yt_channel = $settings['yt_channel'] ?? ''; // expected: channel ID or video id
$fb_video = $settings['fb_video_url'] ?? ''; // full facebook video URL embedable
$ig_post  = $settings['ig_post_url'] ?? ''; // instagram post URL
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"><title>Live — Moonlight</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="../assets/style.css">
<style>
.live-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:18px;padding:18px}
.frame{background:#0f0f1a;padding:12px;border-radius:10px}
.frame h3{color:#e639af;margin:0 0 10px}
.frame iframe{width:100%;height:320px;border:0;border-radius:8px}
@media(max-width:700px){ .frame iframe{height:230px} }
</style>
</head>
<body>
<?php include __DIR__ . '/_partials_header.php'; ?>

<div class="container" style="padding:22px">
  <h2>Live Streams</h2>
  <div class="live-grid">
    <div class="frame">
      <h3>YouTube Live</h3>
      <?php if ($yt_channel): ?>
        <!-- If yt_channel is a channel ID: -->
        <iframe src="https://www.youtube.com/embed/live_stream?channel=<?= e($yt_channel) ?>" allowfullscreen allow="autoplay; encrypted-media"></iframe>
      <?php else: ?>
        <p class="muted">No YouTube channel configured. Set <code>yt_channel</code> in site settings.</p>
      <?php endif; ?>
    </div>

    <div class="frame">
      <h3>Facebook Live</h3>
      <?php if ($fb_video): ?>
        <!-- Facebook embed (ensure the URL is the full video URL) -->
        <iframe src="https://www.facebook.com/plugins/video.php?href=<?= urlencode($fb_video) ?>&show_text=0" allowfullscreen></iframe>
      <?php else: ?>
        <p class="muted">No Facebook video configured. Set <code>fb_video_url</code> in site settings.</p>
      <?php endif; ?>
    </div>

    <div class="frame">
      <h3>Instagram</h3>
      <?php if ($ig_post): ?>
        <blockquote class="instagram-media" data-instgrm-permalink="<?= e($ig_post) ?>" data-instgrm-version="14"></blockquote>
        <script async src="//www.instagram.com/embed.js"></script>
      <?php else: ?>
        <p class="muted">No Instagram post configured. Set <code>ig_post_url</code> in site settings.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/_partials_footer.php'; ?>
</body>
</html>
