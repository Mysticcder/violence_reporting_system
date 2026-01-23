<?php
session_start();
$code = $_GET['code'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report Submitted</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f9f9f9; }
    header, footer { background:#333; color:#fff; padding:10px; }
    header a { color:#fff; text-decoration:none; margin-right:15px; }
    .container { max-width:600px; margin:40px auto; background:#fff; padding:20px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    h2 { color:#2c3e50; }
    .notice { background:#eafaf1; border:1px solid #2ecc71; padding:15px; margin:20px 0; font-size:1.2em; border-radius:4px; }
    .actions a { display:inline-block; margin:10px 10px 0 0; padding:10px 15px; background:#2c3e50; color:#fff; text-decoration:none; border-radius:4px; }
    .actions a:hover { background:#34495e; }
  </style>
</head>
<body>
<header>
  <nav>
    <a href="index.php">🏠 Home</a>
  </nav>
</header>
<main>
  <div class="container">
    <h2>✅ Thank you!</h2>
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <p><?= htmlspecialchars($_SESSION['flash_success']) ?></p>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <p>Your report has been received successfully. Please keep the tracking code below safe — you’ll need it to check your case status:</p>
    <div class="notice"><strong>Tracking code:</strong> <?= htmlspecialchars($code) ?></div>
    <div class="actions">
      <!-- ✅ Pass tracking code directly to status.php -->
      <a href="status.php?code=<?= urlencode($code) ?>">🔍 Check Status</a>
      <a href="report_form.php">✍️ Submit Another Report</a>
    </div>
  </div>
</main>
<footer>
  <small>&copy; <?= date('Y') ?> Violence Reporting System</small>
</footer>
</body>
</html>