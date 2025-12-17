<?php
include(__DIR__ . '/../../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../injection-logs/helpers.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    wl_injection_schedule_settings_error(401, 'Unauthorized');
}

$memberId = $payload['user_id'];
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {
    case 'GET':
        wl_handle_injection_schedule_settings_get($memberId);
        break;
    case 'POST':
        wl_handle_injection_schedule_settings_post($memberId);
        break;
    default:
        header('Allow: GET, POST');
        wl_injection_schedule_settings_error(405, 'Method not allowed');
}

function wl_handle_injection_schedule_settings_get($memberId)
{
    $db = PerchDB::fetch();
    $table = wl_injection_schedule_settings_table();

    $sql = 'SELECT * FROM ' . $table . ' WHERE member_id = ' . $db->pdb($memberId) . ' LIMIT 1';
    $row = $db->get_row($sql);

    if (!is_array($row)) {
        wl_injection_schedule_settings_error(404, 'No settings found for this user.');
    }

    echo json_encode(wl_format_injection_schedule_settings($row));
    exit;
}

function wl_handle_injection_schedule_settings_post($memberId)
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        wl_injection_schedule_settings_error(400, 'Request body must be valid JSON.');
    }

    if (!is_array($data)) {
        $data = [];
    }

    [$errors, $sanitized] = wl_validate_injection_schedule_settings_payload($data);

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        exit;
    }

    $db = PerchDB::fetch();
    $table = wl_injection_schedule_settings_table();

    $sql = 'SELECT * FROM ' . $table . ' WHERE member_id = ' . $db->pdb($memberId) . ' LIMIT 1';
    $existing = $db->get_row($sql);

    $payload = array_merge(['member_id' => $memberId], $sanitized);

    if (is_array($existing)) {
        $payload['created_at'] = $existing['created_at'];

        $assignments = [];
        foreach ($payload as $column => $value) {
            if ($column === 'member_id' || $column === 'created_at') {
                continue;
            }

            if ($value instanceof DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $assignments[] = $column . ' = ' . $db->pdb($value);
        }

        $sql = 'UPDATE ' . $table
            . ' SET ' . implode(', ', $assignments)
            . ' WHERE setting_id = ' . $db->pdb($existing['setting_id'])
            . ' LIMIT 1';

        $result = $db->execute($sql);

        if ($result === false) {
            wl_injection_schedule_settings_error(500, 'Unable to update settings.');
        }
    } else {
        $columns = [];
        $values = [];

        foreach ($payload as $column => $value) {
            if ($value instanceof DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $columns[] = $column;
            $values[] = $db->pdb($value);
        }

        $sql = 'INSERT INTO ' . $table
            . ' (' . implode(',', $columns) . ')'
            . ' VALUES (' . implode(',', $values) . ')';

        $insertId = $db->execute($sql);

        if ($insertId === false) {
            wl_injection_schedule_settings_error(500, 'Unable to create settings.');
        }
    }

    $sql = 'SELECT * FROM ' . $table . ' WHERE member_id = ' . $db->pdb($memberId) . ' LIMIT 1';
    $row = $db->get_row($sql);

    if (!is_array($row)) {
        wl_injection_schedule_settings_error(500, 'Unable to retrieve saved settings.');
    }

    echo json_encode(wl_format_injection_schedule_settings($row));
    exit;
}
