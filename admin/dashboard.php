<?php
session_start();
require_once __DIR__ . '/../lib/auth.php';
require_login();
require_once __DIR__ . '/../config/db.php';

// Monthly case details with PICs
$locationDetails = $pdo->query("
    SELECT r.incident_location, r.report_type, u.name AS pic_name, COUNT(*) as total
    FROM reports r
    LEFT JOIN users u ON r.assigned_to = u.id
    WHERE MONTH(r.created_at) = MONTH(CURRENT_DATE())
      AND YEAR(r.created_at) = YEAR(CURRENT_DATE())
    GROUP BY r.incident_location, r.report_type, u.name
    ORDER BY r.incident_location
")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Yearly cases grouped by type + month
$yearlyCases = $pdo->query("
    SELECT report_type, MONTH(created_at) AS month, COUNT(*) AS total
    FROM reports
    WHERE YEAR(created_at) = YEAR(CURRENT_DATE())
    GROUP BY report_type, month
    ORDER BY month, report_type
")->fetchAll(PDO::FETCH_ASSOC);

// Group by location for monthly summary
$groupedLocations = [];
foreach ($locationDetails as $row) {
  $loc = $row['incident_location'];
  if (!isset($groupedLocations[$loc])) $groupedLocations[$loc] = [];
  $groupedLocations[$loc][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../public/assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .summary-row {
      display: flex;
      justify-content: space-between;
      background: #f4f4f4;
      padding: 10px 15px;
      border-radius: 6px;
      font-weight: bold;
      color: #4b0082;
      cursor: pointer;
      margin-bottom: 8px;
    }
    .summary-left { flex: 1; text-align: left; }
    .summary-right { flex: 0; text-align: right; }
    .location-details {
      margin: 10px 0 20px 0;
      padding: 10px;
      background: #fafafa;
      border: 1px solid #ddd;
      border-radius: 6px;
    }
    .location-details table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }
    .location-details th, .location-details td {
      border: 1px solid #ccc;
      padding: 6px;
      text-align: left;
    }
    canvas {
      max-width: 600px;
      max-height: 300px;
    }
  </style>
</head>
<body>
  <div class="dashboard">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2 class="logo">Violence Reporting</h2>
      <nav>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="reports.php">📄 Reports</a>
        <a href="users.php">👥 Users</a>
        <a href="send_message.php">📩 Messages</a>
        <a href="settings.php">⚙️ Settings</a>
        <a href="../public/logout.php" class="logout">🚪 Logout</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main">
      <header class="topbar">
        <h1>Admin Dashboard</h1>
        <div class="profile">👤 Admin</div>
      </header>

      <section class="content">
        <h2>Welcome back, Leha</h2>
        <p>Here’s an overview of recent activity:</p>

        <div class="cards">
          <div class="card">
            <h3>Total Reports</h3>
            <p id="total-count">...</p>
          </div>
          <div class="card">
            <h3>Pending</h3>
            <p id="pending-count">...</p>
          </div>
          <div class="card">
            <h3>Resolved</h3>
            <p id="resolved-count">...</p>
          </div>
        </div>

        <!-- 📍 Case Summary by Location -->
        <h2>Case Summary (This Month)</h2>
        <div class="location-cards">
          <?php foreach ($groupedLocations as $loc => $cases): ?>
            <div class="summary-row" onclick="toggleDetails('<?= md5($loc) ?>')">
              <div class="summary-left"><?= htmlspecialchars($loc) ?></div>
              <div class="summary-right"><?= array_sum(array_column($cases, 'total')) ?> cases</div>
            </div>

            <div class="location-details" id="details-<?= md5($loc) ?>" style="display:none;">
              <table>
                <thead><tr><th>Type</th><th>Assigned To</th><th>Total</th></tr></thead>
                <tbody>
                  <?php foreach ($cases as $row): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['report_type']) ?></td>
                      <td><?= htmlspecialchars($row['pic_name'] ?? '—') ?></td>
                      <td><?= $row['total'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <canvas id="chart-<?= md5($loc) ?>"></canvas>
              <script>
                const ctx<?= md5($loc) ?> = document.getElementById('chart-<?= md5($loc) ?>').getContext('2d');
                new Chart(ctx<?= md5($loc) ?>, {
                  type: 'bar',
                  data: {
                    labels: [<?php foreach ($cases as $row): ?>'<?= $row['report_type'] ?>',<?php endforeach; ?>],
                    datasets: [{
                      label: 'Cases at <?= addslashes($loc) ?>',
                      data: [<?php foreach ($cases as $row): ?><?= $row['total'] ?>,<?php endforeach; ?>],
                      backgroundColor: '#6a0dad',
                      barThickness: 20
                    }]
                  },
                  options: { responsive: true, scales: { y: { beginAtZero: true } } }
                });
              </script>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- 📊 Yearly Case Distribution (by type + month) -->
        <h2>Yearly Case Distribution</h2>
        <canvas id="caseChart"></canvas>
        <script>
          const yearlyRaw = <?= json_encode($yearlyCases) ?>;
          const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
          const caseTypes = [...new Set(yearlyRaw.map(item => item.report_type))];

          const colorMap = {
            'Assault': '#FF6384',
            'Theft': '#36A2EB',
            'Harassment': '#FFCE56',
            'Fraud': '#4BC0C0',
            'Abuse': '#9966FF'
          };

          const datasets = caseTypes.map(type => {
            return {
              label: type,
              data: months.map((m,i) => {
                const found = yearlyRaw.find(item => item.report_type === type && item.month == (i+1));
                return found ? parseInt(found.total) : 0;
              }),
              backgroundColor: colorMap[type] || '#6a0dad'
            };
          });

          new Chart(document.getElementById('caseChart').getContext('2d'), {
            type: 'bar',
            data: { labels: months, datasets: datasets },
            options: {
              responsive: true,
              plugins: { title: { display: true, text: 'Cases per Type per Month (<?= date("Y") ?>)' } },
              scales: { y: { beginAtZero: true } }
            }
          });
        </script>
      </section>
    </main>
  </div>

  <!-- ✅ JavaScript to load stats -->
  <script>
    function loadStats() {
      fetch('../public/api/stats.php')
        .then(res => res.json())
        .then(data => {
          document.getElementById('total-count').textContent = data.total;
          document.getElementById('pending-count').textContent = data.pending;
          document.getElementById('resolved-count').textContent = data.resolved;
        })
        .catch(err => { console.error('Failed to load stats:', err); });
    }
    loadStats();
    setInterval(loadStats, 10000);

    function toggleDetails(id) {
      const el = document.getElementById('details-' + id);
      el.style.display = (el.style.display === 'none') ? 'block' : 'none';
    }
  </script>
</body>
</html>