<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');
$secret = 'l0ss_ky_9harCY'; // Shared secret
$data = json_decode(file_get_contents('php://input'), true);
// Get raw POST data
$rawData = file_get_contents('php://input');

$providedSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? ''; // Custom header from sender

// Compute expected HMAC
$expectedSignature = hash_hmac('sha256', $rawData, $secret);


// Validate
if (!hash_equals($expectedSignature, $providedSignature)) {
    http_response_code(403);
    exit('Invalid signature');
}


// Set log file path
$logFile = __DIR__ . '/webhook_log.txt';


//perch_shop_update_pharmacy_order_webhook($data);
// Optionally decode JSON
//$decodedData = json_decode($rawData, true);

// Build log entry
$logEntry = "[" . date('Y-m-d H:i:s') . "]\n";
$logEntry .= "Raw Data: $rawData\n";
$logEntry .= "Decoded: " . print_r($data, true) . "\n\n";

// Append to log file
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

// Respond to sender
http_response_code(200);
echo "Webhook received.";
?>
