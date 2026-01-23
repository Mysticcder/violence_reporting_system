<?php
session_start();
require_once __DIR__ . '/../config/csrf.php';
$config = require __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit a report</title>
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
    <h2>Submit a report</h2>
    <form action="submit_report.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">

      <!-- Incident Type -->
      <label for="report_type">Type of incident</label>
      <select name="report_type" id="report_type" required>
        <option value="">-- Select Type --</option>
        <option value="GBV">GBV</option>
        <option value="PHYSICAL_HARASSMENT">Physical Harassment</option>
        <option value="SEXUAL_ASSAULT">Sexual Assault</option>
        <option value="SEXUAL_HARASSMENT">Sexual Harassment</option>
        <option value="INTIMATE_PARTNER_VIOLENCE">Intimate Partner Violence</option>
        <option value="CAT_CALLING">Cat Calling</option>
        <option value="OUTING">Outing</option>
        <option value="THREATS">Threats</option>
        <option value="PHYSICAL_ASSAULT">Physical Assault</option>
        <option value="CONVERSION_PRACTICES">Conversion Practices</option>
        <option value="OSTRACIZATION">Ostracization</option>
        <option value="OTHER">Other</option>
      </select>

      <!-- Title -->
      <label for="title">Title</label>
      <input type="text" id="title" name="title" placeholder="Brief summary" required>

      <!-- Description -->
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="6" placeholder="Provide details" required></textarea>

      <!-- Location -->
      <label for="incident_location">Incident location</label>
      <input type="text" id="incident_location" name="incident_location" placeholder="e.g., Thika, Kiambu County" required>

      <!-- Date -->
      <label for="incident_date">Incident date</label>
      <input type="date" id="incident_date" name="incident_date">

      <!-- Reporter Info -->
      <label for="reporter_name">Full Name / Preferred Name</label>
      <input type="text" id="reporter_name" name="reporter_name" placeholder="Your name" required>

      <label for="pronouns">Pronouns</label>
      <select id="pronouns" name="pronouns">
        <option value="">Select pronouns</option>
        <option value="SHE/HER">She/Her</option>
        <option value="HE/HIM">He/Him</option>
        <option value="THEY/THEM">They/Them</option>
        <option value="SHE/THEY">She/They</option>
        <option value="HE/THEY">He/They</option>
        <option value="OTHER">Other</option>
      </select>

      <label for="gender">Gender</label>
      <select id="gender" name="gender">
        <option value="">Select gender</option>
        <option value="cisgender">Cisgender</option>
        <option value="transman">TransMan</option>
        <option value="transwoman">TransWoman</option>
        <option value="nonbinary">Non-Binary</option>
      </select>

      <label for="sexual_orientation">Sexual Orientation</label>
      <select id="sexual_orientation" name="sexual_orientation">
        <option value="">Select orientation</option>
        <option value="lesbian">Lesbian</option>
        <option value="queer">Queer</option>
        <option value="bisexual">Bisexual</option>
        <option value="pansexual">Pansexual</option>
        <option value="asexual">Asexual</option>
      </select>

      <!-- Contact -->
      <label for="reporter_contact">Phone Number</label>
      <input type="text" id="reporter_contact" name="reporter_contact" placeholder="e.g., +254712345678" required pattern="^\+2547\d{8}$" title="Format: +2547XXXXXXXX">

      <label for="reporter_email">Email</label>
      <input type="email" id="reporter_email" name="reporter_email" placeholder="e.g., yourname@example.com" required>

      <!-- Evidence -->
      <label for="evidence">Evidence file (jpg, png, pdf, docx)</label>
      <input type="file" id="evidence" name="evidence" accept=".jpg,.jpeg,.png,.pdf,.docx">

      <button type="submit">Submit report</button>
    </form>
  </div>
</main>
<footer><small>&copy; <?= date('Y') ?></small></footer>
</body>
</html>