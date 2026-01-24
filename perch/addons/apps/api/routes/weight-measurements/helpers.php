<?php
if (!function_exists('wl_weight_measurements_table')) {
    function wl_weight_measurements_table()
    {
        return PERCH_DB_PREFIX . 'getweightloss_measurements';
    }
}

if (!function_exists('wl_weight_measurement_percent_fields')) {
    function wl_weight_measurement_percent_fields()
    {
        return [
            'body_fat_percent',
            'muscle_percent',
            'moisture_percent',
            'protein_percent',
            'skeletal_muscle_percent',
        ];
    }
}

if (!function_exists('wl_cast_float')) {
    function wl_cast_float($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float)$value;
    }
}

if (!function_exists('wl_cast_int')) {
    function wl_cast_int($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int)$value;
    }
}

if (!function_exists('wl_format_datetime')) {
    function wl_format_datetime($value)
    {
        if (!$value) {
            return null;
        }

        try {
            $dt = new DateTimeImmutable($value);
            return $dt->setTimezone(new DateTimeZone('UTC'))->format(DateTime::ATOM);
        } catch (Exception $e) {
            return $value;
        }
    }
}

if (!function_exists('wl_format_measurement')) {
    function wl_format_measurement(array $row, $includeBodyComposition = true)
    {
        $measurement = [
            'id' => isset($row['id']) ? (int)$row['id'] : null,
            'weight_kg' => wl_cast_float($row['weight_kg'] ?? null),
            'bmi' => wl_cast_float($row['bmi'] ?? null),
            'body_fat_percent' => wl_cast_float($row['body_fat_percent'] ?? null),
            'muscle_percent' => wl_cast_float($row['muscle_percent'] ?? null),
            'moisture_percent' => wl_cast_float($row['moisture_percent'] ?? null),
            'bone_mass' => wl_cast_float($row['bone_mass'] ?? null),
            'protein_percent' => wl_cast_float($row['protein_percent'] ?? null),
            'bmr' => wl_cast_int($row['bmr'] ?? null),
            'visceral_fat' => wl_cast_float($row['visceral_fat'] ?? null),
            'skeletal_muscle_percent' => wl_cast_float($row['skeletal_muscle_percent'] ?? null),
            'physical_age' => wl_cast_int($row['physical_age'] ?? null),
            'measurement_date' => wl_format_datetime($row['measurement_date'] ?? null),
            'created_at' => wl_format_datetime($row['created_at'] ?? null),
            'updated_at' => wl_format_datetime($row['updated_at'] ?? null),
        ];

        if (!$includeBodyComposition) {
            foreach ([
                'body_fat_percent',
                'muscle_percent',
                'moisture_percent',
                'bone_mass',
                'protein_percent',
                'skeletal_muscle_percent',
                'visceral_fat',
            ] as $field) {
                unset($measurement[$field]);
            }
        }

        return $measurement;
    }
}

if (!function_exists('wl_parse_bool')) {
    function wl_parse_bool($value, $default = false)
    {
        if ($value === null) {
            return $default;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($filtered === null) {
            return $default;
        }

        return $filtered;
    }
}

if (!function_exists('wl_determine_trend')) {
    function wl_determine_trend($start, $end, $threshold = 0.1)
    {
        if ($start === null || $end === null) {
            return 'unknown';
        }

        $difference = (float)$end - (float)$start;
        if (abs($difference) <= $threshold) {
            return 'stable';
        }

        return $difference > 0 ? 'increasing' : 'decreasing';
    }
}

if (!function_exists('wl_weight_measurements_error')) {
    function wl_weight_measurements_error($statusCode, $message)
    {
        http_response_code($statusCode);
        echo json_encode(['error' => $message]);
        exit;
    }
}

require_once __DIR__ . '/WeightMeasurementsRepository.php';

if (!function_exists('wl_weight_measurements_repository')) {
    function wl_weight_measurements_repository()
    {
        static $repository = null;

        if ($repository === null) {
            $repository = new WeightMeasurementsRepository('getweightloss_measurements', 'weight_measurements');
        }

        return $repository;
    }
}
