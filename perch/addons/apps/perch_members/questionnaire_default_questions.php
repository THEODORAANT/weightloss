<?php
return array (
  'reorder' => 
  array (
    'weight' => 
    array (
      'label' => 'What is your current weight?',
      'type' => 'text',
      'name' => 'weight',
    ),
    'weight2' => 
    array (
      'label' => 'Weight (lbs hidden input)',
      'type' => 'hidden',
      'name' => 'weight2',
    ),
    'weightunit' => 
    array (
      'label' => 'Weight Unit (kg or st/lbs)',
      'type' => 'radio',
      'name' => 'weightradio-unit',
      'options' => 
      array (
        'kg' => 'kg',
        'st-lbs' => 'st/lbs',
      ),
    ),
    'side_effects' => 
    array (
      'label' => 'Have you experienced any side effects whilst taking the medication?',
      'type' => 'button',
      'name' => 'more_side_effects',
      'options' => 
      array (
        'yes' => 'Yes',
        'no' => 'No',
      ),
    ),
    'more_side_effects' => 
    array (
      'label' => 'Please tell us as much as you can about your side effects - the type, duration, severity and whether they have resolved.',
      'type' => 'textarea',
      'name' => 'more_side_effects',
    ),
    'additional_medication' => 
    array (
      'label' => 'Have you started taking any additional medication?',
      'type' => 'button',
      'name' => 'additional-medication',
      'options' => 
      array (
        'yes' => 'Yes',
        'no' => 'No',
      ),
    ),
    'list_additional_medication' => 
    array (
      'label' => 'Please tell us as much as you can about your side effects - the type, duration, severity and whether they have resolved.',
      'type' => 'textarea',
      'name' => 'list_additional_medication',
    ),
    'rate_current_experience' => 
    array (
      'label' => 'Are you happy with your monthly weight loss?',
      'type' => 'button',
      'name' => 'rate_current_experience',
      'options' => 
      array (
        'yes' => 'Yes',
        'no' => 'No',
      ),
    ),
    'no_happy_reasons' => 
    array (
      'label' => 'Please tell us as much as you can about the reasons you are not happy with your monthly weight loss.',
      'type' => 'textarea',
      'name' => 'no_happy_reasons',
    ),
    'chat_with_us' => 
    array (
      'label' => 'Would you like to chat with someone?',
      'type' => 'button',
      'name' => 'chat_with_us',
      'options' => 
      array (
        'yes' => 'Yes',
        'no' => 'No',
      ),
    ),
    'email_address' => 
    array (
      'label' => 'Please enter your  email address',
      'type' => 'text',
      'name' => 'email_address',
    ),
  ),
  'first-order' => 
  array (
    'consultation' => 
    array (
      'label' => 'agree-consultation',
      'type' => 'text',
      'name' => 'consultation',
    ),
    'age' => 
    array (
      'label' => 'How old are you?',
      'type' => 'radio',
      'name' => 'age',
      'options' => 
      array (
        'under18' => 'Under 18',
        '18to74' => '18 to 74',
        '75over' => '75 or over',
      ),
    ),
    'ethnicity' => 
    array (
      'label' => 'Which ethnicity are you?',
      'type' => 'radio',
      'name' => 'ethnicity',
      'options' => 
      array (
        'asian' => 'Asian or Asian British',
        'black' => 'Black (African/Caribbean)',
        'mixed' => 'Mixed ethnicities',
        'other' => 'Other ethnic group',
        'white' => 'White',
      ),
    ),
    'ethnicity-more' => 
    array (
      'label' => 'Please tell us which ethnicities',
      'type' => 'text',
      'name' => 'ethnicity-more',
    ),
    'gender' => 
    array (
      'label' => 'What sex were you assigned at birth?',
      'type' => 'radio',
      'name' => 'gender',
      'options' => 
      array (
        'male' => 'Male',
        'female' => 'Female',
      ),
    ),
    'pregnancy' => 
    array (
      'label' => 'Are you currently pregnant, trying to get pregnant, or breastfeeding?',
      'type' => 'radio',
      'name' => 'pregnancy',
      'options' => 
      array (
        'yes' => 'Yes',
        'no' => 'No',
      ),
    ),
    'weight' => 
    array (
      'label' => 'What is your weight?',
      'type' => 'text',
      'name' => 'weight',
    ),
    'weightunit' => 
    array (
      'label' => 'weight unit',
      'type' => 'radio',
      'name' => 'weightradio-unit',
      'options' => 
      array (
        'kg' => 'kg',
        'st' => 'st/lbs',
      ),
    ),
    'height' => 
    array (
      'label' => 'What is your height?',
      'type' => 'text',
      'name' => 'height',
    ),
    'heightunit' => 
    array (
      'label' => 'height unit',
      'type' => 'radio',
      'name' => 'heightunit-radio',
      'options' => 
      array (
        'cm' => 'cm',
        'ft-in' => 'ft/in',
      ),
    ),
    'diabetes' => 
    array (
      'label' => 'Have you been diagnosed with diabetes?',
      'type' => 'radio',
      'name' => 'diabetes',
      'options' => 
      array (
        'medicated' => 'I have diabetes and take medication for it',
        'diet' => 'I have diabetes and it\'s diet-controlled',
        'family-history' => 'No, but there is history of diabetes in my family',
        'pre-diabetes' => 'I have pre-diabetes',
        'none' => 'I don\'t have diabetes',
      ),
    ),
    'conditions' => 
    array (
      'label' => 'Do any of the following statements apply to you?',
      'type' => 'checkbox',
      'name' => 'conditions[]',
      'options' => 
      array (
        'malabsorption' => 'I have chronic malabsorption syndrome (problems absorbing food)',
        'cholestasis' => 'I have cholestasis',
        'cancer' => 'I’m currently being treated for cancer',
        'retinopathy' => 'I have diabetic retinopathy',
        'heart-failure' => 'I have severe heart failure',
        'thyroid-cancer' => 'I have a family history of thyroid cancer and/or I’ve had thyroid cancer',
        'men2' => 'I have Multiple endocrine neoplasia type 2 (MEN2)',
        'pancreatitis' => 'I have a history of pancreatitis',
        'eating-disorder' => 'I have or have had an eating disorder such as bulimia, anorexia nervosa, or a binge eating disorder',
        'thyroid-op' => 'I have had surgery or an operation to my thyroid',
        'bariatric-op' => 'I have had a bariatric operation such as gastric band or sleeve surgery',
        'none' => 'None of these statements apply to me',
      ),
    ),
    'bariatricoperation' => 
    array (
      'label' => 'Was your bariatric operation in the last 6 months?',
      'type' => 'radio',
      'name' => 'bariatricoperation',
      'options' => 
      array (
        'yes' => 'Yes',
        'no' => 'No',
      ),
    ),
    'more_pancreatitis' => 
    array (
      'label' => 'Please tell us more about your health condition and how you manage it.',
      'type' => 'text',
      'name' => 'more_pancreatitis',
    ),
    'thyroidoperation' => 
    array (
      'label' => 'Please tell us further details on the thyroid surgery you had, the outcome of the surgery and any ongoing monitoring',
      'type' => 'text',
      'name' => 'thyroidoperation',
    ),
    'conditions2' => 
    array (
      'label' => 'Do any of the following statements apply to you?',
      'type' => 'checkbox',
      'name' => 'conditions2[]',
      'options' => 
      array (
        'mentalhealth' => 'I have been diagnosed with a mental health condition such as depression or anxiety',
        'social-anxiety' => 'My weight makes me anxious in social situations',
        'joint-pain' => 'I have joint pains and/or aches',
        'osteoarthritis' => 'I have osteoarthritis',
        'gord' => 'I have GORD and/or indigestion',
        'cardio' => 'I have a heart/cardiovascular problem',
        'bp' => 'I’ve been diagnosed with, or have a family history of, high blood pressure',
        'cholesterol' => 'I’ve been diagnosed with, or have a family history of, high cholesterol',
        'fatty-liver' => 'I have fatty liver disease',
        'apnoea' => 'I have sleep apnoea',
        'asthma' => 'I have asthma or COPD',
        'ed' => 'I have erectile dysfunction',
        'low-t' => 'I have low testosterone',
        'menopause' => 'I have menopausal symptoms',
        'pcos' => 'I have polycystic ovary syndrome (PCOS)',
        'none' => 'None of these statements apply to me',
      ),
    ),
    'medical_conditions' => 
    array (
      'label' => 'Do you have any other medical conditions?',
      'type' => 'radio',
      'name' => 'medical_conditions',
      'options' => 
      array (
        'yes' => 'Yes',
        'no' => 'No',
      ),
    ),
    'medications' => 
    array (
      'label' => 'Have you ever taken any of the following medications to help you lose weight?',
      'type' => 'checkbox',
      'name' => 'medications[]',
      'options' => 
      array (
        'wegovy' => 'Wegovy',
        'ozempic' => 'Ozempic',
        'saxenda' => 'Saxenda',
        'rybelsus' => 'Rybelsus',
        'mounjaro' => 'Mounjaro',
        'alli' => 'Alli',
        'mysimba' => 'Mysimba',
        'other' => 'Other',
        'never' => 'I have never taken medication to lose weight',
      ),
    ),
    'weight-wegovy' => 
    array (
      'label' => 'What was your weight in kg before starting the weight loss medication?',
      'type' => 'text',
      'name' => 'weight-wegovy',
    ),
    'dose-wegovy' => 
    array (
      'label' => 'When was your last dose of the weight loss medication?',
      'type' => 'radio',
      'name' => 'dose-wegovy',
      'options' => 
      array (
        'lt4' => 'Less than 4 weeks ago',
        '4-6' => '4–6 weeks ago',
        'gt6' => 'More than 6 weeks ago',
      ),
    ),
    'recently-dose-wegovy' => 
    array (
      'label' => 'What dose of the weight loss medication were you prescribed most recently?',
      'type' => 'radio',
      'name' => 'recently-dose-wegovy',
      'options' => 
      array (
        '0.25' => '0.25mg/2.5mg',
        '0.5' => '0.5mg/5mg',
        '1.0' => '1mg/7.5mg',
        '1.7' => '1.7mg/12.5mg',
        '2.4' => '2.4mg/15mg',
        'other' => 'Other',
      ),
    ),
    'continue-dose-wegovy' => 
    array (
      'label' => 'What dose would you like to continue with?',
      'type' => 'radio',
      'name' => 'continue-dose-wegovy',
      'options' => 
      array (
        'increase' => 'Increase my dose',
        'keep' => 'Keep my dose',
        'decrease' => 'Decrease my dose',
        'stop' => 'I don\'t want to continue with this medication',
      ),
    ),
    'effects_with_wegovy' => 
    array (
      'label' => 'Have you experienced any side effects with the weight loss medication?',
      'type' => 'radio',
      'name' => 'effects_with_wegovy',
      'options' => 
      array (
        'yes' => 'Yes',
        'no' => 'No',
      ),
    ),
    'wegovy_side_effects' => 
    array (
      'label' => 'Please tell us as much as you can about your side effects',
      'type' => 'text',
      'name' => 'wegovy_side_effects',
    ),
    'medication_allergies' => 
    array (
      'label' => 'Do you currently take any other medication or have any allergies?',
      'type' => 'checkbox',
      'name' => 'medication_allergies[]',
      'options' => 
      array (
        'levothyroxine' => 'I’m on levothyroxine',
        'warfarin' => 'I’m on warfarin',
        'multiple' => 'Other / I take more than one prescription medication',
        'none' => 'I don’t take any medication',
        'allergy' => 'I have allergies',
      ),
    ),
    'email_address' => 
    array (
      'label' => 'Please enter your GP\'s email address',
      'type' => 'text',
      'name' => 'email_address',
    ),
  ),
);
