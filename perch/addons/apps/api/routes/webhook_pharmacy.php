<?php
include(__DIR__ . '/../../../../core/runtime/runtime.php');

$secret = 'l0ss_ky_9harCY';
$rawData = file_get_contents('php://input');
$providedSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
$expectedSignature = hash_hmac('sha256', $rawData, $secret);

if (!hash_equals($expectedSignature, $providedSignature)) {
    http_response_code(403);
    exit('Invalid signature');
}

$payload = json_decode($rawData, true);
if (!is_array($payload)) {
    http_response_code(400);
    exit('Invalid JSON payload');
}

$orderNumber = $payload['orderNumber'] ?? $payload['order_number'] ?? $payload['pharmacy_orderID'] ?? null;
if (!$orderNumber) {
    http_response_code(400);
    exit('Missing order number');
}

$db = PerchDB::fetch();
$table = PERCH_DB_PREFIX . 'orders_match_pharmacy';
$existing = $db->get_row('SELECT * FROM ' . $table . ' WHERE pharmacy_orderID=' . $db->pdb($orderNumber) . ' LIMIT 1');

if (!$existing) {
    http_response_code(404);
    exit('Order not found');
}

$status = trim((string)($payload['status'] ?? $payload['orderStatus'] ?? $payload['pharmacyStatus'] ?? ''));
$statusText = trim((string)($payload['statusText'] ?? $payload['message'] ?? ''));
$dispatchDateRaw = $payload['dispatchDate'] ?? $payload['dispatched_at'] ?? $payload['dispatch_date'] ?? null;
$tracking = $payload['trackingNo'] ?? $payload['tracking_no'] ?? $payload['trackingNumber'] ?? $payload['tracking_number'] ?? $payload['trackingRef'] ?? $payload['tracking_reference'] ?? null;

$updates = [];

if ($status !== '') {
    $updates['status'] = $status;
    $updates['pharmacy_status'] = $status;
    $updates['order_status'] = $status;
    $updates['status_text'] = $statusText !== '' ? $statusText : $status;
}

if ($dispatchDateRaw) {
    $dispatchTimestamp = strtotime((string)$dispatchDateRaw);
    $dispatchDate = $dispatchTimestamp ? date('Y-m-d H:i:s', $dispatchTimestamp) : (string)$dispatchDateRaw;
    $updates['dispatchdate'] = $dispatchDate;
    $updates['dispatch_date'] = $dispatchDate;
    $updates['dispatched_at'] = $dispatchDate;
    $updates['dispatcheddate'] = $dispatchDate;
}

if ($tracking) {
    $tracking = trim((string)$tracking);
    $updates['trackingno'] = $tracking;
    $updates['tracking_no'] = $tracking;
    $updates['trackingnumber'] = $tracking;
    $updates['tracking_number'] = $tracking;
    $updates['trackingref'] = $tracking;
    $updates['tracking_reference'] = $tracking;
}

if (!empty($updates)) {
    $setParts = [];
    foreach ($updates as $column => $value) {
        $setParts[] = $column . '=' . $db->pdb($value);
    }
    $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $setParts) . ', updated_at=' . $db->pdb(date('Y-m-d H:i:s')) . ' WHERE pharmacy_orderID=' . $db->pdb($orderNumber) . ' LIMIT 1';
    $db->execute($sql);
}

$logFile = __DIR__ . '/webhook_log.txt';
$logEntry = '[' . date('Y-m-d H:i:s') . "]\n";
$logEntry .= 'Raw Data: ' . $rawData . "\n";
$logEntry .= 'Decoded: ' . print_r($payload, true) . "\n";
$logEntry .= 'Updates: ' . print_r($updates, true) . "\n\n";
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

http_response_code(200);
echo json_encode(['success' => true]);
