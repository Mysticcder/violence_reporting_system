<?php
require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../config/csrf.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM reports WHERE id = ?');
$stmt->execute([$id]);
$report = $stmt->fetch();
if (!$report) { http_response_code(404); exit('Report not found'); }

$sent = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_verify($_POST['csrf'] ?? '')) { exit('Invalid request'); }
  $subject = trim($_POST['subject'] ?? '');
  $body = trim($_POST['body'] ?? '');
  $to = $report['reporter_email'];

  if ($to) {
    try {
      $mail = mailer();
      $mail->addAddress($to);
      $mail->Subject = $subject;
      $mail->Body = $body;
      $mail->send();
      $sent = 'Message sent successfully.';
    } catch (Throwable $t) {
      $sent = 'Failed to send message.';
    }
  } else {
    $sent = 'No reporter email available.';
  }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Send message</title></head>
<body>
<main>
  <div class="container">
    <h2>Send message to reporter</h2>
    <?php if ($sent): ?><div class="notice"><?= htmlspecialchars($sent) ?></div><?php endif; ?>
    <form method="post" action="send_message.php?id=<?= (int)$report['id'] ?>">
      <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
      <label>Subject</label>
      <input type="text" name="subject" required>
      <label>Message body</label>
      <textarea name="body" rows="6" required></textarea>
      <button type="submit">Send</button>
    </form>
  </div>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>
</body>
</html>