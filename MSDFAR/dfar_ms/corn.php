<?php
// Initialize a cURL session
$ch = curl_init();

// Target URL for marking expired records (local or configured environment)
$baseUrl = getenv('DFAR_BACKEND_URL') ?: 'http://127.0.0.1:8080';
$url = rtrim($baseUrl, '/') . '/site/expire';

echo "Executing expiration cron job at: $url\n";

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Execute the cURL request and get the response
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if ($response === false) {
    $error = curl_error($ch);
    echo "cURL Error ($httpCode): $error\n";
} else {
    if ($httpCode === 200) {
        echo "Successfully executed expiration job! (HTTP Status: $httpCode)\n";
    } else {
        echo "Executed expiration job with HTTP Status: $httpCode\n";
    }

    if (!empty($response)) {
        $data = json_decode($response, true);
        if ($data !== null) {
            print_r($data);
        } else {
            echo $response . "\n";
        }
    }
}

// Close the cURL session
curl_close($ch);