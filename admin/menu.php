<?php
require_once __DIR__ . '/_header.php';
require_role(['Admin','Manager']);

$pdo = db();

// Handle new item
if(isset($_POST['add_item'])){
  $name = trim($_POST['name'] ?? '');
  $cat  = trim($_POST['category'] ?? '');
  $price= (float)($_POST['base_price'] ?? 0);
  if($name){
    $st=$pdo->prepare("INSERT INTO menu_items(name,category,base_price,active) VALUES (?,?,?,1)");
    $st->execute([$name,$cat,$price]);
    flash("Item added."); header("Location: menu.php"); exit;
  } else { flash("Name required"); }
}

// Handle set daily price
if(isset($_POST['set_price'])){
  $item = (int)$_POST['item_id'];
  $p    = (float)$_POST['price'];
  $d    = date('Y-m-d');
  $st=$pdo->prepare("INSERT INTO daily_prices(item_id,price_date,price) VALUES (?,?,?)
                     ON DUPLICATE KEY UPDATE price=VALUES(price)");
  $st->execute([$item,$d,$p]);
  flash("Today's price updated."); header("Location: menu.php"); exit;
}

// Fetch
$items = $pdo->query("SELECT * FROM menu_items ORDER BY category,name")->fetchAll();
$today = date('Y-m-d');
$daily = $pdo->prepare("SELECT item_id, price FROM daily_prices WHERE price_date=?");
$daily->execute([$today]);
$map = [];
foreach($daily as $r){ $map[$r['item_id']]=$r['price']; }
?>
<h2>Menu Manager</h2>
<div class="grid grid-2">
  <div class="card">
    <h3>Add Item</h3>
    <form method="post">
      <div class="row">
        <label>Name*<input name="name" required></label>
        <label>Category<input name="category" placeholder="Cocktails / Food / Bottle"></label>
      </div>
      <label>Base Price (₦)<input name="base_price" type="number" step="0.01" value="0"></label>
      <p><button class="btn" name="add_item">Add</button></p>
    </form>
  </div>
  <div class="card">
    <h3>Set Today’s Prices</h3>
    <form method="post">
      <label>Item
        <select name="item_id">
          <?php foreach($items as $i): ?>
            <option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['name']) ?> (Base ₦<?= number_format($i['base_price'],2) ?>)</option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Today’s Price (₦)<input type="number" step="0.01" name="price" required></label>
      <p><button class="btn" name="set_price">Save Today’s Price</button></p>
    </form>
  </div>
</div>

<div class="card">
  <h3>Items</h3>
  <table class="table">
    <tr><th>Name</th><th>Category</th><th>Base Price</th><th>Price Today</th></tr>
    <?php foreach($items as $i): ?>
      <tr>
        <td><?= htmlspecialchars($i['name']) ?></td>
        <td><?= htmlspecialchars($i['category']) ?></td>
        <td>₦<?= number_format($i['base_price'],2) ?></td>
        <td><?= isset($map[$i['id']]) ? '₦'.number_format($map[$i['id']],2) : '—' ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
