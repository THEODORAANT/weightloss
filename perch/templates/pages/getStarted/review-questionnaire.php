<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['questionnaire']) && isset($_COOKIE['questionnaire'])) {
    $_SESSION['questionnaire'] = json_decode($_COOKIE['questionnaire'], true) ?: [];
}

$structure = perch_member_questionnaire_structure('first-order');
$question_labels = [];
$question_steps = [];
$question_definitions = [];
$alias_map = [];

if (is_array($structure)) {
    foreach ($structure as $key => $question) {
        $question_labels[$key] = $question['label'];
        $question_steps[$key] = $question['step'] ?? $key;
        $question_definitions[$key] = $question;

        $names = [$key];
        if (!empty($question['name'])) {
            $names[] = $question['name'];
        }
        if (!empty($question['aliases']) && is_array($question['aliases'])) {
            $names = array_merge($names, $question['aliases']);
        }

        foreach ($names as $name) {
            $alias_map[$name] = $key;
        }
    }
}

// Render a field with optional second value and unit
function renderMeasurement($value, $unitKey, $secondKey, $questionnaire) {
    $output = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    if (isset($questionnaire[$unitKey])) {
        $unitParts = explode("-", $questionnaire[$unitKey]);
        $output .= " " . htmlspecialchars($unitParts[0]);
        if (isset($questionnaire[$secondKey])) {
            $output .= " " . htmlspecialchars($questionnaire[$secondKey]) . " " . htmlspecialchars($unitParts[1]);
        }
    }
    return $output;
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
                $canonicalKey = $alias_map[$key] ?? $key;
                if (!isset($question_labels[$canonicalKey])) {
                    continue;
                }

                $definition = $question_definitions[$canonicalKey] ?? [];
                if (($definition['type'] ?? '') === 'hidden') {
                    continue;
                }

                $changelink = "/get-started/questionnaire?step=" . ($question_steps[$canonicalKey] ?? $canonicalKey);
            ?>
            <div class="plan">
                <div>
                    <h5><?= htmlspecialchars($question_labels[$canonicalKey]) ?></h5>
                    <p>
                        <?php
                        $displayValue = '';
                        $options = isset($definition['options']) && is_array($definition['options']) ? $definition['options'] : [];

                        if (is_array($value)) {
                            if ($options) {
                                $labels = [];
                                foreach ($value as $item) {
                                    $labels[] = $options[$item] ?? $item;
                                }
                                $displayValue = implode(', ', $labels);
                            } else {
                                $displayValue = implode(', ', $value);
                            }
                        } else {
                            if ($options && isset($options[$value])) {
                                $displayValue = $options[$value];
                            } else {
                                $displayValue = $value;
                            }
                        }

                        if ($canonicalKey === "weight") {
                            $displayValue = renderMeasurement($value, "weightunit", "weight2", $_SESSION['questionnaire']);
                        } elseif ($canonicalKey === "weight-wegovy") {
                            $displayValue = renderMeasurement($value, "unit-wegovy", "weight2-wegovy", $_SESSION['questionnaire']);
                        } elseif ($canonicalKey === "height") {
                            $displayValue = renderMeasurement($value, "heightunit", "height2", $_SESSION['questionnaire']);
                        }

                        echo htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8');
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
