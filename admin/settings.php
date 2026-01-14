<?php
require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

$updated = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['csrf'] ?? '')) { exit('Invalid request'); }
  foreach ($_POST as $key => $val) {
    if ($key === 'csrf') continue;
    $stmt = $pdo->prepare('INSERT INTO settings (name, value) VALUES (?, ?) 
                           ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()');
    $stmt->execute([$key, trim($val)]);
  }
  $updated = 'Settings updated successfully.';
}

// Load settings
$stmt = $pdo->query('SELECT name, value FROM settings');
$settings = [];
foreach ($stmt->fetchAll() as $row) {
  $settings[$row['name']] = $row['value'];
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Settings</title></head>
<body>
<main>
  <div class="container">
    <h2>System Settings</h2>
    <?php if ($updated): ?><div class="notice"><?= htmlspecialchars($updated) ?></div><?php endif; ?>
    <form method="post" action="settings.php">
      <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
      <label>Support email</label>
      <input type="email" name="support_email" value="<?= htmlspecialchars($settings['support_email'] ?? '') ?>">
      <label>Support phone</label>
      <input type="text" name="support_phone" value="<?= htmlspecialchars($settings['support_phone'] ?? '') ?>">
      <label>Organization name</label>
      <input type="text" name="org_name" value="<?= htmlspecialchars($settings['org_name'] ?? '') ?>">
      <button type="submit">Save settings</button>
    </form>
  </div>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>
</body>
</html>