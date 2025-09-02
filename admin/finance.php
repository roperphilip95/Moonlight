<?php
session_start();
require_once "../lib/config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "finance") {
    header("Location: ../public/login.php");
    exit;
}

$message = "";

// Add expense
if (isset($_POST["add_expense"])) {
    $desc = $_POST["description"];
    $amount = $_POST["amount"];
    $stmt = $conn->prepare("INSERT INTO expenses (description, amount) VALUES (?, ?)");
    $stmt->bind_param("sd", $desc, $amount);
    if ($stmt->execute()) {
        $message = "✅ Expense recorded!";
    }
}

// Fetch completed orders (income)
$incomeResult = $conn->query("SELECT SUM(total) AS total_income FROM orders WHERE status = 'completed'");
$income = $incomeResult->fetch_assoc()["total_income"] ?? 0;

// Fetch expenses
$expenseResult = $conn->query("SELECT SUM(amount) AS total_expenses FROM expenses");
$expenses = $expenseResult->fetch_assoc()["total_expenses"] ?? 0;

// Net profit
$profit = $income - $expenses;

// Get all expenses list
$expenseList = $conn->query("SELECT * FROM expenses ORDER BY created_at DESC");

// Get all completed orders
$orderList = $conn->query("SELECT id, total, created_at FROM orders WHERE status = 'completed' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Finance Dashboard - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">🌙 Moonlight Finance</div>
    <nav class="nav">
      <a href="finance.php" class="active">Dashboard</a>
      <a href="../public/logout.php">Logout</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>Finance Overview</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>

    <div class="order-card">
      <h3>Summary</h3>
      <p><strong>Total Income:</strong> $<?= number_format($income, 2) ?></p>
      <p><strong>Total Expenses:</strong> $<?= number_format($expenses, 2) ?></p>
      <p><strong>Net Profit:</strong> $<?= number_format($profit, 2) ?></p>
    </div>

    <h3>Record Expense</h3>
    <form method="post">
      <input type="text" name="description" placeholder="Expense description" required>
      <input type="number" step="0.01" name="amount" placeholder="Amount" required>
      <button type="submit" name="add_expense" class="btn">Add Expense</button>
    </form>

    <h3>Recent Expenses</h3>
    <table class="order-table">
      <tr><th>ID</th><th>Description</th><th>Amount</th><th>Date</th></tr>
      <?php while ($exp = $expenseList->fetch_assoc()): ?>
        <tr>
          <td><?= $exp["id"] ?></td>
          <td><?= $exp["description"] ?></td>
          <td>$<?= number_format($exp["amount"], 2) ?></td>
          <td><?= $exp["created_at"] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <h3>Completed Orders (Income)</h3>
    <table class="order-table">
      <tr><th>Order ID</th><th>Total</th><th>Date</th></tr>
      <?php while ($order = $orderList->fetch_assoc()): ?>
        <tr>
          <td>#<?= $order["id"] ?></td>
          <td>$<?= number_format($order["total"], 2) ?></td>
          <td><?= $order["created_at"] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>