<?php
// config/db.php

// Load environment variables from .env
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    http_response_code(500);
    exit('.env file not found. Please create one in the project root.');
}

$env = parse_ini_file($envPath, false, INI_SCANNER_RAW);

// Validate required keys
$requiredKeys = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($requiredKeys as $key) {
    if (!isset($env[$key])) {
        http_response_code(500);
        exit("Missing required .env key: $key");
    }
}

// Build DSN from .env values
$dsn  = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}