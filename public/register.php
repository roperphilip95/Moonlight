<?php
session_start();
require_once "../lib/config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "⚠️ Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
        $stmt->bind_param("ssss", $name, $email, $phone, $password);
        if ($stmt->execute()) {
            $message = "✅ Registration successful! Please login.";
        } else {
            $message = "❌ Something went wrong.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="form-container">
    <h2>Create Account</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>
    <form method="post">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="phone" placeholder="Phone Number" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn neon-btn">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>