<?php
function sendWhatsAppMessage($to, $message) {
    $env = parse_ini_file(__DIR__ . '/.env', false, INI_SCANNER_RAW);
    if ($env === false) {
        return "❌ Failed to load .env file. Make sure it's in the same directory.";
    }

    $url = "https://graph.facebook.com/{$env['META_API_VERSION']}/{$env['META_PHONE_NUMBER_ID']}/messages";

    $payload = [
        "messaging_product" => "whatsapp",
        "to" => $to,
        "type" => "text",
        "text" => ["body" => $message]
    ];

    $headers = [
        "Authorization: Bearer {$env['META_ACCESS_TOKEN']}",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $response = "cURL Error: " . curl_error($ch);
    }

    curl_close($ch);
    return $response;
}

/**
 * Send an approved WhatsApp template message
 */
function sendWhatsAppTemplate($to, $templateName, $params = []) {
    $env = parse_ini_file(__DIR__ . '/.env', false, INI_SCANNER_RAW);
    if ($env === false) {
        return "❌ Failed to load .env file. Make sure it's in the same directory.";
    }

    $url = "https://graph.facebook.com/{$env['META_API_VERSION']}/{$env['META_PHONE_NUMBER_ID']}/messages";

    $payload = [
        "messaging_product" => "whatsapp",
        "to" => $to,
        "type" => "template",
        "template" => [
            "name" => $templateName,
            "language" => ["code" => "en_US"],
            "components" => [
                [
                    "type" => "body",
                    "parameters" => array_map(fn($text) => ["type" => "text", "text" => $text], $params)
                ]
            ]
        ]
    ];

    $headers = [
        "Authorization: Bearer {$env['META_ACCESS_TOKEN']}",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $response = "cURL Error: " . curl_error($ch);
    }

    curl_close($ch);
    return $response;
}