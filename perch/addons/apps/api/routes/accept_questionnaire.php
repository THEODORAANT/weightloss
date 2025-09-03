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
     if(isset($data['questionnaireid']) && isset($data['accepted'])){
     $questionnaireid=$data['questionnaireid'];
          perch_member_accept_questionnaire_api($memberid,$questionnaireid,$data['accepted']);
          echo json_encode(["success" => true]);
     }else{
        http_response_code(401);
      echo json_encode(["error" => "Missing arguments"]);
             exit;
     }

    ?>
