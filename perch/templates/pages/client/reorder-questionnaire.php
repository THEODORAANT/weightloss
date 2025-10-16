<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!perch_member_logged_in()) { exit;}
if (empty($_SESSION['questionnaire-reorder']) && isset($_COOKIE['questionnaire_reorder'])) {
    $_SESSION['questionnaire-reorder'] = json_decode($_COOKIE['questionnaire_reorder'], true) ?: [];
}
if (!isset($_SESSION['questionnaire-reorder']) || !is_array($_SESSION['questionnaire-reorder'])) {
    $_SESSION['questionnaire-reorder'] = [];
}

$memberGender = perch_member_get('gender');
$memberGender = is_string($memberGender) ? trim($memberGender) : '';
$memberIsFemale = (strcasecmp($memberGender, 'Female') === 0);

if (!$memberIsFemale && isset($_SESSION['questionnaire-reorder']['pregnancy_status'])) {
    unset($_SESSION['questionnaire-reorder']['pregnancy_status']);
}

$pregnancyStatus = '';
if (!empty($_SESSION['questionnaire-reorder']) && is_array($_SESSION['questionnaire-reorder'])) {
    $pregnancyStatus = $_SESSION['questionnaire-reorder']['pregnancy_status'] ?? '';
}

$nextStepAfterWeight = $memberIsFemale ? 'pregnancy-status' : 'side-effects';
$sideEffectsBackStep = $memberIsFemale ? 'pregnancy-status' : 'weight';
$memberGenderForTemplate = $memberIsFemale ? 'Female' : $memberGender;
    //  echo "session";
      //  print_r($_SESSION);
 function generateUUID() {
     return sprintf(
         '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
         mt_rand(0, 0xffff), mt_rand(0, 0xffff),
         mt_rand(0, 0xffff),
         mt_rand(0, 0x0fff) | 0x4000,
         mt_rand(0, 0x3fff) | 0x8000,
         mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
     );
 }
    if (isset($_POST['nextstep'])) {
    $user_id = generateUUID();
    $current_step = $_GET["step"];
    $timestamp = time();
       // Secret key (keep this safe, use env file ideally)
                        $secret_key = 'theoloss1066';

                        // Create a unique data string
                        $data = $user_id . '|' . $current_step . '|' . $timestamp;

                        // Create hash using HMAC with SHA256
                        $hash = hash_hmac('sha256', $data, $secret_key);

                        // Store the step hash in session
                        $_SESSION['step_hash'] = $hash;

                        // Optional: Store the raw data if needed for verification
                        $_SESSION['step_data'] = [
                            'user_id' => $user_id,
                            'step' => $current_step,
                            'timestamp' => $timestamp
                        ];
                        $_SESSION['questionnaire-reorder']['uuid'] = $user_id;
         foreach ($_POST as $key => $value) {
           if(is_array($value)){
                      $_SESSION['questionnaire-reorder'][$key] = $value;

                   }else{
                     $_SESSION['questionnaire-reorder'][$key] = htmlspecialchars($value);

                   }
                     logAnswerChange($key, $_SESSION['questionnaire-reorder'][$key],"reorder");
         }
        setcookie('questionnaire_reorder', json_encode($_SESSION['questionnaire-reorder']), time()+3600, '/');
        //print_r($_SESSION['reorder_answer_log']);

     if($_POST['nextstep']=="cart"){

                    $userId=$_SESSION['step_data']['user_id'];
                       $metadata = [
                           'user_id'    => $userId,
                           'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                           'registered' => date('Y-m-d H:i:s')
                       ];
                      // echo "test";print_r( $metadata);
                       $logDir = '/var/www/html/logs/reorder';
                    if (!is_dir($logDir)) {
                           mkdir($logDir, 0755, true);
                       }

                   if (!is_dir($logDir) && !mkdir($logDir, 0755, true)) {
                       die("Failed to create log directory: $logDir");
                   }

                         if(isset($_SESSION['reorder_answer_log'])){
                           $rawLog = $_SESSION['reorder_answer_log'];

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
                           $_SESSION['questionnaire-reorder']["multiple_answers"]="No";
                           if($multiple_answers){
                           $_SESSION['questionnaire-reorder']["multiple_answers"]="Yes-"."https://".$_SERVER['HTTP_HOST']."/perch/addons/apps/perch_members/questionnaire_logs/?userId=".$userId;
                           //.$_SERVER['HTTP_HOST']."/logs/{$userId}_grouped_log.json";
                           }
                          // $_SESSION['questionnaire-reorder']["documents"]="https://".$_SERVER['HTTP_HOST']."/perch/addons/apps/perch_members/edit/?id=".perch_member_get('id');
                           //print_r( $_SESSION['questionnaire']);
//  echo "test grouped";print_r( $grouped);echo "{$logDir}/{$userId}_grouped_log.json";
                            if (file_put_contents("{$logDir}/{$userId}_grouped_log.json", json_encode([
                                   'metadata' => $metadata,
                                   'grouped_log' => $grouped
                                   ], JSON_PRETTY_PRINT)) === false) {
                                       die("Failed to write log file.");
                                   }
                            // Optional: clear the session log
                            unset($_SESSION['reorder_answer_log']);
                            }

                            header("Location: /order/cart"); // Redirect to the selected URL
                                              exit();
                                 }else{
                                header("Location: /client/questionnaire-re-order?step=".$_POST['nextstep'] ); // Redirect to the selected URL
                                    exit();
                                    }

    }
    if (isset($_SESSION['step_data']['user_id'])) {
        $_SESSION['questionnaire-reorder']['uuid'] = $_SESSION['step_data']['user_id'];
    }
    setcookie('questionnaire_reorder', json_encode($_SESSION['questionnaire-reorder'] ?? []), time()+3600, '/');
        perch_layout('getStarted/header', [
            'page_title' => perch_page_title(true),
        ]);
    ?>

        <div class="main_product">
            <div id="product-selection">
               <h2 class="text-center fw-bolder">Before we send you your next dose we have a few questions! </h2>
    <?php
PerchSystem::set_var('member_gender', $memberGenderForTemplate);
PerchSystem::set_var('pregnancy_status', $pregnancyStatus);
PerchSystem::set_var('next_step_after_weight', $nextStepAfterWeight);
PerchSystem::set_var('side_effects_back_step', $sideEffectsBackStep);
if(isset( $_GET["step"])){
    PerchSystem::set_var('step', $_GET["step"]);
}

 perch_form('reorder-questionnaire.html');

            ?>





            </div></div>
        <?php
      perch_layout('getStarted/footer');?>
