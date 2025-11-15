<?php
    if (!defined('PERCH_MEMBERS_SESSION_TIME')) define('PERCH_MEMBERS_SESSION_TIME', '5 DAYS');
    if (!defined('PERCH_MEMBERS_COOKIE'))       define('PERCH_MEMBERS_COOKIE', 'p_m');

    spl_autoload_register(function($class_name){
        if (strpos($class_name, 'PerchMembers')===0) {
            include(__DIR__.'/'.$class_name.'.class.php');
            return true;
        }
        return false;
    });

    PerchSystem::register_template_handler('PerchMembers_Template');

    if (PERCH_RUNWAY_ROUTED) {
        $members_init = function(){
            $API  = new PerchAPI(1.0, 'perch_members');
            $API->on('page.loaded', 'perch_members_init');
        };
        $members_init();
    }else{
        perch_members_init();
    }

    function perch_members_init()
    {
        perch_members_recover_session();
        perch_members_check_page_access();
    }


        function perch_members_form_handler($SubmittedForm)
    {

        $uploadSuccessful = false;

        if ($SubmittedForm->validate() || $SubmittedForm->formID=="upload") {

    		$API  = new PerchAPI(1.0, 'perch_members');

    		switch($SubmittedForm->formID) {

    			case 'login':
    				$PerchMembers_Auth = new PerchMembers_Auth($API);
    				if (!$PerchMembers_Auth->handle_login($SubmittedForm)) {
                        $SubmittedForm->throw_error('login');
                    }
    				break;

                case 'profile':
                    $Session = PerchMembers_Session::fetch();
                    if ($Session->logged_in && $Session->get('token')==$SubmittedForm->data['token']) {
                        $Members = new PerchMembers_Members($API);
                        if (is_object($Members)) $Member = $Members->find($Session->get('memberID'));
                        if (is_object($Member)) {
                            $Member->update_profile($SubmittedForm);
                            $PerchMembers_Auth = new PerchMembers_Auth($API);
                            $PerchMembers_Auth->refresh_session_data($Member);
                        }
                    }else{
                        $SubmittedForm->throw_error('profile');
                    }
                    break;

                case 'register':
                    $Members = new PerchMembers_Members($API);
                    $Members->register_with_form($SubmittedForm);
                    break;

                case 'reset':
                    $Members = new PerchMembers_Members($API);
                    $Members->reset_member_password($SubmittedForm);
                    break;
                case 'upload':

                       $Session = PerchMembers_Session::fetch();

                                        if ($Session->logged_in ){
                                        //&& $Session->get('token')==$SubmittedForm->data['token']) {
                                            $Members = new PerchMembers_Members($API);
                                            if (is_object($Members)) $Member = $Members->find($Session->get('memberID'));
                                            if (is_object($Member)) {
                                                $uploadSuccessful = $Member->upload_member_file($SubmittedForm);

                                            }
                                        }else{
                                            $SubmittedForm->throw_error('profile');
                                        }
                    break;
                case 'password':
                    $Session = PerchMembers_Session::fetch();
                    if ($Session->logged_in && $Session->get('token')==$SubmittedForm->data['token']) {
                        $Members = new PerchMembers_Members($API);
                        if (is_object($Members)) $Member = $Members->find($Session->get('memberID'));
                        if (is_object($Member)) $Member->change_password($SubmittedForm);
                    }else{
                        $SubmittedForm->throw_error('password');
                    }
                    break;


    		}

            if (!$SubmittedForm->redispatched) {
                $Tag = $SubmittedForm->get_form_attributes();
                PerchUtil::mark('here mem');
                $Perch = Perch::fetch();
                $formErrors = $Perch->get_form_errors($SubmittedForm->formID);

                if ($uploadSuccessful && !$formErrors) {
                    $redirectTarget = $_SERVER['REQUEST_URI'];

                    if (is_object($Tag) && $Tag->next()) {
                        $redirectTarget = $Tag->next();
                    }

                    $SubmittedForm->clear_from_post_env();
                    PerchUtil::redirect($redirectTarget);
                }

                if (is_object($Tag) && $Tag->next()) {
                    if (!$formErrors) {
                        PerchUtil::redirect($Tag->next());
                    }else{
                        PerchUtil::debug($formErrors, 'error');
                    }
                }
            }else{

            }


        }

        $Perch = Perch::fetch();
        PerchUtil::debug($Perch->get_form_errors($SubmittedForm->formID));
    }



	function perch_members_login_form($opts=array(), $return=false)
	{
		$API  = new PerchAPI(1.0, 'perch_members');

        $defaults = array();
        $defaults['template']        = 'login/login_register_form.html';

        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        $Template = $API->get('Template');
        $Template->set('members/'.$opts['template'], 'members');

        $html = $Template->render(array());
        $html = $Template->apply_runtime_post_processing($html);

        if ($return) return $html;
        echo $html;

	}


    function perch_members_recover_session()
    {
        $API  = new PerchAPI(1.0, 'perch_members');
        $PerchMembers_Auth = new PerchMembers_Auth($API);
        $PerchMembers_Auth->recover_session();
    }
function perch_member_api_register($data)
{
  $API  = new PerchAPI(1.0, 'perch_members');
 $Members = new PerchMembers_Members($API);
 $memberID=$Members->register_with_api($data);
 $Tags = new PerchMembers_Tags($API);
 $Tag  = $Tags->find_or_create("register-app");
   if (is_object($Tag)) {
         $Tag->add_to_member($memberID, false);
    }
 return $memberID;
}
function perch_member_api_update_profile($memberID,$data)
{
  $API  = new PerchAPI(1.0, 'perch_members');

  $Members = new PerchMembers_Members($API);
    if (is_object($Members)) $Member = $Members->find($memberID);
      if (is_object($Member)) {
            $Member->update_profile($data,true);
        }
        return true;
    }
function perch_member_profile($memberID)
{
$API  = new PerchAPI(1.0, 'perch_members');

  $Members = new PerchMembers_Members($API);
 if (is_object($Members)) $Member = $Members->find($memberID);
                        if (is_object($Member)) {
                        return $Member->to_array();
                        }
                        return [];
                        }
           function perch_member_api_auth($token)
           {     $Session = PerchMembers_Session::fetch();
         //  echo "perch_member_api_auth"; echo $token;
         //  print_r($Session );
                   if ($Session->logged_in && $Session->get('token')==$token) {
                   return true;
                   }
                      return false;
         }
function perch_member_api_login($data)
{
  $API  = new PerchAPI(1.0, 'perch_members');

	$PerchMembers_Auth = new PerchMembers_Auth($API);
	return $PerchMembers_Auth->handle_login_api($data);

}

function perch_member_upload_document_api($memberID,$data){
  $API  = new PerchAPI(1.0, 'perch_members');

 $Members = new PerchMembers_Members($API);
  if (is_object($Members)) $Member = $Members->find($memberID);
  if (is_object($Member)) {   $Member->upload_member_file($data,$memberID);  }
}
    function perch_members_check_page_access()
    {
        $Session = PerchMembers_Session::fetch();

        if ($Session->logged_in) {
            $user_tags = $Session->get_tags();
        }else{
            $user_tags = array();
        }

        if (!is_array($user_tags)) $user_tags = array();
        $Page = PerchSystem::get_page_object();


        if (!$Page) {
            $Pages = new PerchContent_Pages;
            $Perch = Perch::fetch();
            $Page = $Pages->find_by_path($Perch->get_page());
            if ($Page instanceof PerchContent_Page) {
                PerchSystem::set_page_object($Page);
            }
        }

        if ($Page) {
            $page_tags = $Page->access_tags();

            if (!is_array($page_tags)) $page_tags = array();

            if (PerchUtil::count($page_tags)) {
                $intersection = array_intersect($user_tags, $page_tags);

                if (PerchUtil::count($intersection)===0) {
                    // no access!
                    $API  = new PerchAPI(1.0, 'perch_members');
                    $Settings = $API->get('Settings');
                    $redirect_url = $Settings->get('perch_members_login_page')->val();
                    if ($redirect_url) {
                        $redirect_url = str_replace('{returnURL}', $Perch->get_page(), $redirect_url);
                        PerchUtil::redirect($redirect_url);
                    }else{
                        die('Access denied.');
                    }
                }
            }
        }
    }

    function perch_member_logged_in()
    {
        $Session = PerchMembers_Session::fetch();
        return $Session->logged_in;
    }

    function perch_member_log_out()
    {
        $API  = new PerchAPI(1.0, 'perch_members');
        $PerchMembers_Auth = new PerchMembers_Auth($API);
        $PerchMembers_Auth->log_out();
    }

    function perch_member_get($property=null)
    {
        if ($property) {
            $Session = PerchMembers_Session::fetch();

            if ($Session->logged_in) {
                return $Session->get($property);
            }
        }

        return false;
    }

    function perch_member_is_passwordless()
    {
        $Session = PerchMembers_Session::fetch();
        if ($Session->logged_in) {
            $API  = new PerchAPI(1.0, 'perch_members');
            $Members = new PerchMembers_Members($API);
            if (is_object($Members)) $Member = $Members->find($Session->get('memberID'));
            if (is_object($Member)) {
                if (is_null($Member->memberPassword())) {
                    return true;
                }
            }
            return false;
        }

        return null;
    }

    function perch_member_has_tag($tag=false)
    {
        if ($tag) {
            $Session = PerchMembers_Session::fetch();

            if ($Session->logged_in) {
                return $Session->has_tag($tag);
            }
        }

        return false;
    }
function perch_member_questionsForQuestionnaire($type) {
     $API  = new PerchAPI(1.0, 'perch_members');
                       $Questionnaires = new PerchMembers_Questionnaires($API);
                      return $Questionnaires->get_questions_answers($type);


    }
    function perch_member_check_questionnaire_status_for_member($memberid,$qid) {
            $API  = new PerchAPI(1.0, 'perch_members');
              $Questionnaires = new PerchMembers_Questionnaires($API);
           return $Questionnaires->check_questionnaire_status_for_member($memberid,$qid);


            }
    function perch_member_validateQuestionnaire($data) {
     $API  = new PerchAPI(1.0, 'perch_members');
                       $Questionnaires = new PerchMembers_Questionnaires($API);
                      return $Questionnaires->validateQuestionnaire($data);


    }
        function perch_member_requireNextStep($step,$value) {
         $API  = new PerchAPI(1.0, 'perch_members');
                           $Questionnaires = new PerchMembers_Questionnaires($API);
                          return $Questionnaires->requireNextStep($step,$value);


        }
          /* function loginFileAnswers($question, $answer,$type="firstorder") {

           }*/

    function logAnswerChange($question, $answer,$type="firstorder") {

        $isReorder = ($type=="reorder");
        $logKey = $isReorder ? 'reorder_answer_log' : 'answer_log';

        if (!isset($_SESSION[$logKey]) || !is_array($_SESSION[$logKey])) {
            $_SESSION[$logKey] = [];
        }

        $normalisedAnswer = is_array($answer) ? implode(", ", $answer) : (string) $answer;

        $previousAnswer = null;
        for ($idx = count($_SESSION[$logKey]) - 1; $idx >= 0; $idx--) {
            if ($question === ($_SESSION[$logKey][$idx]['question'] ?? null)) {
                $previousAnswer = $_SESSION[$logKey][$idx]['answer'] ?? null;
                if (is_array($previousAnswer)) {
                    $previousAnswer = implode(", ", $previousAnswer);
                }
                break;
            }
        }

        $changed = ($previousAnswer !== null && $previousAnswer !== $normalisedAnswer);
        $action = 'new';
        if ($previousAnswer !== null) {
            $action = $changed ? 'updated' : 'reaffirmed';
        }

        $logEntry = [
            'question' => $question,
            'answer' => $normalisedAnswer,
            'previous_answer' => $previousAnswer,
            'changed' => $changed,
            'action' => $action,
            'time' => date('Y-m-d H:i:s')
        ];

        $_SESSION[$logKey][] = $logEntry;

    }

    function perch_members_normalise_answer_log_value($value)
    {
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
    }

    function perch_members_summarise_answer_log($rawLog)
    {
        if (!is_array($rawLog)) {
            $rawLog = [];
        }

        $grouped = [];
        $answersSeen = [];
        $hasChanges = false;

        foreach ($rawLog as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $question = $entry['question'] ?? null;

            if (!is_string($question) || $question === '') {
                continue;
            }

            $entryCopy = $entry;
            unset($entryCopy['question']);
            $grouped[$question][] = $entryCopy;

            if (!isset($answersSeen[$question])) {
                $answersSeen[$question] = [];
            }

            $currentAnswer = perch_members_normalise_answer_log_value($entry['answer'] ?? null);
            $previousAnswer = perch_members_normalise_answer_log_value($entry['previous_answer'] ?? null);

            if ($currentAnswer !== null) {
                $answersSeen[$question][$currentAnswer] = true;

                if (count($answersSeen[$question]) > 1) {
                    $hasChanges = true;
                }
            }

            if (!$hasChanges) {
                if (!empty($entry['changed'])) {
                    $hasChanges = true;
                } elseif ($previousAnswer !== null && $previousAnswer !== $currentAnswer) {
                    $hasChanges = true;
                }
            }
        }

        return [
            'grouped' => $grouped,
            'has_changes' => $hasChanges,
        ];
    }

    function perch_member_accept_questionnaire_api($memberid,$questionnaireID,$accepted)
            {
                $API  = new PerchAPI(1.0, 'perch_members');
                               $Questionnaires = new PerchMembers_Questionnaires($API);
                               $Questionnaires->accept_questionnaire_for_member($memberid,$questionnaireID,$accepted);


                             return true;
            }
      function perch_member_add_questionnaire_api($memberid,$data,$type,$orderID=null)
        {
            $API  = new PerchAPI(1.0, 'perch_members');
                           $Questionnaires = new PerchMembers_Questionnaires($API);
                        $questionnaireID = $Questionnaires->add_to_member($memberid,$data,$type,$orderID);

                      /*  if ($orderID) {
                            perch_members_link_questionnaire_to_order($orderID, $questionnaireID, $type);
                        }*/

                        return  $questionnaireID;

                       //  return true;
        }
    function perch_member_add_questionnaire($data,$type,$orderID=null)
    { //echo "perch_member_add_questionnaire";print_r($data);
      $Session = PerchMembers_Session::fetch();
$memberid=0;
                if ($Session->logged_in) {
                $memberid=$Session->get('memberID');
                }
                 $API  = new PerchAPI(1.0, 'perch_members');
                   $Questionnaires = new PerchMembers_Questionnaires($API);

                   if ($orderID === null && function_exists('perch_shop_successful_order_id')) {
                       $orderID = perch_shop_successful_order_id();
                   }

                   if ($orderID === null && class_exists('PerchShop_Session') && PerchShop_Session::is_set('shop_order_id')) {
                       $orderID = PerchShop_Session::get('shop_order_id');
                   }

                   $questionnaireID = $Questionnaires->add_to_member($memberid,$data,$type,$orderID);

                   /*if ($orderID) {
                       perch_members_link_questionnaire_to_order($orderID, $questionnaireID, $type);
                   }*/


                 return $questionnaireID;
    }
   function perch_members_link_questionnaire_to_order($orderID, $questionnaireID, $type)
    {
        if (!$orderID || !$questionnaireID) {
            return;
        }

        $ShopAPI = new PerchAPI(1.0, 'perch_shop');
        $Orders  = new PerchShop_Orders($ShopAPI);
        $Order   = $Orders->find((int)$orderID);

        if (!$Order) {
            return;
        }

        $fields = PerchUtil::json_safe_decode($Order->orderDynamicFields(), true);

        if (!is_array($fields)) {
            $fields = [];
        }

        if (!isset($fields['questionnaires']) || !is_array($fields['questionnaires'])) {
            $fields['questionnaires'] = [];
        }

        $fields['questionnaires'][$type] = (int)$questionnaireID;

        $Order->update([
            'orderDynamicFields' => PerchUtil::json_safe_encode($fields),
        ]);
    }
   function perch_member_add_commission($amount)
    { //echo "perch_member_add_questionnaire";print_r($data);
      $Session = PerchMembers_Session::fetch();
$memberid=0;
                if ($Session->logged_in) {
                $memberid=$Session->get('memberID');
                }
                 $API  = new PerchAPI(1.0, 'perch_members');
                   $Affiliate = new PerchMembers_Affiliate($API);
                  // $Affiliate->addCommission($memberid, $amount);
 $Affiliate->recordPurchase($memberid);

                 return true;
    }
    function perch_member_register_affiliate($referred_member_id,$affID){
                     $API  = new PerchAPI(1.0, 'perch_members');
                            $Affiliate = new PerchMembers_Affiliate($API);
                           // $Affiliate->addCommission($memberid, $amount);
          $Affiliate->registerAffiliate($referred_member_id,$affID);
           return true;
         }

     function perch_member_register_referral($referred_member_id, $referrer_affiliate_id){
                 $API  = new PerchAPI(1.0, 'perch_members');
                        $Affiliate = new PerchMembers_Affiliate($API);
                       // $Affiliate->addCommission($memberid, $amount);
      $Affiliate->registerReferral($referred_member_id, $referrer_affiliate_id);
       return true;
     }
    function perch_member_add_tag($tag=false, $expiry_date=false)
    {
        if ($tag) {
            $Session = PerchMembers_Session::fetch();

            if ($Session->logged_in) {
                if (!$Session->has_tag($tag)) {
                    $API  = new PerchAPI(1.0, 'perch_members');
                    $Tags = new PerchMembers_Tags($API);
                    $Tag  = $Tags->find_or_create($tag);
                    if (is_object($Tag)) {
                        $Tag->add_to_member($Session->get('memberID'), $expiry_date);
                        if (!headers_sent()) {
                            $Members = new PerchMembers_Members($API);
                            $Member = $Members->find($Session->get('memberID'));
                            $PerchMembers_Auth = new PerchMembers_Auth($API);
                            $PerchMembers_Auth->refresh_session_data($Member);
                        }
                        return true;
                    }
                }
            }
        }

        return false;
    }

    function perch_member_remove_tag($tag)
    {
        if ($tag) {
            $Session = PerchMembers_Session::fetch();

            if ($Session->logged_in) {
                if ($Session->has_tag($tag)) {
                    $API  = new PerchAPI(1.0, 'perch_members');
                    $Tags = new PerchMembers_Tags($API);
                    $Tag  = $Tags->find_by_tag($tag);
                    if (is_object($Tag)) {
                        $Tag->remove_from_member($Session->get('memberID'));
                        if (!headers_sent()) {
                            $Members = new PerchMembers_Members($API);
                            $Member = $Members->find($Session->get('memberID'));
                            $PerchMembers_Auth = new PerchMembers_Auth($API);
                            $PerchMembers_Auth->refresh_session_data($Member);
                        }
                        return true;
                    }
                }
            }
        }

        return false;
    }



   function perch_member_document($docID)
    {

                $Session = PerchMembers_Session::fetch();
                //print_r( $Session );
                if ($Session->logged_in) {

                    $API  = new PerchAPI(1.0, 'perch_members');
                    $Documents = new PerchMembers_Documents($API);
                    $Document  = $Documents->get_document($Session->get('memberID'),$docID);

                    return  $Document['documentName'];
                }
    }

       function perch_member_commissions()
        {       $Session = PerchMembers_Session::fetch();
                     //print_r( $Session );
                     if ($Session->logged_in) {

                             $API  = new PerchAPI(1.0, 'perch_members');
                             $Affiliate = new PerchMembers_Affiliate($API);
                         $commissions  = $Affiliate->getMemberCommissions($Session->get('affID'));
     if(PerchUtil::count($commissions)){
                    return  $commissions ;
                       }
        return null;
        }
        }

         function perch_member_requestPayout(){
         $Session = PerchMembers_Session::fetch();
                                      //print_r( $Session );
                                      if ($Session->logged_in) {

                                              $API  = new PerchAPI(1.0, 'perch_members');
                                              $Affiliate = new PerchMembers_Affiliate($API);
                                          $payout  = $Affiliate->requestPayout($Session->get('affID'));
                      if(PerchUtil::count($payout)){
                                     return  $payout ;
                                        }
                         return null;
                         }

         }
       function   perch_member_credit(){
         $Session = PerchMembers_Session::fetch();
                                    //print_r( $Session );
                                    if ($Session->logged_in) {

                                            $API  = new PerchAPI(1.0, 'perch_members');
                                            $Affiliate = new PerchMembers_Affiliate($API);
                                        $aff  = $Affiliate->getAffiliateDetails(0,$Session->get('affID'));
                    if(PerchUtil::count($aff)){
                                   return  $aff["credit"] ;
                                      }
                       return null;
                       }

       }
          function perch_member_aff_payouts()
                {       $Session = PerchMembers_Session::fetch();
                             //print_r( $Session );
                             if ($Session->logged_in) {

                                     $API  = new PerchAPI(1.0, 'perch_members');
                                     $Affiliate = new PerchMembers_Affiliate($API);
                                 $payouts  = $Affiliate->getMemberPayouts($Session->get('affID'));
             if(PerchUtil::count($payouts)){
                            return  $payouts ;
                               }
                return null;
                }
                }
   function perch_member_documents($memberID=false)
{
       $API  = new PerchAPI(1.0, 'perch_members');
      $Documents = new PerchMembers_Documents($API);
                if($memberID){
                     $Document  = $Documents->get_for_member($memberID);
                }else{
                  $Session = PerchMembers_Session::fetch();
                            //print_r( $Session );
                            if ($Session->logged_in) {


                                    $Document  = $Documents->get_for_member($Session->get('memberID'));
                 }
                }

                    if(PerchUtil::count($Document)){
                     $opts = array();
                     foreach($Document as $doc) {
                         $target_dir = "http://".$_SERVER['HTTP_HOST']."/documents/";
                         $opts[] = array('id'=>$doc->documentID(),'name'=>$doc->documentName(),'type'=>$doc->documentType(),'status'=>$doc->documentStatus(), 'url'=> $target_dir.$doc->documentName(),'uploadDate'=>$doc->documenUploadDate());
                      }
                      return  $opts;
                    }





return false;
}

function perch_member_add_notification($memberID, $title, $message)
{
    $API  = new PerchAPI(1.0, 'perch_members');
    $Notifications = new PerchMembers_Notifications($API);

    if (!$memberID) {
        $Session = PerchMembers_Session::fetch();
        if (!$Session->logged_in) return false;
        $memberID = $Session->get('memberID');
    }

    $data = [
        'memberID' => (int)$memberID,
        'notificationTitle' => $title,
        'notificationMessage' => $message,
        'notificationDate' => date('Y-m-d H:i:s'),
        'notificationRead' => 0
    ];

    $Notification = $Notifications->create($data);
    if ($Notification) {
        return $Notification->to_array();
    }

    return false;
}

   function perch_member_notifications($memberID=false)
    {
        $API  = new PerchAPI(1.0, 'perch_members');
        $Notifications = new PerchMembers_Notifications($API);
        $rows = [];
        if ($memberID) {
            $rows = $Notifications->get_for_member($memberID);
        } else {
            $Session = PerchMembers_Session::fetch();
            if ($Session->logged_in) {
                $rows = $Notifications->get_for_member($Session->get('memberID'));
            }
        }

        if (PerchUtil::count($rows)) {
            $out = array();
            foreach($rows as $n) {
                $out[] = array(
                    'id'=>$n->notificationID(),
                    'title'=>$n->notificationTitle(),
                    'message'=>$n->notificationMessage(),
                    'date'=>$n->notificationDate(),
                    'read'=>$n->notificationRead()
                );
            }
            return $out;
        }

        return false;
    }

    function perch_member_mark_notifications_read($memberID=false)
    {
        $API  = new PerchAPI(1.0, 'perch_members');
        $db   = $API->get('DB');

        if (!$memberID) {
            $Session = PerchMembers_Session::fetch();
            if (!$Session->logged_in) return false;
            $memberID = $Session->get('memberID');
        }

        $sql = 'UPDATE '.PERCH_DB_PREFIX.'members_notifications
                SET notificationRead=1
                WHERE memberID='.$db->pdb((int)$memberID).' AND notificationRead=0';

        $db->execute($sql);
        return true;
    }

function perch_member_form($template="registration.html", $return=false)
    {
        $API  = new PerchAPI(1.0, 'perch_members');
        $Template = $API->get('Template');
        $Template->set(PerchUtil::file_path('members/forms/'.$template), 'forms');

        $Session = PerchMembers_Session::fetch();

        $data = $Session->to_array();
           if (isset($_COOKIE['branch'])) {
               $branch = $_COOKIE['branch'];
           } else {

               $branch = 'iow';
           }

        PerchSystem::set_var('branch',$branch);
        $data["type"] = $branch;

        $html = $Template->render($data);

        $html = $Template->apply_runtime_post_processing($html, $data);

        if ($return) return $html;
        echo $html;
    }

    function perch_members_secure_download($file="", $bucket_name='default', $force_download=true)
    {

        $Perch = Perch::fetch();
        $bucket = $Perch->get_resource_bucket($bucket_name);

        if ($bucket) {

            $file_path = realpath(PerchUtil::file_path($bucket['file_path'].'/'.ltrim($file, '/')));

            $file_name = ltrim($file, '/');

            // check we're still within the bucket's folder, to secure against bad file paths
            if (substr($file_path, 0, strlen($bucket['file_path'])) == $bucket['file_path']) {

                if (file_exists($file_path)) {


                    // find file type
                    if (function_exists('finfo_file')) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimetype = finfo_file($finfo, $file_path);
                        finfo_close($finfo);
                    }else{
                        $mimetype = mime_content_type($file_path);
                    }

                    if (!$mimetype) $mimetype = 'application/octet-stream';

                    header("Content-Type: $mimetype", true);

                    if ($force_download) {
                        header("Content-Disposition: attachment; filename=\"".$file_name."\"", true);
                        header("Content-Length: ".filesize($file_path), true);
                        header("Content-Transfer-Encoding: binary\n", true);
                    }

                    if ($stream = fopen($file_path, 'rb')){
                        ob_end_flush();
                        while(!feof($stream) && connection_status() == 0){
                            print(fread($stream, 8192));
                            flush();
                        }
                        fclose($stream);
                    }

                    exit;
                }

            }

        }
    }

function perch_members_injection_logs_repository()
{
    static $repository = null;

    if ($repository === null) {
        $repository = new PerchMembers_InjectionLogsRepository();
    }

    return $repository;
}

function perch_members_injection_logs_count($memberID, $startDate = null, $endDate = null)
{
    return perch_members_injection_logs_repository()->countForMember($memberID, $startDate, $endDate);
}

function perch_members_injection_logs_page($memberID, $limit, $offset, $startDate = null, $endDate = null)
{
    return perch_members_injection_logs_repository()->fetchPageForMember($memberID, $limit, $offset, $startDate, $endDate);
}

function perch_members_injection_logs_find($memberID, $logID)
{
    return perch_members_injection_logs_repository()->findForMember($memberID, $logID);
}

function perch_members_injection_logs_create($memberID, array $data)
{
    return perch_members_injection_logs_repository()->createLog($memberID, $data);
}

function perch_members_injection_logs_delete($logID)
{
    return perch_members_injection_logs_repository()->deleteById($logID);
}

function perch_members_injection_logs_latest($memberID)
{
    return perch_members_injection_logs_repository()->fetchLatestForMember($memberID);
}

function perch_members_injection_logs_history($memberID)
{
    return perch_members_injection_logs_repository()->fetchChronologicalForMember($memberID);
}

function perch_members_weight_goals_repository()
{
    static $repository = null;

    if ($repository === null) {
        $repository = new PerchMembers_WeightGoalsRepository();
    }

    return $repository;
}

function perch_members_weight_goal_find($memberID)
{
    return perch_members_weight_goals_repository()->findForMember($memberID);
}

function perch_members_weight_goal_upsert($memberID, array $data)
{
    return perch_members_weight_goals_repository()->upsertGoal($memberID, $data);
}

/**
 * Process a single order for reorder reminder delivery.
 *
 * @param array<string,mixed>          $order
 * @param array<int,bool>              $notifiedCustomers
 * @param PerchDB_MySQL|PerchDB_MySQLi $DB
 */
function send_reorder_reminder(
    array $order,
    bool $dryRun,
    callable $appendLog,
    array &$notifiedCustomers,
    int &$sentCount,
    int &$skippedCount,
    $DB,
    string $ordersTable,
    PerchShop_Customers $Customers,
    PerchAPI $API,
    string $reorderURL,
    string $senderName,
    string $senderEmail
): void {
    $orderID = (int) $order['orderID'];
    $customerID = (int) $order['customerID'];

    $laterOrderSQL = 'SELECT orderID FROM ' . $ordersTable
        . ' WHERE customerID=' . $DB->pdb($customerID)
        . ' AND orderStatus=' . $DB->pdb('paid')
        . ' AND orderDeleted IS NULL'
        . ' AND orderCreated>' . $DB->pdb($order['orderCreated'])
        . ' ORDER BY orderCreated DESC LIMIT 1';

    $laterOrderID = $DB->get_value($laterOrderSQL);
    if ($laterOrderID) {
        echo 'Skipping order ' . $orderID . ' – customer has a later paid order #' . $laterOrderID . '.' . PHP_EOL;
        if (!$dryRun) {
            $appendLog($orderID, $customerID, 'skipped-later-order');
        }
        $skippedCount++;
        return;
    }

    $Customer = $Customers->find($customerID);
    if (!$Customer) {
        echo 'Skipping order ' . $orderID . ' – customer record not found.' . PHP_EOL;
        if (!$dryRun) {
            $appendLog($orderID, $customerID, 'skipped-missing-customer');
        }
        $skippedCount++;
        return;
    }

    $emailAddress = trim((string) $Customer->customerEmail());
    if ($emailAddress === '' || !PerchUtil::is_valid_email($emailAddress)) {
        echo 'Skipping order ' . $orderID . ' – customer has no valid email address.' . PHP_EOL;
        if (!$dryRun) {
            $appendLog($orderID, $customerID, 'skipped-missing-email');
        }
        $skippedCount++;
        return;
    }

    $firstName = trim((string) $Customer->customerFirstName());
    if ($firstName === '') {
        $firstName = 'there';
    }

    try {
        $orderDate = new DateTimeImmutable($order['orderCreated']);
    } catch (Exception $exception) {
        echo 'Skipping order ' . $orderID . ' – invalid order date (' . $exception->getMessage() . ').' . PHP_EOL;
        if (!$dryRun) {
            $appendLog($orderID, $customerID, 'skipped-invalid-date');
        }
        $skippedCount++;
        return;
    }

    $orderDateHuman = $orderDate->format('j F Y');

    $title = 'Time to reorder';
    $message = "It's been about three weeks since your order on {$orderDateHuman}. You can place your next order and pay online at {$reorderURL}.";

    $emailData = [
        'first_name'  => $firstName,
        'order_date'  => $orderDateHuman,
        'reorder_url' => $reorderURL,
        'sender_name' => $senderName,
    ];

    echo 'Preparing reminder for order ' . $orderID . ' (customer ' . $customerID . ').' . PHP_EOL;

    if ($dryRun) {
        $notifiedCustomers[$customerID] = true;
        return;
    }

    try {
        $Email = $API->get('Email');
        $Email->set_template('members/emails/reorder_reminder.html');
        $Email->set_bulk($emailData);
        $Email->subject('Time to reorder your medication');
        $Email->senderName($senderName);
        $Email->senderEmail($senderEmail);
        $Email->recipientEmail($emailAddress);

        $emailSent = $Email->send();
    } catch (Exception $exception) {
        echo 'Failed to send email for order ' . $orderID . ': ' . $exception->getMessage() . PHP_EOL;
        $appendLog($orderID, $customerID, 'error-email');
        $skippedCount++;
        return;
    }

    if (!$emailSent) {
        echo 'Failed to send email for order ' . $orderID . ' – send() returned false.' . PHP_EOL;
        $appendLog($orderID, $customerID, 'error-email');
        $skippedCount++;
        return;
    }

    $memberID = (int) $Customer->memberID();
    if ($memberID > 0) {
        try {
            perch_member_add_notification($memberID, $title, $message);
        } catch (Exception $exception) {
            echo 'Failed to create notification for order ' . $orderID . ': ' . $exception->getMessage() . PHP_EOL;
            $appendLog($orderID, $customerID, 'sent-notification-error');
            $notifiedCustomers[$customerID] = true;
            $sentCount++;
            return;
        }
    }

    $appendLog($orderID, $customerID, 'sent');
    $notifiedCustomers[$customerID] = true;
    $sentCount++;
}
