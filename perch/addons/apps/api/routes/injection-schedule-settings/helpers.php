<?php

function wl_injection_schedule_settings_table()
{
    return PERCH_MEASUREMENTS_DB . "." . PERCH_DB_PREFIX . "injection_schedule_settings";
}

function wl_injection_schedule_settings_error($statusCode, $message)
{
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit;
}

function wl_format_injection_schedule_settings(array $row)
{
    return [
        'id' => isset($row['setting_id']) ? (int)$row['setting_id'] : null,
        'frequency_type' => $row['frequency_type'] ?? null,
        'custom_interval_days' => isset($row['custom_interval_days']) ? (int)$row['custom_interval_days'] : null,
        'preferred_day_of_week' => isset($row['preferred_day_of_week']) ? (int)$row['preferred_day_of_week'] : null,
        'reorder_reminders_enabled' => isset($row['reorder_reminders_enabled']) ? (bool)$row['reorder_reminders_enabled'] : true,
        'day_before_reminders_enabled' => isset($row['day_before_reminders_enabled']) ? (bool)$row['day_before_reminders_enabled'] : true,
        'due_day_reminders_enabled' => isset($row['due_day_reminders_enabled']) ? (bool)$row['due_day_reminders_enabled'] : true,
        'reminder_time' => isset($row['reminder_time']) ? substr($row['reminder_time'], 0, 5) : '09:00',
        'created_at' => wl_format_datetime($row['created_at'] ?? null),
        'updated_at' => wl_format_datetime($row['updated_at'] ?? null),
    ];
}

function wl_validate_injection_schedule_settings_payload(array $input)
{
    $errors = [];
    $utc = new DateTimeZone('UTC');
    $now = new DateTimeImmutable('now', $utc);

    $sanitized = [
        'frequency_type' => 'weekly',
        'custom_interval_days' => null,
        'preferred_day_of_week' => null,
        'reorder_reminders_enabled' => 1,
        'day_before_reminders_enabled' => 1,
        'due_day_reminders_enabled' => 1,
        'reminder_time' => '09:00:00',
        'created_at' => $now,
        'updated_at' => $now,
    ];

    $validFrequencies = ['weekly', 'bi-weekly', 'monthly', 'custom'];
    if (array_key_exists('frequency_type', $input) && $input['frequency_type'] !== null && $input['frequency_type'] !== '') {
        $frequency = strtolower(trim((string)$input['frequency_type']));
        if (!in_array($frequency, $validFrequencies, true)) {
            $errors[] = 'frequency_type must be one of: ' . implode(', ', $validFrequencies) . '.';
        } else {
            $sanitized['frequency_type'] = $frequency;
        }
    }

    if (array_key_exists('custom_interval_days', $input) && $input['custom_interval_days'] !== null && $input['custom_interval_days'] !== '') {
        if (!is_numeric($input['custom_interval_days'])) {
            $errors[] = 'custom_interval_days must be a number.';
        } else {
            $days = (int)$input['custom_interval_days'];
            if ($days < 1 || $days > 365) {
                $errors[] = 'custom_interval_days must be between 1 and 365.';
            } else {
                $sanitized['custom_interval_days'] = $days;
            }
        }
    }

    if ($sanitized['frequency_type'] === 'custom' && $sanitized['custom_interval_days'] === null) {
        $errors[] = 'custom_interval_days is required when frequency_type is "custom".';
    }

    if (array_key_exists('preferred_day_of_week', $input) && $input['preferred_day_of_week'] !== null && $input['preferred_day_of_week'] !== '') {
        if (!is_numeric($input['preferred_day_of_week'])) {
            $errors[] = 'preferred_day_of_week must be a number.';
        } else {
            $day = (int)$input['preferred_day_of_week'];
            if ($day < 1 || $day > 7) {
                $errors[] = 'preferred_day_of_week must be between 1 (Monday) and 7 (Sunday).';
            } else {
                $sanitized['preferred_day_of_week'] = $day;
            }
        }
    }

    if (array_key_exists('reorder_reminders_enabled', $input)) {
        $sanitized['reorder_reminders_enabled'] = $input['reorder_reminders_enabled'] ? 1 : 0;
    }

    if (array_key_exists('day_before_reminders_enabled', $input)) {
        $sanitized['day_before_reminders_enabled'] = $input['day_before_reminders_enabled'] ? 1 : 0;
    }

    if (array_key_exists('due_day_reminders_enabled', $input)) {
        $sanitized['due_day_reminders_enabled'] = $input['due_day_reminders_enabled'] ? 1 : 0;
    }

    if (array_key_exists('reminder_time', $input) && $input['reminder_time'] !== null && $input['reminder_time'] !== '') {
        if (!is_string($input['reminder_time'])) {
            $errors[] = 'reminder_time must be a string in HH:mm format.';
        } else {
            $time = trim($input['reminder_time']);
            if (!preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $time)) {
                $errors[] = 'reminder_time must be in HH:mm format (e.g., 09:00).';
            } else {
                $sanitized['reminder_time'] = $time . ':00';
            }
        }
    }

    return [$errors, $sanitized];
}
