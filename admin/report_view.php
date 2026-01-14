<?php
require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT r.*, u.name AS pic_name 
                       FROM reports r 
                       LEFT JOIN users u ON r.assigned_to = u.id 
                       WHERE r.id = ?');
$stmt->execute([$id]);
$report = $stmt->fetch();
if (!$report) { http_response_code(404); exit('Report not found'); }

$updated = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['csrf'] ?? '')) { exit('Invalid request'); }
  $newStatus = $_POST['status'] ?? $report['status'];
  $stmt = $pdo->prepare('UPDATE reports SET status = ?, updated_at = NOW() WHERE id = ?');
  $stmt->execute([$newStatus, $id]);
  $updated = 'Status updated.';
  // reload report
  $stmt = $pdo->prepare('SELECT r.*, u.name AS pic_name 
                         FROM reports r 
                         LEFT JOIN users u ON r.assigned_to = u.id 
                         WHERE r.id = ?');
  $stmt->execute([$id]);
  $report = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>View report</title></head>
<body>
<main>
  <div class="container">
    <h2>Report: <?= htmlspecialchars($report['title']) ?> (<?= htmlspecialchars($report['tracking_code']) ?>)</h2>
    <?php if ($updated): ?><div class="notice"><?= htmlspecialchars($updated) ?></div><?php endif; ?>
    <p><strong>Type:</strong> <?= htmlspecialchars($report['report_type']) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($report['status']) ?></p>
    <p><strong>Assigned PIC:</strong> <?= htmlspecialchars($report['pic_name'] ?? '—') ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($report['incident_location'] ?? '—') ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($report['incident_date'] ?? '—') ?></p>
    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($report['description'])) ?></p>
    <p><strong>Reporter contact:</strong>
      <?= $report['is_anonymous'] ? 'Anonymous' : htmlspecialchars($report['reporter_name'] . ' | ' . ($report['reporter_contact'] ?? '')) ?>
      <?php if ($report['reporter_email']): ?> | <?= htmlspecialchars($report['reporter_email']) ?><?php endif; ?>
    </p>
    <p><strong>Evidence:</strong>
      <?= $report['evidence_path'] ? '<a href="../public/uploads/evidence/' . htmlspecialchars($report['evidence_path']) . '" target="_blank">Download file</a>' : 'No file provided.' ?>
    </p>

    <form method="post" action="report_view.php?id=<?= (int)$report['id'] ?>">
      <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
      <label>Update status</label>
      <select name="status">
        <?php foreach (['RECEIVED','UNDER_REVIEW','ASSIGNED','IN_PROGRESS','RESOLVED','REJECTED'] as $st): ?>
          <option value="<?= $st ?>" <?= $st === $report['status'] ? 'selected' : '' ?>><?= $st ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Save</button>
    </form>
  </div>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>
</body>
</html>