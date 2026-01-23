<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Collect and sanitize form data
$data = [
  'report_type'       => trim($_POST['report_type'] ?? ''),
  'title'             => trim($_POST['title'] ?? ''),
  'description'       => trim($_POST['description'] ?? ''),
  'incident_location' => trim($_POST['incident_location'] ?? ''),
  'incident_date'     => $_POST['incident_date'] ?? null,
  'is_anonymous'      => (int)($_POST['is_anonymous'] ?? 1),
  'reporter_name'     => trim($_POST['reporter_name'] ?? ''),
  'reporter_contact'  => trim($_POST['reporter_contact'] ?? ''),
  'reporter_email'    => trim($_POST['reporter_email'] ?? ''),
  'pronouns'          => trim($_POST['pronouns'] ?? ''),
  'gender'            => trim($_POST['gender'] ?? ''),
  'sexual_orientation'=> trim($_POST['sexual_orientation'] ?? ''),
  'evidence'          => null, // will be set if file uploaded
];

// ✅ Validation: ensure required fields are present
$errors = [];
if (empty($data['report_type'])) {
    $errors[] = "Case type is required.";
}
if (empty($data['title'])) {
    $errors[] = "Title is required.";
}
if (empty($data['incident_location'])) {
    $errors[] = "Incident location is required.";
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(" ", $errors);
    header('Location: report_form.php');
    exit;
}

// ✅ Handle evidence file upload
if (!empty($_FILES['evidence']['name'])) {
    $allowed = ['jpg','jpeg','png','pdf','docx'];
    $ext = strtolower(pathinfo($_FILES['evidence']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        $filename = uniqid('evidence_') . '.' . $ext;
        $uploadPath = __DIR__ . '/../uploads/' . $filename;
        if (move_uploaded_file($_FILES['evidence']['tmp_name'], $uploadPath)) {
            $data['evidence'] = $filename;
        } else {
            $_SESSION['flash_error'] = "Failed to upload evidence file.";
            header('Location: report_form.php');
            exit;
        }
    } else {
        $_SESSION['flash_error'] = "Invalid file type. Allowed: jpg, png, pdf, docx.";
        header('Location: report_form.php');
        exit;
    }
}

// Generate unique tracking code
$tracking = bin2hex(random_bytes(6));

try {
    // Insert into database
    $stmt = $pdo->prepare('
      INSERT INTO reports (
        tracking_code, report_type, title, description, incident_location, incident_date,
        reporter_name, reporter_contact, reporter_email, is_anonymous,
        pronouns, gender, sexual_orientation, evidence,
        status, created_at
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "RECEIVED", NOW())
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
      $data['pronouns'],
      $data['gender'],
      $data['sexual_orientation'],
      $data['evidence'],
    ]);

    // Flash message + redirect
    $_SESSION['flash_success'] = 'Report submitted successfully.';
    header('Location: thank_you.php?code=' . urlencode($tracking));
    exit;

} catch (PDOException $e) {
    // Handle DB errors gracefully
    $_SESSION['flash_error'] = 'Failed to submit report: ' . $e->getMessage();
    header('Location: report_form.php');
    exit;
}