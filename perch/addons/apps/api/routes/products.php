<?php
    //include(__DIR__ .'/../../../../core/inc/api.php');
include(__DIR__ .'/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/../auth.php';

$data = json_decode(file_get_contents('php://input'), true);


$category = $data['category'] ?? 'products/weight-loss';
$products=perch_shop_products(['category' => $category,'skip-template'=>true]);
if($products){
    echo json_encode(["products" => $products]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
}




?>
