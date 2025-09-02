<?php
session_start();
require_once "../lib/config.php";
require_once "../lib/helpers.php";
if (!isset($_GET['order_id'])) { header("Location: orders.php"); exit; }

$order_id = (int)$_GET['order_id'];
$ord = $conn->query("SELECT * FROM orders WHERE id=$order_id")->fetch_assoc();
if (!$ord) { echo "Order not found"; exit; }

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['method'])) {
  $method = $_POST['method'];
  // For offline methods, mark pending and show instructions
  if (in_array($method, ['cash','pos','transfer'])) {
    $stmt = $conn->prepare("INSERT INTO payments (order_id, method, amount, status) VALUES (?,?,?,'pending')");
    $stmt->bind_param("isd", $order_id, $method, $ord['total']);
    $stmt->execute();
    audit('PAYMENT_INIT_OFFLINE', json_encode(['order'=>$order_id,'method'=>$method]));
    $msg = "Recorded as $method. Please complete payment at the venue.";
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Pay for Order #<?= $order_id ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://js.paystack.co/v1/inline.js"></script>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header class="site-header">
  <div class="logo">🌙 Moonlight</div>
  <nav class="nav"><a href="my_orders.php">My Orders</a><a href="logout.php">Logout</a></nav>
</header>

<div class="form-container">
  <h2>Pay for Order #<?= $order_id ?></h2>
  <p><strong>Total:</strong> $<?= number_format($ord['total'],2) ?></p>
  <?php if (!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>

  <h3>Online Payment</h3>
  <button id="paystackBtn" class="btn neon-btn">Pay with Paystack</button>

  <h3>Offline Payment</h3>
  <form method="post" style="margin-top:10px;">
    <select name="method" required>
      <option value="cash">Cash</option>
      <option value="pos">POS</option>
      <option value="transfer">Bank Transfer</option>
    </select>
    <button class="btn" type="submit">Record Offline</button>
  </form>
</div>

<script>
document.getElementById('paystackBtn').onclick = function () {
  let handler = PaystackPop.setup({
    key: '<?= PAYSTACK_PUBLIC_KEY ?>',
    email: '<?= $_SESSION['name'] ?>@mail.local',
    amount: Math.round(<?= $ord['total'] ?> * 100 * 1600), // naive USD->NGN (adjust/remove if using NGN directly)
    currency: 'NGN',
    ref: 'ML' + Date.now(),
    callback: function(response){
      // POST to verify endpoint
      fetch('paystack_verify.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({order_id: <?= $order_id ?>, reference: response.reference})
      }).then(r=>r.json()).then(d=>{
        alert(d.message);
        window.location = 'my_orders.php';
      });
    },
    onClose: function(){
      alert('Payment window closed.');
    }
  });
  handler.openIframe();
}
</script>
</body>
</html>