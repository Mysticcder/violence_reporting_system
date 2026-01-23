<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$report = null;
$code = '';

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
  <title>Check Status</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body { font-family: Arial, sans-serif; background:#f9f9f9; margin:0; padding:0; }
    header, footer { background:#333; color:#fff; padding:10px; }
    header a { color:#fff; text-decoration:none; margin-right:15px; }
    .container { max-width:600px; margin:40px auto; background:#fff; padding:20px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    h2 { color:#2c3e50; }
    form { margin-bottom:20px; }
    input[type="text"] { padding:8px; width:70%; }
    button { padding:8px 12px; background:#2c3e50; color:#fff; border:none; border-radius:4px; cursor:pointer; }
    button:hover { background:#34495e; }
    .notice { background:#eafaf1; border:1px solid #2ecc71; padding:15px; margin:20px 0; border-radius:4px; }
    .error { background:#fdecea; border:1px solid #e74c3c; padding:15px; margin:20px 0; border-radius:4px; color:#c0392b; }
  </style>
</head>
<body>
<header>
  <nav>
    <a href="index.php">🏠 Home</a>
    <a href="report_form.php">✍️ Submit a Report</a>
    <a href="status.php">🔍 Check Status</a>
  </nav>
</header>
<main>
  <div class="container">
    <h2>🔍 Check Report Status</h2>
    <form method="get" action="status.php">
      <label for="code">Tracking Code</label><br>
      <input type="text" id="code" name="code" placeholder="Enter tracking code" required 
             value="<?= htmlspecialchars($code) ?>">
      <button type="submit">Look Up</button>
    </form>

    <?php if ($report): ?>
      <div class="notice">
        <p><strong>Tracking Code:</strong> <?= htmlspecialchars($report['tracking_code']) ?></p>
        <p><strong>Title:</strong> <?= htmlspecialchars($report['title']) ?></p>
        <p><strong>Type:</strong> <?= htmlspecialchars($report['report_type']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($report['status']) ?></p>
        <p><strong>Created:</strong> <?= htmlspecialchars($report['created_at']) ?></p>
        <p><strong>Updated:</strong> <?= htmlspecialchars($report['updated_at'] ?? '—') ?></p>
      </div>
    <?php elseif (!empty($code)): ?>
      <div class="error">❌ No report found with that tracking code.</div>
    <?php endif; ?>
  </div>
</main>
<footer><small>&copy; <?= date('Y') ?> Violence Reporting System</small></footer>
</body>
</html>