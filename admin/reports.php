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
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Analytics queries ---
// Monthly cases (current month)
$monthlyCases = $pdo->query("
  SELECT incident_location, report_type, COUNT(*) as total
  FROM reports
  WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())
  GROUP BY incident_location, report_type
")->fetchAll(PDO::FETCH_ASSOC);

// Yearly cases (current year)
$yearlyCases = $pdo->query("
  SELECT incident_location, report_type, COUNT(*) as total
  FROM reports
  WHERE YEAR(created_at) = YEAR(CURRENT_DATE())
  GROUP BY incident_location, report_type
")->fetchAll(PDO::FETCH_ASSOC);

// Assigned cases
$assignedCases = $pdo->query("
  SELECT r.incident_location, u.name AS pic_name, COUNT(*) as total
  FROM reports r
  LEFT JOIN users u ON r.assigned_to = u.id
  WHERE r.assigned_to IS NOT NULL
  GROUP BY r.incident_location, u.name
")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Location + case type percentages (share of total cases)
$locationTypePercentages = $pdo->query("
  SELECT incident_location, report_type, COUNT(*) AS case_count,
         ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM reports), 2) AS percentage
  FROM reports
  GROUP BY incident_location, report_type
  ORDER BY incident_location, report_type
")->fetchAll(PDO::FETCH_ASSOC);

// Quarterly breakdown (current year)
$quarterCases = $pdo->query("
  SELECT report_type, QUARTER(created_at) AS quarter, COUNT(*) AS case_count
  FROM reports
  WHERE YEAR(created_at) = YEAR(CURRENT_DATE())
  GROUP BY report_type, quarter
  ORDER BY report_type, quarter
")->fetchAll(PDO::FETCH_ASSOC);

// Monthly breakdown by type (current year)
$monthlyTypeCases = $pdo->query("
  SELECT report_type, MONTH(created_at) AS month, COUNT(*) AS case_count
  FROM reports
  WHERE YEAR(created_at) = YEAR(CURRENT_DATE())
  GROUP BY report_type, month
  ORDER BY report_type, month
")->fetchAll(PDO::FETCH_ASSOC);

// Optional: consistent colors per case type across charts
function caseTypeColors(array $types): array {
    $palette = [
        'Assault'    => '#FF6384',
        'Theft'      => '#36A2EB',
        'Harassment' => '#FFCE56',
        'Fraud'      => '#4BC0C0',
        'Abuse'      => '#9966FF',
        'Other'      => '#8B5CF6',
    ];
    $colors = [];
    foreach ($types as $t) {
        $colors[$t] = $palette[$t] ?? sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
    return $colors;
}
$typesFromQuarter = array_values(array_unique(array_map(fn($r) => $r['report_type'], $quarterCases)));
$typesFromMonthly = array_values(array_unique(array_map(fn($r) => $r['report_type'], $monthlyTypeCases)));
$allTypes = array_values(array_unique(array_merge($typesFromQuarter, $typesFromMonthly)));
$typeColors = caseTypeColors($allTypes);
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
    <!-- Filter -->
    <form method="get" action="reports.php">
      <label>Filter by status</label>
      <select name="status">
        <option value="ALL" <?= $filter==='ALL'?'selected':''; ?>>All</option>
        <option value="RECEIVED" <?= $filter==='RECEIVED'?'selected':''; ?>>Received</option>
        <option value="UNDER_REVIEW" <?= $filter==='UNDER_REVIEW'?'selected':''; ?>>Under review</option>
        <option value="ASSIGNED" <?= $filter==='ASSIGNED'?'selected':''; ?>>Assigned</option>
        <option value="IN_PROGRESS" <?= $filter==='IN_PROGRESS'?'selected':''; ?>>In progress</option>
        <option value="RESOLVED" <?= $filter==='RESOLVED'?'selected':''; ?>>Resolved</option>
        <option value="REJECTED" <?= $filter==='REJECTED'?'selected':''; ?>>Rejected</option>
      </select>
      <button type="submit">Apply</button>
    </form>

    <!-- Reports Table -->
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
          <td><?= (int)$row['total'] ?></td>
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
          <td><?= (int)$row['total'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <!-- 📊 Yearly Case Distribution -->
    <h2>Yearly Case Distribution</h2>
    <canvas id="caseChart" width="600" height="300"></canvas>

    <!-- 📊 Location + Case Type Percentages -->
    <h2>Case Percentages by Location & Type</h2>
    <canvas id="locationChart" width="600" height="300"></canvas>

    <!-- 📊 Quarterly Breakdown -->
    <h2>Quarterly Case Breakdown</h2>
    <canvas id="quarterChart" width="600" height="300"></canvas>

    <!-- 📊 Monthly Breakdown by Case Type -->
    <h2>Monthly Case Breakdown by Type</h2>
    <canvas id="monthlyChart" width="600" height="300"></canvas>

    <script>
      // Yearly chart (location + type totals)
      const yearlyLabels = [
        <?php foreach ($yearlyCases as $row): ?>
          '<?= addslashes($row['incident_location']) ?> - <?= addslashes($row['report_type']) ?>',
        <?php endforeach; ?>
      ];
      const yearlyData = [
        <?php foreach ($yearlyCases as $row): ?>
          <?= (int)$row['total'] ?>,
        <?php endforeach; ?>
      ];
      new Chart(document.getElementById('caseChart').getContext('2d'), {
        type: 'bar',
        data: {
          labels: yearlyLabels,
          datasets: [{
            label: 'Cases in <?= date("Y") ?>',
            data: yearlyData,
            backgroundColor: '#6a0dad'
          }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
      });

      // ✅ Location + Case Type Percentages (Pie chart)
      const locTypeLabels = <?= json_encode(
        array_map(fn($r) => $r['incident_location'].' - '.$r['report_type'], $locationTypePercentages)
      ) ?>;
      const locTypeData = <?= json_encode(array_map('floatval', array_column($locationTypePercentages, 'percentage'))) ?>;
      new Chart(document.getElementById('locationChart'), {
        type: 'pie',
        data: {
          labels: locTypeLabels,
          datasets: [{
            data: locTypeData,
            backgroundColor: [
              '#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF','#8B5CF6',
              '#22C55E','#F59E0B','#EF4444','#3B82F6','#A855F7','#10B981',
              '#F97316','#84CC16','#06B6D4','#64748B'
            ]
          }]
        },
        options: {
          plugins: {
            tooltip: {
              callbacks: {
                label: ctx => `${ctx.label}: ${ctx.parsed}%`
              }
            },
            legend: { position: 'bottom' }
          }
        }
      });

      // Quarterly breakdown (Grouped bar chart)
      const quarterRaw = <?= json_encode($quarterCases) ?>;
      const quarters = ['Q1','Q2','Q3','Q4'];
      const caseTypesQuarter = [...new Set(quarterRaw.map(item => item.report_type))];
      const colorMap = <?= json_encode($typeColors) ?>;

      const quarterDatasets = caseTypesQuarter.map(type => {
        return {
          label: type,
          data: quarters.map((q,i) => {
            const found = quarterRaw.find(item => item.report_type === type && item.quarter == (i+1));
            return found ? parseInt(found.case_count) : 0;
          }),
          backgroundColor: colorMap[type] || '#6a0dad'
        };
      });
      new Chart(document.getElementById('quarterChart'), {
        type: 'bar',
        data: { labels: quarters, datasets: quarterDatasets },
        options: {
          responsive: true,
          plugins: { title: { display: true, text: 'Cases per Type per Quarter (<?= date("Y") ?>)' }, legend: { position: 'bottom' } },
          scales: { y: { beginAtZero: true } }
        }
      });

      // Monthly breakdown by type (Grouped bar chart)
      const monthlyRaw = <?= json_encode($monthlyTypeCases) ?>;
      const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      const caseTypesMonthly = [...new Set(monthlyRaw.map(item => item.report_type))];

      const monthlyDatasets = caseTypesMonthly.map(type => {
        return {
          label: type,
          data: months.map((m,i) => {
            const found = monthlyRaw.find(item => item.report_type === type && item.month == (i+1));
            return found ? parseInt(found.case_count) : 0;
          }),
          backgroundColor: colorMap[type] || '#6a0dad'
        };
      });

      new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: { labels: months, datasets: monthlyDatasets },
        options: {
          responsive: true,
          plugins: { title: { display: true, text: 'Cases per Type per Month (<?= date("Y") ?>)' }, legend: { position: 'bottom' } },
          scales: { y: { beginAtZero: true } }
        }
      });
    </script>
  </div>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>
</body>
</html>