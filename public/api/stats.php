<?php
require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) AS total,
            SUM(status = 'PENDING') AS pending,
            SUM(status = 'RESOLVED') AS resolved
        FROM reports
    ");

    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'total'    => (int)$stats['total'],
        'pending'  => (int)$stats['pending'],
        'resolved' => (int)$stats['resolved']
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load stats']);
}