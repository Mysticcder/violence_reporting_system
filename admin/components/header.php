<?php
session_start();
require_once __DIR__ . '/../../lib/auth.php';
require_login();
?>
<header>
<nav style="background:#6a0dad; color:#fff; padding:14px 24px;">
    <a href="dashboard.php" style="color:#fff; margin-right:10px;">Dashboard</a>
    <a href="reports.php" style="color:#fff; margin-right:10px;">Reports</a>
    <a href="users.php" style="color:#fff; margin-right:10px;">Users</a>
    <a href="settings.php" style="color:#fff; margin-right:10px;">Settings</a>
    <a href="../public/logout.php" style="color:#fff;">Logout</a>
    <span style="float:right;">Logged in as <?= htmlspecialchars($_SESSION['user']['name']) ?> (<?= htmlspecialchars($_SESSION['user']['role']) ?>)</span>
  </nav>
</header>
<link rel="stylesheet" href="../public/assets/css/style.css">