<?php

declare(strict_types=1);

require_once __DIR__ . '/../perch/runtime.php';

$options = getopt('', [
    'order-id:',
    'type::',
    'height-primary:',
    'height-secondary::',
    'height-unit:',
    'weight-primary:',
    'weight-secondary::',
    'weight-unit:',
    'bmi::',
    'bmi-category::',
    'dry-run',
    'help',
]);

if (isset($options['help'])) {
    fwrite(STDOUT, <<<TEXT
Usage: php scripts/update_iquestionnaire_metrics.php --order-id=<id> --height-primary=<value> --height-unit=<unit> --weight-primary=<value> --weight-unit=<unit> [options]

Required options:
  --order-id           The order ID whose questionnaire answers will be updated.
  --height-primary     Primary height value (cm or feet, depending on unit).
  --height-unit        Height unit. Accepted values: cm, ft-in, in.
  --weight-primary     Primary weight value (kg or stones, depending on unit).
  --weight-unit        Weight unit. Accepted values: kg, st-lbs, lbs.

Optional options:
  --height-secondary   Secondary height value (inches when using ft-in, ignored otherwise).
  --weight-secondary   Secondary weight value (pounds when using st-lbs, ignored otherwise).
  --bmi                BMI value to store. If omitted, it will be calculated from the supplied height and weight.
  --bmi-category       BMI category label. If omitted, it will be derived from the BMI value when possible.
  --type               Questionnaire type to restrict the update (e.g. first-order, re-order).
  --dry-run            Preview the changes without applying them.
  --help               Display this help message.
TEXT);
    exit(0);
}

$orderID = isset($options['order-id']) ? (int) $options['order-id'] : 0;
if ($orderID <= 0) {
    fwrite(STDERR, "A valid --order-id value is required.\n");
    exit(1);
}

$heightPrimary = getNumericOption($options, 'height-primary', '--height-primary');
$heightSecondary = getOptionalNumericOption($options, 'height-secondary');
$heightUnit = normaliseHeightUnit($options['height-unit'] ?? null);

$weightPrimary = getNumericOption($options, 'weight-primary', '--weight-primary');
$weightSecondary = getOptionalNumericOption($options, 'weight-secondary');
$weightUnit = normaliseWeightUnit($options['weight-unit'] ?? null);

$bmiValue = null;
if (array_key_exists('bmi', $options)) {
    $bmiValue = getOptionalNumericOption($options, 'bmi');
    if ($bmiValue === null) {
        fwrite(STDERR, "When provided, --bmi must be a numeric value.\n");
        exit(1);
    }
}

$bmiCategory = null;
if (array_key_exists('bmi-category', $options)) {
    $bmiCategory = trim((string) $options['bmi-category']);
}

if ($bmiValue === null) {
    $bmiValue = calculateBmi($weightPrimary, $weightSecondary, $weightUnit, $heightPrimary, $heightSecondary, $heightUnit);
}

if ($bmiValue !== null && ($bmiCategory === null || $bmiCategory === '')) {
    $bmiCategory = classifyBmi($bmiValue);
}

$typeFilter = null;
if (isset($options['type'])) {
    $typeFilter = trim((string) $options['type']);
    if ($typeFilter === '') {
        $typeFilter = null;
    }
}

$dryRun = array_key_exists('dry-run', $options);

$DB = PerchDB::fetch();
$table = PERCH_DB_PREFIX . 'questionnaire';

$sql = 'SELECT id, question_slug, answer_text, qid FROM ' . $table . ' WHERE order_id = ' . $DB->pdb($orderID);
if ($typeFilter !== null) {
    $sql .= ' AND type = ' . $DB->pdb($typeFilter);
}

$rows = $DB->get_rows($sql);

if (!PerchUtil::count($rows)) {
    fwrite(STDERR, 'No questionnaire entries were found for order #' . $orderID . ($typeFilter ? ' (' . $typeFilter . ')' : '') . '.' . PHP_EOL);
    exit(1);
}

$latestRowsBySlug = [];
foreach ($rows as $row) {
    $slug = $row['question_slug'] ?? '';
    if ($slug === '') {
        continue;
    }

    if (!isset($latestRowsBySlug[$slug]) || ((int) $row['id']) > ((int) $latestRowsBySlug[$slug]['id'])) {
        $latestRowsBySlug[$slug] = $row;
    }
}

$updates = [];
$updates['height'] = buildHeightAnswer($heightPrimary, $heightSecondary, $heightUnit);
$updates['height2'] = shouldStoreSecondaryHeight($heightUnit) ? formatNumericAnswer($heightSecondary) : '';
$updates['heightunit'] = $heightUnit;

$updates['weight'] = buildWeightAnswer($weightPrimary, $weightSecondary, $weightUnit);
$updates['weight2'] = shouldStoreSecondaryWeight($weightUnit) ? formatNumericAnswer($weightSecondary) : '';
$updates['weightunit'] = $weightUnit;

if ($bmiValue !== null) {
    $updates['bmi'] = buildBmiAnswer($bmiValue, $bmiCategory);
}

$applied = 0;
$skipped = 0;
$missing = 0;

foreach ($updates as $slug => $value) {
    if (!array_key_exists($slug, $latestRowsBySlug)) {
        fwrite(STDOUT, 'No existing questionnaire entry found for slug "' . $slug . '". Skipping.' . PHP_EOL);
        $missing++;
        continue;
    }

    $row = $latestRowsBySlug[$slug];
    $currentValue = trim((string) ($row['answer_text'] ?? ''));
    $newValue = trim((string) ($value ?? ''));

    if ($currentValue === $newValue) {
        fwrite(STDOUT, 'Question "' . $slug . '" already has the desired value. Skipping.' . PHP_EOL);
        $skipped++;
        continue;
    }

    if ($dryRun) {
        fwrite(STDOUT, '[dry-run] Would update "' . $slug . '" from "' . $currentValue . '" to "' . $newValue . '".' . PHP_EOL);
        $skipped++;
        continue;
    }

    $result = $DB->update($table, ['answer_text' => $newValue], 'id', (int) $row['id']);

    if ($result) {
        fwrite(STDOUT, 'Updated "' . $slug . '" (row #' . $row['id'] . ').' . PHP_EOL);
        $applied++;
    } else {
        fwrite(STDOUT, 'No changes were applied to "' . $slug . '" (row #' . $row['id'] . ').' . PHP_EOL);
        $skipped++;
    }
}

fwrite(STDOUT, PHP_EOL . 'Summary: ' . $applied . ' update(s) applied, ' . $skipped . ' skipped, ' . $missing . ' missing.' . PHP_EOL);

exit(0);

function getNumericOption(array $options, string $key, string $name): float
{
    if (!isset($options[$key])) {
        fwrite(STDERR, 'The ' . $name . ' option is required.' . PHP_EOL);
        exit(1);
    }

    $value = trim((string) $options[$key]);
    if ($value === '' || !is_numeric($value)) {
        fwrite(STDERR, $name . ' must be a numeric value.' . PHP_EOL);
        exit(1);
    }

    return (float) $value;
}

function getOptionalNumericOption(array $options, string $key): ?float
{
    if (!isset($options[$key])) {
        return null;
    }

    $value = trim((string) $options[$key]);
    if ($value === '') {
        return null;
    }

    if (!is_numeric($value)) {
        return null;
    }

    return (float) $value;
}

function normaliseHeightUnit(?string $unit): string
{
    $unit = trim((string) $unit);
    if ($unit === '') {
        fwrite(STDERR, "--height-unit is required and must be one of: cm, ft-in, in.\n");
        exit(1);
    }

    $unit = strtolower($unit);
    $unit = str_replace(['_', ' '], '-', $unit);

    $mapping = [
        'cm' => 'cm',
        'centimeter' => 'cm',
        'centimetre' => 'cm',
        'centimeters' => 'cm',
        'centimetres' => 'cm',
        'ft-in' => 'ft-in',
        'ftin' => 'ft-in',
        'feet-inches' => 'ft-in',
        'feet-in' => 'ft-in',
        'foot-in' => 'ft-in',
        'ft' => 'ft-in',
        'in' => 'in',
        'inch' => 'in',
        'inches' => 'in',
    ];

    if (!isset($mapping[$unit])) {
        fwrite(STDERR, "Invalid height unit provided. Accepted values: cm, ft-in, in.\n");
        exit(1);
    }

    return $mapping[$unit];
}

function normaliseWeightUnit(?string $unit): string
{
    $unit = trim((string) $unit);
    if ($unit === '') {
        fwrite(STDERR, "--weight-unit is required and must be one of: kg, st-lbs, lbs.\n");
        exit(1);
    }

    $unit = strtolower($unit);
    $unit = str_replace(['_', ' '], '-', $unit);

    $mapping = [
        'kg' => 'kg',
        'kgs' => 'kg',
        'kilogram' => 'kg',
        'kilograms' => 'kg',
        'st-lbs' => 'st-lbs',
        'st' => 'st-lbs',
        'stone' => 'st-lbs',
        'stones' => 'st-lbs',
        'stone-lbs' => 'st-lbs',
        'lbs' => 'lbs',
        'lb' => 'lbs',
        'pound' => 'lbs',
        'pounds' => 'lbs',
    ];

    if (!isset($mapping[$unit])) {
        fwrite(STDERR, "Invalid weight unit provided. Accepted values: kg, st-lbs, lbs.\n");
        exit(1);
    }

    return $mapping[$unit];
}

function formatNumericAnswer(?float $value): string
{
    if ($value === null) {
        return '';
    }

    $formatted = number_format($value, 2, '.', '');
    $formatted = rtrim(rtrim($formatted, '0'), '.');

    if ($formatted === '-0') {
        $formatted = '0';
    }

    return $formatted;
}

function buildHeightAnswer(float $primary, ?float $secondary, string $unit): string
{
    if ($unit === 'cm') {
        return formatNumericAnswer($primary) . ' cm';
    }

    if ($unit === 'in') {
        return formatNumericAnswer($primary) . ' in';
    }

    $answer = formatNumericAnswer($primary) . ' ft';
    if ($secondary !== null) {
        $answer .= ' ' . formatNumericAnswer($secondary) . ' in';
    }

    return $answer;
}

function buildWeightAnswer(float $primary, ?float $secondary, string $unit): string
{
    if ($unit === 'kg') {
        return formatNumericAnswer($primary) . ' kg';
    }

    if ($unit === 'lbs') {
        return formatNumericAnswer($primary) . ' lbs';
    }

    $answer = formatNumericAnswer($primary) . ' st';
    if ($secondary !== null) {
        $answer .= ' ' . formatNumericAnswer($secondary) . ' lbs';
    }

    return $answer;
}

function shouldStoreSecondaryHeight(string $unit): bool
{
    return $unit === 'ft-in';
}

function shouldStoreSecondaryWeight(string $unit): bool
{
    return $unit === 'st-lbs';
}

function buildBmiAnswer(float $bmi, ?string $category): string
{
    $value = formatNumericAnswer($bmi);
    if ($category !== null && $category !== '') {
        return $value . ', ' . $category;
    }

    return $value;
}

function calculateBmi(float $weightPrimary, ?float $weightSecondary, string $weightUnit, float $heightPrimary, ?float $heightSecondary, string $heightUnit): ?float
{
    $weightKg = null;
    if ($weightUnit === 'kg') {
        $weightKg = $weightPrimary;
    } elseif ($weightUnit === 'lbs') {
        $weightKg = $weightPrimary * 0.45359237;
    } elseif ($weightUnit === 'st-lbs') {
        $stones = $weightPrimary;
        $pounds = $weightSecondary ?? 0.0;
        $totalPounds = ($stones * 14.0) + $pounds;
        $weightKg = $totalPounds * 0.45359237;
    }

    if ($weightKg === null || $weightKg <= 0) {
        return null;
    }

    $heightM = null;
    if ($heightUnit === 'cm') {
        $heightM = $heightPrimary / 100.0;
    } elseif ($heightUnit === 'in') {
        $heightM = $heightPrimary * 0.0254;
    } else {
        $feet = $heightPrimary;
        $inches = $heightSecondary ?? 0.0;
        $totalInches = ($feet * 12.0) + $inches;
        $heightM = $totalInches * 0.0254;
    }

    if ($heightM <= 0) {
        return null;
    }

    $bmi = $weightKg / ($heightM * $heightM);
    return round($bmi, 2);
}

function classifyBmi(float $bmi): string
{
    if ($bmi < 18.5) {
        return 'Underweight';
    }

    if ($bmi < 24.9) {
        return 'Normal weight';
    }

    if ($bmi < 29.9) {
        return 'Overweight';
    }

    return 'Obese';
}
