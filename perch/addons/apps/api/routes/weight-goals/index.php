<?php
include(__DIR__ . '/../../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../weight-measurements/helpers.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    wl_weight_goals_error(401, 'Unauthorized');
}

$memberId = $payload['user_id'];
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {
    case 'GET':
        wl_handle_weight_goal_get($memberId);
        break;
    case 'POST':
        wl_handle_weight_goal_set($memberId);
        break;
    default:
        header('Allow: GET, POST');
        wl_weight_goals_error(405, 'Method not allowed');
}

function wl_handle_weight_goal_get($memberId)
{
    global $_ROUTE;

    if (!empty($_ROUTE['params'])) {
        wl_weight_goals_error(404, 'Endpoint not found');
    }

    $goal = perch_members_weight_goal_find($memberId);

    if (!is_array($goal)) {
        wl_weight_goals_error(404, 'Weight goal not found.');
    }

    echo json_encode(wl_format_weight_goal($goal));
    exit;
}

function wl_handle_weight_goal_set($memberId)
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        wl_weight_goals_error(400, 'Request body must be valid JSON.');
    }

    if (!is_array($data)) {
        $data = [];
    }

    [$errors, $sanitized] = wl_validate_weight_goal_payload($memberId, $data);

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        exit;
    }

    $goal = perch_members_weight_goal_upsert($memberId, $sanitized);

    if (!is_array($goal)) {
        wl_weight_goals_error(500, 'Unable to save weight goal.');
    }

    echo json_encode(wl_format_weight_goal($goal));
    exit;
}

function wl_validate_weight_goal_payload($memberId, array $input)
{
    $errors = [];
    $validUnits = wl_weight_goals_valid_units();
    $utc = new DateTimeZone('UTC');
    $now = new DateTimeImmutable('now', $utc);

    $sanitized = [
        'goal_weight' => null,
        'unit' => null,
        'target_date' => null,
        'created_at' => $now,
        'updated_at' => $now,
    ];

    if (!array_key_exists('goal_weight', $input)) {
        $errors[] = 'goal_weight is required.';
    } elseif (!is_numeric($input['goal_weight'])) {
        $errors[] = 'goal_weight must be a number.';
    } else {
        $goalWeight = (float)$input['goal_weight'];
        if ($goalWeight <= 0) {
            $errors[] = 'goal_weight must be greater than 0.';
        } else {
            $sanitized['goal_weight'] = $goalWeight;
        }
    }

    if (!array_key_exists('unit', $input) || $input['unit'] === null || $input['unit'] === '') {
        $errors[] = 'unit is required.';
    } else {
        $unit = strtolower(trim((string)$input['unit']));
        if (!in_array($unit, $validUnits, true)) {
            $errors[] = 'unit must be one of: ' . implode(', ', $validUnits) . '.';
        } else {
            $sanitized['unit'] = $unit;
        }
    }

    if (!array_key_exists('target_date', $input) || $input['target_date'] === null || $input['target_date'] === '') {
        $errors[] = 'target_date is required.';
    } else {
        try {
            $target = new DateTimeImmutable($input['target_date']);
            $sanitized['target_date'] = $target->setTimezone($utc);
        } catch (Exception $e) {
            $errors[] = 'target_date must be a valid ISO 8601 datetime.';
        }
    }

    $measurementsRepository = wl_weight_measurements_repository();
    $latestMeasurement = $measurementsRepository->fetchLatestForMember($memberId);

    if (is_array($latestMeasurement) && isset($latestMeasurement['weight_kg'])) {
        $sanitized['starting_weight_kg'] = (float)$latestMeasurement['weight_kg'];
        if (isset($latestMeasurement['measurement_date'])) {
            try {
                $measurementDate = new DateTimeImmutable($latestMeasurement['measurement_date']);
                $sanitized['starting_weight_recorded_at'] = $measurementDate->setTimezone($utc);
            } catch (Exception $e) {
                $sanitized['starting_weight_recorded_at'] = $now;
            }
        } else {
            $sanitized['starting_weight_recorded_at'] = $now;
        }
    }

    return [$errors, $sanitized];
}
