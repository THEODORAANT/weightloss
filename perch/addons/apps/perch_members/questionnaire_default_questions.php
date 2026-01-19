<?php

$contraindicationsQuestionText = <<<QUESTION
Do any of the following statements apply to you?

I have chronic malabsorption syndrome (problems absorbing food)?

I have cholestasis.

I’m currently being treated for cancer.

I have diabetic retinopathy.

I have severe heart failure.

I have a family history of thyroid cancer and/or I’ve had thyroid cancer.

I have Multiple endocrine neoplasia type 2 (MEN2).

I have a history of pancreatitis.

I have or have had an eating disorder such as bulimia, anorexia nervosa, or a binge eating disorder.

I have had surgery or an operation to my thyroid.

I have had a bariatric operation such as gastric band or sleeve surgery.
QUESTION;

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
        ],
        'consent_confirmation' => [
            'label' => 'I will be the sole user of the medication. I will read all relevant information before starting treatment. I will inform the clinical team of any changes to my medical history. I understand that severe diarrhoea for over 24 hours or vomiting within 3 hours of taking the contraceptive pill can reduce its effectiveness. If this happens, I will call my GP or 111 for advice. I understand I may need a repeat dose of the contraceptive pill or to use additional contraception. I will stop the medication if I fall pregnant or try to conceive, and I will let the clinicians know about these changes. I will contact the clinicians if I miss two or more doses. I understand medication may be prescribed off-label when clinically appropriate. I understand that rapid weight loss and injectable weight loss treatments like Mounjaro and Wegovy can both raise the risk of pancreatitis and gallbladder issues. If I have severe abdominal pain, vomiting, jaundice (yellowing of the skin), or worsening symptoms, I will seek urgent medical help. I understand that injectable weight loss treatments like Mounjaro and Wegovy should not be combined with other weight loss medications. I recognise that these treatments may affect my mood. If I experience low mood or any mental health issues, I will stop the treatment and consult a doctor immediately.',
            'type' => 'radio',
            'name' => 'consent_confirmation',
            'options' => [
                'yes' => 'Yes',
            ],
            'step' => 'consent-confirmation',
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
                'PreferNotToSay' => 'Prefer not to say',
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
            'label' => $contraindicationsQuestionText,
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
            'label' => 'Tell me about your gastric surgery procedure.',
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
                'mentalhealth' => 'I have been diagnosed with a mental health condition such as depression or anxiety.',
                'anxious' => 'My weight makes me anxious in social situations.',
                'joint' => 'I have joint pains and/or aches.',
                'osteoarthritis' => 'I have osteoarthritis.',
                'indigestion' => 'I have GORD and/or indigestion.',
                'cardiovascular' => 'I have a heart/cardiovascular problem.',
                'bloodpressure' => 'I’ve been diagnosed with, or have a family history of, high blood pressure.',
                'cholesterol' => 'I’ve been diagnosed with, or have a family history of, high cholesterol.',
                'fattyliver' => 'I have fatty liver disease.',
                'apnoea' => 'I have sleep apnoea.',
                'asthma' => 'I have asthma or COPD.',
                'erectile' => 'I have erectile dysfunction.',
                'testosterone' => 'I have low testosterone.',
                'menopausal' => 'I have menopausal symptoms.',
                'pcos' => 'I have polycystic ovary syndrome (PCOS).',
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
        'other_medications' => [
            'label' => 'Do you currently take any other medication or supplements?',
            'type' => 'radio',
            'name' => 'other_medications',
            'options' => [
                'yes' => 'I take other medication or supplements.',
                'no' => 'I do not take other medication or supplements.',
            ],
            'step' => 'medication_allergies',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'question' => 'other_medication_details',
                    'step' => 'medication_allergies',
                ],
            ],
        ],
        'other_medication_details' => [
            'label' => 'Please provide details of the other medication you take, including the name, dose, and how often you take this.',
            'type' => 'textarea',
            'name' => 'other_medication_details',
            'step' => 'medication_allergies',
        ],
        'allergies' => [
            'label' => 'Do you have any allergies including to medication, food, environmental or anything else?',
            'type' => 'radio',
            'name' => 'allergies',
            'options' => [
                'yes' => 'Yes, I have allergies',
                'no' => 'No allergies',
                'PreferNotToSay' => 'Prefer not to say',
            ],
            'step' => 'medication_allergies',
            'dependencies' => [
                [
                    'values' => ['yes'],
                    'question' => 'allergy_details',
                    'step' => 'medication_allergies',
                ],
            ],
        ],
        'allergy_details' => [
            'label' => 'Please provide detail on your allergy, severity and how it is controlled.',
            'type' => 'textarea',
            'name' => 'allergy_details',
            'step' => 'medication_allergies',
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
                    'question' => 'GP_name',
                    'step' => 'gp_address',
                ],
                [
                    'values' => ['yes'],
                    'question' => 'GP_address',
                    'step' => 'gp_address',
                ],
                [
                    'values' => ['yes'],
                    'question' => 'GP_email_address',
                    'step' => 'gp_address',
                ],
            ],
        ],
        'GP_name' => [
            'label' => "Please enter your GP's name",
            'type' => 'text',
            'name' => 'GP_name',
            'step' => 'gp_address',
        ],
        'GP_address' => [
            'label' => "Please enter your GP's address",
            'type' => 'textarea',
            'name' => 'GP_address',
            'step' => 'gp_address',
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
            'name' => 'special_offers_email',
            'step' => 'access_special_offers',
        ],
    ],
];
