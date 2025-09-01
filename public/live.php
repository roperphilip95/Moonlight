<?php
include __DIR__ . '/_partials_header.php';

$streams = [];
try {
  $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
  $st = $pdo->query("SELECT platform, url, is_active FROM live_streams WHERE is_active=1 ORDER BY id DESC");
  $streams = $st->fetchAll();
} catch(Exception $e){ $streams = []; }

function embed($platform,$url){
  $u = htmlspecialchars($url);
  if($platform==='youtube'){
    // Accept full url or ID; try to extract video ID
    if(preg_match('~(?:v=|be/)([A-Za-z0-9_-]{6,})~',$url,$m)) $vid=$m[1]; else $vid=$url;
    return "<iframe width='100%' height='360' src='https://www.youtube.com/embed/{$vid}' frameborder='0' allowfullscreen></iframe>";
  }
  if($platform==='facebook'){
    return "<div class='card'><a href='{$u}' target='_blank'>Watch on Facebook</a></div>";
  }
  if($platform==='instagram'){
    return "<div class='card'><a href='{$u}' target='_blank'>Watch on Instagram</a></div>";
  }
  return "<div class='card'><a href='{$u}' target='_blank'>Open Stream</a></div>";
}
?>
<h2>Live Stream</h2>
<p class="muted">When live, streams appear below. Follow our socials for alerts.</p>

<div class="grid grid-2">
  <?php if(!$streams): ?>
    <div class="card"><h3>No active streams</h3><p class="muted">Add one via Admin → Livestreams.</p></div>
  <?php else: foreach($streams as $s): ?>
    <div class="card"><?= embed($s['platform'],$s['url']) ?></div>
  <?php endforeach; endif; ?>
</div>
<?php include __DIR__ . '/_partials_footer.php'; ?>
