<?php
    //include(__DIR__ .'/../../../../core/inc/api.php');
include(__DIR__ .'/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/../auth.php';
//require_once __DIR__ . '/../../perch_members/PerchMembers_Auth.class.php';

$data = json_decode(file_get_contents('php://input'), true);


$email = $data['email'] ?? '';
$password = $data['password'] ?? '';


$user_row=perch_member_api_login($data);

if ($user_row!=null && count($user_row)){ //$auth->login($email, $password)) {
   // $member = $auth->get_logged_in_member();
  $member_id = $user_row['memberID'];
  $member_email = $user_row['memberEmail'];

   $token = generate_token($member_id, $member_email);
 // $token = perch_member_get('token');

    echo json_encode(["token" => $token]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
}

