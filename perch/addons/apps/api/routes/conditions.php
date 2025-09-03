<?php
    //include(__DIR__ .'/../../../../core/inc/api.php');
include(__DIR__ .'/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/../auth.php';

$data = json_decode(file_get_contents('php://input'), true);


$type = $data['type'] ?? 'first-order';
$questions=perch_member_questionsForQuestionnaire($type);
if($questions){
    echo json_encode(["questions" => $questions]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
}


?>
