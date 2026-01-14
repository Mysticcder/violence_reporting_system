<?php
require_once __DIR__ . '/components/header.php';
require_once __DIR__ . '/../config/db.php';

// --- Existing reports listing ---
$filter = $_GET['status'] ?? 'ALL';
$query = 'SELECT r.id, r.tracking_code, r.title, r.report_type, r.status, r.created_at, u.name AS pic_name, r.incident_location
          FROM reports r LEFT JOIN users u ON r.assigned_to = u.id';
$params = [];
if ($filter !== 'ALL') {
  $query .= ' WHERE r.status = ?';
  $params[] = $filter;
}
$query .= ' ORDER BY r.created_at DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// --- New analytics queries ---
$monthlyCases = $pdo->query("SELECT incident_location, report_type, COUNT(*) as total 
                             FROM reports 
                             WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                             GROUP BY incident_location, report_type")->fetchAll();

$yearlyCases = $pdo->query("SELECT incident_location, report_type, COUNT(*) as total 
                            FROM reports 
                            WHERE YEAR(created_at) = YEAR(CURRENT_DATE()) 
                            GROUP BY incident_location, report_type")->fetchAll();

$assignedCases = $pdo->query("SELECT incident_location, u.name AS pic_name, COUNT(*) as total 
                              FROM reports r 
                              LEFT JOIN users u ON r.assigned_to = u.id 
                              WHERE r.assigned_to IS NOT NULL 
                              GROUP BY incident_location, u.name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Reports</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<main>
  <div class="container">
    <h2>Reports</h2>
    <form method="get" action="reports.php">
      <label>Filter by status</label>
      <select name="status">
        <option value="ALL">All</option>
        <option value="RECEIVED">Received</option>
        <option value="UNDER_REVIEW">Under review</option>
        <option value="ASSIGNED">Assigned</option>
        <option value="IN_PROGRESS">In progress</option>
        <option value="RESOLVED">Resolved</option>
        <option value="REJECTED">Rejected</option>
      </select>
      <button type="submit">Apply</button>
    </form>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%; margin-top:12px;">
      <tr>
        <th>Tracking</th><th>Title</th><th>Type</th><th>Status</th><th>Assigned PIC</th><th>Location</th><th>Created</th><th>Actions</th>
      </tr>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['tracking_code']) ?></td>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= htmlspecialchars($r['report_type']) ?></td>
        <td><?= htmlspecialchars($r['status']) ?></td>
        <td><?= htmlspecialchars($r['pic_name'] ?? '—') ?></td>
        <td><?= htmlspecialchars($r['incident_location'] ?? '—') ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
        <td>
          <a href="report_view.php?id=<?= (int)$r['id'] ?>">View</a> |
          <a href="assign.php?id=<?= (int)$r['id'] ?>">Assign</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>

    <!-- 📍 Monthly Case Summary -->
    <h2>Case Summary (This Month)</h2>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%; margin-top:12px;">
      <tr><th>Location</th><th>Type</th><th>Total</th></tr>
      <?php foreach ($monthlyCases as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['incident_location']) ?></td>
          <td><?= htmlspecialchars($row['report_type']) ?></td>
          <td><?= $row['total'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <!-- 👥 Assigned Cases -->
    <h2>Assigned Cases</h2>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%; margin-top:12px;">
      <tr><th>Location</th><th>Assigned PIC</th><th>Total</th></tr>
      <?php foreach ($assignedCases as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['incident_location']) ?></td>
          <td><?= htmlspecialchars($row['pic_name']) ?></td>
          <td><?= $row['total'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <!-- 📊 Yearly Case Distribution -->
    <h2>Yearly Case Distribution</h2>
    <canvas id="caseChart" width="600" height="300"></canvas>
    <script>
      const ctx = document.getElementById('caseChart').getContext('2d');
      const caseChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: [
            <?php foreach ($yearlyCases as $row): ?>
              '<?= $row['incident_location'] ?> - <?= $row['report_type'] ?>',
            <?php endforeach; ?>
          ],
          datasets: [{
            label: 'Cases in <?= date("Y") ?>',
            data: [
              <?php foreach ($yearlyCases as $row): ?>
                <?= $row['total'] ?>,
              <?php endforeach; ?>
            ],
            backgroundColor: '#6a0dad'
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    </script>
  </div>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>
</body>
</html>