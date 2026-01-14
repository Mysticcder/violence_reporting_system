<?php
session_start();
$code = $_GET['code'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report submitted</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header><nav><a href="index.php">Home</a></nav></header>
<main>
  <div class="container">
    <h2>Thank you</h2>
    <p>Your report has been received. Use the tracking code below to check status:</p>
    <div class="notice"><strong>Tracking code:</strong> <?= htmlspecialchars($code) ?></div>
    <p><a href="status.php">Check status</a></p>
  </div>
</main>
<footer><small>&copy; <?= date('Y') ?></small></footer>
</body>
</html>