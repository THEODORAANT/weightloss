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

                   echo("<script>location.href = '".$success_url."';</script>");
                }else{
                   setcookie('questionnaire_reorder', json_encode($_SESSION['questionnaire-reorder'] ?? []), time()+3600, '/');
                   setcookie('questionnaire', json_encode($_SESSION['questionnaire'] ?? []), time()+3600, '/');
                   echo("<script>location.href = '".$cancel_url."';</script>");
                }
?>
