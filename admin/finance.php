<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])) { header("Location: ../public/login.php"); exit; }

// Add new record
$msg = '';
if (isset($_POST['add_finance'])) {
  $type = $_POST['type'];
  $category = trim($_POST['category']);
  $amount = $_POST['amount'];
  $desc = trim($_POST['description']);
  $recorder = $_SESSION['user_id'];

  $st = $pdo->prepare("INSERT INTO finance (type,category,amount,description,recorded_by) VALUES (?,?,?,?,?)");
  $st->execute([$type,$category,$amount,$desc,$recorder]);
  $msg = "✅ Finance record added!";
}

// Fetch records
$records = $pdo->query("SELECT f.*,u.name AS staff FROM finance f LEFT JOIN users u ON u.id=f.recorded_by ORDER BY f.created_at DESC")->fetchAll();

// Totals
$totals = $pdo->query("
  SELECT 
    SUM(CASE WHEN type='income' THEN amount ELSE 0 END) AS total_income,
    SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) AS total_expenses,
    SUM(CASE WHEN type='income' THEN amount ELSE -amount END) AS balance
  FROM finance
")->fetch();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Finance Dashboard</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight Admin</div>
  <nav class="nav">
    <a href="index.php">Dashboard</a>
    <a href="finance.php">Finance</a>
    <a href="reservations.php">Reservations</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<div class="container" style="padding:22px">
  <h2>Finance Dashboard</h2>
  <?php if($msg): ?><div class="card" style="background:#1a4220"><?= $msg ?></div><?php endif; ?>

  <div class="card">
    <h3>Add Finance Record</h3>
    <form method="post">
      <label>Type
        <select name="type">
          <option value="income">Income</option>
          <option value="expense">Expense</option>
        </select>
      </label>
      <label>Category<input type="text" name="category" required></label>
      <label>Amount<input type="number" step="0.01" name="amount" required></label>
      <label>Description<textarea name="description"></textarea></label>
      <p><button class="btn" name="add_finance">Save</button></p>
    </form>
  </div>

  <div class="card" style="margin-top:20px">
    <h3>Summary</h3>
    <p><b>Total Income:</b> $<?= number_format($totals['total_income'],2) ?></p>
    <p><b>Total Expenses:</b> $<?= number_format($totals['total_expenses'],2) ?></p>
    <p><b>Balance:</b> $<?= number_format($totals['balance'],2) ?></p>
  </div>

  <div class="card" style="margin-top:20px">
    <h3>Transactions</h3>
    <table class="table">
      <tr><th>Type</th><th>Category</th><th>Amount</th><th>By</th><th>Date</th></tr>
      <?php foreach($records as $r): ?>
        <tr>
          <td><?= ucfirst($r['type']) ?></td>
          <td><?= e($r['category']) ?></td>
          <td>$<?= number_format($r['amount'],2) ?></td>
          <td><?= e($r['staff'] ?? 'System') ?></td>
          <td><?= $r['created_at'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
