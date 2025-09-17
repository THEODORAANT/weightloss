<?php

class PerchMembers_Member extends PerchAPI_Base
{
    protected $table  = 'members';
    protected $pk     = 'memberID';

    public $static_fields = array('memberID', 'memberAuthType', 'memberAuthID', 'memberEmail', 'memberPassword', 'memberStatus', 'memberCreated', 'memberExpires', 'memberProperties', );
    public $field_aliases = array(
			'email'   => 'memberEmail',
			'status'  => 'memberStatus',
			'expires' => 'memberExpires',
			'auth_id' => 'memberAuthID',
			'id'      => 'memberID',	
    	);


    public function delete()
    {
        $this->db->execute('DELETE FROM '.PERCH_DB_PREFIX.'members_member_tags WHERE memberID='.$this->id());

        return parent::delete();
    }

    public function update_profile($SubmittedForm,$api=false)
    {
    if($api){
        	$data = $SubmittedForm;

    }else{
        	$data = $SubmittedForm->data;

    }

    	$out = array();
    	$properties = PerchUtil::json_safe_decode($this->memberProperties(), true);

    	foreach($data as $key=>$val) {

    		if (array_key_exists($key, $this->field_aliases)) {
    			$out[$this->field_aliases[$key]] = $val;
    			$key = $this->field_aliases[$key];
    		}

    		if (!in_array($key, $this->static_fields)) {
    			$properties[$key] = $val;
    		}

    	}

    	if (isset($out['memberEmail'])) {
    		if (!$this->check_email_unique($out['memberEmail'])) {
    			unset($out['memberEmail']);
    		}
		}

		if (isset($data['affID'])) {
		   $API  = new PerchAPI(1.0, 'perch_members');

		  $Affiliate = new PerchMembers_Affiliate($API);
                                   // $Affiliate->addCommission($memberid, $amount);
                  $Affiliate->registerAffiliate($this->id(),$data['affID']);

		}

    	$out['memberProperties'] = PerchUtil::json_safe_encode($properties);

    	$this->update($out);

    }

    public function add_tag($tag, $tagDisplay=false, $tagExpiry=false)
    {
        $Tags = new PerchMembers_Tags;

        $Tag = $Tags->find_or_create($tag, $tagDisplay);

        $data = array();
        $data['memberID'] = $this->id();
        $data['tagID'] = $Tag->id();
        
        if ($tagExpiry) {
            $data['tagExpires'] = date('Y-m-d H:i:s', strtotime($tagExpiry));
        }

        $sql = 'DELETE FROM '.PERCH_DB_PREFIX.'members_member_tags WHERE memberID='.$this->db->pdb($this->id()).' AND tagID='.$this->db->pdb($Tag->id());
        $this->db->execute($sql);

        $this->db->insert(PERCH_DB_PREFIX.'members_member_tags', $data);
    }


    public function reset_password($send_notification_email=true)
    {
        $clear_pwd = $this->_generate_password();
        $data = array();

        $Hasher = PerchUtil::get_password_hasher();
        $data['memberPassword'] = $Hasher->HashPassword($clear_pwd);

        $this->update($data);

        if ($send_notification_email) $this->_email_new_password($clear_pwd);

        return true;

    }
    	public function upload_member_file($SubmittedForm,$memberID=false){

    	 $data = $SubmittedForm->files;
    	 $fielddata=$SubmittedForm->data;

    	       $API  = new PerchAPI(1.0, 'perch_members');
                    $Session = PerchMembers_Session::fetch();
                     if (!$memberID && $Session->logged_in) {
                    $memberID=$Session->get('memberID');
                    }

    	$Documents = new PerchMembers_Documents();
    	  if (isset($data['image'])){
    	        $Document = $Documents->upload($data['image'], $memberID,$fielddata['documentType']);

    	  }
    	    if (isset($data['video'])){

      $Document = $Documents->upload($data['video'], $memberID,$fielddata['documentType']);
      }
       //  $data["email"]= "support@getweightloss.co.uk";//"reshat1987@gmail.com";
       //  $data["ReviewLink"]="https://".$_SERVER['HTTP_HOST']."/perch/addons/apps/perch_members/edit/?id=".$Session->get('memberID');

    //  perch_emailoctopus_update_contact($data);
      $this->sendtoadmin_docs_email( $memberID,"george@nlclinicisleofwight.co.uk");
       $this->sendtoadmin_docs_email( $memberID,"reshat1987@gmail.com");
    	}

    public function change_password($SubmittedForm)
    {
        $data = $SubmittedForm->data;
        if (isset($data['old_password']) && isset($data['password'])) {
            $old_clear_pwd = $data['old_password'];    
            $new_clear_pwd = $data['password'];

            // check existing password
            $API  = new PerchAPI(1.0, 'perch_members');
            $Session = PerchMembers_Session::fetch();
            $PerchMembers_Auth = new PerchMembers_Auth($API);

            $new_password = $PerchMembers_Auth->encrypt_new_password($Session->get('memberID'), $old_clear_pwd, $new_clear_pwd);

            if ($new_password) {
                $this->update(array('memberPassword'=>$new_password));
                return true;
            }else{
                $SubmittedForm->throw_error('valid', 'old_password');
                return false;
            }
        }
        return false;
    }

    public function to_array()
    {
        $details = $this->details;

        if (isset($details['memberProperties']) && $details['memberProperties']!='') {
            $properties = PerchUtil::json_safe_decode($details['memberProperties'], true);

            $details = array_merge($properties, $details);

            unset($details['memberProperties']);
        }

        return $details;
    }


    public function send_welcome_email()
    {
        if ($this->memberStatus()!='active') return false;

        $API = new PerchAPI(1.0, 'perch_members');

        $Settings = $API->get('Settings');
        $login_page = str_replace('{returnURL}', '', $Settings->get('perch_members_login_page')->val());

        $Email = $API->get('Email');
        $Email->set_template('members/emails/welcome.html');
        $Email->set_bulk($this->to_array());
        $Email->set('login_page', $login_page);
        $Email->senderName(PERCH_EMAIL_FROM_NAME);
        $Email->senderEmail(PERCH_EMAIL_FROM);
        $Email->recipientEmail($this->memberEmail());
        $Email->send();

        return true;
    }


    protected function check_email_unique($email)
    {
    	$sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE memberEmail='.$this->db->pdb($email).' AND memberID!='.$this->id();
    	$count = $this->db->get_count($sql);

    	if ($count===0) {
    		return true;
    	}

    	return false;
    }


public function sendtoadmin_docs_email($memberID,$adminemail)
            { //echo "send_booking_email"; print_r($booking);
             //   if ($this->memberStatus()!='active') return false;

                $API = new PerchAPI(1.0, 'perch_members');
       // $login_page = "https://getweightloss.co.uk/perch/";
         $login_page ="https://".$_SERVER['HTTP_HOST']."/perch/addons/apps/perch_members/edit/?id=".$memberID;


                $Email = $API->get('Email');

                 $Email->set_template('members/emails/new_docupload_notificationAdmin.html');
                $Email->set_bulk($this->to_array());
               $Email->set('login_page', $login_page);

                $Email->senderName(PERCH_EMAIL_FROM_NAME);
                $Email->senderEmail(PERCH_EMAIL_FROM);
               $Email->recipientEmail($adminemail);

                $Email->send();


                return true;
            }


    protected function _email_new_password($clear_pwd)
    {
        $API = new PerchAPI(1.0, 'perch_members');

        $Settings = $API->get('Settings');
        $login_page = str_replace('{returnURL}', '', $Settings->get('perch_members_login_page')->val());
         $data["ResetPassword"]= $clear_pwd;
         $data["email"]= $this->memberEmail();
             	$properties = PerchUtil::json_safe_decode($this->memberProperties(), true);

         $data["FirstName"]=$properties["first_name"];
      //  perch_emailoctopus_update_contact($data);
        $Email = $API->get('Email');
        $Email->set_template('members/emails/reset_password.html');
        $Email->set_bulk($this->to_array());
        $Email->set('password', $clear_pwd);
        $Email->set('login_page', $login_page);
        $Email->senderName(PERCH_EMAIL_FROM_NAME);
        $Email->senderEmail(PERCH_EMAIL_FROM);
        $Email->recipientEmail($this->memberEmail());
        $Email->send();
    }

    protected function _generate_password($length=8)
    {
        $vals = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $pwd = '';
        for ($i=0; $i<$length; $i++) {
            $pwd .= $vals[rand(0, strlen($vals)-1)];
        }
        return $pwd;
    }

}
