<?php
///var/www/html/perch/addons/apps/api/routes/weight-measurements<br />

include(__DIR__ . '/../../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/helpers.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    wl_weight_measurements_error(401, 'Unauthorized');
}

$memberId = $payload['user_id'];
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {
    case 'GET':
        wl_handle_weight_measurements_list($memberId);
        break;
    case 'POST':
        wl_handle_weight_measurements_create($memberId);
        break;
    case 'DELETE':
        wl_handle_weight_measurements_delete($memberId);
        break;
    default:
        header('Allow: GET, POST, DELETE');
        wl_weight_measurements_error(405, 'Method not allowed');
}

function wl_handle_weight_measurements_list($memberId)
{
    global $_ROUTE;

    if (!empty($_ROUTE['params'])) {
        wl_weight_measurements_error(404, 'Endpoint not found');
    }

    $pageParam = $_GET['page'] ?? 1;
    $page = filter_var($pageParam, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($page === false) {
        wl_weight_measurements_error(400, 'page must be an integer greater than or equal to 1.');
    }

    $limitParam = $_GET['limit'] ?? 30;
    $limit = filter_var($limitParam, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($limit === false) {
        wl_weight_measurements_error(400, 'limit must be an integer greater than or equal to 1.');
    }

    $limit = min($limit, 100);
    $includeBodyComposition = wl_parse_bool($_GET['include_body_composition'] ?? null, true);

    $utc = new DateTimeZone('UTC');
    $startDate = null;
    $endDate = null;

    if (isset($_GET['start_date']) && $_GET['start_date'] !== '') {
        try {
            $startDate = new DateTimeImmutable($_GET['start_date']);
            $startDate = $startDate->setTimezone($utc);
        } catch (Exception $e) {
            wl_weight_measurements_error(400, 'start_date must be a valid ISO 8601 date.');
        }
    }

    if (isset($_GET['end_date']) && $_GET['end_date'] !== '') {
        try {
            $endDate = new DateTimeImmutable($_GET['end_date']);
            $endDate = $endDate->setTimezone($utc);
        } catch (Exception $e) {
            wl_weight_measurements_error(400, 'end_date must be a valid ISO 8601 date.');
        }
    }

    if ($startDate && $endDate && $startDate > $endDate) {
        wl_weight_measurements_error(400, 'start_date must be before end_date.');
    }

    $repository = wl_weight_measurements_repository();

    $offset = ($page - 1) * $limit;
    $total = $repository->countForMember($memberId, $startDate, $endDate);
    $rows = $repository->fetchPageForMember($memberId, $limit, $offset, $startDate, $endDate);

    $measurements = [];
    foreach ($rows as $row) {
        $measurements[] = wl_format_measurement($row, $includeBodyComposition);
    }

    $totalPages = $total > 0 ? (int)ceil($total / $limit) : 0;

    $response = [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => $totalPages,
        'has_next' => $totalPages > 0 ? $page < $totalPages : false,
        'has_previous' => $page > 1,
        'data' => $measurements,
    ];

    echo json_encode($response);
    exit;
}

function wl_handle_weight_measurements_create($memberId)
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        wl_weight_measurements_error(400, 'Request body must be valid JSON.');
    }

    if (!is_array($data)) {
        $data = [];
    }

    [$errors, $sanitized] = wl_validate_weight_measurement_payload($data);

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        exit;
    }

    if (wl_weight_measurements_rate_limit_exceeded($memberId, $sanitized['created_at'])) {
        wl_weight_measurements_error(429, 'Rate limit exceeded. You can submit up to 10 measurements per hour.');
    }

    $repository = wl_weight_measurements_repository();
    $measurement = $repository->createMeasurement($memberId, $sanitized);

    if (!is_array($measurement)) {
        wl_weight_measurements_error(500, 'Unable to save measurement.');
    }

    http_response_code(201);
    echo json_encode(wl_format_measurement($measurement));
    exit;
}

function wl_handle_weight_measurements_delete($memberId)
{
    global $_ROUTE;

    $idParam = null;
    if (!empty($_ROUTE['params'])) {
        $idParam = $_ROUTE['params'][0];
    }

    if ($idParam === null && isset($_GET['id'])) {
        $idParam = $_GET['id'];
    }

    if ($idParam === null || $idParam === '') {
        wl_weight_measurements_error(400, 'Measurement ID is required.');
    }

    if (!ctype_digit((string)$idParam)) {
        wl_weight_measurements_error(400, 'Measurement ID must be a positive integer.');
    }

    $measurementId = (int)$idParam;
    if ($measurementId <= 0) {
        wl_weight_measurements_error(400, 'Measurement ID must be a positive integer.');
    }

    $repository = wl_weight_measurements_repository();
    $measurement = $repository->findForMember($memberId, $measurementId);

    if (!is_array($measurement)) {
        wl_weight_measurements_error(404, 'Measurement not found.');
    }

    $deleted = $repository->deleteById($measurementId);
    if (!$deleted) {
        wl_weight_measurements_error(500, 'Unable to delete measurement.');
    }

    http_response_code(204);
    exit;
}

function wl_validate_weight_measurement_payload(array $input)
{
    $errors = [];
    $utc = new DateTimeZone('UTC');
    $now = new DateTimeImmutable('now', $utc);

    $sanitized = [
        'weight_kg' => null,
        'bmi' => null,
        'body_fat_percent' => null,
        'muscle_percent' => null,
        'moisture_percent' => null,
        'bone_mass' => null,
        'protein_percent' => null,
        'bmr' => null,
        'visceral_fat' => null,
        'skeletal_muscle_percent' => null,
        'physical_age' => null,
        'measurement_date' => null,
        'created_at' => $now,
        'updated_at' => $now,
    ];

    if (!array_key_exists('weight_kg', $input)) {
        $errors[] = 'weight_kg is required.';
    } elseif (!is_numeric($input['weight_kg'])) {
        $errors[] = 'weight_kg must be a number.';
    } else {
        $weight = (float)$input['weight_kg'];
        if ($weight < 10 || $weight > 500) {
            $errors[] = 'weight_kg must be between 10 and 500.';
        } else {
            $sanitized['weight_kg'] = $weight;
        }
    }

    $floatRules = [
        'bmi' => [10, 100],
        'body_fat_percent' => [3, 80],
        'muscle_percent' => [0, 100],
        'moisture_percent' => [0, 100],
        'bone_mass' => [0, 20],
        'protein_percent' => [0, 100],
        'visceral_fat' => [0, 50],
        'skeletal_muscle_percent' => [0, 100],
    ];

    foreach ($floatRules as $field => [$min, $max]) {
        if (array_key_exists($field, $input) && $input[$field] !== null && $input[$field] !== '') {
            if (!is_numeric($input[$field])) {
                $errors[] = $field . ' must be a number.';
                continue;
            }

            $value = (float)$input[$field];
            if ($value < $min || $value > $max) {
                $errors[] = $field . ' must be between ' . $min . ' and ' . $max . '.';
                continue;
            }

            $sanitized[$field] = $value;
        }
    }

    $intRules = [
        'bmr' => [0, 10000],
        'physical_age' => [0, 150],
    ];

    foreach ($intRules as $field => [$min, $max]) {
        if (array_key_exists($field, $input) && $input[$field] !== null && $input[$field] !== '') {
            if (!is_numeric($input[$field])) {
                $errors[] = $field . ' must be an integer.';
                continue;
            }

            $value = (int)$input[$field];
            if ($value < $min || $value > $max) {
                $errors[] = $field . ' must be between ' . $min . ' and ' . $max . '.';
                continue;
            }

            $sanitized[$field] = $value;
        }
    }

    if (array_key_exists('measurement_date', $input) && $input['measurement_date'] !== null && $input['measurement_date'] !== '') {
        try {
            $measurementDate = new DateTimeImmutable($input['measurement_date']);
            $sanitized['measurement_date'] = $measurementDate->setTimezone($utc);
        } catch (Exception $e) {
            $errors[] = 'measurement_date must be a valid ISO 8601 datetime.';
        }
    }

    if (!$sanitized['measurement_date']) {
        $sanitized['measurement_date'] = $now;
    }

    $percentValues = [];
    foreach (wl_weight_measurement_percent_fields() as $field) {
        if ($sanitized[$field] !== null) {
            $percentValues[] = $sanitized[$field];
        }
    }

  /*  if (!empty($percentValues)) {
        $sum = array_sum($percentValues);
        if (abs($sum - 100) > 5) {
            $errors[] = 'Body composition percentages should total approximately 100%.';
        }
    }*/

    return [$errors, $sanitized];
}

function wl_weight_measurements_rate_limit_exceeded($memberId, DateTimeImmutable $now)
{
    $repository = wl_weight_measurements_repository();
    $windowStart = $now->sub(new DateInterval('PT1H'));
    $count = $repository->countCreatedSince($memberId, $windowStart);

    return $count >= 10;
}
