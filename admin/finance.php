<?php
require_once __DIR__ . '/_header.php';
require_role(['Admin','Finance','Manager']);

$pdo = db();

if(isset($_POST['add_income'])){
  $st=$pdo->prepare("INSERT INTO income(category,amount,notes,entry_date,added_by) VALUES (?,?,?,?,?)");
  $st->execute([trim($_POST['category']), (float)$_POST['amount'], trim($_POST['notes']), $_POST['entry_date'], $_SESSION['uid']]);
  flash("Income recorded."); header("Location: finance.php"); exit;
}
if(isset($_POST['add_expense'])){
  $st=$pdo->prepare("INSERT INTO expenses(category,amount,notes,entry_date,added_by) VALUES (?,?,?,?,?)");
  $st->execute([trim($_POST['category']), (float)$_POST['amount'], trim($_POST['notes']), $_POST['entry_date'], $_SESSION['uid']]);
  flash("Expense recorded."); header("Location: finance.php"); exit;
}

// Summary
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');

$sum = $pdo->prepare("
  SELECT
    (SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status='paid' AND DATE(created_at) BETWEEN ? AND ?) AS sales,
    (SELECT COALESCE(SUM(amount),0) FROM income WHERE entry_date BETWEEN ? AND ?) AS other_income,
    (SELECT COALESCE(SUM(amount),0) FROM expenses WHERE entry_date BETWEEN ? AND ?) AS expenses
");
$sum->execute([$from,$to,$from,$to,$from,$to]);
$tot = $sum->fetch();
$net = $tot['sales'] + $tot['other_income'] - $tot['expenses'];

$inc = $pdo->prepare("SELECT * FROM income WHERE entry_date BETWEEN ? AND ? ORDER BY entry_date DESC");
$inc->execute([$from,$to]); $incomes=$inc->fetchAll();

$exp = $pdo->prepare("SELECT * FROM expenses WHERE entry_date BETWEEN ? AND ? ORDER BY entry_date DESC");
$exp->execute([$from,$to]); $expenses=$exp->fetchAll();
?>
<h2>Finance</h2>
<div class="card">
  <form method="get" class="row">
    <label>From <input type="date" name="from" value="<?= htmlspecialchars($from) ?>"></label>
    <label>To <input type="date" name="to" value="<?= htmlspecialchars($to) ?>"></label>
    <p><button class="btn">Filter</button></p>
  </form>
  <p><strong>Sales:</strong> ₦<?= number_format($tot['sales'],2) ?> |
     <strong>Other income:</strong> ₦<?= number_format($tot['other_income'],2) ?> |
     <strong>Expenses:</strong> ₦<?= number_format($tot['expenses'],2) ?> |
     <strong>Net:</strong> ₦<?= number_format($net,2) ?></p>
</div>

<div class="grid grid-2">
  <div class="card">
    <h3>Add Income</h3>
    <form method="post">
      <div class="row">
        <label>Category<input name="category" required></label>
        <label>Amount (₦)<input type="number" step="0.01" name="amount" required></label>
      </div>
      <label>Date<input type="date" name="entry_date" value="<?= date('Y-m-d') ?>"></label>
      <label>Notes<textarea name="notes"></textarea></label>
      <p><button class="btn" name="add_income">Save Income</button></p>
    </form>
  </div>
  <div class="card">
    <h3>Add Expense</h3>
    <form method="post">
      <div class="row">
        <label>Category<input name="category" required></label>
        <label>Amount (₦)<input type="number" step="0.01" name="amount" required></label>
      </div>
      <label>Date<input type="date" name="entry_date" value="<?= date('Y-m-d') ?>"></label>
      <label>Notes<textarea name="notes"></textarea></label>
      <p><button class="btn" name="add_expense">Save Expense</button></p>
    </form>
  </div>
</div>

<div class="grid grid-2">
  <div class="card">
    <h3>Income (<?= htmlspecialchars($from) ?> → <?= htmlspecialchars($to) ?>)</h3>
    <table class="table">
      <tr><th>Date</th><th>Category</th><th>Amount</th><th>Notes</th></tr>
      <?php foreach($incomes as $r): ?>
        <tr><td><?= htmlspecialchars($r['entry_date']) ?></td><td><?= htmlspecialchars($r['category']) ?></td><td>₦<?= number_format($r['amount'],2) ?></td><td><?= htmlspecialchars($r['notes']) ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>
  <div class="card">
    <h3>Expenses (<?= htmlspecialchars($from) ?> → <?= htmlspecialchars($to) ?>)</h3>
    <table class="table">
      <tr><th>Date</th><th>Category</th><th>Amount</th><th>Notes</th></tr>
      <?php foreach($expenses as $r): ?>
        <tr><td><?= htmlspecialchars($r['entry_date']) ?></td><td><?= htmlspecialchars($r['category']) ?></td><td>₦<?= number_format($r['amount'],2) ?></td><td><?= htmlspecialchars($r['notes']) ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
