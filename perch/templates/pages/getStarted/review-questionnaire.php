<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/addons/apps/perch_members/questionnaire_medication_helpers.php';
if (empty($_SESSION['questionnaire']) && isset($_COOKIE['questionnaire'])) {
    $_SESSION['questionnaire'] = json_decode($_COOKIE['questionnaire'], true) ?: [];
}
$questions = [
    "age" => "How old are you?",
    "ethnicity" => "Which ethnicity are you?",
    "ethnicity-more" => "Please tell us which ethnicities",
    "gender" => "What sex were you assigned at birth?",
    "pregnancy" => "Are you currently pregnant, trying to get pregnant, or breastfeeding?",
    "weight" => "What is your weight?",
    "height" => "What is your height?",
    "diabetes" => "Have you been diagnosed with diabetes?",
    "conditions" => "Do any of the following statements apply to you?",
    "bariatricoperation" => "Was your bariatric operation in the last 6 months?",
    "more_pancreatitis" => "Please tell us more about your mental health condition and how you manage it",
    "thyroidoperation" => "Please tell us further details on the thyroid surgery you had, the outcome of the surgery and any ongoing monitoring",
    "more_conditions" => "Please tell us more about your mental health condition and how you manage it",
    "conditions2" => "Do any of the following statements apply to you?",
    "medical_conditions" => "Do you have any other medical conditions?",
    "medications" => "Have you ever taken any of the following medications to help you lose weight?",
    "weight-wegovy" => "What was your weight in kg before starting the weight loss medication?",
    "dose-wegovy" => "When was your last dose of the weight loss medication?",
    "recently-dose-wegovy" => "What dose of the weight loss medication were you prescribed most recently?",
    "continue-dose-wegovy" => "If you want to continue with the weight loss medication, what dose would you like to continue with?",
    "effects_with_wegovy" => "Have you experienced any side effects with the weight loss medication?",
    "medication_allergies" => "Do you currently take any other medication or have any allergies? This includes prescribed medication, over-the-counter medication, and supplements. Select all that apply to you.",
    "other_medication_details" => "Please provide details of the other medication you take, including the name, dose, and how often you take this.",
    "other_medical_conditions" => "Please list any other medical conditions you have.",
    "wegovy_side_effects" => "Please tell us as much as you can about your side effects - the type, duration, severity and whether they have resolved",
    "gp_informed" => "Would you like your GP to be informed of this consultation?",
    "email_address" => "Please enter your GP's email address",
    "Get access to special offers" => "email_address"
];

$doseOptions = [
    'less4' => 'Less than 4 weeks ago',
    '4to6' => '4-6 weeks ago',
    'over6' => 'More than 6 weeks ago',
];

$medicationSlugs = [];
$recentDoseOptionsBySlug = [];
foreach (perch_questionnaire_medications() as $slug => $label) {
    if ($slug === 'none') {
        continue;
    }

    $medicationSlugs[] = $slug;
    $medicationLabel = perch_questionnaire_medication_label($slug);
    $questions["weight-{$slug}"] = "What was your weight in kg/st-lbs before starting " . $medicationLabel . '?';
    $questions["dose-{$slug}"] = "When was your last dose of " . $medicationLabel . '?';
    $questions["recently-dose-{$slug}"] = "What dose of " . $medicationLabel . " were you prescribed most recently?";

    $options = perch_questionnaire_recent_dose_options($slug);
    if ($options !== null) {
        $recentDoseOptionsBySlug[$slug] = $options;
    }
}

$steps = [
    "age" => "howold",
    "ethnicity" => "18to74",
    "ethnicity-more" => "Mixed",
    "gender" => "ethnicity",
    "pregnancy" => "Female",
    "weight" => "weight",
    "height" => "height",
    "diabetes" => "diabetes",
    "conditions" => "weight2",
    "bariatricoperation" => "bariatricoperation",
    "more_pancreatitis" => "more_pancreatitis",
    "thyroidoperation" => "thyroidoperation",
    "more_conditions" => "more",
    "conditions2" => "conditions",
    "medical_conditions" => "medical_conditions",
    "medications" => "medications",
    "weight-wegovy" => "starting_wegovy",
    "dose-wegovy" => "dose_wegovy",
    "recently-dose-wegovy" => "recently_wegovy",
    "continue-dose-wegovy" => "continue_with_wegovy",
    "effects_with_wegovy" => "effects_with_wegovy",
    "medication_allergies" => "medication_allergies",
    "other_medication_details" => "medication_allergies",
    "other_medical_conditions" => "list_any",
    "wegovy_side_effects" => "wegovy_side_effects",
    "gp_informed" => "gp_informed",
    "email_address" => "gp_address",
    "Get access to special offers" => "access_special_offers"
];

foreach ($medicationSlugs as $slug) {
    $steps["weight-{$slug}"] = "starting_wegovy";
    $steps["dose-{$slug}"] = "dose_wegovy";
    $steps["recently-dose-{$slug}"] = "recently_wegovy";
}

// Render a field with optional second value and unit
function renderMeasurement($value, $unitKey, $secondKey, $questionnaire)
{
    $parts = [];

    if ($value !== null && $value !== '') {
        $parts[] = htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    if (!empty($questionnaire[$unitKey])) {
        $unitParts = explode('-', (string) $questionnaire[$unitKey]);
        $parts[] = htmlspecialchars($unitParts[0], ENT_QUOTES, 'UTF-8');

        if ((isset($questionnaire[$secondKey]) || array_key_exists($secondKey, $questionnaire)) && isset($unitParts[1])) {
            $parts[] = htmlspecialchars((string) $questionnaire[$secondKey], ENT_QUOTES, 'UTF-8');
            $parts[] = htmlspecialchars($unitParts[1], ENT_QUOTES, 'UTF-8');
        }
    }

    return implode(' ', array_filter($parts, static fn($part) => $part !== ''));
}

// Page header
setcookie('questionnaire', json_encode($_SESSION['questionnaire'] ?? []), time()+3600, '/');
perch_layout('product/header', [
    'page_title' => perch_page_title(true),
]);

$errors = perch_member_validateQuestionnaire($_SESSION['questionnaire']);
$_SESSION['questionnaire']["reviewed"] = "InProcess";
?>

<section class="shippin_section">
    <div class="container all_content mt-4">
        <h2 class="text-center fw-bolder">Review your answers</h2>

        <div class="plans mt-4">
            <?php
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<p style='color:red;'>".htmlspecialchars($error).". Please review your answers.</p>";
                }
            }

            foreach ($_SESSION['questionnaire'] as $key => $value) {
                if (array_key_exists($key, $questions)) {
                    $changelink = "/get-started/questionnaire?step=" . $steps[$key];
            ?>
            <div class="plan">
                <div>
                    <h5><?= htmlspecialchars($questions[$key]) ?></h5>
                    <p>
                        <?php
                        if (is_array($value)) {
                            echo htmlspecialchars(implode(", ", $value));
                        } elseif ($key === "weight") {
                            echo renderMeasurement($value, "weightunit", "weight2", $_SESSION['questionnaire']);
                        } elseif (strpos($key, 'weight-') === 0) {
                            $slug = substr($key, 7);
                            echo renderMeasurement($value, "unit-{$slug}", "weight2-{$slug}", $_SESSION['questionnaire']);
                        } elseif (strpos($key, 'recently-dose-') === 0) {
                            $medicationSlug = substr($key, strlen('recently-dose-'));
                            $options = $recentDoseOptionsBySlug[$medicationSlug] ?? [];
                            $answer = $options[$value] ?? $value;
                            echo htmlspecialchars($answer);
                        } elseif (strpos($key, 'dose-') === 0) {
                            $answer = $doseOptions[$value] ?? $value;
                            echo htmlspecialchars($answer);
                        } elseif ($key === "height") {
                            echo renderMeasurement($value, "heightunit", "height2", $_SESSION['questionnaire']);
                        } else {
                            echo htmlspecialchars($value);
                        }
                        ?>
                    </p>
                </div>
                <div class="price-section">
                    <button style="background-color: #00ccbd;text-transform: uppercase;" class="badge text-dark loss">
                        <a style="text-decoration: none; color:black;" href="<?= $changelink ?>">Review Answer</a>
                    </button>
                </div>
            </div>
            <?php
                }
            }
            ?>
        </div>

        <div class="bottom_btn mt-5">
       <?php  $action="/order";
        if(isset($_SESSION["package_billing_type"])){
       $action="/order/cart";
       }
?>
            <form action="<?=$action?>" method="post">
                <input type="hidden" name="confirm" value="true" />
                <button id="clientButton" type="submit" class="btn btn-primary next_btn mt-4 mb-3 next-btn">
                    <span>Confirm <i class="fa-solid fa-arrow-right"></i></span>
                </button>
            </form>
        </div>
    </div>
</section>

<?php
perch_layout('getStarted/footer');
?>
