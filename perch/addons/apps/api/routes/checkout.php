<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/lib/comms_sync.php';

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
 $cartID=$data["cartID"];
 $gateway=$data["gateway"];
 $payment_opts=[
                   'return_url' => "/api/complete_payment",
                   'cancel_url' => "/ap/error"
                 ];

 //check if memebr is for the correct cart
 $return=perch_shop_checkout_for_api($memberID,$cartID,$gateway, $payment_opts);

  if (empty($return)) {
    http_response_code(500);
       echo json_encode(["errors" => "Failed to create Order!"]);
}else{
 echo json_encode(["orderID" =>  $return]);

}

    ?>
