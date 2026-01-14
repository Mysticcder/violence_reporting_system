<?php
require_once __DIR__ . '/../vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;

$username = "leha_sms"; // ✅ your live app username
$apiKey   = "atsk_f766da881d9420d5efa68089770433c7a0da5a4e26b843641163fddf6357627fcf1a5eb1"; // ✅ from app settings

echo "<h3>Testing Africa's Talking Live Credentials</h3>";

try {
    $AT = new AfricasTalking($username, $apiKey);
    $sms = $AT->sms();
    $result = $sms->send([
        'to'      => '+254748959831', // ✅ real number
        'message' => 'Live test from LEHA system'
    ]);
    echo "<pre>";
    print_r($result);
    echo "</pre>";
} catch (Exception $e) {
    echo "<strong>SMS Error:</strong> " . $e->getMessage();
}