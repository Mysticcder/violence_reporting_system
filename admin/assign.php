<?php
require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

// ✅ Include Composer autoloader for Africa's Talking SDK
require_once __DIR__ . '/../vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;

$id = (int)($_GET['id'] ?? 0);
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

  // ✅ Update report assignment
  $stmt = $pdo->prepare('UPDATE reports 
                         SET assigned_to = ?, status = "ASSIGNED", updated_at = NOW() 
                         WHERE id = ?');
  $stmt->execute([$picId, $id]);

  // ✅ Fetch PIC details
  $picStmt = $pdo->prepare('SELECT name, phone FROM users WHERE id = ?');
  $picStmt->execute([$picId]);
  $pic = $picStmt->fetch();

  // ✅ Build and send SMS
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

    $message = "New Case Assigned\n"
             . "Tracking ID: " . $report['tracking_code'] . "\n"
             . "Title: " . $report['title'] . "\n"
             . "Type: " . $report['report_type'] . "\n"
             . "Location: " . $report['incident_location'];

    // ✅ Load credentials from config.php
    $config   = require __DIR__ . '/../config/config.php';
    $username = $config['username'];
    $apiKey   = $config['apiKey'];

    $AT  = new AfricasTalking($username, $apiKey);
    $sms = $AT->sms();

    try {
      $result = $sms->send([
        'to'      => $recipientPhone,
        'message' => $message,
        'from'    => 'AFRICASTKNG' // ✅ Explicit sender ID
      ]);

      // ✅ Optional: log delivery status
      $recipient = $result['data']->SMSMessageData->Recipients[0] ?? null;
      if ($recipient) {
        error_log("SMS to {$recipient->number} → {$recipient->status} ({$recipient->statusCode})");
      }
    } catch (Exception $e) {
      error_log("SMS Error: " . $e->getMessage());
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
  <!-- ✅ Select2 CSS/JS for searchable dropdown -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
<main>
  <div class="container">
    <h2>Assign PIC for report <?= htmlspecialchars($report['tracking_code']) ?></h2>

    <?php if (empty($users)): ?>
      <p><strong>No active PICs available. Please add users with role "PIC".</strong></p>
    <?php else: ?>
      <form method="post" action="assign.php?id=<?= (int)$report['id'] ?>">
        <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">

        <label for="assigned_to">Select PIC</label>
        <select name="assigned_to" id="assigned_to" class="select2" style="width:100%" required>
          <option value="">— Choose a person —</option>
          <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>">
              <?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit">Assign</button>
      </form>
    <?php endif; ?>
  </div>
</main>

<script>
  // ✅ Activate Select2 for searchable dropdown
  $(document).ready(function() {
    $('.select2').select2({
      placeholder: "Search PIC...",
      allowClear: true
    });
  });
</script>

<?php require __DIR__ . '/components/footer.php'; ?>
</body>
</html>