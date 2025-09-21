<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!perch_member_logged_in()) { exit;}
if (empty($_SESSION['questionnaire-reorder']) && isset($_COOKIE['questionnaire_reorder'])) {
    $_SESSION['questionnaire-reorder'] = json_decode($_COOKIE['questionnaire_reorder'], true) ?: [];
}
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
    $requested_step = isset($_GET['step']) ? trim((string)$_GET['step']) : null;

    if (isset($_POST['nextstep'])) {
    $user_id = generateUUID();
    $current_step = $requested_step ?? '';
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
    setcookie('questionnaire_reorder', json_encode($_SESSION['questionnaire-reorder'] ?? []), time()+3600, '/');
        perch_layout('getStarted/header', [
            'page_title' => perch_page_title(true),
        ]);
    ?>

        <div class="main_product">
            <div id="product-selection">
               <h2 class="text-center fw-bolder">Before we send you your next dose we have a few questions! </h2>
<?php
$reorder_structure = perch_member_questionnaire_structure('re-order');
$grouped_steps = [];
$step_sort_index = [];
$dependency_steps = [];
$ordered_step_keys = [];
$first_step = 'weight';

if (is_array($reorder_structure) && PerchUtil::count($reorder_structure)) {
    PerchSystem::set_var('questionnaire_structure_json', PerchUtil::json_safe_encode($reorder_structure));

    $reorder_dependencies = perch_member_questionnaire_dependencies('re-order');
    if (is_array($reorder_dependencies) && PerchUtil::count($reorder_dependencies)) {
        PerchSystem::set_var('questionnaire_dependencies_json', PerchUtil::json_safe_encode($reorder_dependencies));
    }

    foreach ($reorder_structure as $question) {
        $step = isset($question['step']) && $question['step'] !== '' ? $question['step'] : $question['key'];
        $question_sort = isset($question['sort']) ? (int)$question['sort'] : PHP_INT_MAX;

        if (!isset($grouped_steps[$step])) {
            $grouped_steps[$step] = [];
            $step_sort_index[$step] = $question_sort;
        } else {
            if ($question_sort < $step_sort_index[$step]) {
                $step_sort_index[$step] = $question_sort;
            }
        }

        $grouped_steps[$step][] = $question['key'];

        if (isset($question['dependencies']) && is_array($question['dependencies'])) {
            foreach ($question['dependencies'] as $dependency) {
                if (is_array($dependency) && !empty($dependency['step'])) {
                    $dependency_steps[] = $dependency['step'];
                }
            }
        }
    }

    foreach ($grouped_steps as $step => &$keys) {
        usort($keys, function ($a, $b) use ($reorder_structure) {
            $definitionA = $reorder_structure[$a] ?? null;
            $definitionB = $reorder_structure[$b] ?? null;

            $sortA = is_array($definitionA) && isset($definitionA['sort']) ? (int)$definitionA['sort'] : PHP_INT_MAX;
            $sortB = is_array($definitionB) && isset($definitionB['sort']) ? (int)$definitionB['sort'] : PHP_INT_MAX;

            if ($sortA === $sortB) {
                return strcmp((string)$a, (string)$b);
            }

            return $sortA <=> $sortB;
        });
    }
    unset($keys);

    if (PerchUtil::count($grouped_steps)) {
        if (PerchUtil::count($step_sort_index)) {
            asort($step_sort_index, SORT_NUMERIC);
            $ordered_steps = [];
            foreach (array_keys($step_sort_index) as $stepKey) {
                $ordered_steps[$stepKey] = $grouped_steps[$stepKey];
            }
            $grouped_steps = $ordered_steps;
        }

        PerchSystem::set_var('questionnaire_steps_json', PerchUtil::json_safe_encode($grouped_steps));
        $ordered_step_keys = array_keys($grouped_steps);
        if (count($ordered_step_keys)) {
            $first_step = $ordered_step_keys[0];
        }
    }
}

$allowed_steps = array_values(array_unique(array_merge($ordered_step_keys, $dependency_steps)));
if (!in_array($first_step, $allowed_steps, true)) {
    $allowed_steps[] = $first_step;
}

$current_step = $requested_step ?: $first_step;
if (!in_array($current_step, $allowed_steps, true)) {
    $current_step = $first_step;
}

PerchSystem::set_var('step', $current_step);
PerchSystem::set_var('questionnaire_default_step', $first_step);
PerchSystem::set_var('questionnaire_current_step', $current_step);

$reorder_answers = $_SESSION['questionnaire-reorder'] ?? [];
if (PerchUtil::count($reorder_answers)) {
    PerchSystem::set_var('questionnaire_answers_json', PerchUtil::json_safe_encode($reorder_answers));
}

PerchSystem::set_var('previousPage', '/client/re-order');

 perch_form('reorder-questionnaire.html');

            ?>





            </div></div>
        <?php
      perch_layout('getStarted/footer');?>
