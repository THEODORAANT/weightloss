<?php

class PerchMembers_Questionnaires extends PerchAPI_Factory
{
    protected $table     = 'questionnaire';
    protected $pk        = 'id';
    protected $singular_classname = 'PerchMembers_Questionnaire';

    public $reorder_questions = [];
    public $reorder_questions_answers = [];
    public $questions_and_answers = [];
    public $questions = [];

    protected $question_aliases = [
        'reorder'      => [],
        'first-order'  => [],
    ];

    protected $default_sort_column = 'created_at';
    public $static_fields = array('version','question_text', 'question_slug', 'question_slug', 'answer', 'answer_text','member_id');

    protected static $questionnaire_table_checked = false;

    public function __construct($api=false)
    {
        parent::__construct($api);

        $this->ensure_question_schema();
        $this->ensure_questionnaire_table_schema();
        $this->backfill_question_metadata();

        $Questions = new PerchMembers_QuestionnaireQuestions($api);

        $this->load_questions_for_type($Questions, 'reorder');
        $this->load_questions_for_type($Questions, 'first-order');
    }

    protected function ensure_question_schema()
    {
        static $checked = false;
        if ($checked) {
            return;
        }

        $checked = true;
        $table = PERCH_DB_PREFIX.'members_questionnaire_questions';
        $columns = $this->db->get_rows('SHOW COLUMNS FROM '.$table);
        $fields = [];
        if (is_array($columns)) {
            foreach ($columns as $col) {
                if (isset($col['Field'])) {
                    $fields[] = $col['Field'];
                }
            }
        }

        if (!in_array('fieldName', $fields)) {
            $this->db->execute('ALTER TABLE '.$table.' ADD COLUMN fieldName varchar(64) DEFAULT NULL AFTER `type`');
        }

        if (!in_array('stepSlug', $fields)) {
            $this->db->execute('ALTER TABLE '.$table.' ADD COLUMN stepSlug varchar(64) DEFAULT NULL AFTER `fieldName`');
        }

        if (!in_array('dependencies', $fields)) {
            $this->db->execute('ALTER TABLE '.$table.' ADD COLUMN dependencies text AFTER `options`');
        }
    }

    protected function ensure_questionnaire_table_schema()
    {
        if (self::$questionnaire_table_checked) {
            return;
        }

        self::$questionnaire_table_checked = true;

        $table = PERCH_DB_PREFIX.'questionnaire';
        $columns = $this->db->get_rows('SHOW COLUMNS FROM '.$table);

        if (!is_array($columns)) {
            return;
        }

        $fields = [];
        foreach ($columns as $col) {
            if (isset($col['Field'])) {
                $fields[] = $col['Field'];
            }
        }

        if (!in_array('order_id', $fields)) {
            $this->db->execute('ALTER TABLE '.$table.' ADD COLUMN order_id int(10) unsigned DEFAULT NULL AFTER member_id');
        }
    }

    protected function backfill_question_metadata()
    {
        $table = PERCH_DB_PREFIX.'members_questionnaire_questions';
        $needs = $this->db->get_value('SELECT COUNT(*) FROM '.$table.' WHERE fieldName IS NULL OR fieldName="" OR stepSlug IS NULL OR stepSlug=""');

        $needsDependencies = $this->db->get_value('SELECT COUNT(*) FROM '.$table.' WHERE dependencies IS NULL OR dependencies=""');

        if ((int)$needs === 0 && (int)$needsDependencies === 0) {
            return;
        }

        $seed_file = __DIR__ . '/questionnaire_default_questions.php';
        if (!file_exists($seed_file)) {
            return;
        }

        $definitions = include $seed_file;
        if (!is_array($definitions)) {
            return;
        }

        $rows = $this->db->get_rows('SELECT questionID, questionnaireType, questionKey, fieldName, stepSlug, dependencies FROM '.$table);
        if (!is_array($rows)) {
            return;
        }

        foreach ($rows as $row) {
            $type = $row['questionnaireType'];
            $key  = $row['questionKey'];

            if (!isset($definitions[$type]) || !isset($definitions[$type][$key])) {
                continue;
            }

            $definition = $definitions[$type][$key];
            $update = [];

            if ((empty($row['fieldName'])) && isset($definition['name'])) {
                $update['fieldName'] = $definition['name'];
            }

            if ((empty($row['stepSlug'])) && isset($definition['step'])) {
                $update['stepSlug'] = $definition['step'];
            }

            $dependencies_empty = empty($row['dependencies']);
            if (!$dependencies_empty) {
                $decoded_dependencies = PerchUtil::json_safe_decode($row['dependencies'], true);
                if (is_array($decoded_dependencies) && !PerchUtil::count($decoded_dependencies)) {
                    $dependencies_empty = true;
                }
            }

            if ($dependencies_empty && isset($definition['dependencies'])) {
                $encoded = PerchUtil::json_safe_encode($definition['dependencies']);
                if ($encoded !== false) {
                    $update['dependencies'] = $encoded;
                }
            }

            if (!empty($update)) {
                $this->db->update($table, $update, 'questionID', $row['questionID']);
            }
        }
    }

    protected function load_questions_for_type(PerchMembers_QuestionnaireQuestions $Questions, $type)
    {
        $canonical = $this->normalise_type($type);
        $rows = $Questions->get_for_type($canonical);
        if (!$rows) {
            return;
        }

        foreach ($rows as $row) {
            $opts = $row['options'] ? PerchUtil::json_safe_decode($row['options'], true) : [];
            if (!is_array($opts)) {
                $opts = [];
            }

            $deps = $row['dependencies'] ? PerchUtil::json_safe_decode($row['dependencies'], true) : [];
            if (!is_array($deps)) {
                $deps = [];
            }

            $field_name = isset($row['fieldName']) && $row['fieldName'] !== '' ? $row['fieldName'] : $row['questionKey'];
            $step = isset($row['stepSlug']) && $row['stepSlug'] !== '' ? $row['stepSlug'] : $row['questionKey'];

            $question = [
                'label'        => $row['label'],
                'type'         => $row['type'],
                'name'         => $field_name,
                'options'      => $opts,
                'step'         => $step,
                'dependencies' => $deps,
                'sort'         => isset($row['sort']) ? (int)$row['sort'] : null,
                'question_id'  => isset($row['questionID']) ? (int)$row['questionID'] : null,
            ];

            $aliases = $this->expand_aliases($row['questionKey'], $row['type']);
            if ($field_name && $field_name !== $row['questionKey']) {
                $aliases = array_merge($aliases, $this->expand_aliases($field_name, $row['type']));
            }

            $aliases = array_values(array_unique(array_filter($aliases)));
            $question['aliases'] = array_values(array_diff($aliases, [$row['questionKey']]));

            if ($canonical === 'reorder') {
                $this->reorder_questions[$row['questionKey']] = $row['label'];
                $this->reorder_questions_answers[$row['questionKey']] = $question;
            } else {
                $this->questions[$row['questionKey']] = $row['label'];
                $this->questions_and_answers[$row['questionKey']] = $question;
            }

            foreach ($aliases as $alias) {
                $this->question_aliases[$canonical][$alias] = $row['questionKey'];
            }
        }
    }

    protected function expand_aliases($value, $type = null)
    {
        $aliases = [];
        $value = trim((string)$value);
        if ($value === '') {
            return $aliases;
        }

        $aliases[] = $value;

        if (substr($value, -2) === '[]') {
            $aliases[] = substr($value, 0, -2);
        } elseif ($type === 'checkbox') {
            $aliases[] = $value.'[]';
        }

        $extra = [];
        foreach ($aliases as $alias) {
            if (strpos($alias, '-') !== false) {
                $extra[] = str_replace('-', '_', $alias);
            }
            if (strpos($alias, '_') !== false) {
                $extra[] = str_replace('_', '-', $alias);
            }
        }

        return array_values(array_unique(array_merge($aliases, $extra)));
    }

    protected function normalise_type($type)
    {
        return ($type === 're-order') ? 'reorder' : $type;
    }

    protected function resolve_question_key($type, $key)
    {
        $type = $this->normalise_type($type);
        if (isset($this->question_aliases[$type][$key])) {
            return $this->question_aliases[$type][$key];
        }

        foreach ($this->expand_aliases($key) as $alias) {
            if (isset($this->question_aliases[$type][$alias])) {
                return $this->question_aliases[$type][$alias];
            }
        }

        return $key;
    }

    protected function get_question_definition($type, $key)
    {
        $type = $this->normalise_type($type);
        $questions = $this->get_questions_answers($type);
        if (!is_array($questions)) {
            return null;
        }

        $canonical = $this->resolve_question_key($type, $key);
        return $questions[$canonical] ?? null;
    }

    protected function format_answer_value($type, $key, $value)
    {
        $definition = $this->get_question_definition($type, $key);
        if (!$definition) {
            return is_array($value) ? implode(', ', $value) : $value;
        }

        $options = isset($definition['options']) && is_array($definition['options']) ? $definition['options'] : [];

        if (!PerchUtil::count($options)) {
            return is_array($value) ? implode(', ', $value) : $value;
        }

        if (is_array($value)) {
            $labels = [];
            foreach ($value as $item) {
                $labels[] = isset($options[$item]) ? $options[$item] : $item;
            }
            return implode(', ', $labels);
        }

        return isset($options[$value]) ? $options[$value] : $value;
    }

    public function get_questions($type='first-order')
    {
        $type = $this->normalise_type($type);
        if ($type === 'reorder') {
            return $this->reorder_questions;
        }

        return $this->questions;
    }

    public function get_questions_answers($type='first-order')
    {
        $type = $this->normalise_type($type);
        if ($type === 'reorder') {
            return $this->reorder_questions_answers;
        }

        return $this->questions_and_answers;
    }

    public function get_question_structure($type = 'first-order')
    {
        $type = $this->normalise_type($type);
        $questions = $this->get_questions_answers($type);
        $structure = [];

        if (PerchUtil::count($questions)) {
            foreach ($questions as $key => $question) {
                $structure[$key] = [
                    'key'          => $key,
                    'label'        => $question['label'],
                    'type'         => $question['type'],
                    'name'         => $question['name'] ?? $key,
                    'options'      => isset($question['options']) && is_array($question['options']) ? $question['options'] : [],
                    'step'         => $question['step'] ?? $key,
                    'dependencies' => isset($question['dependencies']) && is_array($question['dependencies']) ? $question['dependencies'] : [],
                    'sort'         => isset($question['sort']) ? (int)$question['sort'] : null,
                    'question_id'  => isset($question['question_id']) ? (int)$question['question_id'] : null,
                ];

                if (!empty($question['aliases'])) {
                    $structure[$key]['aliases'] = $question['aliases'];
                }
            }
        }

        if (PerchUtil::count($structure)) {
            uasort($structure, function ($a, $b) {
                $sortA = isset($a['sort']) ? (int)$a['sort'] : PHP_INT_MAX;
                $sortB = isset($b['sort']) ? (int)$b['sort'] : PHP_INT_MAX;

                if ($sortA === $sortB) {
                    $keyA = isset($a['key']) ? (string)$a['key'] : '';
                    $keyB = isset($b['key']) ? (string)$b['key'] : '';
                    return strcmp($keyA, $keyB);
                }

                return $sortA <=> $sortB;
            });
        }

        return $structure;
    }
        public function get_for_member($memberID,$type="first-order",$orderID=null)
    {
        $sql = 'SELECT d.*
                FROM  '.PERCH_DB_PREFIX.'questionnaire d
                WHERE d.member_id='.$this->db->pdb((int)$memberID).' and type="'.$type.'"';

        if ($orderID !== null) {
            $sql .= ' AND d.order_id='.$this->db->pdb((int)$orderID);
        }

        $sql .= ' order by created_at desc';

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
           if ($step=="medications" ){
           if (is_array($value) &&!empty(array_intersect(['wegovy','ozempic','saxenda','rybelsus','mounjaro','alli','mysimba','other'], $value))) {
            return true;
           }
           }

              if ($step=="starting_wegovy" || $step=="unit-wegovy" || $step=="weight2-wegovy" ||  $step=="weight-wegovy"){

                     return true;
                     }

                     if ($step==="dose-wegovy" || $step=="recently-dose-wegovy") {

                         return true;
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

          if (empty($data['height']) || empty($data['heightunit'])) {
              $errors[] = 'Please provide your height and select a unit.';
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
          if (in_array('wegovy', $data['medications'])) {
              if (empty($data['weight-wegovy'])) {
                  $errors[] = 'Please provide your weight before starting Wegovy.';
              }

              if (empty($data['dose-wegovy'])) {
                  $errors[] = 'Please indicate your last dose of Wegovy.';
              }

              if (empty($data['recently-dose-wegovy'])) {
                  $errors[] = 'Please provide the most recent dose prescribed.';
              }

              if (empty($data['continue-dose-wegovy'])) {
                  $errors[] = 'Please select your preferred continuation dose.';
              }

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
    public function add_to_member($memberID,$data,$type,$orderID=null)
     { // echo "add_to_member";

// print_r($memberID);print_r($type); echo PerchUtil::count($this->get_for_member($memberID));
       //     if(!PerchUtil::count($this->get_for_member($memberID)) ){
// echo "add_to_member inn";
$Members = new PerchMembers_Members;
			$Member = $Members->find($memberID);
			  $memberdetails = $Member->to_array();

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
 //$props = json_decode($memberdetails['memberProperties'], true);
     	$props = PerchUtil::json_safe_decode($Member->memberProperties(), true);

        if (!is_array($props)) $props = [];


        $props["height"]=$data["height"];
        $props["height2"]=$height2;
 $props["heightunit"]= $data["heightunit"];
        $props['heightunit-radio'] = $data['heightunit-radio'] ?? $data['heightunit'] ?? 'cm';
$out=[];
 	$out['memberProperties'] = PerchUtil::json_safe_encode($props);

    	$Member->update($out);
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
    	    	//insert_status
            $insert_status ="INSERT INTO ".PERCH_DB_PREFIX."questionnaire_member_status (`questionnaire_id`,memberID,`status`) VALUES ('v1',".$memberID.",'pending'); ";
         $new_id =$this->db->execute($insert_status);
 $data['bmi']=$result;
      foreach ($data as $key => $value) {
       $qdata = array();
        $qdata['type'] = $type;
      $qdata['question_slug']=$key;




      if($type=="first-order"){
          $weightradiounit=$data["weightunit"];
           $heightunitradio=$data["heightunit"];
           if(isset($data["unit-wegovy"])){
           $unitwegovyradio=$data["unit-wegovy"];
           }

      if (array_key_exists($key, $this->questions)) {

      $qdata['question_text']=$this->questions[$key];
      $qdata['answer_text']=$this->format_answer_value('first-order', $key, $value);
      }
      }else{
       if (array_key_exists($key, $this->reorder_questions)) {
       $qdata['question_text']=$this->reorder_questions[$key];
       $qdata['answer_text']=$this->format_answer_value('reorder', $key, $value);
       }
      }
         if($type=="first-order"){
  if($key=="weight"){

        $weightunit=explode("-",$weightradiounit);
        if(count($weightunit)>1){
         $qdata['answer_text']=trim(($qdata['answer_text'] ?? '')." ".$weightunit[0]);
          if(isset($data["weight2"]) ){
                $qdata['answer_text'].= " ".$data["weight2"]."  ".$weightunit[1];

            }
            }
        }
        if($key=="weight-wegovy"){
            $weightwegovyunit=explode("-",$unitwegovyradio);
                if(count($weightwegovyunit)>1){
                 $qdata['answer_text']=trim(($qdata['answer_text'] ?? '')." ".$weightwegovyunit[0]);
                  if(isset($data["weight2-wegovy"]) ){
                         $qdata['answer_text'].= " ".$data["weight2-wegovy"]."  ".$weightwegovyunit[1];

                    }
                    }
        }
         if($key=="height"){

             $heightunit=explode("-",$heightunitradio);
              if(count($heightunit)>1){
                $qdata['answer_text']=trim(($qdata['answer_text'] ?? '')." ".$heightunit[0]);
                   if(isset($data["height2"])){
                                     $qdata['answer_text'].= " ".$data["height2"]."  ".$heightunit[1];

                                }
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
                $qdata['version']="v1";
                 $qdata['qid']= $new_id;
           if ($orderID !== null) {
                $qdata['order_id'] = (int)$orderID;
           }

           $columns = implode(", ", array_keys($qdata)); // Columns as a string
                        $values = "'" . implode("', '", array_map('addslashes', array_values($qdata))) . "'";
           $insert_query .="INSERT INTO ".PERCH_DB_PREFIX."questionnaire (".$columns.") VALUES (".$values."); ";

      }

     // }

//echo  $insert_query;

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
