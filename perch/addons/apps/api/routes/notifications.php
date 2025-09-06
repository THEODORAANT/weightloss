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

$notifications = perch_member_notifications($payload['user_id']);

if ($notifications) {
    echo json_encode($notifications);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Notifications not found"]);
}

