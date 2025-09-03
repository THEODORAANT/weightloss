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
    $data = json_decode(file_get_contents('php://input'), true);
 $memberID=$payload['user_id'];
 if (empty($memberID)  ) {
     http_response_code(500);
        echo json_encode(["errors" => "error"]);
 }else{
 $gateway=$data['gateway'];
 $auth=perch_shop_authPayment_for_api( $memberID,$gateway);

  echo json_encode(["auth" =>  $auth]);

 }
