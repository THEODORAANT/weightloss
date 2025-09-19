<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['questionnaire']) && isset($_COOKIE['questionnaire'])) {
    $_SESSION['questionnaire'] = json_decode($_COOKIE['questionnaire'], true) ?: [];
}

$structure = perch_member_questionnaire_structure('first-order');
if (is_array($structure) && PerchUtil::count($structure)) {
    uasort($structure, function ($a, $b) {
        $sortA = isset($a['sort']) ? (int)$a['sort'] : PHP_INT_MAX;
        $sortB = isset($b['sort']) ? (int)$b['sort'] : PHP_INT_MAX;

        if ($sortA === $sortB) {
            $keyA = isset($a['key']) ? (string)$a['key'] : '';
            $keyB = isset($b['key']) ? (string)$b['key'] : '';
            return strcmp($keyA, $keyB);
        }

        return $sortA <=> $sortB;
    });
}
$question_labels = [];
$question_steps = [];
$question_definitions = [];
$alias_map = [];
$canonical_answers = [];

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

$answers = $_SESSION['questionnaire'] ?? [];
if (is_array($answers)) {
    foreach ($answers as $answerKey => $value) {
        if (!array_key_exists($answerKey, $alias_map)) {
            continue;
        }

        $canonicalKey = $alias_map[$answerKey];
        $canonical_answers[$canonicalKey] = $value;
    }
}

// Render a field with optional second value and unit
function renderMeasurement($value, $unitKey, $secondKey, $questionnaire) {
    $primary = trim((string)$value);
    $units = isset($questionnaire[$unitKey]) ? (string)$questionnaire[$unitKey] : '';
    $secondary = isset($questionnaire[$secondKey]) ? trim((string)$questionnaire[$secondKey]) : '';

    $unitParts = $units !== '' ? explode('-', $units) : [];
    $primaryUnit = isset($unitParts[0]) ? trim((string)$unitParts[0]) : '';
    $secondaryUnit = isset($unitParts[1]) ? trim((string)$unitParts[1]) : '';

    $segments = [];

    if ($primary !== '') {
        $segments[] = $primary;
        if ($primaryUnit !== '') {
            $segments[] = $primaryUnit;
        }
    }

    if ($secondary !== '') {
        $segments[] = $secondary;
        if ($secondaryUnit !== '') {
            $segments[] = $secondaryUnit;
        }
    }

    return trim(implode(' ', array_filter($segments, function ($segment) {
        return $segment !== '';
    })));
}

function extractOptionDisplay($option, $fallback)
{
    if (is_array($option)) {
        foreach (['label', 'title', 'text', 'value'] as $key) {
            if (isset($option[$key]) && $option[$key] !== '') {
                return $option[$key];
            }
        }

        $scalars = array_filter($option, function ($item) {
            return is_scalar($item) && $item !== '';
        });

        if (!empty($scalars)) {
            return implode(' ', array_map('strval', $scalars));
        }

        return $fallback;
    }

    if ($option === '' || $option === null) {
        return $fallback;
    }

    return $option;
}

function resolveOptionLabel($options, $value)
{
    if (!is_array($options) || empty($options)) {
        return $value;
    }

    if (array_key_exists($value, $options)) {
        return extractOptionDisplay($options[$value], $value);
    }

    foreach ($options as $option) {
        if (is_array($option) && isset($option['value']) && (string)$option['value'] === (string)$value) {
            return extractOptionDisplay($option, $value);
        }
    }

    return $value;
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

            if (is_array($structure) && PerchUtil::count($canonical_answers)) {
                foreach ($structure as $canonicalKey => $definition) {
                    if (!array_key_exists($canonicalKey, $question_labels)) {
                        continue;
                    }

                    if (($definition['type'] ?? '') === 'hidden') {
                        continue;
                    }

                    if (!array_key_exists($canonicalKey, $canonical_answers)) {
                        continue;
                    }

                    $value = $canonical_answers[$canonicalKey];
                    $options = isset($definition['options']) && is_array($definition['options']) ? $definition['options'] : [];

                    if (is_array($value)) {
                        $labels = [];
                        foreach ($value as $item) {
                            $labels[] = resolveOptionLabel($options, $item);
                        }
                        $labels = array_filter($labels, function ($label) {
                            return $label !== '' && $label !== null;
                        });
                        $rawDisplay = $labels ? implode(', ', array_map('strval', $labels)) : implode(', ', array_map('strval', $value));
                    } else {
                        $rawDisplay = resolveOptionLabel($options, $value);
                        if ($rawDisplay === '' && $value !== null) {
                            $rawDisplay = (string)$value;
                        }
                    }

                    $measurementDisplay = '';
                    if (!is_array($value)) {
                        if ($canonicalKey === 'weight') {
                            $measurementDisplay = renderMeasurement($value, 'weightunit', 'weight2', $_SESSION['questionnaire']);
                        } elseif ($canonicalKey === 'weight-wegovy') {
                            $measurementDisplay = renderMeasurement($value, 'unit-wegovy', 'weight2-wegovy', $_SESSION['questionnaire']);
                        } elseif ($canonicalKey === 'height') {
                            $measurementDisplay = renderMeasurement($value, 'heightunit', 'height2', $_SESSION['questionnaire']);
                        }
                    }

                    $displayValue = $measurementDisplay !== '' ? $measurementDisplay : $rawDisplay;
                    if ($displayValue === '') {
                        $displayValue = is_array($value)
                            ? implode(', ', array_map('strval', $value))
                            : (string)$value;
                    }

                    $displayValue = trim((string)$displayValue);
                    $changelink = "/get-started/questionnaire?step=" . ($question_steps[$canonicalKey] ?? $canonicalKey);
            ?>
            <div class="plan">
                <div>
                    <h5><?= htmlspecialchars($question_labels[$canonicalKey], ENT_QUOTES, 'UTF-8') ?></h5>
                    <p><?= htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="price-section">
                    <button style="background-color: #00ccbd;text-transform: uppercase;" class="badge text-dark loss">
                        <a style="text-decoration: none; color:black;" href="<?= htmlspecialchars($changelink, ENT_QUOTES, 'UTF-8') ?>">Review Answer</a>
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
