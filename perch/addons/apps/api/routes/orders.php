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


  $results = perch_shop_orders(["user_id"=>$payload['user_id'],"api"=>true,"skip-template"=>true,"sort"=>"orderCreated","sort-order"=>"DESC"]);
echo json_encode($results);
