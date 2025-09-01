<?php
require_once __DIR__ . '/_header.php';
require_role(['Admin']);

$pdo = db();

function set_setting($slug,$value){
  $st = db()->prepare("INSERT INTO customizations(slug,value) VALUES(?,?)
                       ON DUPLICATE KEY UPDATE value=VALUES(value)");
  $st->execute([$slug,$value]);
}
function get_setting($slug,$default=''){
  $st = db()->prepare("SELECT value FROM customizations WHERE slug=?");
  $st->execute([$slug]);
  $r=$st->fetch();
  return $r ? $r['value'] : $default;
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  set_setting('site_name', $_POST['site_name']);
  set_setting('wa_phone', $_POST['wa_phone']);
  set_setting('paystack_public', $_POST['paystack_public']);
  set_setting('paystack_secret', $_POST['paystack_secret']); // store securely in real use
  flash("Saved."); header("Location: customizer.php"); exit;
}

$site_name = get_setting('site_name','Moonlight VIP Lounge');
$wa_phone  = get_setting('wa_phone','2348012345678');
$ps_pub    = get_setting('paystack_public','');
$ps_sec    = get_setting('paystack_secret','');
?>
<h2>Customizer</h2>
<div class="card">
  <form method="post">
    <div class="row">
      <label>Site Name <input name="site_name" value="<?= htmlspecialchars($site_name) ?>"></label>
      <label>WhatsApp Phone <input name="wa_phone" value="<?= htmlspecialchars($wa_phone) ?>"></label>
    </div>
    <div class="row">
      <label>Paystack Public Key <input name="paystack_public" value="<?= htmlspecialchars($ps_pub) ?>"></label>
      <label>Paystack Secret Key <input name="paystack_secret" value="<?= htmlspecialchars($ps_sec) ?>"></label>
    </div>
    <p><button class="btn">Save</button></p>
  </form>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
