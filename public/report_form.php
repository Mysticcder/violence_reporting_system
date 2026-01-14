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
      <label>Type of incident</label>
      <select name="report_type" required>
        <option value="GBV">GBV</option>
        <option value="PHYSICAL_HARASSMENT">Physical Harassment</option>
        <option value="SEXUAL_ASSAULT">Sexual assault</option>
        <option value="SEXUAL_HARASSMENT">Sexual harassment</option>
        <option value="INTIMATE_PARTNER_VIOLENCE">Intimate Partner Violence</option>
        <option value="CAT_CALLING">Cat calling</option>
        <option value="OUTING">Outing</option>
        <option value="THREATS">Threats</option>
        <option value="PHYSICAL_ASSAULT">Physical Assault</option>
        <option value="CONVERSION_PRACTICES">Conversion Practices</option>
        <option value="OSTRACIZATION">Ostracization</option>
        <option value="OTHER">Other</option>
      </select>

      <label>Title</label>
      <input type="text" name="title" placeholder="Brief summary" required>

      <label>Description</label>
      <textarea name="description" rows="6" placeholder="Provide details" required></textarea>

      <label>Incident location</label>
      <input type="text" name="incident_location" placeholder="e.g., Thika, Kiambu County">

      <label>Incident date</label>
      <input type="date" name="incident_date">

      <label>Submit anonymously?</label>
      <select name="is_anonymous" required>
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>

      <label>Full Name / Preferred Name</label>
      <input type="text" name="reporter_name" placeholder="Optional">

      <label>Pronouns</label>
      <select name="pronouns">
        <option value="SHE/HER">She/Her</option>
        <option value="HE/HIM">He/Him</option>
        <option value="THEY/THEM">They/Them</option>
        <option value="SHE/THEY">She/They</option>
        <option value="HE/THEY">He/They</option>
        <option value="OTHER">Other</option>
      </select>

      <label>Gender</label>
      <select name="gender">
        <option value="">Select gender</option>
        <option value="cisgender">CisGender</option>
        <option value="transman">TransMan</option>
        <option value="transwoman">TransWoman</option>
        <option value="nonbinary">Non-Binary</option>
      </select>

      <label>Sexual Orientation</label>
      <select name="sexual_orientation">
        <option value="lesbian">Lesbian</option>
        <option value="queer">Queer</option>
        <option value="bisexual">Bisexual</option>
        <option value="pansexual">Pansexual</option>
        <option value="asexual">Asexual</option>
      </select>

      <label>Phone Number</label>
      <input type="text" name="reporter_contact" placeholder="Optional">

      <label>Email</label>
      <input type="email" name="reporter_email" placeholder="Optional">

      <label>Evidence file (jpg, png, pdf, docx)</label>
      <input type="file" name="evidence">

      <button type="submit">Submit report</button>
    </form>
  </div>
</main>
<footer><small>&copy; <?= date('Y') ?></small></footer>
</body>
</html>