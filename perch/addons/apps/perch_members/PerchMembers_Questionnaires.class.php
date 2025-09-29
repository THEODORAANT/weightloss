<?php

require_once __DIR__ . '/questionnaire_medication_helpers.php';

class PerchMembers_Questionnaires extends PerchAPI_Factory
{
    protected $table     = 'questionnaire';
        protected $pk        = 'id';
        protected $singular_classname = 'PerchMembers_Questionnaire';
        protected $medicationSlugs;
        protected static $questionOrderColumnEnsured = false;
        protected static $questionOrderColumnAvailable = null;
        protected static $questionOrderBackfilled = false;
        protected static $questionOrderSessionState = [];
        public function __construct($api = null)
        {
            parent::__construct($api);

            $doseOptions = [
                'less4' => 'Less than 4 weeks ago',
                '4to6' => '4-6 weeks ago',
                'over6' => 'More than 6 weeks ago',
            ];

            $continueDoseOptions = [
                'increase' => 'Increase my dose',
                'keep' => 'Keep my dose',
                'decrease' => 'Decrease my dose',
                'not-continue' => "I don't want to continue with this medication",
            ];

            foreach ($this->getMedicationSlugs() as $slug) {
                $label = $this->getMedicationLabel($slug);
                $weightLabel = 'What was your weight in kg/st-lbs before starting ' . $label . '?';
                $lastDoseLabel = 'When was your last dose of ' . $label . '?';
                $recentDoseLabel = 'What dose of ' . $label . ' were you prescribed most recently?';
                $continueDoseLabel = 'If you want to continue with ' . $label . ', what dose would you like to continue with?';

                $this->steps["weight-{$slug}"] = 'starting_wegovy';
                $this->steps["unit-{$slug}"] = 'starting_wegovy';
                $this->steps["weight2-{$slug}"] = 'starting_wegovy';
                $this->steps["dose-{$slug}"] = 'dose_wegovy';
                $this->steps["recently-dose-{$slug}"] = 'recently_wegovy';
                $this->steps["continue-dose-{$slug}"] = 'continue_with_wegovy';

                $this->questions["weight-{$slug}"] = $weightLabel;
                $this->questions["dose-{$slug}"] = $lastDoseLabel;
                $this->questions["recently-dose-{$slug}"] = $recentDoseLabel;
                $this->questions["continue-dose-{$slug}"] = $continueDoseLabel;

                $this->questions_and_answers["weight-{$slug}"] = [
                    'label' => $weightLabel,
                    'type' => 'text',
                    'name' => "weight-{$slug}",
                ];

                $this->questions_and_answers["dose-{$slug}"] = [
                    'label' => $lastDoseLabel,
                    'type' => 'radio',
                    'name' => "dose-{$slug}",
                    'options' => $doseOptions,
                ];

                $recentDoseOptions = perch_questionnaire_recent_dose_options($slug);
                $recentDoseField = [
                    'label' => $recentDoseLabel,
                    'name' => "recently-dose-{$slug}",
                ];

                if ($recentDoseOptions !== null) {
                    $recentDoseField['type'] = 'radio';
                    $recentDoseField['options'] = $recentDoseOptions;
                } else {
                    $recentDoseField['type'] = 'text';
                }

                $this->questions_and_answers["recently-dose-{$slug}"] = $recentDoseField;

                $this->questions_and_answers["continue-dose-{$slug}"] = [
                    'label' => $continueDoseLabel,
                    'type' => 'radio',
                    'name' => "continue-dose-{$slug}",
                    'options' => $continueDoseOptions,
                ];
            }

            $this->ensureQuestionOrderColumnExists();
        }

        protected function getMedicationSlugs(): array
        {
            if ($this->medicationSlugs === null) {
                $options = perch_questionnaire_medications();
                $slugs = [];
                foreach ($options as $slug => $label) {
                    if ($slug === 'none') {
                        continue;
                    }
                    $slugs[] = $slug;
                }

                $this->medicationSlugs = $slugs;
            }

            return $this->medicationSlugs;
        }

        protected function getMedicationLabel(string $slug): string
        {
            return perch_questionnaire_medication_label($slug);
        }

        protected function ensureQuestionOrderColumnExists(): void
        {
            if (self::$questionOrderColumnEnsured && self::$questionOrderColumnAvailable !== null) {
                return;
            }

            $table = PERCH_DB_PREFIX . 'questionnaire';
            $sql   = "SHOW COLUMNS FROM `{$table}` LIKE 'question_order'";
            $exists = $this->db->get_value($sql);

            if (!$exists) {
                $alter = "ALTER TABLE `{$table}` ADD COLUMN `question_order` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `question_slug`";
                try {
                    $this->db->execute($alter);
                } catch (Exception $e) {
                    // If the column still cannot be created we silently ignore to avoid blocking execution.
                }

                $exists = $this->db->get_value($sql);
            }

            self::$questionOrderColumnAvailable = $exists ? true : false;
            self::$questionOrderColumnEnsured = true;

            if (self::$questionOrderColumnAvailable) {
                $this->backfillQuestionOrderValues();
            }
        }

        protected function questionOrderColumnAvailable(): bool
        {
            if (self::$questionOrderColumnAvailable !== null) {
                return self::$questionOrderColumnAvailable;
            }

            $this->ensureQuestionOrderColumnExists();

            if (self::$questionOrderColumnAvailable !== null) {
                return self::$questionOrderColumnAvailable;
            }

            $table = PERCH_DB_PREFIX . 'questionnaire';
            $sql   = "SHOW COLUMNS FROM `{$table}` LIKE 'question_order'";
            $exists = $this->db->get_value($sql);

            self::$questionOrderColumnAvailable = $exists ? true : false;

            if (self::$questionOrderColumnAvailable) {
                $this->backfillQuestionOrderValues();
            }

            return self::$questionOrderColumnAvailable;
        }

        protected function backfillQuestionOrderValues(): void
        {
            if (self::$questionOrderBackfilled || !self::$questionOrderColumnAvailable) {
                return;
            }

            $table = PERCH_DB_PREFIX . 'questionnaire';
            $types = ['first-order', 're-order'];

            foreach ($types as $type) {
                [$orderMap] = $this->buildQuestionOrderMap($type);

                if (empty($orderMap)) {
                    continue;
                }

                $caseStatements = [];
                foreach ($orderMap as $slug => $position) {
                    $caseStatements[] = 'WHEN ' . $this->db->pdb($slug) . ' THEN ' . (int)$position;
                }

                if (empty($caseStatements)) {
                    continue;
                }

                $caseSql = implode(' ', $caseStatements);
                $sql = "UPDATE `{$table}` SET question_order = CASE question_slug {$caseSql} ELSE question_order END "
                    . 'WHERE question_order IS NULL AND type = ' . $this->db->pdb($type);

                try {
                    $this->db->execute($sql);
                } catch (Exception $e) {
                    // If the backfill fails we ignore the error so that normal execution can continue.
                }
            }

            self::$questionOrderBackfilled = true;
        }

        protected function buildQuestionOrderMap($type): array
        {
            $orderMap = [];
            $position = 1;

            $questionSets = [$this->get_questions($type), $this->get_questions_answers($type)];
            foreach ($questionSets as $set) {
                if (!is_array($set)) {
                    continue;
                }

                foreach (array_keys($set) as $slug) {
                    if (!isset($orderMap[$slug])) {
                        $orderMap[$slug] = $position++;
                    }
                }
            }

            return [$orderMap, $position];
        }

        protected function getQuestionOrderForSession($sessionKey, $type, $slug): ?int
        {
            if (!$this->questionOrderColumnAvailable()) {
                return null;
            }

            if (!isset(self::$questionOrderSessionState[$sessionKey])) {
                [$orderMap, $nextPosition] = $this->buildQuestionOrderMap($type);
                self::$questionOrderSessionState[$sessionKey] = [
                    'map' => $orderMap,
                    'next' => $nextPosition,
                ];
            }

            $state =& self::$questionOrderSessionState[$sessionKey];

            if (!isset($state['map'][$slug])) {
                $state['map'][$slug] = $state['next'];
                $state['next']++;
            }

            return $state['map'][$slug];
        }

        public $reorder_questions=[
        "weight"=>"What is your weight?",
        "weight2"=>"inches",
        "weightunit"=>"weight unit",
        "pregnancy_status"=>"Are you pregnant or trying to conceive?",
        "bmi"=>"BMI",
        "side_effects"=>"Have you experienced any side effects whilst taking the medication? ",
	"more_side_effects"=>"Please tell us as much as you can about your side effects",
	"additional-medication"=>"Have you started taking any additional medication?",
	"list_additional_medication"=>"Please tell us as much as you can about your  additional medication",
	"rate_current_experience"=>"Are you happy with your monthly weight loss?",
	"no_happy_reasons"=>"Please tell us as much as you can about the reasons you are not happy with your monthly weight loss.",
	"chat_with_us"=>"Would you like to chat with someone?",
	"email_address"=>"Please enter your  email address",
	 "multiple_answers"=>"Have client alter answers?",
	 "documents"=>"Member Documents",
	];
	public $steps=[
    "age"=>"howold",
    "ethnicity"=>"18to74",
    "ethnicity-more"=>"Mixed",
    "gender"=>"ethnicity",
    "pregnancy"=>"Female",
    "weight"=>"weight",
    "height"=>"height",
    "diabetes"=>"diabetes",
    "conditions"=>"weight2",
    "bariatricoperation"=>"bariatricoperation",
    "more_pancreatitis"=>"more_pancreatitis",
    "thyroidoperation"=>"thyroidoperation",
    "more_conditions"=>"more",
    "conditions2"=>"conditions",
    "medical_conditions"=>"medical_conditions",
    "medications"=>"medications",
    "weight-wegovy"=>"starting_wegovy",
    "dose-wegovy"=>"dose_wegovy",
    "recently-dose-wegovy"=>"recently_wegovy",
    "continue-dose-wegovy"=>"continue_with_wegovy",
    "effects_with_wegovy"=>"effects_with_wegovy",
    "medication_allergies"=>"medication_allergies",
    "other_medication_details"=>"medication_allergies",
    "other_medical_conditions"=>"list_any",
    "wegovy_side_effects"=>"wegovy_side_effects",
    "gp_informed"=>"gp_informed",
    "GP_email_address"=>"gp_address",
    "Get access to special offers"=>"access_special_offers"
    ];
    public $reorder_questions_answers = [
        "weight" => [
            "label" => "What is your current weight?",
            "type" => "text",
            "name" => "weight"
        ],
        "weight2" => [
            "label" => "Weight (lbs hidden input)",
            "type" => "hidden",
            "name" => "weight2"
        ],
        "weightunit" => [
            "label" => "Weight Unit (kg or st/lbs)",
            "type" => "radio",
            "name" => "weightradio-unit",
            "options" => [
                "kg" => "kg",
                "st-lbs" => "st/lbs"
            ]
        ],
        "pregnancy_status" => [
            "label" => "Are you pregnant or trying to conceive?",
            "type" => "button",
            "name" => "pregnancy_status",
            "options" => [
                "yes" => "Yes",
                "no" => "No"
            ]
        ],
        "side_effects" => [
            "label" => "Have you experienced any side effects whilst taking the medication?",
            "type" => "button",
            "name" => "more_side_effects",
            "options" => [
                "yes" => "Yes",
                "no" => "No"
            ]
        ],
        "more_side_effects" => [
            "label" => "Please tell us as much as you can about your side effects - the type, duration, severity and whether they have resolved.",
            "type" => "textarea",
            "name" => "more_side_effects"
        ],
        "additional_medication" => [
            "label" => "Have you started taking any additional medication?",
            "type" => "button",
            "name" => "additional-medication",
            "options" => [
                "yes" => "Yes",
                "no" => "No"
            ]
        ],
        "list_additional_medication" => [
            "label" => "Please tell us as much as you can about your side effects - the type, duration, severity and whether they have resolved.",
            "type" => "textarea",
            "name" => "list_additional_medication"
        ],
        "rate_current_experience" => [
            "label" => "Are you happy with your monthly weight loss?",
            "type" => "button",
            "name" => "rate_current_experience",
            "options" => [
                "yes" => "Yes",
                "no" => "No"
            ]
        ],
        "no_happy_reasons" => [
            "label" => "Please tell us as much as you can about the reasons you are not happy with your monthly weight loss.",
            "type" => "textarea",
            "name" => "no_happy_reasons"
        ],
        "chat_with_us" => [
            "label" => "Would you like to chat with someone?",
            "type" => "button",
            "name" => "chat_with_us",
            "options" => [
                "yes" => "Yes",
                "no" => "No"
            ]
        ],
        "email_address" => [
            "label" => "Please enter your  email address",
            "type" => "text",
            "name" => "email_address"
        ]
    ];

    public $questions_and_answers  = [
                                       "consultation" => [
                                           "label" => "agree-consultation",
                                           "type" => "text",
                                           "name" => "consultation"
                                       ],
                                       "age" => [
                                           "label" => "How old are you?",
                                           "type" => "radio",
                                           "name" => "age",
                                           "options" => [
                                               "under18" => "Under 18",
                                               "18to74" => "18 to 74",
                                               "75over" => "75 or over"
                                           ]
                                       ],
                                       "ethnicity" => [
                                           "label" => "Which ethnicity are you?",
                                           "type" => "radio",
                                           "name" => "ethnicity",
                                           "options" => [
                                               "asian" => "Asian or Asian British",
                                               "Black (African/Caribbean)" => "Black (African/Caribbean)",
                                               "mixed" => "Mixed ethnicities",
                                               "other" => "Other ethnic group",
                                               "white" => "White"
                                           ]
                                       ],
                                       "ethnicity-more" => [
                                           "label" => "Please tell us which ethnicities",
                                           "type" => "text",
                                           "name" => "ethnicity-more"
                                       ],
                                       "gender" => [
                                           "label" => "What sex were you assigned at birth?",
                                           "type" => "radio",
                                           "name" => "gender",
                                           "options" => [
                                               "male" => "Male",
                                               "female" => "Female"
                                           ]
                                       ],
                                       "pregnancy" => [
                                           "label" => "Are you currently pregnant, trying to get pregnant, or breastfeeding?",
                                           "type" => "radio",
                                           "name" => "pregnancy",
                                           "options" => [
                                               "yes" => "Yes",
                                               "no" => "No"
                                           ]
                                       ],
                                       "weight" => [
                                           "label" => "What is your weight?",
                                           "type" => "text",
                                           "name" => "weight"
                                       ],
                                       "weightunit" => [
                                           "label" => "weight unit",
                                           "type" => "radio",
                                           "name" => "weightradio-unit",
                                           "options" => [
                                               "kg" => "kg",
                                               "st" => "st/lbs"
                                           ]
                                       ],
                                       "height" => [
                                           "label" => "What is your height?",
                                           "type" => "text",
                                           "name" => "height"
                                       ],
                                       "heightunit" => [
                                           "label" => "height unit",
                                           "type" => "radio",
                                           "name" => "heightunit-radio",
                                           "options" => [
                                               "cm" => "cm",
                                               "ft-in" => "ft/in"
                                           ]
                                       ],
                                       "diabetes" => [
                                           "label" => "Have you been diagnosed with diabetes?",
                                           "type" => "radio",
                                           "name" => "diabetes",
                                           "options" => [
                                               "yes-medication" => "I have diabetes and take medication for it",
                                               "yes-diet" => "I have diabetes and it's diet-controlled",
                                               "nohistory" => "No, but there is history of diabetes in my family",
                                               "pre-diabetes" => "I have pre-diabetes",
                                               "no" => "I don't have diabetes"
                                           ]
                                       ],
                                       "conditions" => [
                                           "label" => "Do any of the following statements apply to you?",
                                           "type" => "checkbox",
                                           "name" => "conditions[]",
                                           "options" => [
                                               "malabsorption" => "I have chronic malabsorption syndrome (problems absorbing food)",
                                               "cholestasis" => "I have cholestasis",
                                               "cancer" => "I’m currently being treated for cancer",
                                               "retinopathy" => "I have diabetic retinopathy",
                                               "heartfailure" => "I have severe heart failure",
                                               "familythyroid" => "I have a family history of thyroid cancer and/or I’ve had thyroid cancer",
                                               "neoplasia" => "I have Multiple endocrine neoplasia type 2 (MEN2)",
                                               "pancreatitishistory" => "I have a history of pancreatitis",
                                               "eatingdisorder" => "I have or have had an eating disorder such as bulimia, anorexia nervosa, or a binge eating disorder",
                                               "thyroidoperation" => "I have had surgery or an operation to my thyroid",
                                               "bariatricoperation" => "I have had a bariatric operation such as gastric band or sleeve surgery",
                                               "none" => "None of these statements apply to me"
                                           ]
                                       ],
                                       "bariatricoperation" => [
                                           "label" => "Was your bariatric operation in the last 6 months?",
                                           "type" => "radio",
                                           "name" => "bariatricoperation",
                                           "options" => [
                                               "yes" => "Yes",
                                               "no" => "No"
                                           ]
                                       ],
                                       "more_pancreatitis" => [
                                           "label" => "Please tell us more about your health condition and how you manage it.",
                                           "type" => "text",
                                           "name" => "more_pancreatitis"
                                       ],
                                       "thyroidoperation" => [
                                           "label" => "Please tell us further details on the thyroid surgery you had, the outcome of the surgery and any ongoing monitoring",
                                           "type" => "text",
                                           "name" => "thyroidoperation"
                                       ],
                                       "conditions2" => [
                                           "label" => "Do any of the following statements apply to you?",
                                           "type" => "checkbox",
                                           "name" => "conditions2[]",
                                           "options" => [
                                               "mentalhealth" => "I have been diagnosed with a mental health condition such as depression or anxiety",
                                               "anxious" => "My weight makes me anxious in social situations",
                                               "joint" => "I have joint pains and/or aches",
                                               "osteoarthritis" => "I have osteoarthritis",
                                               "indigestion" => "I have GORD and/or indigestion",
                                               "cardiovascular" => "I have a heart/cardiovascular problem",
                                               "bloodpressure" => "I’ve been diagnosed with, or have a family history of, high blood pressure",
                                               "cholesterol" => "I’ve been diagnosed with, or have a family history of, high cholesterol",
                                               "fattyliver" => "I have fatty liver disease",
                                               "apnoea" => "I have sleep apnoea",
                                               "asthma" => "I have asthma or COPD",
                                               "erectile" => "I have erectile dysfunction",
                                               "testosterone" => "I have low testosterone",
                                               "menopausal" => "I have menopausal symptoms",
                                               "pcos" => "I have polycystic ovary syndrome (PCOS)",
                                               "none" => "None of these statements apply to me"
                                           ]
                                       ],
                                       "medical_conditions" => [
                                           "label" => "Do you have any other medical conditions?",
                                           "type" => "radio",
                                           "name" => "medical_conditions",
                                           "options" => [
                                               "yes" => "Yes",
                                               "no" => "No"
                                           ]
                                       ],
                                       "medications" => [
                                           "label" => "Have you ever taken any of the following medications to help you lose weight?",
                                           "type" => "checkbox",
                                           "name" => "medications[]",
                                           "options" => [
                                               "wegovy" => "Wegovy",
                                               "ozempic" => "Ozempic",
                                               "saxenda" => "Saxenda",
                                               "rybelsus" => "Rybelsus",
                                               "mounjaro" => "Mounjaro",
                                               "alli" => "Alli",
                                               "mysimba" => "Mysimba",
                                               "other" => "Other",
                                               "never" => "I have never taken medication to lose weight"
                                           ]
                                       ],
                                       "weight-wegovy" => [
                                           "label" => "What was your weight in kg before starting the weight loss medication?",
                                           "type" => "text",
                                           "name" => "weight-wegovy"
                                       ],
                                       "dose-wegovy" => [
                                           "label" => "When was your last dose of the weight loss medication?",
                                           "type" => "radio",
                                           "name" => "dose-wegovy",
                                           "options" => [
                                               "less4" => "Less than 4 weeks ago",
                                               "4to6" => "4–6 weeks ago",
                                               "over6" => "More than 6 weeks ago"
                                           ]
                                       ],
                                       "recently-dose-wegovy" => [
                                           "label" => "What dose of the weight loss medication were you prescribed most recently?",
                                           "type" => "radio",
                                           "name" => "recently-dose-wegovy",
                                           "options" => [
                                               "25mg" => "0.25mg/2.5mg",
                                               "05mg" => "0.5mg/5mg",
                                               "1mg" => "1mg/7.5mg",
                                               "17mg" => "1.7mg/12.5mg",
                                               "24mg" => "2.4mg/15mg",
                                               "other" => "Other"
                                           ]
                                       ],
                                       "continue-dose-wegovy" => [
                                           "label" => "What dose would you like to continue with?",
                                           "type" => "radio",
                                           "name" => "continue-dose-wegovy",
                                           "options" => [
                                               "increase" => "Increase my dose",
                                               "keep" => "Keep my dose",
                                               "decrease" => "Decrease my dose",
                                               "not-continue" => "I don't want to continue with this medication"
                                           ]
                                       ],
                                       "effects_with_wegovy" => [
                                           "label" => "Have you experienced any side effects with the weight loss medication?",
                                           "type" => "radio",
                                           "name" => "effects_with_wegovy",
                                           "options" => [
                                               "yes" => "Yes",
                                               "no" => "No"
                                           ]
                                       ],
                                       "wegovy_side_effects" => [
                                           "label" => "Please tell us as much as you can about your side effects",
                                           "type" => "text",
                                           "name" => "wegovy_side_effects"
                                       ],
                                       "medication_allergies" => [
                                           "label" => "Do you currently take any other medication or have any allergies?",
                                           "type" => "checkbox",
                                           "name" => "medication_allergies[]",
                                           "options" => [
                                               "levothyroxine" => "I’m on levothyroxine",
                                               "warfarin" => "I’m on warfarin",
                                               "other" => "Other / I take more than one prescription medication",
                                               "no-medication" => "I don’t take any medication",
                                               "allergies" => "I have allergies"
                                           ]
                                       ],
                                       "other_medication_details" => [
                                           "label" => "Please provide details of the other medication you take, including the name, dose, and how often you take this.",
                                           "type" => "textarea",
                                           "name" => "other_medication_details"
                                       ],
                                       "email_address" => [
                                           "label" => "Please enter your GP's email address",
                                           "type" => "text",
                                           "name" => "email_address"
                                       ]
                                   ];


		public $questions=[
	"consultation"=>"agree-consultation",
    "age"=>"How old are you?",
    "ethnicity"=>"Which ethnicity are you?",
    "ethnicity-more"=>"Please tell us which ethnicities",
    "gender"=>"What sex were you assigned at birth?",
    "pregnancy"=>"Are you currently pregnant, trying to get pregnant, or breastfeeding?",
    "weight"=>"What is your weight?",
      //"weight2"=>"",
    "weightunit"=>"weight unit",
    "height"=>"What is your height?",
    // "height2"=>"",
    "heightunit"=>"height unit",
    "diabetes"=>"Have you been diagnosed with diabetes?",
    "conditions"=>"Do any of the following statements apply to you?",
    "bariatricoperation"=>"Was your bariatric operation in the last 6 months? ",
    "more_pancreatitis"=>"Please tell us more about your mental health condition and how you manage it",
    "thyroidoperation"=>"Please tell us further details on the thyroid surgery you had, the outcome of the surgery and any ongoing monitoring",
    "more_conditions"=>"Please tell us more about your mental health condition and how you manage it",
    "conditions2"=>"Do any of the following statements apply to you?",
    "medical_conditions"=>"Do you have any other medical conditions?",
    "medications"=>"Have you ever taken any of the following medications to help you lose weight?",
    "weight-wegovy"=>"What was your weight in kg before starting the weight loss medication?",
    "dose-wegovy"=>"When was your last dose of the weight loss medication?",
    "recently-dose-wegovy"=>"What dose of the weight loss medication were you prescribed most recently?",
    "continue-dose-wegovy"=>"If you want to continue with the weight loss medication, what dose would you like to continue with?",
    "effects_with_wegovy"=>"Have you experienced any side effects with the weight loss medication?",
    "medication_allergies"=>"Do you currently take any other medication or have any allergies?",
    "other_medication_details"=>"Please provide details of the other medication you take, including the name, dose, and how often you take this.",
    "other_medical_conditions"=>"Please list any other medical conditions you have. ",
    "wegovy_side_effects"=>"Please tell us as much as you can about your side effects - the type, duration, severity and whether they have resolved",
    "gp_informed"=>"Would you like your GP to be informed of this consultation?",
    "email_address"=>"Please enter your GP's email address",
    "Get access to special offers"=>"email_address",
    "multiple_answers"=>"Have client alter answers?",
    "documents"=>"Member Documents",
    "bmi"=>"BMI",
    ];
   /* protected $required_answers=[
    "age"=>["18to74"],
    "ethnicity"=>["asian","Black (African/Caribbean)"],

    ]*/

        protected $default_sort_column = 'created_at';
        public $static_fields = array('version','question_text', 'question_slug', 'question_order', 'question_slug', 'answer', 'answer_text','member_id');
	public function get_questions($type='first-order')
    {
    if($type=="re-order"){
     return $this->reorder_questions;
    }
    return $this->questions;
    }
        public function get_questions_answers($type='first-order')
        {
        if($type=="re-order"){
         return $this->reorder_questions_answers;
        }
        return $this->questions_and_answers;
        }

    protected function resolveAnswerTextFromConfig($value, array $questionConfig)
    {
        $options = (isset($questionConfig['options']) && is_array($questionConfig['options']))
            ? $questionConfig['options']
            : null;

        if (is_array($value)) {
            $formatted = [];
            $rawValues = [];

            foreach ($value as $optionKey => $optionValue) {
                if (is_scalar($optionValue)) {
                    $rawValues[] = trim((string)$optionValue);
                } elseif (is_array($optionValue)) {
                    $rawValues[] = trim(implode(', ', array_map('strval', $optionValue)));
                }

                if (is_array($optionValue)) {
                    $candidate = $optionValue;
                } elseif (!is_numeric($optionKey) && ($optionValue === 'on' || $optionValue === true || $optionValue === 1)) {
                    $candidate = $optionKey;
                } else {
                    $candidate = $optionValue;
                }

                if ((is_string($candidate) || is_numeric($candidate)) && trim((string)$candidate) === '' && !is_numeric($optionKey)) {
                    $candidate = $optionKey;
                }

                if (is_array($candidate)) {
                    $candidate = trim(implode(', ', array_map('strval', $candidate)));
                }

                $label = $this->mapOptionValueToLabel($candidate, $options);

                if ($label !== '') {
                    $formatted[] = $label;
                }
            }

            $formatted = array_values(array_unique(array_filter(array_map(function ($text) {
                return trim((string)$text);
            }, $formatted), 'strlen')));

            if (!empty($formatted)) {
                return implode(', ', $formatted);
            }

            $rawValues = array_values(array_unique(array_filter($rawValues, 'strlen')));
            if (!empty($rawValues)) {
                return implode(', ', $rawValues);
            }

            return '';
        }

        return $this->mapOptionValueToLabel($value, $options);
    }

    protected function mapOptionValueToLabel($value, ?array $options)
    {
        if (is_array($value)) {
            $value = implode(', ', array_map('strval', $value));
        }

        if ($value === null) {
            $value = '';
        }

        if (!is_array($options) || empty($options)) {
            return is_scalar($value) ? trim((string)$value) : '';
        }

        if (isset($options[$value])) {
            return trim((string)$options[$value]);
        }

        foreach ($options as $optionKey => $optionLabel) {
            if (strcasecmp((string)$optionKey, (string)$value) === 0) {
                return trim((string)$optionLabel);
            }
        }

        foreach ($options as $optionKey => $optionLabel) {
            if (strcasecmp((string)$optionLabel, (string)$value) === 0) {
                return trim((string)$optionLabel);
            }
        }

        return is_scalar($value) ? trim((string)$value) : '';
    }
        public function get_for_member($memberID,$type="first-order")
    {
        $sql = 'SELECT d.*
                FROM  '.PERCH_DB_PREFIX.'questionnaire d
                WHERE d.member_id='.$this->db->pdb((int)$memberID)
                .' AND d.type='.$this->db->pdb($type);

        if ($this->questionOrderColumnAvailable()) {
            $sql .= ' ORDER BY d.qid DESC, (d.question_order IS NULL), d.question_order ASC, d.created_at ASC, d.id ASC';
        } else {
            $sql .= ' ORDER BY d.id DESC';
        }

        return $this->return_instances($this->db->get_rows($sql));
    }

    public function get_for_order($orderID, $type = null)
    {
        $sql = 'SELECT d.*
                FROM  '.PERCH_DB_PREFIX.'questionnaire d
                WHERE d.order_id='.$this->db->pdb((int)$orderID);

        if ($type !== null) {
            $sql .= ' AND d.type='.$this->db->pdb($type);
        }

        if ($this->questionOrderColumnAvailable()) {
            $sql .= ' ORDER BY d.qid DESC, (d.question_order IS NULL), d.question_order ASC, d.created_at ASC, d.id ASC';
        } else {
            $sql .= ' ORDER BY d.id DESC';
        }

        return $this->return_instances($this->db->get_rows($sql));
    }

function displayUserAnswerHistoryUI(string $userId, string $logDir = 'logs') {
    $filePath = "/var/www/html/{$logDir}/{$userId}_raw_log.json";

    if (!file_exists($filePath)) {
        echo "<p style='color:red;'>❌ Log file not found for user ID: {$userId}</p>";
        return;
    }

    $data = json_decode(file_get_contents($filePath), true);

    if (!$data || !isset($data['log'])) {
        echo "<p style='color:red;'>⚠️ Log file is invalid or missing log entries.</p>";
        return;
    }

    $logEntries = $data['log'];

    if (is_array($logEntries)) {
        foreach ($logEntries as $index => &$entry) {
            if (!is_array($entry)) {
                $entry = ['_value' => $entry, '_sequence' => $index];
                continue;
            }

            $entry['_sequence'] = $index;
        }
        unset($entry);

        usort($logEntries, function ($a, $b) {
            $timeA = isset($a['time']) ? strtotime((string)$a['time']) : false;
            $timeB = isset($b['time']) ? strtotime((string)$b['time']) : false;

            if ($timeA !== false && $timeB !== false && $timeA !== $timeB) {
                return $timeA <=> $timeB;
            }

            if ($timeA !== false && $timeB === false) {
                return -1;
            }

            if ($timeA === false && $timeB !== false) {
                return 1;
            }

            $seqA = $a['_sequence'] ?? 0;
            $seqB = $b['_sequence'] ?? 0;

            return $seqA <=> $seqB;
        });

        foreach ($logEntries as &$entry) {
            if (!is_array($entry)) {
                continue;
            }

            if (array_key_exists('_sequence', $entry)) {
                unset($entry['_sequence']);
            }

            if (array_key_exists('_value', $entry)) {
                $entry = $entry['_value'];
            }
        }
        unset($entry);
    }

    return $logEntries;
}
function getNextStepforFirstOrder(array $data): string {
    // Priority-based conditional routing

    if (isset($data['side_effects'])) {
        return $data['side_effects'] === 'yes' ? 'more_side_effects' : 'additional-medication';
    }

    if (isset($data['additional-medication'])) {
        return $data['additional-medication'] === 'yes' ? 'list_additional_medication' : 'rate_current_experience';
    }

    if (isset($data['rate_current_experience'])) {
        return $data['rate_current_experience'] === 'no' ? 'no-happy' : 'contact';
    }

    if (isset($data['chat_with_us'])) {
        return $data['chat_with_us'] === 'no' ? 'cart' : 'contact';
    }

    if (isset($data['pregnancy'])) {
        return $data['pregnancy'] === 'yes' ? 'pregnancy' : 'weight';
    }

    if (isset($data['medical_conditions'])) {
        return $data['medical_conditions'] === 'yes' ? 'list_any' : 'medications';
    }

    if (isset($data['effects_with_wegovy'])) {
        return $data['effects_with_wegovy'] === 'yes' ? 'wegovy_side_effects' : 'medication_allergies';
    }

    if (isset($data['more_side_effects'])) {
        return $data['more_side_effects'] === 'yes' ? 'wegovy_side_effects' : 'medication_allergies';
    }

    if (isset($data['medication_allergies'])) {
        $values = $data['medication_allergies'];
        if (!is_array($values)) {
            $values = [$values];
        }

        if (in_array('other', $values, true)) {
            $details = trim((string)($data['other_medication_details'] ?? ''));
            if ($details === '') {
                return 'medication_allergies';
            }
        }

        return 'gp_informed';
    }

    if (isset($data['gp_informed'])) {
        return $data['gp_informed'] === 'yes' ? 'gp_address' : 'access_special_offers';
    }

    if (isset($data['bariatricoperation'])) {
        return $data['bariatricoperation'] === 'yes' ? 'history_pancreatitis' : 'more_pancreatitis';
    }

    // Default fallback
    return $data['nextstep'] ?? 'start';
}

  function requireNextStep($step,$value) {
  /*echo "requireNextStep";
  echo $step;
  echo "--";
  print_r( $value);*/

   if($step=="pregnancy"){
           if($value=="yes"){
           return true;

              }
       }

       if($step=="medical_conditions"){
           if($value=="yes"){
             return true;
              // document.getElementById("nextstep").value="list_any";
           }

       }
       if($step=="medication_allergies"){
           $values = is_array($value) ? $value : [$value];
           if (in_array('other', $values, true)) {
               return true;
           }
       }
           if ($step=="medications" ){
           if (is_array($value) && !empty(array_intersect($this->getMedicationSlugs(), array_map('perch_questionnaire_medication_slug', (array)$value)))) {
            return true;
           }
           }

              if ($step=="starting_wegovy"){

                     return true;
                     }

                     if (is_string($step)) {
                         foreach ($this->getMedicationSlugs() as $slug) {
                             if (
                                 $step === "unit-{$slug}" ||
                                 $step === "weight2-{$slug}" ||
                                 $step === "weight-{$slug}" ||
                                 $step === "dose-{$slug}" ||
                                 $step === "recently-dose-{$slug}"
                             ) {
                                 return true;
                             }
                         }
                     }

                     if ($step=="recently_wegovy") {

                        return true;
                     }

                     if ($step==="continue_with_wegovy" || $step=="continue-dose-wegovy") {

                           return true;
                     }


       if($step=="more_side_effects"){

           if($value=="yes"){
               return true;
           }
       }


       if($step=="gp_informed"){

           if($value=="yes"){
               return true;
           }
       }
       if($step=="bariatricoperation"){

           if($value=="yes"){
               return true;
           }
       }
       if($step=="ethnicity"){

            if($value=="Mixed" || $value=="Other"){
               return true;
           }
       }
       return false;

  }
  function validateQuestionnaire(array $data): array {
      $errors = [];

      // 1. Age must be provided
      if (empty($data['age'])) {
          $errors[] = 'Please select your age group.';
      }

      // 2. Check if user is between 18–74
      if ($data['age'] === '18to74') {
          // Ethnicity required
          if (empty($data['ethnicity'])) {
              $errors[] = 'Please select your ethnicity.';
          }

          // If ethnicity is Mixed or Other, require ethnicity-more
          if (in_array($data['ethnicity'], ['Mixed', 'Other'])) {
              if (empty($data['ethnicity-more'])) {
                  $errors[] = 'Please specify your ethnicity in the text field.';
              }
          }

          // Gender required
          if (empty($data['gender'])) {
              $errors[] = 'Please select the sex assigned at birth.';
          }

          // If Female, check pregnancy status
          if ($data['gender'] === 'Female' && empty($data['pregnancy'])) {
              $errors[] = 'Please indicate if you are pregnant, trying to get pregnant, or breastfeeding.';
          }

          // Weight and height required
          if (empty($data['weight']) || empty($data['weightunit'])) {
              $errors[] = 'Please provide your weight and select a unit.';
          }

          $heightUnit = $this->resolveHeightUnit($data);
          $heightPrimary = isset($data['height']) ? trim((string)$data['height']) : '';

          if ($heightPrimary === '' || $heightUnit === null) {
              $errors[] = 'Please provide your height and select a unit.';
          } else {
              $heightSecondary = $data['height2'] ?? null;
              $heightInCm = $this->normaliseHeightToCentimetres($heightPrimary, $heightSecondary, $heightUnit);

              if ($heightInCm === null) {
                  $errors[] = 'Please enter your height using valid numbers. The shortest adult in the UK is 89 cm.';
              } else {
                  $minHeightCm = 89;
                  $maxHeightCm = 272;

                  if ($heightInCm < $minHeightCm || $heightInCm > $maxHeightCm) {
                      $rangeDescription = $this->formatHeightRangeForUnit($heightUnit, $minHeightCm, $maxHeightCm);
                      $errors[] = 'Please enter a realistic height between ' . $rangeDescription . '. The shortest adult in the UK is 89 cm.';
                  }
              }
          }

          // Diabetes required
          if (empty($data['diabetes'])) {
              $errors[] = 'Please select your diabetes status.';
          }

          // Conditions (first set) required
          if (empty($data['conditions']) || !is_array($data['conditions'])) {
              $errors[] = 'Please select at least one condition (or "None").';
          } else {
              // If 'bariatricoperation' selected, check follow-up question
              if (in_array('bariatricoperation', $data['conditions']) && empty($data['bariatricoperation'])) {
                  $errors[] = 'Please confirm if your bariatric operation was within 6 months.';
              }

              // If 'thyroidoperation' selected, require details
              if (in_array('thyroidoperation', $data['conditions']) && empty($data['thyroidoperation'])) {
                  $errors[] = 'Please provide more details on your thyroid operation.';
              }

              // If 'pancreatitishistory' selected, require more_pancreatitis info
              if (in_array('pancreatitishistory', $data['conditions']) && empty($data['more_pancreatitis'])) {
                  $errors[] = 'Please tell us more about your pancreatitis history.';
              }

              // If 'eatingdisorder' selected, require more_conditions
              if (in_array('eatingdisorder', $data['conditions']) && empty($data['more_conditions'])) {
                  $errors[] = 'Please tell us more about your eating disorder history.';
              }
          }

          // Second conditions block is optional but requires at least one checkbox
          if (empty($data['conditions2']) || !is_array($data['conditions2'])) {
              $errors[] = 'Please select at least one second-level condition (or "None").';
          }

          // Medical conditions yes/no
          if (empty($data['medical_conditions'])) {
              $errors[] = 'Please indicate if you have other medical conditions.';
          }

          // If yes, require details
          if ($data['medical_conditions'] === 'yes' && empty($data['other_medical_conditions'])) {
              $errors[] = 'Please list your other medical conditions.';
          }

          // Medications question
          if (empty($data['medications']) || !is_array($data['medications'])) {
              $errors[] = 'Please select any medications you’ve taken (or "None").';
          }

          // If 'wegovy' selected, check extra info
          $selectedMedicationSlugs = is_array($data['medications'])
              ? array_map('perch_questionnaire_medication_slug', $data['medications'])
              : [];

          foreach ($this->getMedicationSlugs() as $medicationSlug) {
              if (!in_array($medicationSlug, $selectedMedicationSlugs, true)) {
                  continue;
              }

              $label = $this->getMedicationLabel($medicationSlug);
              $weightKey = "weight-{$medicationSlug}";
              if (empty($data[$weightKey])) {
                  $errors[] = 'Please provide your weight before starting ' . $label . '.';
              }

              $doseKey = "dose-{$medicationSlug}";
              if (empty($data[$doseKey])) {
                  $errors[] = 'Please indicate your last dose of ' . $label . '.';
              }

              $recentDoseKey = "recently-dose-{$medicationSlug}";
              if (empty($data[$recentDoseKey])) {
                  $errors[] = 'Please provide the most recent dose prescribed for ' . $label . '.';
              }

              $continueDoseKey = "continue-dose-{$medicationSlug}";
              if (array_key_exists($continueDoseKey, $data) && empty($data[$continueDoseKey])) {
                  $errors[] = 'Please select how you would like to continue with ' . $label . '.';
              }
          }

          if (in_array('wegovy', $selectedMedicationSlugs, true)) {
              if (empty($data['effects_with_wegovy'])) {
                  $errors[] = 'Please indicate if you’ve had any side effects.';
              }

              if ($data['effects_with_wegovy'] === 'yes' && empty($data['wegovy_side_effects'])) {
                  $errors[] = 'Please describe your side effects with Wegovy.';
              }
          }

          // Medication allergies check
          if (empty($data['medication_allergies']) || !is_array($data['medication_allergies'])) {
              $errors[] = 'Please tell us about any medications or allergies.';
          }

          $medicationAllergies = is_array($data['medication_allergies'] ?? null)
              ? $data['medication_allergies']
              : [];

          if (in_array('other', $medicationAllergies, true)) {
              $otherMedicationDetails = trim((string)($data['other_medication_details'] ?? ''));
              if ($otherMedicationDetails === '') {
                  $errors[] = 'Please provide details of the other medication you take, including the name, dose, and how often you take this.';
              }
          }

          // GP informed
          if (empty($data['gp_informed'])) {
              $errors[] = 'Please tell us if you want your GP to be informed.';
          }

          if ($data['gp_informed'] === 'yes' && empty($data['GP_email_address'])) {
              $errors[] = 'Please enter your GP’s email address.';
          }
      }else{
         $errors[] = 'No permitted age!';
      }

      return $errors;
  }

  private function resolveHeightUnit(array $data): ?string
  {
      $unit = $data['heightunit'] ?? ($data['heightunit-radio'] ?? null);

      if ($unit === null) {
          return null;
      }

      $unit = strtolower(trim((string)$unit));

      if ($unit === '') {
          return null;
      }

      if ($unit === 'ft_in' || $unit === 'ftin') {
          return 'ft-in';
      }

      return $unit;
  }

  private function normaliseHeightToCentimetres($primaryValue, $secondaryValue, string $unit): ?float
  {
      $unit = strtolower(trim($unit));
      $primaryNumeric = $this->normaliseNumericValue($primaryValue);

      if ($primaryNumeric === null || $primaryNumeric <= 0) {
          return null;
      }

      if ($unit === 'cm') {
          return $primaryNumeric;
      }

      if ($unit === 'in') {
          return $primaryNumeric * 2.54;
      }

      if ($unit === 'ft-in') {
          $secondaryNumeric = $this->normaliseNumericValue($secondaryValue);

          if ($secondaryNumeric === null) {
              $secondaryNumeric = 0.0;
          }

          if ($secondaryNumeric < 0 || $secondaryNumeric >= 12) {
              return null;
          }

          $totalInches = ($primaryNumeric * 12) + $secondaryNumeric;

          return $totalInches * 2.54;
      }

      return null;
  }

  private function normaliseNumericValue($value): ?float
  {
      if (is_array($value)) {
          return null;
      }

      if ($value === null) {
          return null;
      }

      $value = str_replace(',', '.', trim((string)$value));

      if ($value === '') {
          return null;
      }

      if (!is_numeric($value)) {
          return null;
      }

      return (float)$value;
  }

  private function formatHeightRangeForUnit(string $unit, float $minHeightCm, float $maxHeightCm): string
  {
      $unit = strtolower(trim($unit));

      if ($unit === 'ft-in') {
          $format = function (float $cm): string {
              $totalInches = $cm / 2.54;
              $feet = floor($totalInches / 12);
              $inches = round($totalInches - ($feet * 12));

              if ($inches === 12) {
                  $feet += 1;
                  $inches = 0;
              }

              return $feet . ' ft ' . $inches . ' in';
          };

          return $format($minHeightCm) . ' and ' . $format($maxHeightCm);
      }

      if ($unit === 'in') {
          $toInches = function (float $cm): string {
              return rtrim(rtrim(number_format($cm / 2.54, 1, '.', ''), '0'), '.');
          };

          return $toInches($minHeightCm) . ' in and ' . $toInches($maxHeightCm) . ' in';
      }

      return $minHeightCm . ' cm and ' . $maxHeightCm . ' cm';
  }

  function calculateBMIAdvanced($weight, $weightUnit, $height1, $weight2=0, $height2 = 0, $heightUnit = 'cm') {
      // Normalise numeric values defensively before calculations
      $weightNumeric  = is_numeric($weight)  ? (float)$weight  : null;
      $weight2Numeric = is_numeric($weight2) ? (float)$weight2 : null;
      $height1Numeric = is_numeric($height1) ? (float)$height1 : null;
      $height2Numeric = is_numeric($height2) ? (float)$height2 : null;

      // Convert weight to kilograms if needed
      if ($weightUnit === 'st-lbs') {
          if ($weightNumeric === null || $weight2Numeric === null) {
              return "Invalid weight value.";
          }

          $totalPounds = ($weightNumeric * 14) + $weight2Numeric;
          $weightKg = $totalPounds * 0.453592;

      } elseif ($weightUnit === 'kg') {
          if ($weightNumeric === null) {
              return "Invalid weight value.";
          }

          $weightKg = $weightNumeric;
      } else {
          return "Invalid weight unit. Use 'kg' or 'lbs'.";
      }

      // Convert height to meters based on unit
      if ($heightUnit === 'cm') {
          if ($height1Numeric === null) {
              return "Invalid height value.";
          }

          $heightM = $height1Numeric / 100;
      } elseif ($heightUnit === 'in') {
          if ($height1Numeric === null) {
              return "Invalid height value.";
          }

          $heightM = $height1Numeric * 0.0254;
      } elseif ($heightUnit === 'ft-in') {
          if ($height1Numeric === null || $height2Numeric === null) {
              return "Invalid height value.";
          }

          $totalInches = ($height1Numeric * 12) + $height2Numeric;
          $heightM = $totalInches * 0.0254;
      } else {
          return "Invalid height unit. Use 'cm', 'in', or 'ft_in'.";
      }

      if ($heightM <= 0) {
          return "Height must be greater than zero.";
      }

      $bmi = $weightKg / ($heightM * $heightM);
      $bmi = round($bmi, 2);

      // Determine category
      if ($bmi < 18.5) {
          $category = "Underweight";
      } elseif ($bmi < 24.9) {
          $category = "Normal weight";
      } elseif ($bmi < 29.9) {
          $category = "Overweight";
      } else {
          $category = "Obese";
      }

      return [
          'bmi' => $bmi,
          'category' => $category
      ];
  }
function parseHeight($input) {
    // Remove all spaces
    $input = str_replace(' ', '', (string)($input ?? ''));

    // Validate the format: digits + 'ft' + digits + 'in'
    if (!preg_match('/^\d+ft\d+in$/', $input)) {
        return false;
    }

    // Extract digits before 'ft' and 'in'
    preg_match('/(\d+)ft/', $input, $ftMatch);
    preg_match('/(\d+)in/', $input, $inMatch);

    return [
        'feet' => (int)$ftMatch[1],
        'inches' => (int)$inMatch[1]
    ];
}
 public function check_questionnaire_status_for_member($memberID,$qid)
     {

        $sql = 'SELECT status,accepted
                FROM  '.PERCH_DB_PREFIX.'questionnaire_member_status
                WHERE memberID='.$this->db->pdb((int)$memberID).' and questionnaire_id="v1" limit 1';

        return $this->db->get_row($sql);
     }

 public function update_questionnaire_status_for_member($memberID,$status)
     {
    $update_status ="UPDATE ".PERCH_DB_PREFIX."questionnaire_member_status  SET `status`=".$status." where `questionnaire_id`='v1' and memberID=".$memberID." ";
$this->db->execute($update_status);
     }
 public function accept_questionnaire_for_member($memberID,$questionnaireID,$accepted)
     {
    $update_status ="UPDATE ".PERCH_DB_PREFIX."questionnaire_member_status  SET `accepted`=".$accepted." where `questionnaire_id`='v1' and memberID=".$memberID." and id=".$questionnaireID." ";
$this->db->execute($update_status);
     }

    public function add_to_member($memberID,$data,$type,$orderID)
     { // echo "add_to_member";

// print_r($memberID);print_r($type); echo PerchUtil::count($this->get_for_member($memberID));
       //     if(!PerchUtil::count($this->get_for_member($memberID)) ){
// echo "add_to_member inn";
$Members = new PerchMembers_Members;
        $Member = $Members->find($memberID);
        $memberdetails = $Member ? $Member->to_array() : [];

        $props = [];
        $propsUpdated = false;

        if (is_object($Member)) {
            $props = PerchUtil::json_safe_decode($Member->memberProperties(), true);
            if (!is_array($props)) {
                $props = [];
            }
        }

  // --- BEGIN defensive defaults normalization ---
  if (!is_array($data)) { $data = []; }
  if (!is_array($memberdetails)) { $memberdetails = []; }

  // units defaults (prefer explicit fields, then radio fields, then safe unit)
  if (!isset($data['heightunit'])) { $data['heightunit'] = $data['heightunit-radio'] ?? 'cm'; }
  if (!isset($data['weightunit'])) { $data['weightunit'] = $data['weightunit-radio'] ?? 'kg'; }

  if (!isset($memberdetails['heightunit'])) { $memberdetails['heightunit'] = $memberdetails['heightunit-radio'] ?? 'cm'; }
  if (!isset($memberdetails['weightunit'])) { $memberdetails['weightunit'] = $memberdetails['weightunit-radio'] ?? 'kg'; }

  // numeric/text defaults
  if (!isset($data['height']))  { $data['height'] = ''; }
  if (!isset($data['height2'])) { $data['height2'] = 0; }
  if (!isset($data['weight']))  { $data['weight'] = ''; }
  if (!isset($data['weight2'])) { $data['weight2'] = 0; }

  if (!isset($memberdetails['height']))  { $memberdetails['height'] = ''; }
  if (!isset($memberdetails['height2'])) { $memberdetails['height2'] = 0; }
  if (!isset($memberdetails['weight']))  { $memberdetails['weight'] = ''; }
  if (!isset($memberdetails['weight2'])) { $memberdetails['weight2'] = 0; }
  // --- END defensive defaults normalization ---
 $insert_query ="";
   // print_r($data);
     if( isset($data)){
     $weight2=0;
     $height2=0;
     if(isset($data["weight2"])){
      $weight2=$data["weight2"];
     }
     if(isset($data["height2"])){
           $height2=$data["height2"];
          }
      if($type=="first-order"){
      $result = $this->calculateBMIAdvanced($data["weight"], $data["weightunit"], $data["height"],$weight2, $height2, $data["heightunit"]);

        if (is_object($Member)) {
            $props["height"]=$data["height"];
            $props["height2"]=$height2;
            $props["heightunit"]= $data["heightunit"];
            $props['heightunit-radio'] = $data['heightunit-radio'] ?? $data['heightunit'] ?? 'cm';
            $propsUpdated = true;
        }
    	}else{
    	$heightcheck=$this->parseHeight($memberdetails["height"]);
    	if($heightcheck){
    	$memberdetails["height"]=$heightcheck["feet"];
    	$memberdetails["height2"]=$heightcheck["inches"];
    	}
    	if(!isset($memberdetails["heightunit"])){
    	$memberdetails["heightunit"]=$memberdetails["heightunit-radio"];
    	}

    	      $result = $this->calculateBMIAdvanced($data["weight"], $data["weightunit"], $memberdetails["height"],$data["weight2"], $memberdetails["height2"], $memberdetails["heightunit"]);


        }

        if (is_object($Member) && array_key_exists('gender', $data)) {
            $genderValue = $data['gender'];
            if (is_array($genderValue)) {
                $genderValue = array_filter($genderValue, function ($item) {
                    return $item !== '' && $item !== null;
                });
                $genderValue = $genderValue ? reset($genderValue) : '';
            }

            $genderValue = is_scalar($genderValue) ? trim((string) $genderValue) : '';

            if ($genderValue !== '') {
                if (!isset($props['gender']) || $props['gender'] !== $genderValue) {
                    $props['gender'] = $genderValue;
                    $propsUpdated = true;
                }
            }
        }

        if ($propsUpdated && is_object($Member)) {
            $out = [];
            $out['memberProperties'] = PerchUtil::json_safe_encode($props);
            $Member->update($out);
        }
                //insert_status
            $insert_status ="INSERT INTO ".PERCH_DB_PREFIX."questionnaire_member_status (`questionnaire_id`,memberID,`status`) VALUES ('v1',".$memberID.",'pending'); ";
         $new_id =$this->db->execute($insert_status);
      $data['bmi']=$result;
      $sessionKey = $new_id ?: ($memberID . ':' . $type);

      $weightUnitValue = $data['weightunit'] ?? ($data['weightunit-radio'] ?? ($data['weightradio-unit'] ?? ''));
      $heightUnitValue = $data['heightunit'] ?? ($data['heightunit-radio'] ?? '');

      $medicationUnits = [];
      if ($type=="first-order") {
          foreach ($this->getMedicationSlugs() as $slug) {
              $unitKey = "unit-{$slug}";
              if (isset($data[$unitKey])) {
                  $medicationUnits[$slug] = $data[$unitKey];
              }
          }
      }

      $questionConfigSet = ($type=="first-order") ? $this->questions_and_answers : $this->reorder_questions_answers;
      $questionLookup = ($type=="first-order") ? $this->questions : $this->reorder_questions;
      if ($type=="first-order") {
          foreach ($this->getMedicationSlugs() as $slug) {
              $label = $this->getMedicationLabel($slug);
              $questionLookup["weight-{$slug}"] = "What was your weight in kg/st-lbs before starting " . $label . '?';
              $questionLookup["dose-{$slug}"] = "When was your last dose of " . $label . '?';
              $questionLookup["recently-dose-{$slug}"] = "What dose of " . $label . " were you prescribed most recently?";
          }
      }

      foreach ($data as $key => $value) {
          $questionConfig = $questionConfigSet[$key] ?? null;

          if ($key === 'email_address') {
              $emailValue = '';
              if (is_array($value)) {
                  $firstEmail = reset($value);
                  if (is_scalar($firstEmail) || $firstEmail === null) {
                      $emailValue = trim((string)$firstEmail);
                  }
              } elseif (is_scalar($value) || $value === null) {
                  $emailValue = trim((string)$value);
              }

              if ($emailValue === '') {
                  $questionStep = is_array($questionConfig) ? ($questionConfig['step'] ?? null) : null;
                  if ($questionStep === 'access_special_offers') {
                      $value = 'skipped';
                  } else {
                      $value = 'no-email-added';
                  }
              } else {
                  $value = $emailValue;
              }

              $data[$key] = $value;
          }

          $qdata = array();
          $qdata['type'] = $type;
          $qdata['question_slug'] = $key;
          $qdata['question_text'] = $questionConfig['label'] ?? ($questionLookup[$key] ?? $key);
          $questionOrder = $this->getQuestionOrderForSession($sessionKey, $type, $key);
          if ($questionOrder !== null) {
              $qdata['question_order'] = $questionOrder;
          }

          if ($questionConfig) {
              $qdata['answer_text'] = $this->resolveAnswerTextFromConfig($value, $questionConfig);
          } elseif (is_array($value)) {
              $flattened = array_filter(array_map(function ($item) {
                  if (is_scalar($item)) {
                      return (string)$item;
                  }

                  if (is_array($item)) {
                      return implode(', ', array_map('strval', $item));
                  }

                  return '';
              }, $value), 'strlen');

              $qdata['answer_text'] = implode(', ', $flattened);
          } elseif (is_scalar($value) || $value === null) {
              $qdata['answer_text'] = trim((string)$value);
          } else {
              $qdata['answer_text'] = '';
          }
      if ($key === 'weight') {
          $weightunit = array_values(array_filter(array_map('trim', explode('-', (string)$weightUnitValue)), 'strlen'));
          if (!empty($weightunit) && $qdata['answer_text'] !== '') {
              if (count($weightunit) === 1) {
                  $qdata['answer_text'] .= ' ' . $weightunit[0];
              } else {
                  $qdata['answer_text'] .= ' ' . $weightunit[0];
                  $secondWeight = $data['weight2'] ?? null;
                  if ($secondWeight !== null && $secondWeight !== '') {
                      $qdata['answer_text'] .= ' ' . $secondWeight;
                      if (isset($weightunit[1])) {
                          $qdata['answer_text'] .= ' ' . $weightunit[1];
                      }
                  }
              }
          }
      }

      if ($key === 'height') {
          $heightunit = array_values(array_filter(array_map('trim', explode('-', (string)$heightUnitValue)), 'strlen'));
          if (!empty($heightunit) && $qdata['answer_text'] !== '') {
              if (count($heightunit) === 1) {
                  $qdata['answer_text'] .= ' ' . $heightunit[0];
              } else {
                  $qdata['answer_text'] .= ' ' . $heightunit[0];
                  $secondHeight = $data['height2'] ?? null;
                  if ($secondHeight !== null && $secondHeight !== '') {
                      $qdata['answer_text'] .= ' ' . $secondHeight;
                      if (isset($heightunit[1])) {
                          $qdata['answer_text'] .= ' ' . $heightunit[1];
                      }
                  }
              }
          }
      }

      if($type=="first-order"){
        if (strpos($key, 'weight-') === 0 && !empty($medicationUnits)) {
            $slug = substr($key, 7);
            if (isset($medicationUnits[$slug])) {
                $unitParts = array_values(array_filter(array_map('trim', explode('-', (string)$medicationUnits[$slug])), 'strlen'));
                if (!empty($unitParts) && $qdata['answer_text'] !== '') {
                    if (count($unitParts) === 1) {
                        $qdata['answer_text'] .= ' ' . $unitParts[0];
                    } else {
                        $qdata['answer_text'] .= ' ' . $unitParts[0];
                        $secondKey = "weight2-{$slug}";
                        if (isset($data[$secondKey]) && $data[$secondKey] !== '') {
                            $qdata['answer_text'] .= ' ' . $data[$secondKey];
                            if (isset($unitParts[1])) {
                                $qdata['answer_text'] .= ' ' . $unitParts[1];
                            }
                        }
                    }
                }
            }
        }
           if (strpos($key, 'recently-dose-') === 0) {
            $medicationSlug = substr($key, strlen('recently-dose-'));
            $recentDoseOptions = perch_questionnaire_recent_dose_options($medicationSlug);
            if (is_array($recentDoseOptions) && isset($recentDoseOptions[$value])) {
                $qdata['answer_text'] = $recentDoseOptions[$value];
            }
        }

}
if(isset($data["uuid"])){
  $qdata['uuid']=$data["uuid"];

}else{
 $qdata['uuid']="unknown";
}
/*if(isset($_SESSION['step_data'])){
           $qdata['uuid']=$_SESSION['step_data']['user_id'];
          }*/



                $qdata['member_id']=$memberID;
                  $qdata['order_id']=$orderID;
                $qdata['version']="v1";
                 $qdata['qid']= $new_id;
           $columns = implode(", ", array_keys($qdata)); // Columns as a string
                      //  $values = "'" . implode("', '", array_map('addslashes', array_values($qdata))) . "'";

           $db = $this->db;

           $escaped_values = array_map(function ($value) use ($db) {
               if (is_string($value)) {
                   $value = PerchUtil::safe_stripslashes($value);
               }

               return $db->pdb($value);
           }, array_values($qdata));

           $values = implode(', ', $escaped_values);

           $insert_query .="INSERT INTO ".PERCH_DB_PREFIX."questionnaire (".$columns.") VALUES (".$values."); ";

      }

     // }

//echo  $insert_query;
//exit();
//die();
try{

    	$this->db->execute($insert_query);


return $new_id ;
}catch (Exception $e) {
          echo $e->getMessage();
         }

        // $this->db->insert(PERCH_DB_PREFIX.'questionnaire', $data);
        }
     }

     }
