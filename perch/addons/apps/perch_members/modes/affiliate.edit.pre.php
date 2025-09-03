<?php
    
    $Members = new PerchMembers_Members($API);
    $message = false;


    $HTML = $API->get('HTML');

    if (isset($_GET['affID']) && $_GET['affID']!='') {
        $affID = $_GET['affID'];

 $details=$Affiliate->getAffiliateDetails($affID);

    
        $heading1 = 'Editing Affiliate';

    }else{

        $affID = false;
        $details = array();

        $heading1 = 'Adding a Affiliate';
    }

    $heading2 = 'Affiliate details';
    
    print_r( $details);

    $Form = $API->get('Form');




/*
    if ($Form->submitted()) {
   	        
        $post = $_POST;

        $postvars = array('memberEmail', 'memberStatus');
		
    	//$data = $Form->receive($postvars);

        $data = $Form->get_posted_content($Template, $Members, $Member, false);

        // PerchUtil::debug($data);

    	$result = false;


    	if (is_object($Member)) {
    	    $Member->update($data);
            $result = true;
    	}else{

            $data['memberCreated'] = date('Y-m-d H:i:s');




            if (!$Members->check_email($data['memberEmail'])) {
                $message = $HTML->failure_message('A member with that email address already exists.');
            }else{

                //$data['memberProperties'] = '';

                $Member = $Members->create($data);
                if ($Member) {

                    $member = array(
                        'memberAuthID'=>$Member->id()
                    );

                    $Member->update($member);


                    $result = true;
                    PerchUtil::redirect($API->app_path() .'/edit/?id='.$Member->id().'&created=1');
                }else{
                    $message = $HTML->failure_message('Sorry, that member could not be updated.');
                }
            }
    	    
    	}


        if ($result) {

           $message = $HTML->success_message('The member has been successfully updated. Return to %smember listing%s', '<a href="'.$API->app_path() .'">', '</a>');
        }else{
            if (!$message) $message = $HTML->failure_message('Sorry, that member could not be updated, or no changes were made.');
        }
        
        if (is_object($Member)) {
            $details = $Member->to_array();
        }else{
            $details = array();
        }
        
    }*/
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('The member has been successfully created. Return to %smember listing%s', '<a href="'.$API->app_path() .'">', '</a>'); 
    }

