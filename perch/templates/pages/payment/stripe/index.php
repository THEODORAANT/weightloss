<?php //include('../../perch/runtime.php');?>

<?php

      // your 'success' and 'failure' URLs
    $success_url= "https://".$_SERVER['HTTP_HOST']."/payment/success";
   $cancel_url = "https://".$_SERVER['HTTP_HOST']."/payment/went/wrong";
//$success_url="/payment/success";
//$cancel_url ="/payment/went/wrong";
     $result= perch_shop_complete_payment('stripe',[
         'success_url' => $success_url,
         'cancel_url'=> $cancel_url
       ]);


	if ($result) {
	if(isset($_SESSION['questionnaire-reorder']) && !empty($_SESSION['questionnaire-reorder'])){
	unset($_SESSION['questionnaire-reorder']['nextstep']);
    perch_member_add_questionnaire($_SESSION['questionnaire-reorder'],'re-order');
    $_SESSION['questionnaire-reorder'] = array();
    }
     // perch_shop_shipping_method_form();
            // $stripeform=true;
            if(isset($_SESSION['questionnaire']) && !isset($_SESSION['questionnaire-reorder']["dose"])){

        $userId=$_SESSION['step_data']['user_id'];
        $metadata = [
            'user_id'    => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'registered' => date('Y-m-d H:i:s')
        ];
        $logDir = '/var/www/html/logs';
     if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

    if (!is_dir($logDir) && !mkdir($logDir, 0755, true)) {
        die("Failed to create log directory: $logDir");
    }

          if(isset($_SESSION['answer_log'])){
            $rawLog = $_SESSION['answer_log'];

            if (file_put_contents("{$logDir}/{$userId}_raw_log.json", json_encode([
                'metadata' => $metadata,
                'log' => $rawLog
            ], JSON_PRETTY_PRINT)) === false) {
                die("Failed to write log file.");
            }


            // Step 5: Save grouped log
            $grouped = [];
            $multiple_answers=false;
            foreach ($rawLog as $entry) {
                $question = $entry['question'];
                unset($entry['question']);
                $grouped[$question][] = $entry;
               // echo $question ;echo count( $grouped[$question]);
                if(count( $grouped[$question])>=2){
    $multiple_answers=true;
                }
            }
            $_SESSION['questionnaire']["multiple_answers"]="No";
            if($multiple_answers){
            $_SESSION['questionnaire']["multiple_answers"]="Yes-"."https://".$_SERVER['HTTP_HOST']."/perch/addons/apps/perch_members/questionnaire_logs/?userId=".$userId;
            //.$_SERVER['HTTP_HOST']."/logs/{$userId}_grouped_log.json";
            }
            $_SESSION['questionnaire']["documents"]="https://".$_SERVER['HTTP_HOST']."/perch/addons/apps/perch_members/edit/?id=".perch_member_get('id');
            //print_r( $_SESSION['questionnaire']);
             perch_member_add_questionnaire($_SESSION['questionnaire'],'first-order');

             if (file_put_contents("{$logDir}/{$userId}_grouped_log.json", json_encode([
                    'metadata' => $metadata,
                    'grouped_log' => $grouped
                    ], JSON_PRETTY_PRINT)) === false) {
                        die("Failed to write log file.");
                    }
             // Optional: clear the session log
             unset($_SESSION['answer_log']);
             }

              $_SESSION['questionnaire'] = array();
            }

		   echo("<script>location.href = '".$success_url."';</script>");
		}else{
		   echo("<script>location.href = '".$cancel_url."';</script>");
		}
?>
