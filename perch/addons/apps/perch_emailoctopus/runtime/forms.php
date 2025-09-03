<?php

	function perch_emailoctopus_form_handler($SubmittedForm)
    { //echo "perch_emailoctopus_form_handler";
    //print_r($SubmittedForm->validate());
        //if ($SubmittedForm->validate()) {
            $API  = new PerchAPI(1.0, 'perch_emailoctopus');
            $Subscribers = new PerchEmailOctopus_Subscribers($API);
            $Subscribers->subscribe_from_form($SubmittedForm);
     //   }
        $Perch = Perch::fetch();
        PerchUtil::debug($Perch->get_form_errors($SubmittedForm->formID));

    }
    	function perch_emailoctopus_subscribe($data)
        {

                $API  = new PerchAPI(1.0, 'perch_emailoctopus');
              $Factory = new PerchEmailOctopus_Factory();
              $opts=array();
              $opts["url"]="/lists/xxxxx/contacts";
              $opts["data"]=$data;
              //print_r($opts);
              $result = $Factory->curl_api($opts);
            $Perch = Perch::fetch();
           // PerchUtil::debug($Perch->get_form_errors($SubmittedForm->formID));

        }

    	function perch_emailoctopus_update_contact($data)
        {

                $API  = new PerchAPI(1.0, 'perch_emailoctopus');
              $Factory = new PerchEmailOctopus_Factory();
              $opts=array();
        	$hash 		  = $Factory->subscriberHash($data['email']);
        	$listID="";
                $opts["url"]= "/lists/$listID/contacts/$hash";
               // echo $opts["url"];
               // unset($data['email']);
                 $opts["data"]= $data;
        		$update_sub = $Factory->curl_put_api($opts);

        }
    function perch_emailoctopus_form($template, $content=array(), $return=false)
    {
        $API      = new PerchAPI(1.0, 'perch_emailoctopus');
        $Template = $API->get('Template');
        $Template->set('emailoctopus/'.$template, 'emailoctopus');
        $html     = $Template->render($content);
        $html     = $Template->apply_runtime_post_processing($html, $content);

        if ($return) return $html;
        echo $html;
    }
