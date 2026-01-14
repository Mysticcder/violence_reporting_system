<?php
$dsn  = 'mysql:host=localhost;dbname=violence_reporting_db;charset=utf8mb4';
$user = 'root';   // XAMPP default
$pass = '';       // XAMPP default empty
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  http_response_code(500);
  exit('Database connection failed: ' . $e->getMessage()); // ✅ Shows the actual error
}