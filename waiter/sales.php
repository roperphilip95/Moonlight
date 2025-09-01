<?php
session_start();
require_once __DIR__ . '/../lib/config.php';

if(!isset($_SESSION['waiter_id'])){ header("Location: index.php"); exit; }

$pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
  PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
]);

// Create order
if(isset($_POST['create_order'])){
  $pm = $_POST['payment_method'];
  $pdo->prepare("INSERT INTO orders(order_no,user_id,payment_method,status,total_amount) VALUES (?,?,?,?,0)")
      ->execute([uniqid('ORD'), $_SESSION['waiter_id'], $pm, $pm==='cash' ? 'pending_cash' : 'paid']);
  header("Location: sales.php?order=" . $pdo->lastInsertId()); exit;
}

// Add item
if(isset($_POST['add_item']) && isset($_GET['order'])){
  $oid = (int)$_GET['order'];
  $item = (int)$_POST['item_id'];
  $qty  = (int)$_POST['qty'];
  $today = date('Y-m-d');
  // price = today price or base price
  $st=$pdo->prepare("SELECT m.base_price, COALESCE(dp.price,m.base_price) AS price FROM menu_items m LEFT JOIN daily_prices dp ON dp.item_id=m.id AND dp.price_date=? WHERE m.id=?");
  $st->execute([$today,$item]);
  $row=$st->fetch(); $price=$row?$row['price']:0;
  $line = $qty * $price;
  $pdo->prepare("INSERT INTO order_items(order_id,item_id,qty,unit_price,line_total) VALUES (?,?,?,?,?)")
      ->execute([$oid,$item,$qty,$price,$line]);
  // update order total
  $pdo->prepare("UPDATE orders SET total_amount = (SELECT COALESCE(SUM(line_total),0) FROM order_items WHERE order_id=?) WHERE id=?")
      ->execute([$oid,$oid]);
  header("Location: sales.php?order=$oid"); exit;
}

// Mark cash order as paid (end-of-shift cash control)
if(isset($_POST['mark_paid']) && isset($_GET['order'])){
  $oid=(int)$_GET['order'];
  $pdo->prepare("UPDATE orders SET status='paid' WHERE id=?")->execute([$oid]);
  header("Location: sales.php?order=$oid"); exit;
}

// Data
$items = $pdo->query("SELECT * FROM menu_items WHERE active=1 ORDER BY category,name")->fetchAll();
$oid = isset($_GET['order']) ? (int)$_GET['order'] : 0;
$order = null; $lines=[];
if($oid){
  $st=$pdo->prepare("SELECT * FROM orders WHERE id=?"); $st->execute([$oid]); $order=$st->fetch();
  $st=$pdo->prepare("SELECT oi.*, m.name FROM order_items oi JOIN menu_items m ON m.id=oi.item_id WHERE oi.order_id=?");
  $st->execute([$oid]); $lines=$st->fetchAll();
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/style.css"><title>Waiter POS</title></head>
<body class="container">
  <div class="grid grid-2">
    <div class="card">
      <h2>Create Order</h2>
      <form method="post">
        <label>Payment Method
          <select name="payment_method">
            <option value="cash">Cash (approve later)</option>
            <option value="transfer">Transfer</option>
            <option value="card">Card</option>
            <option value="paystack">Paystack (online)</option>
          </select>
        </label>
        <p><button class="btn" name="create_order">Start Order</button></p>
      </form>

      <?php if($order): ?>
        <h3>Order #<?= htmlspecialchars($order['order_no']) ?> — Total ₦<?= number_format($order['total_amount'],2) ?> — Status <?= htmlspecialchars($order['status']) ?></h3>
        <form method="post">
          <label>Item
            <select name="item_id">
              <?php foreach($items as $it): ?>
                <option value="<?= $it['id'] ?>"><?= htmlspecialchars($it['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Qty<input type="number" name="qty" value="1" min="1"></label>
          <p><button class="btn" name="add_item">Add</button></p>
        </form>

        <div class="card">
          <table class="table">
            <tr><th>Item</th><th>Qty</th><th>Unit</th><th>Total</th></tr>
            <?php foreach($lines as $ln): ?>
              <tr><td><?= htmlspecialchars($ln['name']) ?></td><td><?= $ln['qty'] ?></td><td>₦<?= number_format($ln['unit_price'],2) ?></td><td>₦<?= number_format($ln['line_total'],2) ?></td></tr>
            <?php endforeach; ?>
          </table>
        </div>

        <?php if($order['payment_method']==='cash' && $order['status']!=='paid'): ?>
          <form method="post"><button class="btn" name="mark_paid">Mark Cash as Paid</button></form>
        <?php elseif($order['payment_method']==='paystack'): ?>
          <p><a class="btn" href="/public/paystack_start.php?order=<?= $order['id'] ?>">Pay with Paystack</a></p>
        <?php endif; ?>

      <?php endif; ?>
    </div>

    <div class="card">
      <h2>My Recent Orders</h2>
      <table class="table">
        <tr><th>#</th><th>Total</th><th>Status</th><th>When</th></tr>
        <?php
        $st=$pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY id DESC LIMIT 20");
        $st->execute([$_SESSION['waiter_id']]);
        foreach($st as $o): ?>
          <tr>
            <td><a href="sales.php?order=<?= $o['id'] ?>"><?= htmlspecialchars($o['order_no']) ?></a></td>
            <td>₦<?= number_format($o['total_amount'],2) ?></td>
            <td><?= htmlspecialchars($o['status']) ?></td>
            <td><?= htmlspecialchars($o['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>
</html>
