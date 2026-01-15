<?php

if (!function_exists('api_normalize_dob')) {
    function api_normalize_dob($dob)
    {
        if (!is_string($dob)) {
            return $dob;
        }

        $dob = trim($dob);
        if ($dob === '') {
            return $dob;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
            return $dob;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dob)) {
            $date = DateTime::createFromFormat('d/m/Y', $dob);
            if ($date && $date->format('d/m/Y') === $dob) {
                return $date->format('Y-m-d');
            }
        }

        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dob)) {
            $date = DateTime::createFromFormat('d-m-Y', $dob);
            if ($date && $date->format('d-m-Y') === $dob) {
                return $date->format('Y-m-d');
            }
        }

        return $dob;
    }
}
