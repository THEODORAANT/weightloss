<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/lib/pharmacy.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing order ID"]);
    exit;
}


$orders = perch_shop_orders([
    "user_id" => $payload['user_id'],
    "api" => true,
    "skip-template" => true,
    "orderID" => $id,
    "limit" => 1,
]);

$order = null;
if (is_array($orders) && PerchUtil::count($orders)) {
    $order = $orders[0];
}

if ($order) {
    if (isset($order['orderID'])) {
        $pharmacyLookup = get_pharmacy_lookup([(int)$order['orderID']]);
        if (isset($pharmacyLookup[(int)$order['orderID']])) {
            $order['pharmacy'] = $pharmacyLookup[(int)$order['orderID']];
        }
    }

    echo json_encode($order);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Order not found or access denied"]);
}
