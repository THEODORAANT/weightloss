<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!perch_member_logged_in()) {
    exit;
}

$currentStep = isset($_GET['step']) ? trim((string)$_GET['step']) : 'weight';

if (empty($_SESSION['questionnaire-reorder']) && isset($_COOKIE['questionnaire_reorder'])) {
    $_SESSION['questionnaire-reorder'] = json_decode($_COOKIE['questionnaire_reorder'], true) ?: [];
}
if (!isset($_SESSION['questionnaire-reorder']) || !is_array($_SESSION['questionnaire-reorder'])) {
    $_SESSION['questionnaire-reorder'] = [];
}

$validationErrors = $_SESSION['reorder_validation_errors'] ?? [];
unset($_SESSION['reorder_validation_errors']);

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
    $requiredStepFields = [
        'rate_current_experience' => [
            'field' => 'rate_current_experience',
            'message' => 'Please select whether you are happy with your monthly weight loss.',
        ],
        'chat_with_us' => [
            'field' => 'chat_with_us',
            'message' => 'Please tell us if you would like to chat with someone.',
        ],
    ];

    if (isset($requiredStepFields[$currentStep])) {
        $fieldName = $requiredStepFields[$currentStep]['field'];
        $value = isset($_POST[$fieldName]) ? trim((string)$_POST[$fieldName]) : '';
        if ($value === '') {
            $_SESSION['reorder_validation_errors'] = [
                $currentStep => $requiredStepFields[$currentStep]['message'],
            ];
            header('Location: /client/questionnaire-re-order?step=' . $currentStep);
            exit();
        }
    }

    $user_id = generateUUID();
    $timestamp = time();
    // Secret key (keep this safe, use env file ideally)
    $secret_key = 'theoloss1066';

    // Create a unique data string
    $data = $user_id . '|' . $currentStep . '|' . $timestamp;

    // Create hash using HMAC with SHA256
    $hash = hash_hmac('sha256', $data, $secret_key);

    // Store the step hash in session
    $_SESSION['step_hash'] = $hash;

    // Optional: Store the raw data if needed for verification
    $_SESSION['step_data'] = [
        'user_id' => $user_id,
        'step' => $currentStep,
        'timestamp' => $timestamp,
    ];
    $_SESSION['questionnaire-reorder']['uuid'] = $user_id;

    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            $_SESSION['questionnaire-reorder'][$key] = $value;
        } else {
            $_SESSION['questionnaire-reorder'][$key] = htmlspecialchars($value);
        }
        logAnswerChange($key, $_SESSION['questionnaire-reorder'][$key], 'reorder');
    }

    setcookie('questionnaire_reorder', json_encode($_SESSION['questionnaire-reorder']), time() + 3600, '/');

    if ($_POST['nextstep'] === 'cart') {
        $userId = $_SESSION['step_data']['user_id'];
        $metadata = [
            'user_id'    => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'registered' => date('Y-m-d H:i:s'),
        ];
        $logDir = '/var/www/html/logs/reorder';
        if (!is_dir($logDir) && !mkdir($logDir, 0755, true)) {
            die("Failed to create log directory: $logDir");
        }

        $_SESSION['questionnaire-reorder']['multiple_answers'] = 'No';

        if (isset($_SESSION['reorder_answer_log'])) {
            $rawLog = is_array($_SESSION['reorder_answer_log']) ? $_SESSION['reorder_answer_log'] : [];

            if (file_put_contents("{$logDir}/{$userId}_raw_log.json", json_encode([
                'metadata' => $metadata,
                'log' => $rawLog,
            ], JSON_PRETTY_PRINT)) === false) {
                die('Failed to write log file.');
            }

            $summary = perch_members_summarise_answer_log($rawLog);
            $grouped = $summary['grouped'];

            if (!empty($summary['has_changes'])) {
                $_SESSION['questionnaire-reorder']['multiple_answers'] = 'Yes-' . 'https://' . $_SERVER['HTTP_HOST'] . '/perch/addons/apps/perch_members/questionnaire_logs/?userId=' . $userId;
            }

            if (file_put_contents("{$logDir}/{$userId}_grouped_log.json", json_encode([
                'metadata' => $metadata,
                'grouped_log' => $grouped,
            ], JSON_PRETTY_PRINT)) === false) {
                die('Failed to write log file.');
            }

            // Optional: clear the session log
            unset($_SESSION['reorder_answer_log']);
        }

        header('Location: /order/cart'); // Redirect to the selected URL
        exit();
    }

    header('Location: /client/questionnaire-re-order?step=' . $_POST['nextstep']);
    exit();
}

if (isset($_SESSION['step_data']['user_id'])) {
    $_SESSION['questionnaire-reorder']['uuid'] = $_SESSION['step_data']['user_id'];
}
setcookie('questionnaire_reorder', json_encode($_SESSION['questionnaire-reorder'] ?? []), time() + 3600, '/');
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
PerchSystem::set_var('rate_current_experience_error', $validationErrors['rate_current_experience'] ?? '');
PerchSystem::set_var('chat_with_us_error', $validationErrors['chat_with_us'] ?? '');
if ($currentStep) {
    PerchSystem::set_var('step', $currentStep);
}
PerchSystem::set_vars($_SESSION['questionnaire-reorder']);

 perch_form('reorder-questionnaire.html');

            ?>





            </div></div>
        <?php
      perch_layout('getStarted/footer');?>
