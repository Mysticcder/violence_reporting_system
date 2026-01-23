<?php
require_once __DIR__ . '/../vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;

// Load credentials
$config   = require __DIR__ . '/../config/config.php';
$username = $config['username'];
$apiKey   = $config['apiKey'];

// Diagnostic tip: confirm credentials being used
echo "Using username: $username<br>";
echo "Using API key: " . substr($apiKey, 0, 6) . "...<br>";

$AT  = new AfricasTalking($username, $apiKey);
$sms = $AT->sms();

try {
    $result = $sms->send([
        'to'      => '+254728035910',
        'message' => 'Test message from LEHA system (Live Mode)'
    ]);
    echo "<pre>";
    print_r($result);
    echo "</pre>";
} catch (Exception $e) {
    echo "SMS Error: " . $e->getMessage();
}