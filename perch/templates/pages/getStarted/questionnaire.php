<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once dirname(__DIR__, 3) . '/addons/apps/perch_members/questionnaire_medication_helpers.php';

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
}*/
if (!function_exists('formatMeasurementAnswer')) {
    function formatMeasurementAnswer($value, $unitRaw, $secondaryValue = null)
    {
        if ($value === '' || $unitRaw === null || $unitRaw === '') {
            return $value;
        }

        $formatted = $value;
        $units = explode('-', (string)$unitRaw);
        $primaryUnit = trim($units[0] ?? '');

        if ($primaryUnit !== '') {
            $formatted .= ' ' . $primaryUnit;
        }

        if (!empty($units[1])) {
            $secondaryValue = trim((string)($secondaryValue ?? ''));
            $secondaryUnit = trim($units[1]);
            if ($secondaryValue !== '' && $secondaryUnit !== '') {
                $formatted .= ' ' . $secondaryValue . ' ' . $secondaryUnit;
            }
        }

        return $formatted;
    }
}
/*
$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No referrer';

//echo "get step"; echo $_GET["step"];
//print_r($_SESSION);
if (isset($_GET["step"]) && $_GET["step"] == "startagain") {
    $_SESSION['questionnaire'] = array();

    header("Location: /get-started/questionnaire?step=howold"); // Redirect to the selected URL
    exit();
} else {
    if (!isset($_SESSION['questionnaire']["reviewed"]) || (isset($_SESSION['questionnaire']["reviewed"]) && $_SESSION['questionnaire']["reviewed"] != "InProcess")) {
        $_SESSION['questionnaire']["reviewed"] = "Pending";
    }
    $parts = explode('/', $previousPage);
    $lastPart = end($parts);
    //echo "lastPart".$lastPart;
    if ($lastPart == "consultation") {
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


            if ($_SESSION['questionnaire']["gender"] == "Male" && isset($_SESSION['questionnaire']["pregnancy"])) {
                unset($_SESSION['questionnaire']["pregnancy"]);
            }
            if (isset($_SESSION['questionnaire']["weightradio-unit"]) && $_SESSION['questionnaire']["weightradio-unit"] == "kg") {
                unset($_SESSION['questionnaire']["weight2"]);
            }
            if (isset($_SESSION['questionnaire']["heightunit-radio"]) && $_SESSION['questionnaire']["heightunit-radio"] == "cm") {
                unset($_SESSION['questionnaire']["height2"]);
            }
            if ($key != "nextstep") {
                if (is_array($value)) {
                    $_SESSION['questionnaire'][$key] = $value;
                } else {
                    $_SESSION['questionnaire'][$key] = htmlspecialchars($value);
                }
                //  echo "_SESSION **";
                //  print_r( $_SESSION['questionnaire']);

                if (is_array($_SESSION['questionnaire'][$key])) {

                    logAnswerChange($key, implode(", ", $_SESSION['questionnaire'][$key]));
                } else {
                    logAnswerChange($key, $_SESSION['questionnaire'][$key]);
                }

                echo "key";
                echo $key;
                if (isset($_SESSION['questionnaire']["reviewed"]) && $_SESSION['questionnaire']["reviewed"] == "InProcess") {

                    echo "reviewed";
                    echo $_SESSION['questionnaire'][$key];
                    print_r($_POST);
                    if (perch_member_requireNextStep($key, $_SESSION['questionnaire'][$key])) {
                        echo "Location: /get-started/questionnaire?step=" . $_POST['nextstep'];
                        //header("Location: /get-started/questionnaire?step=".$_POST['nextstep'] ); // Redirect to the selected URL
                        exit();
                    } else {
                        echo "else";
                        $_POST['nextstep'] = "plans";
                        //  header("Location: /get-started/review-questionnaire"); // Redirect to the selected URL
                        exit();
                    }
                }

                //perch_log_mongo([ 'user_id' =>  $user_id,  'step' => $key,   'answer' =>  $_SESSION['questionnaire'][$key]]);
            }

            if ($_POST['nextstep'] == "plans") {
                unset($_SESSION['questionnaire']["nextstep"]);
                header("Location: /get-started/review-questionnaire"); // Redirect to the selected URL
                exit();
            } else {
                unset($_SESSION['questionnaire']["nextstep"]);
                header("Location: /get-started/questionnaire?step=" . $_POST['nextstep']); // Redirect to the selected URL
                exit();
            }
        }
    }
}
*/


function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

$cookieQuestionnaire = isset($_COOKIE['questionnaire'])
    ? json_decode($_COOKIE['questionnaire'], true)
    : [];
if (!isset($_SESSION['questionnaire']) || empty($_SESSION['questionnaire'])) {
    $_SESSION['questionnaire'] = $cookieQuestionnaire;
}
if (!isset($_SESSION['questionnaire_question_order']) || !is_array($_SESSION['questionnaire_question_order'])) {
    $_SESSION['questionnaire_question_order'] = [];
}

$previousPage = $_SERVER['HTTP_REFERER'] ?? 'No referrer';
$redirect = true;
if (isset($_GET['step']) && $_GET['step'] === 'startagain') {
    $_SESSION['questionnaire'] = [];
    $_SESSION['questionnaire_question_order'] = [];
    setcookie('questionnaire', '', time() - 3600, '/');
    header("Location: /get-started/questionnaire?step=howold");
    exit();
}

// Initialize 'reviewed' status if not set or not in progress
if (!isset($_SESSION['questionnaire']['reviewed']) || $_SESSION['questionnaire']['reviewed'] !== 'InProcess') {
    $_SESSION['questionnaire']['reviewed'] = 'Pending';
}
$_SESSION['question_order'] = 0;
// Check if coming from consultation page
$lastPart = basename(parse_url($previousPage, PHP_URL_PATH));
if ($lastPart === 'consultation') {
    $_SESSION['questionnaire']['consultation'] = 'agree';
}

if (isset($_POST['nextstep'])) {
    if (array_key_exists('email_address', $_POST) && trim((string)$_POST['email_address']) === '') {
        $_POST['email_address'] = 'no-email-added';
    }

    $_SESSION['questionnaire']['confirmed'] = false;

    $user_id = generateUUID();
    $current_step = $_GET['step'] ?? '';
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
    $_SESSION['question_order']++;
    foreach ($_POST as $key => $value) {
        // Cleanup based on logic
        if (
            isset($_SESSION['questionnaire']['gender']) &&
            $_SESSION['questionnaire']['gender'] === 'Male' &&
            isset($_SESSION['questionnaire']['pregnancy'])
        ) {
            unset($_SESSION['questionnaire']['pregnancy']);
            unset($_SESSION['questionnaire_question_order']['pregnancy']);
        }

        if (
            isset($_SESSION['questionnaire']['weightunit']) &&
            $_SESSION['questionnaire']['weightunit'] === 'kg'
        ) {
            unset($_SESSION['questionnaire']['weight2']);
            unset($_SESSION['questionnaire_question_order']['weight2']);
        }

        if (
            isset($_SESSION['questionnaire']['heightunit']) &&
            $_SESSION['questionnaire']['heightunit'] == 'cm'
        ) {
            unset($_SESSION['questionnaire']['height2']);
            unset($_SESSION['questionnaire_question_order']['height2']);
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

            $_SESSION['questionnaire_question_order'][$key] = $_SESSION['question_order'];
            $_SESSION['question_order']++;

            // Log answer
            $loggedValue = is_array($_SESSION['questionnaire'][$key])
                ? implode(", ", $_SESSION['questionnaire'][$key])
                : $_SESSION['questionnaire'][$key];

            if (!is_array($_SESSION['questionnaire'][$key]) && ($key === 'weight' || $key === 'height')) {
                $unitKey = $key === 'weight' ? 'weightunit' : 'heightunit';
                $secondaryKey = $key === 'weight' ? 'weight2' : 'height2';

                $unitValue = $_SESSION['questionnaire'][$unitKey] ?? null;
                if ($unitValue === null && isset($_POST[$unitKey])) {
                    $unitValue = htmlspecialchars($_POST[$unitKey]);
                }

                $secondaryValue = $_SESSION['questionnaire'][$secondaryKey] ?? null;
                if ($secondaryValue === null && isset($_POST[$secondaryKey])) {
                    $secondaryValue = htmlspecialchars($_POST[$secondaryKey]);
                }

                $loggedValue = formatMeasurementAnswer($loggedValue, $unitValue, $secondaryValue);
            }

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
        : "/get-started/questionnaire?step=" . urlencode($nextStep);
    setcookie('questionnaire', json_encode($_SESSION['questionnaire']), time() + 3600, '/');
    /* if($_SESSION['questionnaire']['reviewed'] === 'InProcess' && $nextStep=="plans" ){
        exit;
        }else{
          header("Location: $redirectUrl");
            exit();
        }*/
    // print_r($_SESSION['questionnaire']);
    if ($redirect) {
        header("Location: $redirectUrl");
        exit();
    }
}
if (isset($_SESSION['step_data']['user_id'])) {
    $_SESSION['questionnaire']['uuid'] = $_SESSION['step_data']['user_id'];
}
setcookie('questionnaire', json_encode($_SESSION['questionnaire'] ?? []), time() + 3600, '/');

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

<?php PerchSystem::set_var('step', $_GET["step"]);
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
    'medication_allergies_other' => '/get-started/questionnaire?step=medication_allergies',
    'gp_informed' => '/get-started/questionnaire?step=medication_allergies',
    'gp_address' => '/get-started/questionnaire?step=gp_informed',
    'access_special_offers' => '/get-started/questionnaire?step=gp_address',
    'review-questionnaire' => '/get-started/questionnaire?step=access_special_offers'
];
//print_r($_SESSION['questionnaire']);

if (isset($_SESSION['questionnaire']['more_pancreatitis'])) {
    $back_links['conditions'] = "/get-started/questionnaire?step=more_pancreatitis";
}
if (isset($_SESSION['questionnaire']['conditions2'])) {
    if (in_array('mentalhealth', $_SESSION['questionnaire']['conditions2'])) {
        $back_links['more'] = "/get-started/questionnaire?step=conditions";
    }
}
if (isset($_SESSION['questionnaire']['medications'])) {
    if (in_array('none', $_SESSION['questionnaire']['medications'])) {
        $back_links['medication_allergies'] = "/get-started/questionnaire?step=medications";
    }
}

if (isset($_SESSION['questionnaire']['more_conditions'])) {
    $back_links['medical_conditions'] = "/get-started/questionnaire?step=more";
}
if (isset($_SESSION['questionnaire']['medical_conditions']) && $_SESSION['questionnaire']['medical_conditions'] == "no") {
    $back_links['medications'] = "/get-started/questionnaire?step=medical_conditions";
}

if (isset($_SESSION['questionnaire']['effects_with_wegovy']) && $_SESSION['questionnaire']['effects_with_wegovy'] == "no") {
    $back_links['medication_allergies'] = "/get-started/questionnaire?step=effects_with_wegovy";
}

$back_link = $back_links[$_GET["step"]] ?? '/get-started';

PerchSystem::set_var('previousPage', $back_link);
PerchSystem::set_var('answers', $_SESSION['questionnaire']);
PerchSystem::set_vars($_SESSION['questionnaire']);

$selectedMedications = [];
if (!empty($_SESSION['questionnaire']['medications']) && is_array($_SESSION['questionnaire']['medications'])) {
    foreach ($_SESSION['questionnaire']['medications'] as $medication) {
        $slug = perch_questionnaire_medication_slug((string) $medication);
        if ($slug === '' || $slug === 'none') {
            continue;
        }

        $recentDoseOptionsConfig = perch_questionnaire_recent_dose_options($slug);
        $recentDoseOptions = [];
        if (is_array($recentDoseOptionsConfig)) {
            foreach ($recentDoseOptionsConfig as $value => $displayLabel) {
                $recentDoseOptions[] = [
                    'value' => $value,
                    'label' => $displayLabel,
                ];
            }
        }

        $selectedMedications[$slug] = [
            'slug' => $slug,
            'label' => perch_questionnaire_medication_label($slug),
            'weight' => $_SESSION['questionnaire']["weight-{$slug}"] ?? '',
            'weight2' => $_SESSION['questionnaire']["weight2-{$slug}"] ?? '',
            'unit' => $_SESSION['questionnaire']["unit-{$slug}"] ?? 'kg',
            'dose' => $_SESSION['questionnaire']["dose-{$slug}"] ?? '',
            'recentDose' => $_SESSION['questionnaire']["recently-dose-{$slug}"] ?? '',
            'continueDose' => $_SESSION['questionnaire']["continue-dose-{$slug}"] ?? '',
            'recentDoseOptions' => $recentDoseOptions,
        ];
    }
}

if (empty($selectedMedications)) {
    $defaultSlug = 'wegovy';
    $recentDoseOptionsConfig = perch_questionnaire_recent_dose_options($defaultSlug);
    $recentDoseOptions = [];
    if (is_array($recentDoseOptionsConfig)) {
        foreach ($recentDoseOptionsConfig as $value => $displayLabel) {
            $recentDoseOptions[] = [
                'value' => $value,
                'label' => $displayLabel,
            ];
        }
    }
    $selectedMedications[$defaultSlug] = [
        'slug' => $defaultSlug,
        'label' => perch_questionnaire_medication_label($defaultSlug),
        'weight' => $_SESSION['questionnaire']['weight-wegovy'] ?? '',
        'weight2' => $_SESSION['questionnaire']['weight2-wegovy'] ?? '',
        'unit' => $_SESSION['questionnaire']['unit-wegovy'] ?? 'kg',
        'dose' => $_SESSION['questionnaire']['dose-wegovy'] ?? '',
        'recentDose' => $_SESSION['questionnaire']['recently-dose-wegovy'] ?? '',
        'continueDose' => $_SESSION['questionnaire']['continue-dose-wegovy'] ?? '',
        'recentDoseOptions' => $recentDoseOptions,
    ];
}

$medicationWeightJson = json_encode(array_values($selectedMedications), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
PerchSystem::set_var('medication_weight_json', $medicationWeightJson ?: '[]');

perch_form('questionnaire.html');
?>




<script>
    function redirectToPage() {
        const selectedValue = document.querySelector('input[name="option"]:checked').value;
        window.location.href = selectedValue; // Redirect to the selected value (URL)
    }
</script>

<?php
perch_layout('getStarted/footer'); ?>