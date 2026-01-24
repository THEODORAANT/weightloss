<?php

  //  include(__DIR__ .'/../../../../core/inc/api.php');

//require_once '/perch/addons/apps/perch_members/PerchMembers_Member.class.php';
//require_once __DIR__ . '/../../perch_members/PerchMembers_Member.class.php';
include(__DIR__ .'/../../../../core/runtime/runtime.php');

$data = json_decode(file_get_contents('php://input'), true);

$required = ['email', 'password', 'first_name', 'last_name','gender'];
//'dob','phone','shipping_address_1','postcode','country','shipping_country','city'];
/*foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "$field is required"]);
        exit;
    }
}*/

//$Members = new PerchMembers_Members();
$data["country"]=236;
$data["device"]="app";
//print_r($data);
$memberID=perch_member_api_register($data);
if ($memberID) {
    if(perch_shop_register_customer_from_api($memberID,$data)){
      echo json_encode(["success" => true]);
    }

} else {
    http_response_code(500);
    echo json_encode(["error" => "Registration failed"]);
}

