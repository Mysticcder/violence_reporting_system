<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Collect basic form data
$data = [
  'report_type'   => trim($_POST['report_type'] ?? ''),
  'title'         => trim($_POST['title'] ?? ''),
  'description'   => trim($_POST['description'] ?? ''),
  'incident_location' => trim($_POST['incident_location'] ?? ''),
  'reporter_name' => trim($_POST['reporter_name'] ?? ''),
  'reporter_contact' => trim($_POST['reporter_contact'] ?? ''),
  'reporter_email'   => trim($_POST['reporter_email'] ?? ''),
];

// Generate tracking code
$tracking = bin2hex(random_bytes(6));

try {
    // Insert into database (minimal fields)
    $stmt = $pdo->prepare('
        INSERT INTO reports (
            tracking_code, report_type, title, description, incident_location,
            reporter_name, reporter_contact, reporter_email,
            status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, "RECEIVED", NOW())
    ');

    $stmt->execute([
        $tracking,
        $data['report_type'],
        $data['title'],
        $data['description'],
        $data['incident_location'],
        $data['reporter_name'],
        $data['reporter_contact'],
        $data['reporter_email'],
    ]);

    // Redirect to thank you page with tracking code
    $_SESSION['flash_success'] = 'Report submitted successfully.';
    header('Location: thank_you.php?code=' . urlencode($tracking));
    exit;

} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    $_SESSION['flash_error'] = 'Failed to submit report.';
    header('Location: report_form.php');
    exit;
}