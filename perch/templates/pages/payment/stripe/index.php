<?php //include('../../perch/runtime.php');?>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['questionnaire']) && isset($_COOKIE['questionnaire'])) {
    $_SESSION['questionnaire'] = json_decode($_COOKIE['questionnaire'], true) ?: [];
}
if (empty($_SESSION['questionnaire-reorder']) && isset($_COOKIE['questionnaire_reorder'])) {
    $_SESSION['questionnaire-reorder'] = json_decode($_COOKIE['questionnaire_reorder'], true) ?: [];
}

      // your 'success' and 'failure' URLs
    $success_url= "https://".$_SERVER['HTTP_HOST']."/payment/success";
   $cancel_url = "https://".$_SERVER['HTTP_HOST']."/payment/went/wrong";
//$success_url="/payment/success";
//$cancel_url ="/payment/went/wrong";
     $result= perch_shop_complete_payment('stripe',[
         'success_url' => $success_url,
         'cancel_url'=> $cancel_url
       ]);


        $ShopRuntime = PerchShop_Runtime::fetch();
        $ActiveOrder = $ShopRuntime->get_active_order();
        $order_status = null;
        if ($ActiveOrder) {
            $order_status = strtolower((string)$ActiveOrder->orderStatus());
        }

        $successful_statuses = ['paid', 'pending'];
        $redirect_to_success = in_array($order_status, $successful_statuses, true);

        if (!$redirect_to_success && $result === true) {
            $redirect_to_success = true;
        }

        if ($redirect_to_success) {
        if(isset($_SESSION['questionnaire-reorder']) && !empty($_SESSION['questionnaire-reorder'])){
        unset($_SESSION['questionnaire-reorder']['nextstep']);
    perch_member_add_questionnaire($_SESSION['questionnaire-reorder'],'re-order');
    $_SESSION['questionnaire-reorder'] = array();
    setcookie('questionnaire_reorder', '', time()-3600, '/');
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
            setcookie('questionnaire', '', time()-3600, '/');
            }

                   echo("<script>location.href = '".$success_url."';</script>");
                }else{
                   setcookie('questionnaire_reorder', json_encode($_SESSION['questionnaire-reorder'] ?? []), time()+3600, '/');
                   setcookie('questionnaire', json_encode($_SESSION['questionnaire'] ?? []), time()+3600, '/');
                   echo("<script>location.href = '".$cancel_url."';</script>");
                }
?>
