<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../auth.php';

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


$data = perch_shop_order($id,['skip-template'=>true]);

if ($data){// && $data['memberID'] == $payload['user_id']) {
    echo json_encode($data);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Order not found or access denied"]);
}
