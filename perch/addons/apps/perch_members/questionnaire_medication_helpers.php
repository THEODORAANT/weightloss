<?php

if (!function_exists('perch_questionnaire_medications')) {
    function perch_questionnaire_medications(): array
    {
        return [
            'wegovy' => 'Wegovy',
            'ozempic' => 'Ozempic',
            'saxenda' => 'Saxenda',
            'rybelsus' => 'Rybelsus',
            'mounjaro' => 'Mounjaro',
            'alli' => 'Alli',
            'mysimba' => 'Mysimba',
            'other' => 'the weight loss medication',
            'none' => 'None',
        ];
    }
}

if (!function_exists('perch_questionnaire_medication_label')) {
    function perch_questionnaire_medication_label(string $slug): string
    {
        $medications = perch_questionnaire_medications();
        $key = strtolower($slug);
        if (isset($medications[$key])) {
            return $medications[$key];
        }

        $formatted = ucwords(str_replace(['-', '_'], ' ', $key));
        return $formatted !== '' ? $formatted : 'the weight loss medication';
    }
}

if (!function_exists('perch_questionnaire_medication_slug')) {
    function perch_questionnaire_medication_slug(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        return trim($value, '-');
    }
}
