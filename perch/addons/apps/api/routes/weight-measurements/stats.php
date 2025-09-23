<?php
include(__DIR__ . '/../../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/helpers.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    wl_weight_measurements_error(401, 'Unauthorized');
}

$periodParam = $_GET['period'] ?? 'month';
$metricParam = $_GET['metric'] ?? 'all';

$allowedPeriods = [
    'week' => new DateInterval('P7D'),
    'month' => new DateInterval('P1M'),
    '3months' => new DateInterval('P3M'),
    '6months' => new DateInterval('P6M'),
    'year' => new DateInterval('P1Y'),
    'all' => null,
];

if (!array_key_exists($periodParam, $allowedPeriods)) {
    wl_weight_measurements_error(400, 'Invalid period.');
}

$allowedMetrics = ['weight', 'bmi', 'body_fat', 'muscle', 'all'];
if (!in_array($metricParam, $allowedMetrics, true)) {
    wl_weight_measurements_error(400, 'Invalid metric.');
}

$utc = new DateTimeZone('UTC');
$now = new DateTimeImmutable('now', $utc);
$startDate = null;

if ($periodParam !== 'all') {
    $interval = $allowedPeriods[$periodParam];
    $startDate = $now->sub($interval);
}

$memberId = $payload['user_id'];
$repository = wl_weight_measurements_repository();
$rows = $repository->fetchChronologicalForMember($memberId, $startDate);

if (empty($rows)) {
    echo json_encode([
        'period' => $periodParam,
        'data_points' => [],
        'summary' => [
            'start_weight' => null,
            'current_weight' => null,
            'weight_change' => null,
            'weight_change_percent' => null,
            'average_weekly_change' => null,
            'measurements_count' => 0,
        ],
        'trends' => [
            'weight' => 'unknown',
            'body_fat' => 'unknown',
            'muscle' => 'unknown',
        ],
    ]);
    exit;
}

$fieldsForDaily = ['weight_kg', 'bmi', 'body_fat_percent', 'muscle_percent'];
$daily = [];
$firstRow = null;
$lastRow = null;

foreach ($rows as $row) {
    try {
        $date = new DateTimeImmutable($row['measurement_date']);
    } catch (Exception $e) {
        continue;
    }

    $date = $date->setTimezone($utc);
    $dayKey = $date->format('Y-m-d');

    if (!isset($daily[$dayKey])) {
        $daily[$dayKey] = [
            'sums' => array_fill_keys($fieldsForDaily, 0.0),
            'counts' => array_fill_keys($fieldsForDaily, 0),
        ];
    }

    foreach ($fieldsForDaily as $field) {
        if (isset($row[$field]) && $row[$field] !== null && $row[$field] !== '') {
            $daily[$dayKey]['sums'][$field] += (float)$row[$field];
            $daily[$dayKey]['counts'][$field] += 1;
        }
    }

    if ($firstRow === null) {
        $firstRow = $row;
    }
    $lastRow = $row;
}

ksort($daily);

$metricFieldMap = [
    'weight' => 'weight_kg',
    'bmi' => 'bmi',
    'body_fat' => 'body_fat_percent',
    'muscle' => 'muscle_percent',
];

$fieldsToInclude = $metricParam === 'all'
    ? $fieldsForDaily
    : [$metricFieldMap[$metricParam]];

$dataPoints = [];
foreach ($daily as $day => $aggregate) {
    $point = ['date' => $day];
    foreach ($fieldsToInclude as $field) {
        if ($aggregate['counts'][$field] > 0) {
            $point[$field] = round($aggregate['sums'][$field] / $aggregate['counts'][$field], 3);
        } else {
            $point[$field] = null;
        }
    }
    $dataPoints[] = $point;
}

$summary = wl_build_weight_measurement_summary($firstRow, $lastRow, count($rows), $utc);
$trends = wl_build_weight_measurement_trends($firstRow, $lastRow);

echo json_encode([
    'period' => $periodParam,
    'data_points' => $dataPoints,
    'summary' => $summary,
    'trends' => $trends,
]);

exit;

function wl_build_weight_measurement_summary($firstRow, $lastRow, $count, DateTimeZone $utc)
{
    $startWeight = (is_array($firstRow) && isset($firstRow['weight_kg']) && $firstRow['weight_kg'] !== null)
        ? (float)$firstRow['weight_kg']
        : null;
    $currentWeight = (is_array($lastRow) && isset($lastRow['weight_kg']) && $lastRow['weight_kg'] !== null)
        ? (float)$lastRow['weight_kg']
        : null;

    $weightChange = null;
    $weightChangePercent = null;
    $averageWeeklyChange = null;

    if ($startWeight !== null && $currentWeight !== null) {
        $weightChange = round($currentWeight - $startWeight, 3);
        if ($startWeight > 0) {
            $weightChangePercent = round(($weightChange / $startWeight) * 100, 3);
        }

        try {
            if (is_array($firstRow) && isset($firstRow['measurement_date']) && is_array($lastRow) && isset($lastRow['measurement_date'])) {
                $firstDate = new DateTimeImmutable($firstRow['measurement_date']);
                $lastDate = new DateTimeImmutable($lastRow['measurement_date']);
                $firstDate = $firstDate->setTimezone($utc);
                $lastDate = $lastDate->setTimezone($utc);
                $days = max($firstDate->diff($lastDate)->days, 1);
                $weeks = max($days / 7, 1);
                $averageWeeklyChange = round($weightChange / $weeks, 3);
            }
        } catch (Exception $e) {
            $averageWeeklyChange = null;
        }
    }

    return [
        'start_weight' => $startWeight,
        'current_weight' => $currentWeight,
        'weight_change' => $weightChange,
        'weight_change_percent' => $weightChangePercent,
        'average_weekly_change' => $averageWeeklyChange,
        'measurements_count' => (int)$count,
    ];
}

function wl_build_weight_measurement_trends($firstRow, $lastRow)
{
    $startWeight = (is_array($firstRow) && isset($firstRow['weight_kg']) && $firstRow['weight_kg'] !== null)
        ? (float)$firstRow['weight_kg']
        : null;
    $currentWeight = (is_array($lastRow) && isset($lastRow['weight_kg']) && $lastRow['weight_kg'] !== null)
        ? (float)$lastRow['weight_kg']
        : null;

    $startBodyFat = (is_array($firstRow) && isset($firstRow['body_fat_percent']) && $firstRow['body_fat_percent'] !== null)
        ? (float)$firstRow['body_fat_percent']
        : null;
    $currentBodyFat = (is_array($lastRow) && isset($lastRow['body_fat_percent']) && $lastRow['body_fat_percent'] !== null)
        ? (float)$lastRow['body_fat_percent']
        : null;

    $startMuscle = (is_array($firstRow) && isset($firstRow['muscle_percent']) && $firstRow['muscle_percent'] !== null)
        ? (float)$firstRow['muscle_percent']
        : null;
    $currentMuscle = (is_array($lastRow) && isset($lastRow['muscle_percent']) && $lastRow['muscle_percent'] !== null)
        ? (float)$lastRow['muscle_percent']
        : null;

    return [
        'weight' => wl_determine_trend($startWeight, $currentWeight),
        'body_fat' => wl_determine_trend($startBodyFat, $currentBodyFat),
        'muscle' => wl_determine_trend($startMuscle, $currentMuscle),
    ];
}
