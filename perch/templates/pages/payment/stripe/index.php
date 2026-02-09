<?php //include('../../perch/runtime.php');?>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['questionnaire']) && isset($_COOKIE['questionnaire'])) {
    $_SESSION['questionnaire'] = json_decode($_COOKIE['questionnaire'], true) ?: [];
}
if (empty($_SESSION['questionnaire-reorder']) && isset($_COOKIE['questionnaire_reorder'])) {
    $_SESSION['questionnaire-reorder'] = json_decode($_COOKIE['questionnaire_reorder'], true) ?: [];
}
if (defined('PERCH_PATH')) {
    require_once PERCH_PATH . '/addons/apps/api/routes/lib/comms_sync.php';
}

      // your 'success' and 'failure' URLs

    $success_url= "https://".$_SERVER['HTTP_HOST']."/payment/success";
            if(isset($_SESSION['questionnaire-reorder']) && !empty($_SESSION['questionnaire-reorder'])){
    $success_url= "https://".$_SERVER['HTTP_HOST']."/client/order?id=".perch_shop_successful_order_id()."&success";


}
$cancel_url = "https://".$_SERVER['HTTP_HOST']."/payment/went/wrong";
//$success_url="/payment/success";
//$cancel_url ="/payment/went/wrong";

        $orderIdForQuestionnaire = perch_shop_successful_order_id();
        if (!$orderIdForQuestionnaire) {
            $ShopRuntime = PerchShop_Runtime::fetch();
            if ($ShopRuntime) {
                $ActiveOrder = $ShopRuntime->get_active_order();
                if ($ActiveOrder) {
                    $orderIdForQuestionnaire = $ActiveOrder->id();
                }
            }
        }

        if (empty($_SESSION['questionnaire_saved']) && $orderIdForQuestionnaire) {
            if (isset($_SESSION['questionnaire-reorder']) && !empty($_SESSION['questionnaire-reorder'])) {
                unset($_SESSION['questionnaire-reorder']['nextstep']);

                perch_member_add_questionnaire($_SESSION['questionnaire-reorder'], 're-order', $orderIdForQuestionnaire);
                $_SESSION['questionnaire_saved'] = true;
            }

            if (isset($_SESSION['questionnaire']) && !isset($_SESSION['questionnaire-reorder']["dose"])) {
                $userId = $_SESSION['step_data']['user_id'];
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

                $_SESSION['questionnaire']["multiple_answers"] = "No";

                if (isset($_SESSION['answer_log'])) {
                    $rawLog = is_array($_SESSION['answer_log']) ? $_SESSION['answer_log'] : [];

                    if (file_put_contents("{$logDir}/{$userId}_raw_log.json", json_encode([
                        'metadata' => $metadata,
                        'log' => $rawLog
                    ], JSON_PRETTY_PRINT)) === false) {
                        die("Failed to write log file.");
                    }

                    $summary = perch_members_summarise_answer_log($rawLog);
                    $grouped = $summary['grouped'];

                    if (!empty($summary['has_changes'])) {
                        $_SESSION['questionnaire']["multiple_answers"] = "Yes-" . "https://" . $_SERVER['HTTP_HOST'] . "/perch/addons/apps/perch_members/questionnaire_logs/?userId=" . $userId;
                    }
                    $_SESSION['questionnaire']["documents"] = "https://" . $_SERVER['HTTP_HOST'] . "/perch/addons/apps/perch_members/edit/?id=" . perch_member_get('id');
                    //print_r( $_SESSION['questionnaire']);
                    perch_member_add_questionnaire($_SESSION['questionnaire'], 'first-order', $orderIdForQuestionnaire);

                    if (file_put_contents("{$logDir}/{$userId}_grouped_log.json", json_encode([
                        'metadata' => $metadata,
                        'grouped_log' => $grouped
                    ], JSON_PRETTY_PRINT)) === false) {
                        die("Failed to write log file.");
                    }
                    // Optional: clear the session log
                    unset($_SESSION['answer_log']);
                }

                $_SESSION['questionnaire_saved'] = true;
            }
        }


        $order_complete = perch_shop_active_order_has_status(['paid', 'pending']);
        $redirect_to_success = $order_complete;
        if(!isset($_GET["pending"])){
           $result= perch_shop_complete_payment('stripe',[
                 'success_url' => $success_url,
                 'cancel_url'=> $cancel_url
               ]);
        }


        if (!$redirect_to_success && $result === true) {
            $redirect_to_success = true;
        }

        if ($redirect_to_success) {
    /*    if ($orderIdForQuestionnaire && function_exists('comms_sync_order')) {
            $memberId = null;
            if (function_exists('perch_member_logged_in') && perch_member_logged_in()) {
                $memberId = perch_member_get('id');
            }
            comms_sync_order((int)$orderIdForQuestionnaire, $memberId ? (int)$memberId : null);
        }*/

     // perch_shop_shipping_method_form();
            // $stripeform=true;
        if (isset($_SESSION['questionnaire-reorder']) && !empty($_SESSION['questionnaire-reorder'])) {
            $_SESSION['questionnaire-reorder'] = array();
            setcookie('questionnaire_reorder', '', time()-3600, '/');
        }
        if (isset($_SESSION['questionnaire']) && !empty($_SESSION['questionnaire'])) {
            $_SESSION['questionnaire'] = array();
            if (isset($_SESSION['questionnaire_question_order'])) {
                $_SESSION['questionnaire_question_order'] = [];
            }
            setcookie('questionnaire', '', time()-3600, '/');
        }
        unset($_SESSION['questionnaire_saved']);

                   //echo("<script>location.href = '".$success_url."';</script>");
                }else{
                   setcookie('questionnaire_reorder', json_encode($_SESSION['questionnaire-reorder'] ?? []), time()+3600, '/');
                   setcookie('questionnaire', json_encode($_SESSION['questionnaire'] ?? []), time()+3600, '/');
                   echo("<script>location.href = '".$cancel_url."';</script>");
                }
?>
