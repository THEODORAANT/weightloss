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

if (!function_exists('perch_questionnaire_recent_dose_options')) {
    function perch_questionnaire_recent_dose_options(string $slug): ?array
    {
        $slug = perch_questionnaire_medication_slug($slug);

        $defaultOptions = [
            '25mg' => '0.25mg/2.5mg',
            '05mg' => '0.5mg/5mg',
            '1mg'  => '1mg/7.5mg',
            '17mg' => '1.7mg/12.5mg',
            '24mg' => '2.4mg/15mg',
            'other' => 'Other',
        ];

        if ($slug === 'ozempic') {
            return [
                '0.25 mg' => '0.25 mg',
                '0.5 mg' => '0.5 mg',
                '1 mg' => '1 mg',
                '2 mg' => '2 mg',
            ];
        }

        if ($slug === 'wegovy' || $slug === 'mounjaro') {
            return $defaultOptions;
        }

        return null;
    }
}
