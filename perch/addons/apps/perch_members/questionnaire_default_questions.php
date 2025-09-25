<?php

return [
    'reorder' => [
        'weight' => [
            'label' => 'What is your current weight?',
            'type' => 'text',
            'name' => 'weight',
            'step' => 'weight',
        ],
        'weight2' => [
            'label' => 'Weight (secondary value)',
            'type' => 'hidden',
            'name' => 'weight2',
            'step' => 'weight',
        ],
        'weightunit' => [
            'label' => 'Weight unit',
            'type' => 'radio',
            'name' => 'weightradio-unit',
            'options' => [
                'kg' => 'kg',
                'st-lbs' => 'st/lbs',
            ],
            'step' => 'weight',
        ],
        'side_effects' => [
            'label' => 'Have you experienced any side effects whilst taking the medication?',
            'type' => 'radio',
            'name' => 'side_effects',
            'options' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'step' => 'side-effects',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'question' => 'more_side_effects',
                    'step' => 'more_side_effects',
                ],
            ],
        ],
        'more_side_effects' => [
            'label' => 'Please tell us as much as you can about your side effects - the type, duration, severity and whether they have resolved.',
            'type' => 'textarea',
            'name' => 'more_side_effects',
            'step' => 'more_side_effects',
        ],
        'additional_medication' => [
            'label' => 'Have you started taking any additional medication?',
            'type' => 'radio',
            'name' => 'additional-medication',
            'options' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'step' => 'additional-medication',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'question' => 'list_additional_medication',
                    'step' => 'list_additional_medication',
                ],
            ],
        ],
        'list_additional_medication' => [
            'label' => 'Please tell us as much as you can about your additional medication - the type, duration, severity and whether any side effects have resolved.',
            'type' => 'textarea',
            'name' => 'list_additional_medication',
            'step' => 'list_additional_medication',
        ],
        'rate_current_experience' => [
            'label' => 'Are you happy with your monthly weight loss?',
            'type' => 'radio',
            'name' => 'rate_current_experience',
            'options' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'step' => 'rate_current_experience',
            'dependencies' => [
                [
                    'values' => ['no'],
                    'question' => 'no_happy_reasons',
                    'step' => 'no-happy',
                ],
            ],
        ],
        'no_happy_reasons' => [
            'label' => 'Please tell us as much as you can about the reasons you are not happy with your monthly weight loss.',
            'type' => 'textarea',
            'name' => 'no_happy_reasons',
            'step' => 'no-happy',
        ],
        'chat_with_us' => [
            'label' => 'Would you like to chat with someone?',
            'type' => 'radio',
            'name' => 'chat_with_us',
            'options' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'step' => 'chat_with_us',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'question' => 'email_address',
                    'step' => 'contact',
                ],
            ],
        ],
        'email_address' => [
            'label' => 'Please enter your email address',
            'type' => 'text',
            'name' => 'email_address',
            'step' => 'contact',
        ],
    ],
    'first-order' => [
        'age' => [
            'label' => 'How old are you?',
            'type' => 'radio',
            'name' => 'age',
            'options' => [
                'under18' => 'Under 18',
                '18to74' => 'Older than 18 or Younger than 75',
                '75over' => '75 or over',
            ],
            'step' => 'howold',
            'dependencies' => [
                [
                    'values' => ['under18'],
                    'step'   => 'under18',
                ],
                [
                    'values' => ['75over'],
                    'step'   => '75over',
                ],
                [
                    'values' => ['18to74'],
                    'step'   => '18to74',
                ],
            ],
        ],
        'ethnicity' => [
            'label' => 'Which ethnicity are you?',
            'type' => 'radio',
            'name' => 'ethnicity',
            'options' => [
                'asian' => 'Asian or Asian British',
                'Black (African/Caribbean)' => 'Black (African/Caribbean)',
                'Mixed' => 'Mixed ethnicities',
                'Other' => 'Other ethnic group',
                'White' => 'White',
            ],
            'step' => '18to74',
            'dependencies' => [
                [
                    'values' => ['Mixed', 'Other'],
                    'question' => 'ethnicity-more',
                    'step' => 'Mixed',
                ],
            ],
        ],
        'ethnicity-more' => [
            'label' => 'Please tell us which ethnicities',
            'type' => 'textarea',
            'name' => 'ethnicity-more',
            'step' => 'Mixed',
        ],
        'gender' => [
            'label' => 'What sex were you assigned at birth?',
            'type' => 'radio',
            'name' => 'gender',
            'options' => [
                'Male' => 'Male',
                'Female' => 'Female',
            ],
            'step' => 'ethnicity',
            'dependencies' => [
                [
                    'values' => ['Female'],
                    'question' => 'pregnancy',
                    'step' => 'Female',
                ],
            ],
        ],
        'pregnancy' => [
            'label' => 'Are you currently pregnant, trying to get pregnant, or breastfeeding?',
            'type' => 'radio',
            'name' => 'pregnancy',
            'options' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'step' => 'Female',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'step' => 'pregnancy',
                ],
            ],
        ],
        'weight' => [
            'label' => 'What is your weight?',
            'type' => 'text',
            'name' => 'weight',
            'step' => 'weight',
        ],
        'weight2' => [
            'label' => 'Weight (secondary value)',
            'type' => 'hidden',
            'name' => 'weight2',
            'step' => 'weight',
        ],
        'weightunit' => [
            'label' => 'Weight unit',
            'type' => 'radio',
            'name' => 'weightradio-unit',
            'options' => [
                'kg' => 'kg',
                'st-lbs' => 'st/lbs',
            ],
            'step' => 'weight',
        ],
        'height' => [
            'label' => 'What is your height?',
            'type' => 'text',
            'name' => 'height',
            'step' => 'height',
        ],
        'height2' => [
            'label' => 'Height (secondary value)',
            'type' => 'hidden',
            'name' => 'height2',
            'step' => 'height',
        ],
        'heightunit' => [
            'label' => 'Height unit',
            'type' => 'radio',
            'name' => 'heightunit-radio',
            'options' => [
                'cm' => 'cm',
                'ft-in' => 'ft/in',
            ],
            'step' => 'height',
        ],
        'diabetes' => [
            'label' => 'Have you been diagnosed with diabetes?',
            'type' => 'radio',
            'name' => 'diabetes',
            'options' => [
                'yes-medication' => 'I have diabetes and take medication for it',
                'yes-diet' => 'I have diabetes and it\'s diet-controlled',
                'nohistory' => 'No, but there is history of diabetes in my family',
                'pre-diabetes' => 'I have pre-diabetes',
                'no' => 'I don\'t have diabetes',
            ],
            'step' => 'diabetes',
        ],
        'conditions' => [
            'label' => 'Do any of the following statements apply to you?',
            'type' => 'checkbox',
            'name' => 'conditions[]',
            'options' => [
                'malabsorption' => 'I have chronic malabsorption syndrome (problems absorbing food)',
                'cholestasis' => 'I have cholestasis',
                'cancer' => 'I’m currently being treated for cancer',
                'retinopathy' => 'I have diabetic retinopathy',
                'heartfailure' => 'I have severe heart failure',
                'familythyroid' => 'I have a family history of thyroid cancer and/or I’ve had thyroid cancer',
                'neoplasia' => 'I have Multiple endocrine neoplasia type 2 (MEN2)',
                'pancreatitishistory' => 'I have a history of pancreatitis',
                'eatingdisorder' => 'I have or have had an eating disorder such as bulimia, anorexia nervosa, or a binge eating disorder',
                'thyroidoperation' => 'I have had surgery or an operation to my thyroid',
                'bariatricoperation' => 'I have had a bariatric operation such as gastric band or sleeve surgery',
                'none' => 'None of these statements apply to me',
            ],
            'step' => 'weight2',
            'dependencies' => [
                [
                    'values' => ['pancreatitishistory'],
                    'step' => 'history_pancreatitis',
                ],
                [
                    'values' => ['thyroidoperation'],
                    'question' => 'thyroidoperation',
                    'step' => 'thyroidoperation',
                ],
                [
                    'values' => ['bariatricoperation'],
                    'question' => 'bariatricoperation',
                    'step' => 'bariatricoperation',
                ],
                [
                    'values' => ['eatingdisorder'],
                    'question' => 'more_conditions',
                    'step' => 'more',
                ],
            ],
        ],
        'bariatricoperation' => [
            'label' => 'Was your bariatric operation in the last 6 months?',
            'type' => 'radio',
            'name' => 'bariatricoperation',
            'options' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'step' => 'bariatricoperation',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'step' => 'history_pancreatitis',
                ],
            ],
        ],
        'more_pancreatitis' => [
            'label' => 'Please tell us more about your health condition and how you manage it.',
            'type' => 'textarea',
            'name' => 'more_pancreatitis',
            'step' => 'more_pancreatitis',
        ],
        'thyroidoperation' => [
            'label' => 'Please tell us further details on the thyroid surgery you had, the outcome of the surgery and any ongoing monitoring',
            'type' => 'textarea',
            'name' => 'thyroidoperation',
            'step' => 'thyroidoperation',
        ],
        'more_conditions' => [
            'label' => 'Please tell us more about your health condition and how you manage it.',
            'type' => 'textarea',
            'name' => 'more_conditions',
            'step' => 'more',
        ],
        'conditions2' => [
            'label' => 'Do any of the following statements apply to you?',
            'type' => 'checkbox',
            'name' => 'conditions2[]',
            'options' => [
                'mentalhealth' => 'I have been diagnosed with a mental health condition such as depression or anxiety',
                'anxious' => 'My weight makes me anxious in social situations',
                'joint' => 'I have joint pains and/or aches',
                'osteoarthritis' => 'I have osteoarthritis',
                'indigestion' => 'I have GORD and/or indigestion',
                'cardiovascular' => 'I have a heart/cardiovascular problem',
                'bloodpressure' => 'I’ve been diagnosed with, or have a family history of, high blood pressure',
                'cholesterol' => 'I’ve been diagnosed with, or have a family history of, high cholesterol',
                'fattyliver' => 'I have fatty liver disease',
                'apnoea' => 'I have sleep apnoea',
                'asthma' => 'I have asthma or COPD',
                'erectile' => 'I have erectile dysfunction',
                'testosterone' => 'I have low testosterone',
                'menopausal' => 'I have menopausal symptoms',
                'pcos' => 'I have polycystic ovary syndrome (PCOS)',
                'none' => 'None of these statements apply to me',
            ],
            'step' => 'conditions',
        ],
        'medical_conditions' => [
            'label' => 'Do you have any other medical conditions?',
            'type' => 'radio',
            'name' => 'medical_conditions',
            'options' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'step' => 'medical_conditions',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'question' => 'other_medical_conditions',
                    'step' => 'list_any',
                ],
            ],
        ],
        'other_medical_conditions' => [
            'label' => 'Please list your other medical conditions.',
            'type' => 'textarea',
            'name' => 'other_medical_conditions',
            'step' => 'list_any',
        ],
        'medications' => [
            'label' => 'Have you ever taken any of the following medications to help you lose weight?',
            'type' => 'checkbox',
            'name' => 'medications[]',
            'options' => [
                'wegovy' => 'Wegovy',
                'ozempic' => 'Ozempic',
                'saxenda' => 'Saxenda',
                'rybelsus' => 'Rybelsus',
                'mounjaro' => 'Mounjaro',
                'alli' => 'Alli',
                'mysimba' => 'Mysimba',
                'other' => 'Other',
                'none' => 'I have never taken medication to lose weight',
            ],
            'step' => 'medications',
            'dependencies' => [
                [
                    'values' => ['wegovy', 'ozempic', 'saxenda', 'rybelsus', 'mounjaro', 'alli', 'mysimba', 'other'],
                    'question' => 'weight-wegovy',
                    'step' => 'starting_wegovy',
                ],
            ],
        ],
        'weight-wegovy' => [
            'label' => 'What was your weight before starting the weight loss medication?',
            'type' => 'text',
            'name' => 'weight-wegovy',
            'step' => 'starting_wegovy',
        ],
        'weight2-wegovy' => [
            'label' => 'Weight before medication (secondary value)',
            'type' => 'hidden',
            'name' => 'weight2-wegovy',
            'step' => 'starting_wegovy',
        ],
        'unit-wegovy' => [
            'label' => 'Weight before medication unit',
            'type' => 'radio',
            'name' => 'unit-wegovy',
            'options' => [
                'kg' => 'kg',
                'st-lbs' => 'st/lbs',
            ],
            'step' => 'starting_wegovy',
        ],
        'dose-wegovy' => [
            'label' => 'When was your last dose of the weight loss medication?',
            'type' => 'radio',
            'name' => 'dose-wegovy',
            'options' => [
                'less4' => 'Less than 4 weeks ago',
                '4to6' => '4-6 weeks ago',
                'over6' => 'More than 6 weeks ago',
            ],
            'step' => 'dose_wegovy',
        ],
        'recently-dose-wegovy' => [
            'label' => 'What dose of the weight loss medication were you prescribed most recently?',
            'type' => 'radio',
            'name' => 'recently-dose-wegovy',
            'options' => [
                '25mg' => '0.25mg/2.5mg',
                '05mg' => '0.5mg/5mg',
                '1mg' => '1mg/7.5mg',
                '17mg' => '1.7mg/12.5mg',
                '24mg' => '2.4mg/15mg',
                'other' => 'Other',
            ],
            'step' => 'recently_wegovy',
        ],
        'continue-dose-wegovy' => [
            'label' => 'If you want to continue with the weight loss medication, what dose would you like to continue with?',
            'type' => 'radio',
            'name' => 'continue-dose-wegovy',
            'options' => [
                'increase' => 'Increase my dose',
                'keep' => 'Keep my dose',
                'decrease' => 'Decrease my dose',
                'not-continue' => 'I don\'t want to continue with this medication',
            ],
            'step' => 'continue_with_wegovy',
        ],
        'effects_with_wegovy' => [
            'label' => 'Have you experienced any side effects with the weight loss medication?',
            'type' => 'radio',
            'name' => 'effects_with_wegovy',
            'options' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'step' => 'effects_with_wegovy',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'question' => 'wegovy_side_effects',
                    'step' => 'wegovy_side_effects',
                ],
            ],
        ],
        'wegovy_side_effects' => [
            'label' => 'Please tell us as much as you can about your side effects',
            'type' => 'textarea',
            'name' => 'wegovy_side_effects',
            'step' => 'wegovy_side_effects',
        ],
        'medication_allergies' => [
            'label' => 'Do you currently take any other medication or have any allergies?',
            'type' => 'checkbox',
            'name' => 'medication_allergies[]',
            'options' => [
                'levothyroxine' => 'I’m on levothyroxine',
                'warfarin' => 'I’m on warfarin',
                'other' => 'Other / I take more than one prescription medication',
                'no-medication' => 'I don’t take any medication',
                'allergies' => 'I have allergies',
            ],
            'step' => 'medication_allergies',
            'dependencies' => [
                [
                    'values' => ['other'],
                    'question' => 'other_medication_details',
                    'step' => 'medication_allergies_other',
                ],
            ],
        ],
        'other_medication_details' => [
            'label' => 'Please provide details of the other medication you take, including the name, dose, and how often you take this.',
            'type' => 'textarea',
            'name' => 'other_medication_details',
            'step' => 'medication_allergies_other',
        ],
        'gp_informed' => [
            'label' => 'Would you like your GP to be informed of this consultation?',
            'type' => 'radio',
            'name' => 'gp_informed',
            'options' => [
                'yes' => 'Yes',
                'no' => 'No',
            ],
            'step' => 'gp_informed',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'question' => 'GP_email_address',
                    'step' => 'gp_address',
                ],
            ],
        ],
        'GP_email_address' => [
            'label' => 'Please enter your GP\'s email address',
            'type' => 'text',
            'name' => 'GP_email_address',
            'step' => 'gp_address',
        ],
        'access_special_offers' => [
            'label' => 'Get access to special offers',
            'type' => 'text',
            'name' => 'email_address',
            'step' => 'access_special_offers',
        ],
    ],
];
