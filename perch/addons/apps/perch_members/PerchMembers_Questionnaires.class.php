<?php

class PerchMembers_Questionnaires extends PerchAPI_Factory
{
    protected $table     = 'questionnaire';
	protected $pk        = 'id';
	protected $singular_classname = 'PerchMembers_Questionnaire';
	public $reorder_questions = [];
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
    "other_medical_conditions"=>"list_any",
    "wegovy_side_effects"=>"wegovy_side_effects",
    "gp_informed"=>"gp_informed",
    "GP_email_address"=>"gp_address",
    "Get access to special offers"=>"access_special_offers"
    ];
    public $reorder_questions_answers = [];

    public $questions_and_answers = [];


                public $questions = [];

    public function __construct($api=false)
    {
        parent::__construct($api);

        $Questions = new PerchMembers_QuestionnaireQuestions($api);

        $rows = $Questions->get_for_type('reorder');
        if ($rows) {
            foreach($rows as $row) {
                $opts = $row['options'] ? PerchUtil::json_safe_decode($row['options'], true) : [];
                $this->reorder_questions[$row['questionKey']] = $row['label'];
                $this->reorder_questions_answers[$row['questionKey']] = [
                    'label' => $row['label'],
                    'type'  => $row['type'],
                    'name'  => $row['questionKey'],
                    'options' => $opts
                ];
            }
        }

        $rows = $Questions->get_for_type('first-order');
        if ($rows) {
            foreach($rows as $row) {
                $opts = $row['options'] ? PerchUtil::json_safe_decode($row['options'], true) : [];
                $this->questions[$row['questionKey']] = $row['label'];
                $this->questions_and_answers[$row['questionKey']] = [
                    'label' => $row['label'],
                    'type'  => $row['type'],
                    'name'  => $row['questionKey'],
                    'options' => $opts
                ];
            }
        }
    }

public $doses = [
    '25mg' => '0.25mg/2.5mg',
    '05mg' => '0.5mg/5mg',
    '1mg'  => '1mg/7.5mg',
    '17mg' => '1.7mg/12.5mg',
    '24mg' => '2.4mg/15mg',
    'other'=> 'Other'
];


   /* protected $required_answers=[
    "age"=>["18to74"],
     "ethnicity"=>["asian","African"],

    ]*/

	protected $default_sort_column = 'created_at';
	public $static_fields = array('version','question_text', 'question_slug', 'question_slug', 'answer', 'answer_text','member_id');
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
	public function get_for_member($memberID,$type="first-order")
    {
        $sql = 'SELECT d.*
                FROM  '.PERCH_DB_PREFIX.'questionnaire d
                WHERE d.member_id='.$this->db->pdb((int)$memberID).' and type="'.$type.'" order by created_at desc';

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
      // Convert weight to kilograms if needed
      if ($weightUnit === 'st-lbs') {
         $totalPounds = ($weight * 14) + $weight2;
          $weightKg = $totalPounds * 0.453592;

      } elseif ($weightUnit === 'kg') {
          $weightKg = $weight;
      } else {
          return "Invalid weight unit. Use 'kg' or 'lbs'.";
      }

      // Convert height to meters based on unit
      if ($heightUnit === 'cm') {
          $heightM = $height1 / 100;
      } elseif ($heightUnit === 'in') {
          $heightM = $height1 * 0.0254;
      } elseif ($heightUnit === 'ft-in') {

          $totalInches = ($height1 * 12) + $height2;
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
    public function add_to_member($memberID,$data,$type)
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
       if(is_array($value)){
                  $qdata['answer_text']=implode(", ", $value);

                }else{
                   $qdata['answer_text']=$value;

                }
      }
      }else{
       if (array_key_exists($key, $this->reorder_questions)) {
       $qdata['question_text']=$this->reorder_questions[$key];
        if(is_array($value)){
                   $qdata['answer_text']=implode(", ", $value);

                 }else{
                    $qdata['answer_text']=$value;

                 }
       }
      }
         if($type=="first-order"){
  if($key=="weight"){

        $weightunit=explode("-",$weightradiounit);
        if(count($weightunit)>1){
         $qdata['answer_text'].= " ".$weightunit[0];
          if(isset($data["weight2"]) ){
                 $qdata['answer_text'].= " ".$data["weight2"]."  ".$weightunit[1];

            }
            }
        }
        if($key=="weight-wegovy"){
            $weightwegovyunit=explode("-",$unitwegovyradio);
                if(count($weightwegovyunit)>1){
                 $qdata['answer_text'].= " ".$weightwegovyunit[0];
                  if(isset($data["weight2-wegovy"]) ){
                         $qdata['answer_text'].= " ".$data["weight2-wegovy"]."  ".$weightwegovyunit[1];

                    }
                    }
        }
           if($key=="weight-wegovy"){

            $qdata['answer_text']=$doses[$value];
        }

         if($key=="height"){

             $heightunit=explode("-",$heightunitradio);
              if(count($heightunit)>1){
                 $qdata['answer_text'].= " ".$heightunit[0];
                   if(isset($data["height2"])){
                                     $qdata['answer_text'].= " ".$data["height2"]."  ".$heightunit[1];

                                }
                                }

       }

}
if(isset($data["uuid"])){
  $qdata['uuid']=$data["uuid"];
}elseif(isset($_SESSION['step_data'])){
           $qdata['uuid']=$_SESSION['step_data']['user_id'];
          }



                $qdata['member_id']=$memberID;
                $qdata['version']="v1";
                 $qdata['qid']= $new_id;
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
