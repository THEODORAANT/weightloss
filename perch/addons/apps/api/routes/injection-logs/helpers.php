<?php
if (!function_exists('wl_injection_logs_table')) {
    function wl_injection_logs_table()
    {
        return PERCH_DB_PREFIX . 'getweightloss_injection_logs';
    }
}

if (!function_exists('wl_format_datetime')) {
    function wl_format_datetime($value)
    {
        if (!$value) {
            return null;
        }

        try {
            $dt = $value instanceof DateTimeInterface ? $value : new DateTimeImmutable($value);
            return $dt->setTimezone(new DateTimeZone('UTC'))->format(DateTime::ATOM);
        } catch (Exception $e) {
            return $value;
        }
    }
}

if (!function_exists('wl_injection_logs_format_float')) {
    function wl_injection_logs_format_float($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float)$value;
    }
}

if (!function_exists('wl_format_injection_log')) {
    function wl_format_injection_log(array $row)
    {
        return [
            'id' => isset($row['log_id']) ? (int)$row['log_id'] : (isset($row['id']) ? (int)$row['id'] : null),
            'injection_date' => wl_format_datetime($row['injection_date'] ?? null),
            'dose_mg' => wl_injection_logs_format_float($row['dose_mg'] ?? null),
            'medication_type' => $row['medication_type'] ?? null,
            'notes' => array_key_exists('notes', $row) ? $row['notes'] : null,
            'created_at' => wl_format_datetime($row['created_at'] ?? null),
            'updated_at' => wl_format_datetime($row['updated_at'] ?? null),
        ];
    }
}

if (!function_exists('wl_injection_logs_error')) {
    function wl_injection_logs_error($statusCode, $message)
    {
        http_response_code($statusCode);
        echo json_encode(['error' => $message]);
        exit;
    }
}
