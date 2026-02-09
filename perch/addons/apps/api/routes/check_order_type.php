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



if (isset($payload['user_id'])) {
$details = customer_has_paid_order($payload['user_id']);
    echo json_encode(["is_reorder"=>$details]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Member not found"]);
}
