<?php
require_once __DIR__ . '/../lib/config.php';
$pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$order_id = (int)($_GET['order'] ?? 0);
$st=$pdo->prepare("SELECT * FROM orders WHERE id=?"); $st->execute([$order_id]); $o=$st->fetch();
if(!$o){ die("Order not found"); }

$pub = ''; $sec = '';
$st=$pdo->prepare("SELECT slug,value FROM customizations WHERE slug IN ('paystack_public','paystack_secret')");
$st->execute();
foreach($st as $r){ if($r['slug']=='paystack_public') $pub=$r['value']; if($r['slug']=='paystack_secret') $sec=$r['value']; }
if(!$pub){ die("Paystack not configured. Ask admin to set keys in Customizer."); }

// Build a simple HTML that loads Paystack inline popup
$email = "guest@example.com"; // or collect from customer
$amount_k = (int)round($o['total_amount'] * 100);
$ref = 'PSK_'.uniqid();
$callback = (defined('BASE_URL')?BASE_URL:'')."/public/paystack_return.php?order=".$o['id']."&ref=".$ref;
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Pay with Paystack</title></head>
<body>
  <h2>Pay for Order <?= htmlspecialchars($o['order_no']) ?></h2>
  <p>Total: ₦<?= number_format($o['total_amount'],2) ?></p>
  <button id="payBtn">Pay Now</button>
  <script src="https://js.paystack.co/v1/inline.js"></script>
  <script>
  document.getElementById('payBtn').onclick = function(){
    var handler = PaystackPop.setup({
      key: '<?= $pub ?>',
      email: '<?= $email ?>',
      amount: <?= $amount_k ?>,
      ref: '<?= $ref ?>',
      callback: function(response){
        window.location.href = '<?= $callback ?>&status=success';
      },
      onClose: function(){
        window.location.href = '<?= $callback ?>&status=closed';
      }
    });
    handler.openIframe();
  };
  </script>
</body>
</html>
