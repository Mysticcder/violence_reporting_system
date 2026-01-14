<?php
require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/../config/db.php';

$users = $pdo->query('SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Users</title></head>
<body>
<main>
  <div class="container">
    <h2>System Users</h2>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%;">
      <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr>
      <?php foreach ($users as $u): ?>
      <tr>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>
        <td><?= htmlspecialchars($u['status']) ?></td>
        <td><?= htmlspecialchars($u['created_at']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>
</body>
</html>