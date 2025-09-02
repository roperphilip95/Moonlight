<?php
require_once __DIR__."/config.php";

function audit($action, $details = null) {
  $uid = $_SESSION['user_id'] ?? null;
  $ip  = $_SERVER['REMOTE_ADDR'] ?? null;
  $stmt = $GLOBALS['conn']->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?,?,?,?)");
  $stmt->bind_param("isss", $uid, $action, $details, $ip);
  $stmt->execute();
}

function price_today($item_id) {
  $today = date('Y-m-d');
  $stmt = $GLOBALS['conn']->prepare("
    SELECT price FROM item_prices 
    WHERE item_id = ? AND effective_date <= ? 
    ORDER BY effective_date DESC LIMIT 1
  ");
  $stmt->bind_param("is", $item_id, $today);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  return $res ? (float)$res['price'] : null;
}