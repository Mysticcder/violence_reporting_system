<?php
session_start();

// Load config (which should parse .env)
$config = require __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($config['app_name'] ?? 'Violence Reporting System') ?></title>
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
    <h2>Violence Reporting and Support</h2>
    <p>
      This system helps collect and respond to reports of Gender-Based Violence (GBV) and related incidents.
      It provides a safe, confidential platform for survivors and witnesses to share information, 
      and enables administrators and Persons In Charge (PICs) to track, assign, and resolve cases.
    </p>

    <h3>What you can do here:</h3>
    <ul>
      <li><strong>Submit a report:</strong> Share details of an incident securely.</li>
      <li><strong>Check status:</strong> Track the progress of your submitted case using a tracking code.</li>
     
    </ul>

    <p>
      Your privacy and safety are our priority. Evidence files can be uploaded securely, 
      and only authorized personnel will have access to case details.
    </p>
  </div>
</main>
<footer>
  <small>&copy; <?= date('Y') ?> <?= htmlspecialchars($config['app_name'] ?? 'Violence Reporting System') ?></small>
</footer>
</body>
</html>