<?php
    include(__DIR__ .'/../../../../core/runtime/runtime.php');

    require_once __DIR__ . '/../auth.php';

    $token = get_bearer_token();
    $payload = verify_token($token);
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$payload) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }
    $memberid=$payload['user_id'];
   $status=perch_member_check_questionnaire_status_for_member($memberid,'v1');
          echo json_encode(["result" => $status]);
    ?>
