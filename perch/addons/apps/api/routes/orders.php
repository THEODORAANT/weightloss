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

$results = perch_shop_orders([
    "user_id" => $payload['user_id'],
    "api" => true,
    "skip-template" => true,
    "sort" => "orderCreated",
    "sort-order" => "DESC"
]);

if (is_array($results)) {
    $orderIDs = [];
    foreach ($results as $order) {
        if (isset($order['orderID'])) {
            $orderIDs[] = (int)$order['orderID'];
        }
    }

    $pharmacyLookup = get_pharmacy_lookup($orderIDs);

    foreach ($results as &$order) {
        $orderId = isset($order['orderID']) ? (int)$order['orderID'] : null;
        if ($orderId !== null && isset($pharmacyLookup[$orderId])) {
            $order['pharmacy'] = $pharmacyLookup[$orderId];
        }
    }
    unset($order);
}

echo json_encode($results);
