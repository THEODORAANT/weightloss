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
if (empty($memberID) || empty($data['paymentIntentId']) ) {
    http_response_code(500);
       echo json_encode(["errors" => "paymentIntentId error"]);
}else{
$paymentIntentId=$data["paymentIntentId"];
    $paymentIntent =perch_shop_confirmPayment_for_api($memberID,$paymentIntentId);
     $status = $paymentIntent['status'] ?? 'unknown';

     if ($status === 'succeeded') {
         $Orders = new PerchShop_Orders(new PerchAPI(1.0, 'perch_shop'));
         $Order = $Orders->get_one_by('orderGatewayRef', $paymentIntentId);
         if ($Order) {
             comms_sync_order((int)$Order->id(), (int)$memberID);
         }
     }

            echo json_encode(["status" => $status]);
            }

 ?>
