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
 $member_id=$payload['user_id'];
$product=$data["product"];
 $qty=$data["qty"];
 $cart_id=perch_shop_add_to_cart_for_api($member_id,$product, $qty);
  if (!empty($errors)) {
    http_response_code(500);
       echo json_encode(["errors" => $errors]);
}else{
 echo json_encode(["cart_id" =>  $cart_id]);

}

?>
