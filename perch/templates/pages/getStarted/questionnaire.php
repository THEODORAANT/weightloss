<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/*
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
$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No referrer';

//echo "get step"; echo $_GET["step"];
//print_r($_SESSION);
if(isset($_GET["step"]) && $_GET["step"]=="startagain"){
$_SESSION['questionnaire'] = array();

     header("Location: /get-started/questionnaire?step=howold" ); // Redirect to the selected URL
             exit();
}else{
   if(!isset( $_SESSION['questionnaire']["reviewed"]) || (isset( $_SESSION['questionnaire']["reviewed"]) && $_SESSION['questionnaire']["reviewed"]!="InProcess")){
$_SESSION['questionnaire']["reviewed"] = "Pending";
}
$parts = explode('/', $previousPage);
$lastPart = end($parts);
//echo "lastPart".$lastPart;
if($lastPart =="consultation"){
 $_SESSION['questionnaire']["consultation"] = "agree";
}
if (isset($_POST['nextstep'])) {
$_SESSION['questionnaire']["confirmed"] = false;

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
//echo "POST **";
  // print_r($_POST);
          foreach ($_POST as $key => $value) {


          if($_SESSION['questionnaire']["gender"]=="Male" && isset($_SESSION['questionnaire']["pregnancy"])){
                 unset($_SESSION['questionnaire']["pregnancy"]);
          }
          if(isset( $_SESSION['questionnaire']["weightradio-unit"]) && $_SESSION['questionnaire']["weightradio-unit"]=="kg"){
          unset($_SESSION['questionnaire']["weight2"]);

          }
               if(isset( $_SESSION['questionnaire']["heightunit-radio"]) && $_SESSION['questionnaire']["heightunit-radio"]=="cm"){
                    unset($_SESSION['questionnaire']["height2"]);

                    }
           if($key!="nextstep"){
          if(is_array($value)){
             $_SESSION['questionnaire'][$key] = $value;

          }else{
            $_SESSION['questionnaire'][$key] = htmlspecialchars($value);

          }
        //  echo "_SESSION **";
           //  print_r( $_SESSION['questionnaire']);

               if(is_array($_SESSION['questionnaire'][$key])){

                      logAnswerChange($key,implode(", ", $_SESSION['questionnaire'][$key]));

                       }else{
              logAnswerChange($key, $_SESSION['questionnaire'][$key]);
              }

                echo "key";   echo $key;
                            if(isset($_SESSION['questionnaire']["reviewed"]) && $_SESSION['questionnaire']["reviewed"]=="InProcess"){

                      echo "reviewed";
                            echo $_SESSION['questionnaire'][$key];
                            print_r($_POST);
                         if(perch_member_requireNextStep($key,$_SESSION['questionnaire'][$key])){
                         echo "Location: /get-started/questionnaire?step=".$_POST['nextstep'];
                          //header("Location: /get-started/questionnaire?step=".$_POST['nextstep'] ); // Redirect to the selected URL
                                                    exit();
                         }else{
                         echo "else";
                         $_POST['nextstep']="plans";
                           //  header("Location: /get-started/review-questionnaire"); // Redirect to the selected URL
                                                                 exit();
                                                                 }


             }

//perch_log_mongo([ 'user_id' =>  $user_id,  'step' => $key,   'answer' =>  $_SESSION['questionnaire'][$key]]);
              }

                if($_POST['nextstep']=="plans"){
                 unset($_SESSION['questionnaire']["nextstep"]);
                               header("Location: /get-started/review-questionnaire"); // Redirect to the selected URL
                                     exit();
                        }else{
                          unset($_SESSION['questionnaire']["nextstep"]);
                         header("Location: /get-started/questionnaire?step=".$_POST['nextstep'] ); // Redirect to the selected URL
                           exit();
                           }



      }
}
*/


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

$cookieQuestionnaire = isset($_COOKIE['questionnaire'])
    ? json_decode($_COOKIE['questionnaire'], true)
    : [];
if (!isset($_SESSION['questionnaire']) || empty($_SESSION['questionnaire'])) {
    $_SESSION['questionnaire'] = $cookieQuestionnaire;
}

$previousPage = $_SERVER['HTTP_REFERER'] ?? 'No referrer';
$redirect=true;
if (isset($_GET['step']) && $_GET['step'] === 'startagain') {
    $_SESSION['questionnaire'] = [];
    setcookie('questionnaire', '', time()-3600, '/');
    header("Location: /get-started/questionnaire?step=howold");
    exit();
}

// Initialize 'reviewed' status if not set or not in progress
if (!isset($_SESSION['questionnaire']['reviewed']) || $_SESSION['questionnaire']['reviewed'] !== 'InProcess') {
    $_SESSION['questionnaire']['reviewed'] = 'Pending';
}

// Check if coming from consultation page
$lastPart = basename(parse_url($previousPage, PHP_URL_PATH));
if ($lastPart === 'consultation') {
    $_SESSION['questionnaire']['consultation'] = 'agree';
}

$requested_step = isset($_GET['step']) ? trim((string)$_GET['step']) : null;

if (isset($_POST['nextstep'])) {
    $_SESSION['questionnaire']['confirmed'] = false;

    $user_id = generateUUID();
    $current_step = $requested_step ?? '';
    $timestamp = time();

    $secret_key = 'theoloss1066';
    $data = "$user_id|$current_step|$timestamp";
    $hash = hash_hmac('sha256', $data, $secret_key);

    $_SESSION['step_hash'] = $hash;
    $_SESSION['step_data'] = [
        'user_id' => $user_id,
        'step' => $current_step,
        'timestamp' => $timestamp
    ];

    foreach ($_POST as $key => $value) {
        // Cleanup based on logic
        if (
            $_SESSION['questionnaire']['gender'] === 'Male' &&
            isset($_SESSION['questionnaire']['pregnancy'])
        ) {
            unset($_SESSION['questionnaire']['pregnancy']);
        }

        if (
            isset($_SESSION['questionnaire']['weightunit']) &&
            $_SESSION['questionnaire']['weightunit'] === 'kg'
        ) {
            unset($_SESSION['questionnaire']['weight2']);
        }

        if (
            isset($_SESSION['questionnaire']['heightunit']) &&
            $_SESSION['questionnaire']['heightunit'] == 'cm'
        ) {
            unset($_SESSION['questionnaire']['height2']);
        }
      /*  if ($key == 'diabetes') {
        $redirect=false;
        }*/

        if ($key !== 'nextstep') {
            if (is_array($value)) {
                $_SESSION['questionnaire'][$key] = $value;
            } else {
                $_SESSION['questionnaire'][$key] = htmlspecialchars($value);
            }

            // Log answer
            $loggedValue = is_array($_SESSION['questionnaire'][$key])
                ? implode(", ", $_SESSION['questionnaire'][$key])
                : $_SESSION['questionnaire'][$key];

            logAnswerChange($key, $loggedValue);

            // If reviewed in progress, check next step requirement
            if (
                isset($_SESSION['questionnaire']['reviewed']) &&
                $_SESSION['questionnaire']['reviewed'] === 'InProcess'
            ) {
                if (!perch_member_requireNextStep($key, $_SESSION['questionnaire'][$key])) {


                    $_POST['nextstep'] = 'plans';
                    //header("Location: /get-started/review-questionnaire");
                  //  exit();
                }
            }
        }
    }

    // Final redirect after loop
    unset($_SESSION['questionnaire']['nextstep']);
    $nextStep = $_POST['nextstep'];
    $redirectUrl = ($nextStep === 'plans')
        ? "/get-started/review-questionnaire"
        : "/get-started/questionnaire?step=$nextStep";
    setcookie('questionnaire', json_encode($_SESSION['questionnaire']), time()+3600, '/');
       /* if($_SESSION['questionnaire']['reviewed'] === 'InProcess' && $nextStep=="plans" ){
        exit;
        }else{
          header("Location: $redirectUrl");
            exit();
        }*/
                 // print_r($_SESSION['questionnaire']);
        if($redirect){
 header("Location: $redirectUrl");
            exit();
            }

}
if(isset($_SESSION['step_data']['user_id'])){
$_SESSION['questionnaire']['uuid']=$_SESSION['step_data']['user_id'];

}
setcookie('questionnaire', json_encode($_SESSION['questionnaire'] ?? []), time()+3600, '/');

?>


    <?php  // output the top of the page
    perch_layout('getStarted/header', [
        'page_title' => perch_page_title(true),
    ]);

        /* main navigation
        perch_pages_navigation([
            'levels'   => 1,
            'template' => 'main_nav.html',
        ]);*/

    ?>

<?php
PerchSystem::set_var('reviewed', $_SESSION['questionnaire']['reviewed']);
$back_links = [
    'howold' => '/get-started',
    'under18' => '/get-started/questionnaire?step=howold',
    '75over' => '/get-started/questionnaire?step=howold',
    '18to74' => '/get-started/questionnaire?step=howold',
    'Other' => '/get-started/questionnaire?step=18to74',
    'Mixed' => '/get-started/questionnaire?step=18to74',
    'asian' => '/get-started/questionnaire?step=18to74',
    'Black (African/Caribbean)' => '/get-started/questionnaire?step=18to74',
    'White' => '/get-started/questionnaire?step=18to74',
    'ethnicity' => '/get-started/questionnaire?step=18to74',
    'Female' => '/get-started/questionnaire?step=ethnicity',
    'pregnancy' => '/get-started/questionnaire?step=Female',
    'Male' => '/get-started/questionnaire?step=ethnicity',
    'weight' => '/get-started/questionnaire?step=ethnicity',
    'height' => '/get-started/questionnaire?step=weight',
    'diabetes' => '/get-started/questionnaire?step=height',
    'weight2' => '/get-started/questionnaire?step=diabetes',
    'conditions' => '/get-started/questionnaire?step=weight2',
    'bariatricoperation' => '/get-started/questionnaire?step=weight2',
    'history_pancreatitis' => '/get-started/questionnaire?step=weight2',
    'more_pancreatitis' => '/get-started/questionnaire?step=bariatricoperation',
    'thyroidoperation' => '/get-started/questionnaire?step=weight2',
    'more' => '/get-started/questionnaire?step=medical_conditions',
    'medical_conditions' => '/get-started/questionnaire?step=conditions',
    'list_any' => '/get-started/questionnaire?step=medical_conditions',
    'medications' => '/get-started/questionnaire?step=list_any_medications',
    'starting_wegovy' => '/get-started/questionnaire?step=medications',
    'dose_wegovy' => '/get-started/questionnaire?step=starting_wegovy',
    'recently_wegovy' => '/get-started/questionnaire?step=dose_wegovy',
    'continue_with_wegovy' => '/get-started/questionnaire?step=recently_wegovy',
    'effects_with_wegovy' => '/get-started/questionnaire?step=continue_with_wegovy',
    'wegovy_side_effects' => '/get-started/questionnaire?step=effects_with_wegovy',
    'medication_allergies' => '/get-started/questionnaire?step=wegovy_side_effects',
    'gp_informed' => '/get-started/questionnaire?step=medication_allergies',
    'gp_address' => '/get-started/questionnaire?step=gp_informed',
    'access_special_offers' => '/get-started/questionnaire?step=gp_address',
    'review-questionnaire' => '/get-started/questionnaire?step=access_special_offers'
];
//print_r($_SESSION['questionnaire']);

    if(isset($_SESSION['questionnaire']['more_pancreatitis'])){
$back_links['conditions']="/get-started/questionnaire?step=more_pancreatitis";
    }
        if(isset($_SESSION['questionnaire']['conditions2'])){
         if (in_array('mentalhealth',$_SESSION['questionnaire']['conditions2']) ) {
                            $back_links['more']="/get-started/questionnaire?step=conditions";
                      }

        }
          if(isset($_SESSION['questionnaire']['medications'])){
                 if (in_array('none',$_SESSION['questionnaire']['medications']) ) {
                                    $back_links['medication_allergies']="/get-started/questionnaire?step=medications";
                              }

                }

                  if(isset($_SESSION['questionnaire']['more_conditions'])){
                    $back_links['medical_conditions']="/get-started/questionnaire?step=more";
                  }
       if(isset($_SESSION['questionnaire']['medical_conditions']) && $_SESSION['questionnaire']['medical_conditions']=="no"){
        $back_links['medications']="/get-started/questionnaire?step=medical_conditions";
       }

           if(isset($_SESSION['questionnaire']['effects_with_wegovy']) && $_SESSION['questionnaire']['effects_with_wegovy']=="no"){ 
                    $back_links['medication_allergies']="/get-started/questionnaire?step=effects_with_wegovy";
          }

$questionnaire_structure = perch_member_questionnaire_structure('first-order');
$grouped_steps = [];
$step_sort_index = [];
$dependency_steps = [];
$ordered_step_keys = [];
$first_step = 'howold';

if (is_array($questionnaire_structure) && PerchUtil::count($questionnaire_structure)) {
    PerchSystem::set_var('questionnaire_structure_json', PerchUtil::json_safe_encode($questionnaire_structure));

    $questionnaire_dependencies = perch_member_questionnaire_dependencies('first-order');
    if (is_array($questionnaire_dependencies) && PerchUtil::count($questionnaire_dependencies)) {
        PerchSystem::set_var('questionnaire_dependencies_json', PerchUtil::json_safe_encode($questionnaire_dependencies));
    }

    foreach ($questionnaire_structure as $question) {
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
        usort($keys, function ($a, $b) use ($questionnaire_structure) {
            $definitionA = $questionnaire_structure[$a] ?? null;
            $definitionB = $questionnaire_structure[$b] ?? null;

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

$back_link = $back_links[$current_step] ?? '/get-started';

PerchSystem::set_var('step', $current_step);
PerchSystem::set_var('questionnaire_default_step', $first_step);
PerchSystem::set_var('questionnaire_current_step', $current_step);

$answers = $_SESSION['questionnaire'] ?? [];
PerchSystem::set_var('previousPage', $back_link);
PerchSystem::set_var('answers', $answers);
if (PerchUtil::count($answers)) {
    PerchSystem::set_var('questionnaire_answers_json', PerchUtil::json_safe_encode($answers));
}
 PerchSystem::set_vars($answers);
 perch_form('questionnaire.html');
?>




    <script>
        function redirectToPage() {
            const selectedValue = document.querySelector('input[name="option"]:checked').value;
            window.location.href = selectedValue; // Redirect to the selected value (URL)
        }
    </script>

    <?php
  perch_layout('getStarted/footer');?>
