?<?php
require_once __DIR__ . '/../vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;

$username = "leha_sms"; // ✅ Your live app username
$apiKey   = "atsk_241d30fa8d0a5ecb7a8bd531975bf9124f18535aeab4c33f8f5360696ef0004cc872dbb7"; // ✅ From app settings

$AT  = new AfricasTalking($username, $apiKey);
$sms = $AT->sms();

try {
    $result = $sms->send([
        'to'      => '+254748959831', 
        'message' => 'Test message from LEHA system'
    ]);
    echo "<pre>";
    print_r($result);
    echo "</pre>";
} catch (Exception $e) {
    echo "SMS Error: " . $e->getMessage();
}