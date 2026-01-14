<?php
session_start();
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../lib/auth.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['csrf'] ?? '')) {
    $error = 'Invalid request.';
  } else {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login($email, $password)) {
      header('Location: ../admin/dashboard.php');
      exit;
    } else {
      $error = 'Invalid credentials.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
  <nav>
    <a href="index.php">Home</a>
  </nav>
</header>
<main>
  <div class="container">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="login.php">
      <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</main>
<footer><small>&copy; <?= date('Y') ?></small></footer>
</body>
</html>