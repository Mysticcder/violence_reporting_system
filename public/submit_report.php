<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Collect and sanitize form data
$data = [
  'report_type'       => $_POST['report_type'] ?? '',
  'title'             => trim($_POST['title'] ?? ''),
  'description'       => trim($_POST['description'] ?? ''),
  'incident_location' => trim($_POST['incident_location'] ?? ''), // ✅ location field
  'incident_date'     => $_POST['incident_date'] ?? null,
  'is_anonymous'      => (int)($_POST['is_anonymous'] ?? 1),
  'reporter_name'     => trim($_POST['reporter_name'] ?? ''),
  'reporter_contact'  => trim($_POST['reporter_contact'] ?? ''),
  'reporter_email'    => trim($_POST['reporter_email'] ?? ''),
];

// Generate unique tracking code
$tracking = bin2hex(random_bytes(6));

// Insert into database
$stmt = $pdo->prepare('
  INSERT INTO reports (
    tracking_code, report_type, title, description, incident_location, incident_date,
    reporter_name, reporter_contact, reporter_email, is_anonymous, status, created_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "RECEIVED", NOW())
');

$stmt->execute([
  $tracking,
  $data['report_type'],
  $data['title'],
  $data['description'],
  $data['incident_location'] ?: null,
  $data['incident_date'] ?: null,
  $data['reporter_name'],
  $data['reporter_contact'],
  $data['reporter_email'],
  $data['is_anonymous'],
]);

// Flash message + redirect
$_SESSION['flash_success'] = 'Report submitted successfully.';
header('Location: thank_you.php?code=' . urlencode($tracking));
exit;