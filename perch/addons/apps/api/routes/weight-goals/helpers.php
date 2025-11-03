<?php
if (!function_exists('wl_weight_goals_table')) {
    function wl_weight_goals_table()
    {
        return PERCH_DB_PREFIX . 'getweightloss_weight_goals';
    }
}

if (!function_exists('wl_weight_goals_valid_units')) {
    function wl_weight_goals_valid_units()
    {
        return ['kg', 'lbs', 'stone'];
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

if (!function_exists('wl_weight_goals_convert_to_kg')) {
    function wl_weight_goals_convert_to_kg($value, $unit)
    {
        if ($value === null) {
            return null;
        }

        switch ($unit) {
            case 'lbs':
                return (float)$value * 0.45359237;
            case 'stone':
                return (float)$value * 6.35029318;
            default:
                return (float)$value;
        }
    }
}

if (!function_exists('wl_weight_goals_convert_from_kg')) {
    function wl_weight_goals_convert_from_kg($value, $unit)
    {
        if ($value === null) {
            return null;
        }

        switch ($unit) {
            case 'lbs':
                return (float)$value / 0.45359237;
            case 'stone':
                return (float)$value / 6.35029318;
            default:
                return (float)$value;
        }
    }
}

if (!function_exists('wl_weight_goals_round')) {
    function wl_weight_goals_round($value)
    {
        if ($value === null) {
            return null;
        }

        return round($value, 1);
    }
}

if (!function_exists('wl_format_weight_goal')) {
    function wl_format_weight_goal(array $row)
    {
        return [
            'id' => isset($row['goal_id']) ? (int)$row['goal_id'] : (isset($row['id']) ? (int)$row['id'] : null),
            'goal_weight' => wl_weight_goals_round($row['goal_weight'] ?? null),
            'unit' => $row['unit'] ?? null,
            'target_date' => wl_format_datetime($row['target_date'] ?? null),
            'created_at' => wl_format_datetime($row['created_at'] ?? null),
            'updated_at' => wl_format_datetime($row['updated_at'] ?? null),
        ];
    }
}

if (!function_exists('wl_weight_goals_error')) {
    function wl_weight_goals_error($statusCode, $message)
    {
        http_response_code($statusCode);
        echo json_encode(['error' => $message]);
        exit;
    }
}
