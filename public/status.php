<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$report = null;

if (!empty($_GET['code'])) {
  $code = strtoupper(trim($_GET['code']));
  $stmt = $pdo->prepare('SELECT tracking_code, report_type, title, status, created_at, updated_at 
                         FROM reports WHERE tracking_code = ? LIMIT 1');
  $stmt->execute([$code]);
  $report = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Check status</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
  <nav>
    <a href="index.php">Home</a>
    <a href="report_form.php">Submit a report</a>
    <a href="status.php">Check status</a>
  </nav>
</header>
<main>
  <div class="container">
    <h2>Check report status</h2>
    <form method="get" action="status.php">
      <label>Tracking code</label>
      <input type="text" name="code" placeholder="Enter tracking code" required 
             value="<?= htmlspecialchars($_GET['code'] ?? '') ?>">
      <button type="submit">Look up</button>
    </form>

    <?php if ($report): ?>
      <div class="notice">
        <strong>Title:</strong> <?= htmlspecialchars($report['title']) ?><br>
        <strong>Type:</strong> <?= htmlspecialchars($report['report_type']) ?><br>
        <strong>Status:</strong> <?= htmlspecialchars($report['status']) ?><br>
        <strong>Created:</strong> <?= htmlspecialchars($report['created_at']) ?><br>
        <strong>Updated:</strong> <?= htmlspecialchars($report['updated_at'] ?? '—') ?>
      </div>
    <?php elseif (isset($_GET['code'])): ?>
      <div class="error">No report found with that tracking code.</div>
    <?php endif; ?>
  </div>
</main>
<footer><small>&copy; <?= date('Y') ?></small></footer>
</body>
</html>