<?php
require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../whatsapp_test.php';

$id = (int)($_GET['id'] ?? 0);

// Fetch report
$stmt = $pdo->prepare('SELECT * FROM reports WHERE id = ?');
$stmt->execute([$id]);
$report = $stmt->fetch();
if (!$report) { http_response_code(404); exit('Report not found'); }

// Fetch active PICs
$users = $pdo->query('SELECT id, name, email, phone 
                      FROM users 
                      WHERE role = "PIC" AND status = "ACTIVE" 
                      ORDER BY name ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf'] ?? '')) { exit('Invalid request'); }
    $picId = (int)($_POST['assigned_to'] ?? 0);

    // Update report assignment
    $stmt = $pdo->prepare('UPDATE reports 
                           SET assigned_to = ?, status = "ASSIGNED", updated_at = NOW() 
                           WHERE id = ?');
    $stmt->execute([$picId, $id]);

    // Fetch PIC details
    $picStmt = $pdo->prepare('SELECT name, phone FROM users WHERE id = ?');
    $picStmt->execute([$picId]);
    $pic = $picStmt->fetch();

    // Send WhatsApp notification using template
    if ($pic && !empty($pic['phone'])) {
        // Format phone number into +2547XXXXXXXX
        function formatPhone($number) {
            $number = trim($number);
            if (preg_match('/^0\d{9}$/', $number)) {
                return '+254' . substr($number, 1);
            }
            return $number;
        }
        $recipientPhone = formatPhone($pic['phone']);

        try {
            // Use your approved template "case_assignment_v1"
            $response = sendWhatsAppTemplate($recipientPhone, 'case_assignment_v1', [
                $report['tracking_code'],         // {{1}} Tracking ID
                $report['title'],                 // {{2}} Title
                $report['incident_location'],     // {{3}} Location
                $pic['name']                      // {{4}} PIC Name
            ]);

            error_log("WhatsApp template response: " . print_r($response, true));
        } catch (Throwable $ex) {
            error_log("WhatsApp Error: " . $ex->getMessage());
        }
    }

    header('Location: report_view.php?id=' . $id);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Assign PIC</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .container { max-width:600px; margin:40px auto; background:#fff; padding:20px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    label, select, button { display:block; margin-top:10px; }
    select, button { padding:8px; width:100%; }
    button { background:#2c3e50; color:#fff; border:none; border-radius:4px; cursor:pointer; }
    button:hover { background:#34495e; }
  </style>
</head>
<body>
<main>
  <div class="container">
    <h2>Assign Person In Charge</h2>

    <p><strong>Tracking ID:</strong> <?= htmlspecialchars($report['tracking_code']) ?></p>
    <p><strong>Title:</strong> <?= htmlspecialchars($report['title']) ?></p>
    <p><strong>Type:</strong> <?= htmlspecialchars($report['report_type']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($report['incident_location']) ?></p>

    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <label for="assigned_to">Select PIC:</label>
      <select name="assigned_to" id="assigned_to" required>
        <option value="">-- Choose PIC --</option>
        <?php foreach ($users as $user): ?>
          <option value="<?= $user['id'] ?>">
            <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['phone']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Assign</button>
    </form>
  </div>
</main>
</body>
</html>