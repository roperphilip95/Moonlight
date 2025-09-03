<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])) { header("Location: ../public/login.php"); exit; }

$period = $_GET['period'] ?? 'monthly';

// Grouping
$groupBy = $period=='daily' ? "DATE(created_at)" :
           ($period=='yearly' ? "YEAR(created_at)" : "DATE_FORMAT(created_at,'%Y-%m')");
$records = $pdo->query("
  SELECT $groupBy as label,
    SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
    SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expenses,
    SUM(CASE WHEN type='income' THEN amount ELSE -amount END) as balance
  FROM finance GROUP BY label ORDER BY label DESC
")->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Finance Report</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight Admin</div>
  <nav class="nav">
    <a href="finance.php">Finance</a>
    <a href="finance_report.php?period=daily">Daily</a>
    <a href="finance_report.php?period=monthly">Monthly</a>
    <a href="finance_report.php?period=yearly">Yearly</a>
  </nav>
</header>
<div class="container" style="padding:22px">
  <h2>Finance Report (<?= ucfirst($period) ?>)</h2>
  <table class="table">
    <tr><th>Period</th><th>Income</th><th>Expenses</th><th>Balance</th></tr>
    <?php foreach($records as $r): ?>
      <tr>
        <td><?= $r['label'] ?></td>
        <td>$<?= number_format($r['income'],2) ?></td>
        <td>$<?= number_format($r['expenses'],2) ?></td>
        <td>$<?= number_format($r['balance'],2) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
</body></html>
