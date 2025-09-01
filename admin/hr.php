<?php
require_once __DIR__ . '/_header.php';
require_role(['Admin','HR','Manager']);

$pdo = db();

// Approve/Decline leave
if(isset($_POST['review_leave'])){
  $id = (int)$_POST['leave_id'];
  $status = $_POST['status']==='approved' ? 'approved' : 'declined';
  $st=$pdo->prepare("UPDATE leaves SET status=?, approved_by=?, approved_at=NOW() WHERE id=?");
  $st->execute([$status, $_SESSION['uid'], $id]);
  flash("Leave $status."); header("Location: hr.php"); exit;
}

// Attendance today
$today = date('Y-m-d');
$att = $pdo->prepare("
  SELECT a.*, u.name, u.email
  FROM attendance a JOIN users u ON u.id=a.user_id
  WHERE DATE(a.clock_in)=? ORDER BY a.clock_in DESC
");
$att->execute([$today]); $attendance = $att->fetchAll();

// Leaves pending
$lv = $pdo->query("SELECT l.*, u.name FROM leaves l JOIN users u ON u.id=l.user_id WHERE l.status='pending' ORDER BY l.created_at DESC")->fetchAll();
?>
<h2>HR</h2>

<div class="card">
  <h3>Today's Attendance</h3>
  <table class="table">
    <tr><th>User</th><th>Clock In</th><th>Clock Out</th></tr>
    <?php foreach($attendance as $a): ?>
      <tr><td><?= htmlspecialchars($a['name']) ?> (<?= htmlspecialchars($a['email']) ?>)</td><td><?= htmlspecialchars($a['clock_in']) ?></td><td><?= htmlspecialchars($a['clock_out'] ?: '—') ?></td></tr>
    <?php endforeach; ?>
  </table>
</div>

<div class="card">
  <h3>Pending Leave Requests</h3>
  <table class="table">
    <tr><th>User</th><th>Type</th><th>From</th><th>To</th><th>Reason</th><th>Action</th></tr>
    <?php if(!$lv): ?>
      <tr><td colspan="6">No pending leaves.</td></tr>
    <?php else: foreach($lv as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($r['type']) ?></td>
        <td><?= htmlspecialchars($r['from_date']) ?></td>
        <td><?= htmlspecialchars($r['to_date']) ?></td>
        <td><?= htmlspecialchars($r['reason']) ?></td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="leave_id" value="<?= $r['id'] ?>">
            <button class="btn" name="review_leave" value="1" onclick="this.form.status.value='approved'">Approve</button>
            <button class="btn" name="review_leave" value="1" onclick="this.form.status.value='declined'">Decline</button>
            <input type="hidden" name="status" value="">
          </form>
        </td>
      </tr>
    <?php endforeach; endif; ?>
  </table>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
