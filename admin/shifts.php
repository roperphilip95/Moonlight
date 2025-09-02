<?php
require_once "../lib/config.php";
require_once "../lib/auth.php";
require_once "../lib/helpers.php";
require_role(['waiter','manager','admin']);

$role = $_SESSION['role'];
$uid  = $_SESSION['user_id'];
$msg  = "";

/* Open shift (waiter or manager on behalf) */
if (isset($_POST['open_shift'])) {
  $opening = (float)$_POST['opening_float'];
  $stmt = $conn->prepare("INSERT INTO shifts (waiter_id, opened_at, opening_float) VALUES (?,?,?)");
  $now = date('Y-m-d H:i:s');
  $stmt->bind_param("isd", $uid, $now, $opening);
  if ($stmt->execute()) { $msg = "✅ Shift opened"; audit('SHIFT_OPEN', json_encode(['opening'=>$opening])); }
}

/* Close shift */
if (isset($_POST['close_shift'])) {
  $shift_id = (int)$_POST['shift_id'];
  $closing  = (float)$_POST['closing_cash'];
  // Compute variance: (opening + sales - payouts - drops?) simplified demo:
  $sales = $conn->query("SELECT COALESCE(SUM(amount),0) s FROM cash_movements WHERE shift_id=$shift_id AND type='sale'")->fetch_assoc()['s'];
  $drops = $conn->query("SELECT COALESCE(SUM(amount),0) s FROM cash_movements WHERE shift_id=$shift_id AND type='drop'")->fetch_assoc()['s'];
  $payouts= $conn->query("SELECT COALESCE(SUM(amount),0) s FROM cash_movements WHERE shift_id=$shift_id AND type='payout'")->fetch_assoc()['s'];
  $open   = $conn->query("SELECT opening_float FROM shifts WHERE id=$shift_id")->fetch_assoc()['opening_float'];
  $expected = $open + $sales - $payouts - $drops;
  $variance = $closing - $expected;

  $stmt = $conn->prepare("UPDATE shifts SET closed_at=?, closing_cash=?, variance=? WHERE id=?");
  $now = date('Y-m-d H:i:s');
  $stmt->bind_param("ssdi", $now, $closing, $variance, $shift_id);
  if ($stmt->execute()) { $msg = "✅ Shift closed (Variance: ".number_format($variance,2).")"; audit('SHIFT_CLOSE', json_encode(['shift'=>$shift_id,'variance'=>$variance])); }
}

/* List my open shift */
$open_shift = $conn->query("SELECT * FROM shifts WHERE waiter_id=$uid AND closed_at IS NULL ORDER BY id DESC LIMIT 1")->fetch_assoc();

/* Movements add (record cash sales/drops) */
if ($open_shift && isset($_POST['add_move'])) {
  $type = $_POST['type'];
  $amt  = (float)$_POST['amount'];
  $note = trim($_POST['note']);
  $stmt = $conn->prepare("INSERT INTO cash_movements (shift_id, type, amount, note) VALUES (?,?,?,?)");
  $stmt->bind_param("isds", $open_shift['id'], $type, $amt, $note);
  if ($stmt->execute()) { $msg = "✅ Recorded $type"; audit('CASH_MOVE', json_encode(['type'=>$type,'amount'=>$amt])); }
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Shifts - Moonlight</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header class="site-header">
  <div class="logo">🌙 Shifts</div>
  <nav class="nav">
    <a href="../public/logout.php">Logout</a>
  </nav>
</header>

<div class="form-container">
  <h2>Cash Shift Control</h2>
  <?php if ($msg) echo "<p class='msg'>$msg</p>"; ?>

  <?php if (!$open_shift): ?>
    <h3>Open Shift</h3>
    <form method="post">
      <input type="number" step="0.01" name="opening_float" placeholder="Opening float" required>
      <button class="btn neon-btn" name="open_shift" type="submit">Open Shift</button>
    </form>
  <?php else: ?>
    <div class="order-card">
      <h3>Shift #<?= $open_shift['id'] ?> Opened: <?= $open_shift['opened_at'] ?></h3>
      <p>Opening Float: $<?= number_format($open_shift['opening_float'],2) ?></p>
    </div>

    <h3>Add Movement</h3>
    <form method="post">
      <select name="type" required>
        <option value="sale">Sale (cash)</option>
        <option value="drop">Safe Drop</option>
        <option value="payout">Payout</option>
        <option value="correction">Correction</option>
      </select>
      <input type="number" step="0.01" name="amount" placeholder="Amount" required>
      <input type="text" name="note" placeholder="Note (optional)">
      <button class="btn" name="add_move" type="submit">Add</button>
    </form>

    <h3>Close Shift</h3>
    <form method="post">
      <input type="hidden" name="shift_id" value="<?= $open_shift['id'] ?>">
      <input type="number" step="0.01" name="closing_cash" placeholder="Counted cash" required>
      <button class="btn" name="close_shift" type="submit">Close Shift</button>
    </form>
  <?php endif; ?>
</div>
</body>
</html>