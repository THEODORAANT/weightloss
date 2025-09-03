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
$payment_method=$data['payment_method'];
if (empty($memberID) || empty($data['order_id']) ) {
    http_response_code(500);
       echo json_encode(["errors" => "gateway error"]);
}else{
$PaymentIntent=perch_shop_createPaymentIntent_for_api($memberID,$payment_method,$data['order_id']);

 echo json_encode(["PaymentIntent" =>  $PaymentIntent]);

}
 ?>
