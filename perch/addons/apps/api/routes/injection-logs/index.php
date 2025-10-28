<?php
include(__DIR__ . '/../../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/helpers.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    wl_injection_logs_error(401, 'Unauthorized');
}

$memberId = $payload['user_id'];
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {
    case 'GET':
        wl_handle_injection_logs_list($memberId);
        break;
    case 'POST':
        wl_handle_injection_logs_create($memberId);
        break;
    case 'DELETE':
        wl_handle_injection_logs_delete($memberId);
        break;
    default:
        header('Allow: GET, POST, DELETE');
        wl_injection_logs_error(405, 'Method not allowed');
}

function wl_handle_injection_logs_list($memberId)
{
    global $_ROUTE;

    if (!empty($_ROUTE['params'])) {
        wl_injection_logs_error(404, 'Endpoint not found');
    }

    $pageParam = $_GET['page'] ?? 1;
    $page = filter_var($pageParam, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($page === false) {
        wl_injection_logs_error(400, 'page must be an integer greater than or equal to 1.');
    }

    $limitParam = $_GET['limit'] ?? 30;
    $limit = filter_var($limitParam, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($limit === false) {
        wl_injection_logs_error(400, 'limit must be an integer greater than or equal to 1.');
    }

    $limit = min($limit, 100);

    $utc = new DateTimeZone('UTC');
    $startDate = null;
    $endDate = null;

    if (isset($_GET['start_date']) && $_GET['start_date'] !== '') {
        try {
            $startDate = new DateTimeImmutable($_GET['start_date']);
            $startDate = $startDate->setTimezone($utc);
        } catch (Exception $e) {
            wl_injection_logs_error(400, 'start_date must be a valid ISO 8601 date.');
        }
    }

    if (isset($_GET['end_date']) && $_GET['end_date'] !== '') {
        try {
            $endDate = new DateTimeImmutable($_GET['end_date']);
            $endDate = $endDate->setTimezone($utc);
        } catch (Exception $e) {
            wl_injection_logs_error(400, 'end_date must be a valid ISO 8601 date.');
        }
    }

    if ($startDate && $endDate && $startDate > $endDate) {
        wl_injection_logs_error(400, 'start_date must be before end_date.');
    }

    $offset = ($page - 1) * $limit;
    $total = perch_members_injection_logs_count($memberId, $startDate, $endDate);
    $rows = perch_members_injection_logs_page($memberId, $limit, $offset, $startDate, $endDate);

    $logs = [];
    foreach ($rows as $row) {
        $logs[] = wl_format_injection_log($row);
    }

    $totalPages = $total > 0 ? (int)ceil($total / $limit) : 0;

    $response = [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => $totalPages,
        'has_next' => $totalPages > 0 ? $page < $totalPages : false,
        'has_previous' => $page > 1,
        'data' => $logs,
    ];

    echo json_encode($response);
    exit;
}

function wl_handle_injection_logs_create($memberId)
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        wl_injection_logs_error(400, 'Request body must be valid JSON.');
    }

    if (!is_array($data)) {
        $data = [];
    }

    [$errors, $sanitized] = wl_validate_injection_log_payload($data);

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        exit;
    }

    $log = perch_members_injection_logs_create($memberId, $sanitized);

    if (!is_array($log)) {
        wl_injection_logs_error(500, 'Unable to save injection log.');
    }

    http_response_code(201);
    echo json_encode(wl_format_injection_log($log));
    exit;
}

function wl_handle_injection_logs_delete($memberId)
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
        wl_injection_logs_error(400, 'Injection log ID is required.');
    }

    if (!ctype_digit((string)$idParam)) {
        wl_injection_logs_error(400, 'Injection log ID must be a positive integer.');
    }

    $logId = (int)$idParam;
    if ($logId <= 0) {
        wl_injection_logs_error(400, 'Injection log ID must be a positive integer.');
    }

    $log = perch_members_injection_logs_find($memberId, $logId);

    if (!is_array($log)) {
        wl_injection_logs_error(404, 'Injection log not found.');
    }

    $deleted = perch_members_injection_logs_delete($logId);
    if (!$deleted) {
        wl_injection_logs_error(500, 'Unable to delete injection log.');
    }

    http_response_code(204);
    exit;
}

function wl_validate_injection_log_payload(array $input)
{
    $errors = [];
    $utc = new DateTimeZone('UTC');
    $now = new DateTimeImmutable('now', $utc);

    $sanitized = [
        'injection_date' => null,
        'dose_mg' => null,
        'medication_type' => null,
        'notes' => null,
        'created_at' => $now,
        'updated_at' => $now,
    ];

    if (!array_key_exists('injection_date', $input) || $input['injection_date'] === null || $input['injection_date'] === '') {
        $errors[] = 'injection_date is required.';
    } else {
        try {
            $injectionDate = new DateTimeImmutable($input['injection_date']);
            $sanitized['injection_date'] = $injectionDate->setTimezone($utc);
        } catch (Exception $e) {
            $errors[] = 'injection_date must be a valid ISO 8601 datetime.';
        }
    }

    if (!array_key_exists('dose_mg', $input)) {
        $errors[] = 'dose_mg is required.';
    } elseif (!is_numeric($input['dose_mg'])) {
        $errors[] = 'dose_mg must be a number.';
    } else {
        $dose = (float)$input['dose_mg'];
        if ($dose < 0.25 || $dose > 15.0) {
            $errors[] = 'dose_mg must be between 0.25 and 15.0.';
        } else {
            $sanitized['dose_mg'] = $dose;
        }
    }

    $validMedications = ['semaglutide', 'tirzepatide'];
    if (!array_key_exists('medication_type', $input) || $input['medication_type'] === null || $input['medication_type'] === '') {
        $errors[] = 'medication_type is required.';
    } else {
        $medication = strtolower(trim((string)$input['medication_type']));
        if (!in_array($medication, $validMedications, true)) {
            $errors[] = 'medication_type must be one of: ' . implode(', ', $validMedications) . '.';
        } else {
            $sanitized['medication_type'] = $medication;
        }
    }

    if (array_key_exists('notes', $input) && $input['notes'] !== null && $input['notes'] !== '') {
        if (!is_string($input['notes'])) {
            $errors[] = 'notes must be a string.';
        } else {
            $notes = trim($input['notes']);
            if (mb_strlen($notes) > 1000) {
                $errors[] = 'notes must be 1000 characters or fewer.';
            } else {
                $sanitized['notes'] = $notes;
            }
        }
    }

    return [$errors, $sanitized];
}
