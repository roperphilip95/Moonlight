<?php
require_once __DIR__ . '/../lib/config.php';
$pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$order_id = (int)($_GET['order'] ?? 0);
$status   = $_GET['status'] ?? 'closed';

// In production, verify with Paystack API using secret key.
// For now, if status=success, mark as paid.
if($status==='success'){
  $pdo->prepare("UPDATE orders SET status='paid' WHERE id=?")->execute([$order_id]);
  echo "<h2>Payment successful ✅</h2><p>Your order is now paid.</p>";
} else {
  echo "<h2>Payment not completed</h2><p>Status: ".htmlspecialchars($status)."</p>";
}
