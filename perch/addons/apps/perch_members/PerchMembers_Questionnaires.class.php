<?php

require_once __DIR__ . '/questionnaire_medication_helpers.php';

class PerchMembers_Questionnaires extends PerchAPI_Factory
{
    private const CONSULTATION_AGREEMENT = <<<'QUESTION'
You are completing this consultation for yourself, providing information to the best of your knowledge.

You agree to disclose any medical conditions, serious illnesses, or past surgeries, as well as any prescription medications you are currently taking. Additionally, you acknowledge that you will use only one weight loss treatment at a time.

By proceeding, you confirm your acceptance of our Terms & Conditions, Privacy Policy and acknowledge that you have read our Privacy Policy.

It is essential to provide honest and accurate responses to this online questionnaire. Withholding or misrepresenting information can pose serious health risks, including life-threatening consequences. By submitting this questionnaire, you affirm that your responses are truthful and understand the potential dangers of misinformation.
QUESTION;


    private const COMORBIDITIES_QUESTION = <<<'QUESTION'
Do any of the following statements apply to you?

I have been diagnosed with a mental health condition such as depression or anxiety.

My weight makes me anxious in social situations.

I have joint pains and/or aches.

I have osteoarthritis.

I have GORD and/or indigestion.

I have a heart/cardiovascular problem.

I’ve been diagnosed with, or have a family history of, high blood pressure.

I’ve been diagnosed with, or have a family history of, high cholesterol.

I have fatty liver disease.

I have sleep apnoea.

I have asthma or COPD.

I have erectile dysfunction.

I have low testosterone.

I have menopausal symptoms.

I have polycystic ovary syndrome (PCOS).
QUESTION;
    private const CONTRAINDICATIONS_QUESTION = <<<'QUESTION'
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

                $this->questions_and_answers["weight2-{$slug}"] = [
                    'label' => 'Weight before medication (secondary value)',
                    'type' => 'hidden',
                    'name' => "weight2-{$slug}",
                ];

                $this->questions_and_answers["unit-{$slug}"] = [
                    'label' => 'Weight before medication unit',
                    'type' => 'radio',
                    'name' => "unit-{$slug}",
                    'options' => [
                        'kg' => 'kg',
                        'st-lbs' => 'st/lbs',
                    ],
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
       // "weight2"=>"inches",
        "weightunit"=>"weight unit",
        "pregnancy_status"=>"Are you pregnant or trying to conceive?",
        "bmi"=>"BMI",
        "side_effects"=>"Have you experienced any side effects whilst taking the medication? ",
      "more_side_effects"=>"Please tell us what side effects you have been experiencing.",
	"additional-medication"=>"Have you started taking any additional medication?",
     "list_additional_medication"=>"Please tell us what new medication you have started including the name of the medication, dose and how often you take it.",        "rate_current_experience"=>"Are you happy with your monthly weight loss?",
        "rate_current_experience"=>"Are you happy with your monthly weight loss?",
        "no_happy_reasons"=>"Please tell us as much as you can about the reasons you are not happy with your monthly weight loss.",
        "chat_with_us"=>"Would you like to chat with someone?",
        "nhs_summary_permission"=>"Do you give permission for our clinical team to access your NHS Summary Care Record?",
        "consent_confirmation"=>"I will be the sole user of the medication. I will read all relevant information before starting treatment. I will inform the clinical team of any changes to my medical history. I understand that severe diarrhoea for over 24 hours or vomiting within 3 hours of taking the contraceptive pill can reduce its effectiveness. If this happens, I will call my GP or 111 for advice. I understand I may need a repeat dose of the contraceptive pill or to use additional contraception. I will stop the medication if I fall pregnant or try to conceive, and I will let the clinicians know about these changes. I will contact the clinicians if I miss two or more doses. I understand medication may be prescribed off-label when clinically appropriate. I understand that rapid weight loss and injectable weight loss treatments like Mounjaro and Wegovy can both raise the risk of pancreatitis and gallbladder issues. If I have severe abdominal pain, vomiting, jaundice (yellowing of the skin), or worsening symptoms, I will seek urgent medical help. I understand that injectable weight loss treatments like Mounjaro and Wegovy should not be combined with other weight loss medications. I recognise that these treatments may affect my mood. If I experience low mood or any mental health issues, I will stop the treatment and consult a doctor immediately.",
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
    "other_medications"=>"medication_allergies",
    "allergies"=>"medication_allergies",
    "other_medication_details"=>"medication_allergies",
    "allergy_details"=>"medication_allergies",
    "other_medical_conditions"=>"list_any",
    "wegovy_side_effects"=>"wegovy_side_effects",
    "gp_informed"=>"gp_informed",
    "GP_name"=>"gp_address",
    "GP_address"=>"gp_address",
    "GP_email_address"=>"gp_address",
    "nhs_summary_permission"=>"nhs_summary_record",
    "special_offers_email"=>"access_special_offers"
    ];
    public $reorder_questions_answers = [
        "weight" => [
            "label" => "What is your current weight?",
            "type" => "text",
            "name" => "weight"
        ],
      /*  "weight2" => [
            "label" => "Weight (lbs hidden input)",
            "type" => "hidden",
            "name" => "weight2"
        ],*/
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
            "name" => "side_effects",
            "options" => [
                "yes" => "Yes",
                "no" => "No"
            ]
        ],
        "more_side_effects" => [
  "label" => "Please tell us what side effects you have been experiencing.",
            "type" => "textarea",
            "name" => "more_side_effects"
        ],
        "additional-medication" => [
            "label" => "Have you started taking any additional medication?",
            "type" => "button",
            "name" => "additional-medication",
            "options" => [
                "yes" => "Yes",
                "no" => "No"
            ]
        ],
        "list_additional_medication" => [
 "label" => "Please tell us what new medication you have started including the name of the medication, dose and how often you take it.",            "type" => "textarea",
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
        "nhs_summary_permission" => [
            "label" => "Do you give permission for our clinical team to access your NHS Summary Care Record?",
            "type" => "checkbox",
            "name" => "nhs_summary_permission",
            "options" => [
                "yes" => "Yes",
                "no" => "No"
            ]
        ],
         "consent_confirmation" => [
        "consent_confirmation"=>"I will be the sole user of the medication. I will read all relevant information before starting treatment. I will inform the clinical team of any changes to my medical history. I understand that severe diarrhoea for over 24 hours or vomiting within 3 hours of taking the contraceptive pill can reduce its effectiveness. If this happens, I will call my GP or 111 for advice. I understand I may need a repeat dose of the contraceptive pill or to use additional contraception. I will stop the medication if I fall pregnant or try to conceive, and I will let the clinicians know about these changes. I will contact the clinicians if I miss two or more doses. I understand medication may be prescribed off-label when clinically appropriate. I understand that rapid weight loss and injectable weight loss treatments like Mounjaro and Wegovy can both raise the risk of pancreatitis and gallbladder issues. If I have severe abdominal pain, vomiting, jaundice (yellowing of the skin), or worsening symptoms, I will seek urgent medical help. I understand that injectable weight loss treatments like Mounjaro and Wegovy should not be combined with other weight loss medications. I recognise that these treatments may affect my mood. If I experience low mood or any mental health issues, I will stop the treatment and consult a doctor immediately.",
                    "type" => "button",
                    "name" => "consent_confirmation",
                    "options" => [
                        "yes" => "Yes"
                    ]
                ],
        "bmi" => [
            "label" => "BMI",
            "type" => "text",
            "name" => "bmi"
        ],
        "multiple_answers" => [
            "label" => "Have client alter answers?",
            "type" => "text",
            "name" => "multiple_answers"
        ],
        "documents" => [
            "label" => "Member Documents",
            "type" => "text",
            "name" => "documents"
        ]
    ];

    public $questions_and_answers  = [
                                       "consultation" => [
                                           "label" =>  self::CONSULTATION_AGREEMENT,
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
                                               "white" => "White",
                                               "PreferNotToSay" => "Prefer not to say"
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
                                     /*  "weight2" => [
                                           "label" => "Weight (lbs hidden input)",
                                           "type" => "hidden",
                                           "name" => "weight2"
                                       ],*/
                                       "weightunit" => [
                                           "label" => "weight unit",
                                           "type" => "radio",
                                           "name" => "weightunit",
                                           "options" => [
                                               "kg" => "kg",
                                               "st-lbs" => "st/lbs"
                                           ]
                                       ],
                                       "height" => [
                                           "label" => "What is your height?",
                                           "type" => "text",
                                           "name" => "height"
                                       ],
                                      /* "height2" => [
                                           "label" => "Height (secondary value)",
                                           "type" => "hidden",
                                           "name" => "height2"
                                       ],*/
                                       "heightunit" => [
                                           "label" => "height unit",
                                           "type" => "radio",
                                           "name" => "heightunit",
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
                                           "label" => self::CONTRAINDICATIONS_QUESTION,
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
                                           "label" => "Tell me about your gastric surgery procedure.",
                                           "type" => "text",
                                           "name" => "more_pancreatitis"
                                       ],
                                       "thyroidoperation" => [
                                           "label" => "Please tell us further details on the thyroid surgery you had, the outcome of the surgery and any ongoing monitoring",
                                           "type" => "text",
                                           "name" => "thyroidoperation"
                                       ],
                                       "more_conditions" => [
                                           "label" => "Please tell us more about your health condition and how you manage it.",
                                           "type" => "textarea",
                                           "name" => "more_conditions"
                                       ],
                                       "conditions2" => [
                                           "label" => self::COMORBIDITIES_QUESTION,
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
                                               "none" => "I have never taken medication to lose weight",
                                               //"never" => "I have never taken medication to lose weight"
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
                                       "other_medications" => [
                                           "label" => "Do you currently take any other medication or supplements?",
                                           "type" => "radio",
                                           "name" => "other_medications",
                                           "options" => [
                                               "yes" => "I take other medication or supplements.",
                                               "no" => "I do not take other medication or supplements."
                                           ]
                                       ],
                                       "other_medication_details" => [
                                           "label" => "Please provide details of the other medication you take, including the name, dose, and how often you take this.",
                                           "type" => "textarea",
                                           "name" => "other_medication_details"
                                       ],
                                       "allergies" => [
                                           "label" => "Do you have any allergies including to medication, food, environmental or anything else?",
                                           "type" => "radio",
                                           "name" => "allergies",
                                           "options" => [
                                               "yes" => "Yes, I have allergies",
                                               "no" => "No allergies",
                                               "PreferNotToSay" => "Prefer not to say"
                                           ]
                                       ],
                                       "allergy_details" => [
                                           "label" => "Please provide detail on your allergy, severity and how it is controlled.",
                                           "type" => "textarea",
                                           "name" => "allergy_details"
                                       ],
                                       "other_medical_conditions" => [
                                           "label" => "Please list any other medical conditions you have.",
                                           "type" => "textarea",
                                           "name" => "other_medical_conditions"
                                       ],
                                       "gp_informed" => [
                                           "label" => "Would you like your GP to be informed of this consultation?",
                                           "type" => "radio",
                                           "name" => "gp_informed",
                                           "options" => [
                                               "yes" => "Yes",
                                               "no" => "No"
                                           ]
                                       ],
                                       "GP_name" => [
                                           "label" => "Please enter your GP's name",
                                           "type" => "text",
                                           "name" => "GP_name",
                                           "step" => "gp_address"
                                       ],
                                       "GP_address" => [
                                           "label" => "Please enter your GP's address",
                                           "type" => "textarea",
                                           "name" => "GP_address",
                                           "step" => "gp_address"
                                       ],
                                       "GP_email_address" => [
                                           "label" => "Please enter your GP's email address",
                                           "type" => "text",
                                           "name" => "GP_email_address",
                                           "step" => "gp_address"
                                       ],
                                       "nhs_summary_permission" => [
                                           "label" => "Do you give permission for our clinical team to access your NHS Summary Care Record?",
                                           "type" => "checkbox",
                                           "name" => "nhs_summary_permission",
                                           "options" => [
                                               "yes" => "Yes",
                                               "no" => "No"
                                           ],
                                           "step" => "nhs_summary_record"
                                       ],
                                       "special_offers_email" => [
                                           "label" => "Get access to special offers",
                                           "type" => "text",
                                           "name" => "special_offers_email",
                                           "step" => "access_special_offers"
                                       ],
                                       "multiple_answers" => [
                                           "label" => "Have client alter answers?",
                                           "type" => "text",
                                           "name" => "multiple_answers"
                                       ],
                                       "documents" => [
                                           "label" => "Member Documents",
                                           "type" => "text",
                                           "name" => "documents"
                                       ],
                                       "bmi" => [
                                           "label" => "BMI",
                                           "type" => "text",
                                           "name" => "bmi"
                                       ]
                                  ];


		public $questions=[
	"consultation"=>"You are completing this consultation for yourself, providing information to the best of your knowledge.You agree to disclose any medical conditions, serious illnesses, or past surgeries, as well as any prescription medications you are currently taking. Additionally, you acknowledge that you will use only one weight loss treatment at a time.By proceeding, you confirm your acceptance of our Terms & Conditions, Privacy Policy and acknowledge that you have read our Privacy Policy.It is essential to provide honest and accurate responses to this online questionnaire. Withholding or misrepresenting information can pose serious health risks, including life-threatening consequences. By submitting this questionnaire, you affirm that your responses are truthful and understand the potential dangers of misinformation.",
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
    "conditions"=>self::CONTRAINDICATIONS_QUESTION,
    "bariatricoperation"=>"Was your bariatric operation in the last 6 months? ",
    "more_pancreatitis"=>"Tell me about your gastric surgery procedure.",
    "thyroidoperation"=>"Please tell us further details on the thyroid surgery you had, the outcome of the surgery and any ongoing monitoring",
    "more_conditions"=>"Please tell us more about your mental health condition and how you manage it",
   "label" => self::COMORBIDITIES_QUESTION,
    "medical_conditions"=>"Do you have any other medical conditions?",
    "medications"=>"Have you ever taken any of the following medications to help you lose weight?",
    "weight-wegovy"=>"What was your weight in kg before starting the weight loss medication?",
    "dose-wegovy"=>"When was your last dose of the weight loss medication?",
    "recently-dose-wegovy"=>"What dose of the weight loss medication were you prescribed most recently?",
    "continue-dose-wegovy"=>"If you want to continue with the weight loss medication, what dose would you like to continue with?",
    "effects_with_wegovy"=>"Have you experienced any side effects with the weight loss medication?",
    "other_medications"=>"Do you currently take any other medication or supplements?",
    "other_medication_details"=>"Please provide details of the other medication you take, including the name, dose, and how often you take this.",
    "allergies"=>"Do you have any allergies including to medication, food, environmental or anything else?",
    "allergy_details"=>"Please provide detail on your allergy, severity and how it is controlled.",
    "other_medical_conditions"=>"Please list any other medical conditions you have. ",
    "wegovy_side_effects"=>"Please tell us as much as you can about your side effects - the type, duration, severity and whether they have resolved",
    "gp_informed"=>"Would you like your GP to be informed of this consultation?",
    "GP_name"=>"Please enter your GP's name",
    "GP_address"=>"Please enter your GP's address",
    "GP_email_address"=>"Please enter your GP's email address",
    "nhs_summary_permission"=>"Do you give permission for our clinical team to access your NHS Summary Care Record?",
    "special_offers_email"=>"Get access to special offers",
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

    protected function formatWeightValue($value): string
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        if ($value === '' || $value === null) {
            return '';
        }

        if (is_numeric($value)) {
            return number_format((float)$value, 2, '.', '');
        }

        return is_scalar($value) ? (string)$value : '';
    }
        public function get_for_member($memberID,$type="first-order")
    {
        $sql = 'SELECT d.*
                FROM  '.PERCH_DB_PREFIX.'questionnaire d
                WHERE d.member_id='.$this->db->pdb((int)$memberID)
                .' AND d.type='.$this->db->pdb($type);

     /*   if ($this->questionOrderColumnAvailable()) {
         $sql .= ' ORDER BY d.question_order DESC';
            $sql .= ' ORDER BY d.qid DESC, (d.question_order IS NULL), d.question_order ASC, d.created_at ASC, d.id ASC';
        } else {
            $sql .= ' ORDER BY d.id DESC';
        }*/

       if ($this->questionOrderColumnAvailable()) {
                        $sql .= ' ORDER BY (d.question_order IS NULL), d.question_order ASC, d.created_at ASC, d.id ASC';
                    } else {
                        $sql .= ' ORDER BY d.created_at ASC, d.id ASC';
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
                        $sql .= ' ORDER BY (d.question_order IS NULL), d.question_order ASC, d.created_at ASC, d.id ASC';
                    } else {
                        $sql .= ' ORDER BY d.created_at ASC, d.id ASC';
                    }
      /*  if ($this->questionOrderColumnAvailable()) {
          $sql .= ' ORDER BY d.question_order DESC';
            $sql .= ' ORDER BY d.qid DESC, (d.question_order IS NULL), d.question_order ASC, d.created_at ASC, d.id ASC';
        } else {
            $sql .= ' ORDER BY d.id DESC';
        }*/

        return $this->return_instances($this->db->get_rows($sql));
    }

function displayUserAnswerHistoryUI(string $userId, string $logDir = 'logs'): array {
    $result = [
        'entries'  => [],
        'metadata' => [],
        'error'    => null,
    ];

    $sanitisedUserId = trim($userId);
    if ($sanitisedUserId === '') {
        $result['error'] = 'A valid user ID is required to view the answer history.';
        return $result;
    }

    $sanitisedUserId = basename(str_replace('\\', '/', $sanitisedUserId));
    $sanitisedUserId = str_replace(array("\"", "'"), '', $sanitisedUserId);

    $logDir = trim($logDir);
    if ($logDir === '') {
        $logDir = 'logs';
    }

    if ($logDir[0] === '/' || preg_match('/^[A-Za-z]:[\\\\\/]/', $logDir)) {
        $basePath = PerchUtil::file_path($logDir);
    } else {
        $logDir = trim($logDir, '/\\');
        $siteRoot = defined('PERCH_SITEPATH') ? PERCH_SITEPATH : null;
        if ($siteRoot === null && defined('PERCH_PATH')) {
            $siteRoot = realpath(PERCH_PATH . '/../');
        }
        if (!is_string($siteRoot) || $siteRoot === '') {
            $siteRoot = getcwd();
        }

        $basePath = PerchUtil::file_path(rtrim($siteRoot, '/\\') . '/' . $logDir);
    }

    $filePath = PerchUtil::file_path($basePath . DIRECTORY_SEPARATOR . $sanitisedUserId . '_raw_log.json');

    if (!file_exists($filePath)) {
        $result['error'] = sprintf('No answer history was found for user ID "%s".', $sanitisedUserId);
        return $result;
    }

    $json = file_get_contents($filePath);
    if ($json === false) {
        $result['error'] = sprintf('Unable to read the answer history file for user ID "%s".', $sanitisedUserId);
        return $result;
    }

    $data = json_decode($json, true);
    if (!is_array($data)) {
        $message = 'JSON error';
        if (function_exists('json_last_error') && json_last_error() !== JSON_ERROR_NONE) {
            if (function_exists('json_last_error_msg')) {
                $message = json_last_error_msg();
            } else {
                $message = 'code ' . json_last_error();
            }
        }
        $result['error'] = sprintf('The answer history file for user ID "%s" could not be parsed: %s', $sanitisedUserId, $message);
        return $result;
    }

    if (isset($data['metadata']) && is_array($data['metadata'])) {
        $result['metadata'] = $data['metadata'];
    }

    $logEntries = $data['log'] ?? [];
    if (!is_array($logEntries)) {
        $result['error'] = sprintf('The answer history for user ID "%s" does not contain any log entries.', $sanitisedUserId);
        return $result;
    }

    $normalise = function ($value) {
        if (function_exists('perch_members_normalise_answer_log_value')) {
            return perch_members_normalise_answer_log_value($value);
        }

        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_array($value)) {
            $flattened = [];
            array_walk_recursive($value, function ($item) use (&$flattened) {
                if (is_scalar($item) || $item === null) {
                    $flattened[] = (string)$item;
                }
            });

            if (empty($flattened)) {
                return '';
            }

            return trim(implode(', ', $flattened));
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string)$value;
        }

        if (is_scalar($value)) {
            return trim((string)$value);
        }

        $encoded = json_encode($value);

        return $encoded === false ? '' : trim($encoded);
    };

    foreach ($logEntries as $index => &$entry) {
        if (!is_array($entry)) {
            $entry = ['_value' => $entry, '_sequence' => $index];
            continue;
        }

        $entry['_sequence'] = $index;

        if (!isset($entry['question']) || !is_string($entry['question'])) {
            $entry['question'] = '';
        }

        if (!array_key_exists('answer', $entry) || !(is_scalar($entry['answer']) || $entry['answer'] === null)) {
            $entry['answer'] = $normalise($entry['answer'] ?? null);
        }

        if (!isset($entry['time']) || !is_string($entry['time'])) {
            $entry['time'] = '';
        }

        if (!isset($entry['action']) || !is_string($entry['action'])) {
            $entry['action'] = '';
        }

        if (!isset($entry['previous_answer']) || !(is_scalar($entry['previous_answer']) || $entry['previous_answer'] === null)) {
            $entry['previous_answer'] = $normalise($entry['previous_answer'] ?? null);
        }

        if (!isset($entry['changed'])) {
            $entry['changed'] = false;
        } else {
            $entry['changed'] = (bool)$entry['changed'];
        }
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
            continue;
        }

        $entry['question'] = trim((string)$entry['question']);
        $entry['answer'] = $normalise($entry['answer'] ?? null);
        $entry['previous_answer'] = $normalise($entry['previous_answer'] ?? null);
        $entry['time'] = trim((string)$entry['time']);
        $entry['action'] = trim((string)$entry['action']);
    }
    unset($entry);

    $result['entries'] = $logEntries;

    return $result;
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
        return $data['rate_current_experience'] === 'no' ? 'no-happy' : 'chat_with_us';
    }

    if (isset($data['chat_with_us'])) {
        return 'cart';
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

    if (isset($data['other_medications']) || isset($data['allergies'])) {
        $otherMedications = $data['other_medications'] ?? '';
        if ($otherMedications === '') {
            return 'medication_allergies';
        }

        if ($otherMedications === 'yes') {
            $details = trim((string)($data['other_medication_details'] ?? ''));
            if ($details === '') {
                return 'medication_allergies';
            }
        }

        $allergies = $data['allergies'] ?? '';
        if ($allergies === '') {
            return 'medication_allergies';
        }

        if ($allergies === 'yes') {
            $allergyDetails = trim((string)($data['allergy_details'] ?? ''));
            if ($allergyDetails === '') {
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

    if (isset($data['conditions']) && is_array($data['conditions'])) {
        $historyPancreatitisConditions = [
            'familythyroid',
            'heartfailure',
            'malabsorption',
            'cholestasis',
            'cancer',
            'retinopathy',
            'pancreatitishistory',
            'eatingdisorder',
            'neoplasia',
        ];

        if (!empty(array_intersect($data['conditions'], $historyPancreatitisConditions))) {
            return 'history_pancreatitis';
        }
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
       if($step=="other_medications"){
           if($value=="yes"){
               return true;
           }
       }
       if($step=="allergies"){
           if($value=="yes"){
               return true;
           }
       }
         /*  if ($step=="medications" ){
           if (is_array($value) && !empty(array_intersect($this->getMedicationSlugs(), array_map('perch_questionnaire_medication_slug', (array)$value)))) {
            return true;
           }
           }*/

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

          // Other medications question
          $otherMedications = (string)($data['other_medications'] ?? '');
          if ($otherMedications === '') {
              $errors[] = 'Please tell us if you take other medication or supplements.';
          } elseif ($otherMedications === 'yes') {
              $otherMedicationDetails = trim((string)($data['other_medication_details'] ?? ''));
              if ($otherMedicationDetails === '') {
                  $errors[] = 'Please provide details of the other medication you take, including the name, dose, and how often you take this.';
              }
          }

          // Allergies question
          $allergies = (string)($data['allergies'] ?? '');
          if ($allergies === '') {
              $errors[] = 'Please tell us if you have any allergies.';
          } elseif ($allergies === 'yes') {
              $allergyDetails = trim((string)($data['allergy_details'] ?? ''));
              if ($allergyDetails === '') {
                  $errors[] = 'Please provide details about your allergies.';
              }
          }

          // GP informed
          if (empty($data['gp_informed'])) {
              $errors[] = 'Please tell us if you want your GP to be informed.';
          }

          if (($data['gp_informed'] ?? '') === 'yes') {
              $gpName = trim((string)($data['GP_name'] ?? ''));
              $gpAddress = trim((string)($data['GP_address'] ?? ''));
              $gpEmail = trim((string)($data['GP_email_address'] ?? ''));

              if ($gpName === '') {
                  $errors[] = 'Please enter your GP’s name.';
              }

              if ($gpAddress === '') {
                  $errors[] = 'Please enter your GP’s address.';
              }

              if ($gpEmail === '') {
                  $errors[] = 'Please enter your GP’s email address.';
              }
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

  private function extractNumericHeightValue($value): ?float
  {
      if ($value === null) {
          return null;
      }

      if (is_numeric($value)) {
          return (float)$value;
      }

      if (!is_scalar($value)) {
          return null;
      }

      $value = str_replace(',', '.', trim((string)$value));

      if ($value === '') {
          return null;
      }

      if (preg_match('/-?\d+(?:\.\d+)?/', $value, $matches)) {
          return (float)$matches[0];
      }

      return null;
  }

  private function extractHeightValuesFromAnswer($value): array
  {
      $result = ['primary' => null, 'secondary' => null];

      if (!is_scalar($value)) {
          return $result;
      }

      $value = str_replace(',', '.', trim((string)$value));

      if ($value === '') {
          return $result;
      }

      if (preg_match_all('/-?\d+(?:\.\d+)?/', $value, $matches) && isset($matches[0])) {
          if (isset($matches[0][0])) {
              $result['primary'] = (float)$matches[0][0];
          }

          if (isset($matches[0][1])) {
              $result['secondary'] = (float)$matches[0][1];
          }
      }

      return $result;
  }

  private function getLatestFirstOrderHeightDataForMember(int $memberID): ?array
  {
      $questionSlugs = [
          $this->db->pdb('height'),
          $this->db->pdb('height2'),
          $this->db->pdb('heightunit'),
      ];

      $sql = 'SELECT id, qid, question_slug, answer_text'
          . ' FROM  ' . PERCH_DB_PREFIX . 'questionnaire'
          . ' WHERE member_id=' . $this->db->pdb($memberID)
          . ' AND type=' . $this->db->pdb('first-order')
          . ' AND question_slug IN (' . implode(',', $questionSlugs) . ')'
          . ' ORDER BY qid DESC, id DESC';

      $rows = $this->db->get_rows($sql);

      if (!is_array($rows) || empty($rows)) {
          return null;
      }

      $latestQid = null;
      $heightData = [
          'height' => null,
          'height2' => null,
          'heightunit' => null,
      ];

      foreach ($rows as $row) {
          $rowQid = $row['qid'] ?? null;

          if ($latestQid === null) {
              $latestQid = $rowQid;
          } elseif ($rowQid !== $latestQid) {
              break;
          }

          $slug = $row['question_slug'] ?? '';
          $answer = $row['answer_text'] ?? '';

          switch ($slug) {
              case 'height':
                  $values = $this->extractHeightValuesFromAnswer($answer);
                  if ($values['primary'] !== null) {
                      $heightData['height'] = $values['primary'];
                  }
                  if ($values['secondary'] !== null && $heightData['height2'] === null) {
                      $heightData['height2'] = $values['secondary'];
                  }
                  break;
              case 'height2':
                  $secondary = $this->extractNumericHeightValue($answer);
                  if ($secondary !== null) {
                      $heightData['height2'] = $secondary;
                  }
                  break;
              case 'heightunit':
                  $unit = $this->resolveHeightUnit(['heightunit' => $answer]);
                  if ($unit === null) {
                      $unit = $this->resolveHeightUnit(['heightunit-radio' => $answer]);
                  }

                  if ($unit === null) {
                      $unit = strtolower(trim((string)$answer));
                      if ($unit === 'ft_in' || $unit === 'ftin') {
                          $unit = 'ft-in';
                      }
                  }

                  $heightData['heightunit'] = $unit;
                  break;
          }
      }

      if ($heightData['height'] === null || $heightData['heightunit'] === null || $heightData['heightunit'] === '') {
          return null;
      }

      if ($heightData['height2'] === null) {
          $heightData['height2'] = 0;
      }

      return $heightData;
  }

  function calculateBMIAdvanced($weight, $weightUnit, $height1, $weight2=0, $height2 = 0, $heightUnit = 'cm') {
      $heightUnit = strtolower(trim((string)$heightUnit));
      $heightUnit = str_replace(['_', '/'], '-', $heightUnit);

      // Normalise numeric values defensively before calculations
      $weightNumeric  = is_numeric($weight)  ? (float)$weight  : null;
      $weight2Numeric = is_numeric($weight2) ? (float)$weight2 : null;
      $height1Numeric = is_numeric($height1) ? (float)$height1 : null;
      $height2Numeric = is_numeric($height2) ? (float)$height2 : null;

      // Convert weight to kilograms if needed
      if ($weightUnit === 'st-lbs') {
          if ($weightNumeric === null) {
              return "Invalid weight value.";
          }

          if ($weight2Numeric === null) {
              $weight2Trimmed = is_string($weight2) ? trim($weight2) : $weight2;
              if ($weight2Trimmed === '' || $weight2Trimmed === null) {
                  $weight2Numeric = 0.0;
              } else {
                  return "Invalid weight value.";
              }
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
          if ($height1Numeric === null) {
              return "Invalid height value.";
          }

          if ($height2Numeric === null) {
              $height2Trimmed = is_string($height2) ? trim($height2) : $height2;
              if ($height2Trimmed === '' || $height2Trimmed === null) {
                  $height2Numeric = 0.0;
              } else {
                  return "Invalid height value.";
              }
          }

          $totalInches = ($height1Numeric * 12) + $height2Numeric;
          $heightM = $totalInches * 0.0254;
      } else {
          return "Invalid height unit. Use 'cm', 'in', or 'ft-in'.";
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
     $questionnaireHeight = $this->getLatestFirstOrderHeightDataForMember((int)$memberID);

     $heightForBMI = null;

     if (is_array($questionnaireHeight)) {
         $heightForBMI = [
             'primary' => $questionnaireHeight['height'],
             'secondary' => $questionnaireHeight['height2'] ?? 0,
             'unit' => $questionnaireHeight['heightunit'] ?? 'cm',
         ];
     }

     if ($type=="first-order" && $heightForBMI === null) {
     $heightForBMI = [
         'primary' => $data["height"],
         'secondary' => $height2,
         'unit' => $data["heightunit"],
     ];
     }

     if ($heightForBMI === null) {
     $heightcheck=$this->parseHeight($memberdetails["height"]);
     if($heightcheck){
     $memberdetails["height"]=$heightcheck["feet"];
     $memberdetails["height2"]=$heightcheck["inches"];
     }
     if(!isset($memberdetails["heightunit"])){
     $memberdetails["heightunit"]=$memberdetails["heightunit-radio"];
     }

     $heightForBMI = [
         'primary' => $memberdetails["height"],
         'secondary' => $memberdetails["height2"],
         'unit' => $memberdetails["heightunit"],
     ];
     }

     $heightForBMI['secondary'] = $heightForBMI['secondary'] ?? 0;

     if($type=="first-order"){
     $result = $this->calculateBMIAdvanced($data["weight"], $data["weightunit"], $heightForBMI['primary'],$weight2, $heightForBMI['secondary'], $heightForBMI['unit']);

        if (is_object($Member)) {
            $props["height"]=$data["height"];
            $props["height2"]=$height2;
            $props["heightunit"]= $data["heightunit"];
            $props['heightunit-radio'] = $data['heightunit-radio'] ?? $data['heightunit'] ?? 'cm';
            $propsUpdated = true;
        }
        }else{

              $result = $this->calculateBMIAdvanced($data["weight"], $data["weightunit"], $heightForBMI['primary'],$data["weight2"], $heightForBMI['secondary'], $heightForBMI['unit']);


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
        $rawLogEntries = [];
        $rawLogMetadata = [];
        if (isset($data['log']) && is_array($data['log'])) {
            $rawLogEntries = $data['log'];
        }

        if (isset($data['log_metadata']) && is_array($data['log_metadata'])) {
            $rawLogMetadata = $data['log_metadata'];
        }

        $questionnaireUUID = isset($data['uuid']) && is_string($data['uuid'])
            ? trim($data['uuid'])
            : '';

        $logSummary = null;
        if (!empty($rawLogEntries) && function_exists('perch_members_summarise_answer_log')) {
            $logSummary = perch_members_summarise_answer_log($rawLogEntries);
        }

        $logUrl = $this->buildQuestionnaireLogUrl($questionnaireUUID, $type);

        if (!empty($rawLogEntries)) {
            $hasChanges = is_array($logSummary) && !empty($logSummary['has_changes']);

            if ($hasChanges && $logUrl !== null) {
                $data['multiple_answers'] = 'Yes-' . $logUrl;
            } elseif (!$hasChanges && !isset($data['multiple_answers'])) {
                $data['multiple_answers'] = 'No';
            }
        }

        unset($data['log'], $data['log_metadata'], $data['grouped_log']);

        //insert_status
        $this->db->execute('START TRANSACTION');
        $new_id = $this->db->insert(
            PERCH_DB_PREFIX.'questionnaire_member_status',
            [
                'questionnaire_id' => 'v1',
                'memberID' => $memberID,
                'status' => 'pending',
            ]
        );

        if ($new_id === false) {
            $this->db->execute('ROLLBACK');
            return false;
        }

      $data['bmi']=$result;
      $sessionKey = $new_id ?: ($memberID . ':' . $type);
      $sessionQuestionOrderMap = [];
      if (isset($_SESSION['questionnaire_question_order']) && is_array($_SESSION['questionnaire_question_order'])) {
          $sessionQuestionOrderMap = $_SESSION['questionnaire_question_order'];
      }

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

      $rowsToInsert = [];
      $medicationsRowIndex = null;
      $medicationsOriginalAnswer = '';
      $selectedMedicationSlugs = [];
      $medicationDetails = [];

      foreach ($data as $key => $value) {
          $questionConfig = $questionConfigSet[$key] ?? null;

          if (in_array($key, ['email_address', 'GP_email_address', 'special_offers_email'], true)) {
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
                  if ($questionStep === 'access_special_offers' || $key === 'special_offers_email') {
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

          $questionOrder = null;
          if (is_array($value) && array_key_exists('questionOrder', $value)) {
              $questionOrder = $value['questionOrder'];
          } elseif (isset($sessionQuestionOrderMap[$key])) {
              $questionOrder = $sessionQuestionOrderMap[$key];
          } else {
              $questionOrder = $this->getQuestionOrderForSession($sessionKey, $type, $key);
          }

          if ($questionOrder !== null) {
              $qdata['question_order'] = (int) $questionOrder;
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

          if ($key === 'other_medication_details' && trim((string)$qdata['answer_text']) === '') {
              $qdata['answer_text'] = 'No medication being taken.';
          }

      $isMedicationWeightQuestion = (strpos($key, 'weight-') === 0);
      if (($key === 'weight' || $isMedicationWeightQuestion) && $qdata['answer_text'] !== '') {
          $qdata['answer_text'] = $this->formatWeightValue($qdata['answer_text']);
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
                      $qdata['answer_text'] .= ' ' . $this->formatWeightValue($secondWeight);
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
                            $qdata['answer_text'] .= ' ' . $this->formatWeightValue($data[$secondKey]);
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
          if ($key === 'medications') {
              $medicationsRowIndex = count($rowsToInsert);
              $medicationsOriginalAnswer = $qdata['answer_text'];

              $rawSelections = [];
              if (is_array($value)) {
                  $rawSelections = $value;
              } elseif ($value !== null && $value !== '') {
                  $rawSelections = [$value];
              }

              $selectedMedicationSlugs = [];
              foreach ($rawSelections as $selection) {
                  $slug = perch_questionnaire_medication_slug((string)$selection);
                  if ($slug !== '') {
                      $selectedMedicationSlugs[] = $slug;
                  }
              }

              if (!empty($selectedMedicationSlugs)) {
                  $selectedMedicationSlugs = array_values(array_unique($selectedMedicationSlugs));
              }
          }

          if ($isMedicationWeightQuestion) {
              $slug = substr($key, 7);
              if ($slug !== '' && $qdata['answer_text'] !== '') {
                  $medicationDetails[$slug]['weight'] = $qdata['answer_text'];
              }
          }

          if (strpos($key, 'dose-') === 0) {
              $slug = substr($key, strlen('dose-'));
              if ($slug !== '' && $qdata['answer_text'] !== '') {
                  $medicationDetails[$slug]['dose'] = $qdata['answer_text'];
              }
          }

          if (strpos($key, 'recently-dose-') === 0) {
              $slug = substr($key, strlen('recently-dose-'));
              if ($slug !== '' && $qdata['answer_text'] !== '') {
                  $medicationDetails[$slug]['recent'] = $qdata['answer_text'];
              }
          }

          if (strpos($key, 'continue-dose-') === 0) {
              $slug = substr($key, strlen('continue-dose-'));
              if ($slug !== '' && $qdata['answer_text'] !== '') {
                  $medicationDetails[$slug]['continue'] = $qdata['answer_text'];
              }
          }

          $rowsToInsert[] = $qdata;

      }

    /*  if ($medicationsRowIndex !== null && isset($rowsToInsert[$medicationsRowIndex])) {
          $summaries = [];
          foreach ($selectedMedicationSlugs as $slug) {
              if ($slug === '' || $slug === 'none') {
                  continue;
              }

              $label = $this->getMedicationLabel($slug);
              $details = $medicationDetails[$slug] ?? [];
              $detailParts = [];

              if (!empty($details['weight'])) {
                  $detailParts[] = 'Starting weight: ' . $details['weight'];
              }

              if (!empty($details['dose'])) {
                  $detailParts[] = 'Last dose: ' . $details['dose'];
              }

              if (!empty($details['recent'])) {
                  $detailParts[] = 'Most recent dose: ' . $details['recent'];
              }

              if (!empty($details['continue'])) {
                  $detailParts[] = 'Continuation preference: ' . $details['continue'];
              }

              $detailParts = array_values(array_filter($detailParts, 'strlen'));

              if (!empty($detailParts)) {
                  $summaries[] = $label . ' — ' . implode('; ', $detailParts);
              } else {
                  $summaries[] = $label;
              }
          }

          $summaries = array_values(array_filter($summaries, 'strlen'));

          if (!empty($summaries)) {
              $rowsToInsert[$medicationsRowIndex]['answer_text'] = implode(' | ', $summaries);
          } elseif ($medicationsOriginalAnswer !== '') {
              $rowsToInsert[$medicationsRowIndex]['answer_text'] = $medicationsOriginalAnswer;
          }
      }*/

      $insertSucceeded = true;

      foreach ($rowsToInsert as $qdata) {
          $qdata['qid'] = $new_id;

          $result = $this->db->insert(PERCH_DB_PREFIX.'questionnaire', $qdata);

          if ($result === false) {
              $insertSucceeded = false;
              break;
          }
      }

      if ($insertSucceeded) {
          $logsPersisted = $this->persistQuestionnaireLogs(
              $questionnaireUUID !== '' ? $questionnaireUUID : $memberID . ':' . $type,
              $memberID,
              $type,
              $rawLogEntries,
              $rawLogMetadata,
              $logSummary
          );

          if ($logsPersisted) {
              $this->db->execute('COMMIT');
              return $new_id;
          }
      }

        $this->db->execute('ROLLBACK');
        return false;
    }
}

    private function buildQuestionnaireLogUrl($uuid, $type)
    {
        if (!is_string($uuid) || $uuid === '') {
            return null;
        }

        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($host === '') {
            return null;
        }

        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
        $scheme = $isSecure ? 'https' : 'http';
        $url = sprintf('%s://%s/perch/addons/apps/perch_members/questionnaire_logs/?userId=%s', $scheme, $host, rawurlencode($uuid));

        if ($type !== 'first-order') {
            $url .= '&type=re-order';
        }

        return $url;
    }

    private function persistQuestionnaireLogs($uuid, $memberID, $type, array $rawLogEntries, array $metadata, $logSummary)
    {
        if (empty($rawLogEntries)) {
            return true;
        }

        $logDirectory = $this->resolveQuestionnaireLogDirectory($type);
        if ($logDirectory === null) {
            PerchUtil::debug('Unable to resolve questionnaire log directory.', 'error');
            return false;
        }

        if (!$this->ensureDirectoryExists($logDirectory)) {
            PerchUtil::debug('Unable to create questionnaire log directory: ' . $logDirectory, 'error');
            return false;
        }

        $metadata = array_merge(
            [
                'user_id' => $uuid,
                'member_id' => $memberID,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'saved_at' => date('Y-m-d H:i:s'),
            ],
            $metadata
        );

        $rawPayload = [
            'metadata' => $metadata,
            'log' => $rawLogEntries,
        ];

        $rawPath = PerchUtil::file_path($logDirectory . DIRECTORY_SEPARATOR . $uuid . '_raw_log.json');
        if (@file_put_contents($rawPath, json_encode($rawPayload, JSON_PRETTY_PRINT)) === false) {
            PerchUtil::debug('Unable to write questionnaire raw log file: ' . $rawPath, 'error');
            return false;
        }

        if (!is_array($logSummary) || !array_key_exists('grouped', $logSummary)) {
            if (function_exists('perch_members_summarise_answer_log')) {
                $logSummary = perch_members_summarise_answer_log($rawLogEntries);
            } else {
                $logSummary = ['grouped' => []];
            }
        }

        $groupedPayload = [
            'metadata' => $metadata,
            'grouped_log' => $logSummary['grouped'] ?? [],
        ];

        $groupedPath = PerchUtil::file_path($logDirectory . DIRECTORY_SEPARATOR . $uuid . '_grouped_log.json');
        if (@file_put_contents($groupedPath, json_encode($groupedPayload, JSON_PRETTY_PRINT)) === false) {
            @unlink($rawPath);
            PerchUtil::debug('Unable to write questionnaire grouped log file: ' . $groupedPath, 'error');
            return false;
        }

        return true;
    }

    private function resolveQuestionnaireLogDirectory($type)
    {
        $basePath = $_SERVER['DOCUMENT_ROOT'] ?? '';

        if ($basePath === '' && defined('PERCH_SITEPATH')) {
            $basePath = PERCH_SITEPATH;
        }

        if ($basePath === '' && defined('PERCH_PATH')) {
            $basePath = realpath(PERCH_PATH . '/../');
        }

        if ($basePath === '') {
            $basePath = getcwd();
        }

        if (!is_string($basePath) || $basePath === '') {
            return null;
        }

        $basePath = rtrim($basePath, '/\\') . '/logs';
        if ($type !== 'first-order') {
            $basePath .= '/reorder';
        }

        return PerchUtil::file_path($basePath);
    }

    private function ensureDirectoryExists($directory)
    {
        if (is_dir($directory)) {
            return true;
        }

        return @mkdir($directory, 0755, true) || is_dir($directory);
    }
}
