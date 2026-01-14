<?php
session_start();
$config = require __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($config['app_name']) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
  <nav>
    <a href="index.php">Home</a>
    <a href="report_form.php">Submit a report</a>
    <a href="status.php">Check status</a>
    <a href="login.php">Admin/PIC</a>
  </nav>
</header>
<main>
  <div class="container">
    <h2>Violence reporting and support</h2>
    <p>This system helps collect and respond to reports of GBV,