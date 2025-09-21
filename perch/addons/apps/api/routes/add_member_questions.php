<?php
    include(__DIR__ .'/../../../../core/runtime/runtime.php');

    require_once __DIR__ . '/../auth.php';

    $token = get_bearer_token();
    $payload = verify_token($token);
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$payload) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }
    $types = array("first-order","re-order");
    if(!in_array($data['type'],$types)){
       http_response_code(500);
          echo json_encode(["error" => "Invalid type"]);
           exit;
      }
    $memberid=$payload['user_id'];
    if($data['type']=="first-order"){
        $errors = perch_member_validateQuestionnaire($data['questionnaire']);

    }else{
    $errors ="";

    }
  /* if (!empty($errors)) {
    http_response_code(500);
    echo json_encode(["errors" => $errors]);
   }else{*/
   $data['questionnaire']['uuid']= sprintf(
                         '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                         mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                         mt_rand(0, 0xffff),
                         mt_rand(0, 0x0fff) | 0x4000,
                         mt_rand(0, 0x3fff) | 0x8000,
                         mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                     );
       if(isset($data['questionnaire']) && !empty($data['questionnaire'])){
         $data['questionnaire']["documents"]="https://getweightloss.co.uk/perch/addons/apps/perch_members/edit/?id=".$memberid;

     $orderID = isset($data['order_id']) ? (int)$data['order_id'] : null;
     if ($orderID !== null && isset($data['questionnaire']['order_id'])) {
         unset($data['questionnaire']['order_id']);
     }
     $id= perch_member_add_questionnaire_api($memberid,$data['questionnaire'],$data['type'],$orderID);
      echo json_encode(["success" => true,"questionnaireID"=>$id]);
       }
  // }


    ?>
