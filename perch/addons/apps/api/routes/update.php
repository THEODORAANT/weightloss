<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');


require_once __DIR__ . '/../auth.php';

//require_once __DIR__ . '/../../perch_members/PerchMembers_Member.class.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (perch_member_api_update_profile($payload['user_id'], $data)) {
    echo json_encode(["success" => true]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Update failed"]);
}
