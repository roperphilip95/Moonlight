<?php
session_start();
require_once __DIR__ . '/../lib/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $phone = trim($_POST['phone']);
  $email = trim($_POST['email']);
  $guests = $_POST['guests'];
  $date = $_POST['date'];
  $time = $_POST['time'];
  $message = $_POST['message'];
  $customer_id = $_SESSION['user_id'] ?? null;

  $st = $pdo->prepare("INSERT INTO reservations (customer_id,name,phone,email,guests,date,time,message) VALUES (?,?,?,?,?,?,?,?)");
  $st->execute([$customer_id,$name,$phone,$email,$guests,$date,$time,$message]);

  echo "<script>alert('✅ Reservation submitted! We will confirm soon.');window.location='contact.php';</script>";
} else {
  header("Location: contact.php");
}
