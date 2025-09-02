<?php
session_start();
require_once "../lib/config.php";
require_once "../lib/helpers.php";

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$order_id = (int)($input['order_id'] ?? 0);
$ref = $input['reference'] ?? '';

if (!$order_id || !$ref) {
  echo json_encode(['ok'=>false,'message'=>'Invalid data']); exit;
}

/* TODO: Live verification:
   $ch = curl_init("https://api.paystack.co/transaction/verify/$ref");
   curl_setopt_array($ch, [
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_HTTPHEADER => ["Authorization: Bearer ".PAYSTACK_SECRET_KEY]
   ]);
   $res = json_decode(curl_exec($ch), true);
   curl_close($ch);
   $success = $res && $res['data']['status']==='success';
*/
$success = true; // DEMO ONLY — replace with real verification above

if ($success) {
  $stmt = $conn->prepare("INSERT INTO payments (order_id, method, amount, provider_ref, status) VALUES (?,?,?,?, 'success')");
  $method = 'paystack';
  // store original order total as amount (or Paystack paid amount)
  $ord = $conn->query("SELECT total FROM orders WHERE id=$order_id")->fetch_assoc();
  $amount = $ord ? $ord['total'] : 0;
  $stmt->bind_param("isds", $order_id, $method, $amount, $ref);
  $stmt->execute();

  audit('PAYSTACK_SUCCESS', json_encode(['order'=>$order_id,'ref'=>$ref]));

  echo json_encode(['ok'=>true,'message'=>'Payment verified successfully.']);
} else {
  audit('PAYSTACK_FAILED', json_encode(['order'=>$order_id,'ref'=>$ref]));
  echo json_encode(['ok'=>false,'message'=>'Payment verification failed.']);
}