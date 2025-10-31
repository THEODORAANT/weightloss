<?php
    
    $Members = new PerchMembers_Members($API);
    $message = false;
    
    $Tags = new PerchMembers_Tags($API);
    $Notes = new PerchMembers_Notes($API);
    $NotePharmacyStatuses = new PerchMembers_NotePharmacyStatuses($API);
    $Documents = new PerchMembers_Documents($API);
     $Notifications = new PerchMembers_Notifications($API);
    $Questionnaires = new PerchMembers_Questionnaires($API);
	$Orders   = new PerchShop_Orders($API);
	$Customers = new PerchShop_Customers($API);

    $HTML = $API->get('HTML');

    if (isset($_GET['id']) && $_GET['id']!='') {
        $memberID = (int) $_GET['id'];    
        $Member = $Members->find($memberID);
       $Customer = $Customers->find_by_memberID($memberID);

        $details = $Member->to_array();
    
        $heading1 = 'Editing a Member';

    }else{
        $Member = false;
        $memberID = false;
        $details = array();
 $Customer = false;
        $heading1 = 'Adding a Member';
    }

    $heading2 = 'Member details';
    
    //print_r( $details);
    $Template   = $API->get('Template');
    $Template->set('members/member.html', 'members', $Members->default_fields);

    $Form = $API->get('Form');

    $Form->handle_empty_block_generation($Template);
    $Form->set_required_fields_from_template($Template, $details);


    if (!is_object($Member)) {
        $Form->require_field('memberPassword', 'Required');
    }

    if ($Form->submitted()) {

        $post = $_POST;

        if (isset($post['send_note_to_pharmacy']) && $post['send_note_to_pharmacy'] !== '') {
            if (!is_object($Member)) {
                $message = $HTML->failure_message('The note could not be sent because the member record could not be found.');
            } else {
                $noteID = (int)$post['send_note_to_pharmacy'];
                $should_escalate = false;
                if (isset($post['note_escalate']) && is_array($post['note_escalate']) && isset($post['note_escalate'][$noteID])) {
                    $should_escalate = $post['note_escalate'][$noteID] === '1';
                }
                $Note = $Notes->find($noteID);

                if (!$Note) {
                    $message = $HTML->failure_message('The selected note could not be found.');
                } else {
                    $memberEmail = trim((string)$Member->memberEmail());

                    if ($memberEmail === '') {
                        $message = $HTML->failure_message('The note could not be sent because the member does not have an email address.');
                    } else {
                        $pharmacy_api = new PerchMembers_PharmacyApiClient('https://api.myprivatechemist.com/api', '4a1f7a59-9d24-4e38-a3ff-9f8be74c916b');
                        $apiResponse = $pharmacy_api->sendCustomerNote($memberEmail, (string) $Note->note_text(), false);

                        if ($apiResponse['success']) {
                            $message = $HTML->success_message('The note has been sent to the pharmacy.');

                            $statusValue = 'Sent';
                            if (isset($apiResponse['data']) && is_array($apiResponse['data']) && isset($apiResponse['data']['status']) && $apiResponse['data']['status'] !== '') {
                                $statusValue = (string) $apiResponse['data']['status'];
                            }

                            $statusMessage = null;
                            if (isset($apiResponse['data']) && is_array($apiResponse['data']) && isset($apiResponse['data']['message']) && $apiResponse['data']['message'] !== '') {
                                $statusMessage = (string) $apiResponse['data']['message'];
                            }

                            if ($should_escalate) {
                                $properties = PerchUtil::json_safe_decode($Member->memberProperties(), true);
                                if (!is_array($properties)) {
                                    $properties = [];
                                }

                                $memberFirstName = isset($properties['first_name']) ? trim((string) $properties['first_name']) : '';
                                $memberLastName  = isset($properties['last_name']) ? trim((string) $properties['last_name']) : '';
                                $noteAddedBy     = trim((string) $Note->addedBy());
                                $noteDateValue   = $Note->noteDate();
                                $noteDate        = $noteDateValue ? date('d M Y H:i', strtotime($noteDateValue)) : date('d M Y H:i');
                                $memberLink      = PerchUtil::url_to_ssl_if_needed('http://'.$_SERVER['HTTP_HOST'].'/perch/addons/apps/perch_members/edit/?id='.(int) $Member->id());

                                $emailData = [
                                    'first_name'         => $memberFirstName,
                                    'last_name'          => $memberLastName,
                                    'memberEmail'        => $memberEmail,
                                    'note'               => (string) $Note->note_text(),
                                    'note_added_by'      => $noteAddedBy,
                                    'note_date'          => $noteDate,
                                    'note_id'            => $noteID,
                                    'member_admin_link'  => $memberLink,
                                ];

                                $EscalationEmail = $API->get('Email');
                                $EscalationEmail->set_template('members/emails/note_escalation_notification.html');
                                $EscalationEmail->set_bulk($emailData);
                                $EscalationEmail->subject('Clinical review escalation - '.$memberEmail);
                                $EscalationEmail->senderName(PERCH_EMAIL_FROM_NAME);
                                $EscalationEmail->senderEmail(PERCH_EMAIL_FROM);
                                $EscalationEmail->recipientEmail('management@thestgroup.co.uk');
                                $EscalationEmail->ccToEmail('support@getweightloss.co.uk');

                                if (!$EscalationEmail->send()) {
                                    PerchUtil::debug('Failed to send clinical escalation email for note '.$noteID.': '.$EscalationEmail->errors, 'error');
                                }

                                $escalationMessage = 'Escalated for clinical review';
                                if ($statusMessage !== null && $statusMessage !== '') {
                                    $statusMessage .= ' '.$escalationMessage;
                                } else {
                                    $statusMessage = $escalationMessage;
                                }
                            }

                            $NotePharmacyStatuses->record_sent_status((int) $Member->id(), (int) $Note->id(), $statusValue, $statusMessage);
                        } else {
                            $errorMessage = 'The note could not be sent to the pharmacy.';

                            if (isset($apiResponse['message']) && $apiResponse['message']) {
                                $errorMessage .= ' '.$apiResponse['message'];
                            } elseif (isset($apiResponse['data']) && is_array($apiResponse['data']) && isset($apiResponse['data']['message'])) {
                                $errorMessage .= ' '.$apiResponse['data']['message'];
                            }

                            $message = $HTML->failure_message($errorMessage);

                            if (isset($apiResponse['data']) && !is_string($apiResponse['data'])) {
                                PerchUtil::debug($apiResponse['data'], 'error');
                            }
                        }
                    }
                }
            }

            if (is_object($Member)) {
                $details = $Member->to_array();
            }
        } else {
            $existing_tagIDs = $Form->find_items('tag-', true);

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

                // Password
                if (isset($post['memberPassword']) && $post['memberPassword']!='') {
                    $clear_pwd = trim($post['memberPassword']);
                    $Hasher = PerchUtil::get_password_hasher();
                    $data['memberPassword'] = $Hasher->HashPassword($clear_pwd);
                }


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

                        if (isset($post['send_email']) && $post['send_email']=='1') {
                            $Member->send_welcome_email();
                        }

                        $result = true;
                        PerchUtil::redirect($API->app_path() .'/edit/?id='.$Member->id().'&created=1');
                    }else{
                        $message = $HTML->failure_message('Sorry, that member could not be updated.');
                    }
                }

            }


            // Tags
            if ($result) {
                if (is_object($Member) && isset($post['questionnaire_bmi']) && is_array($post['questionnaire_bmi'])) {
                    foreach ($post['questionnaire_bmi'] as $questionnaireID => $bmiValue) {
                        $questionnaireID = (int) $questionnaireID;
                        if ($questionnaireID <= 0) {
                            continue;
                        }

                        $bmiValue = trim((string) $bmiValue);

                        $QuestionnaireEntry = $Questionnaires->find($questionnaireID);
                        if (!$QuestionnaireEntry) {
                            continue;
                        }

                        if ((int) $QuestionnaireEntry->member_id() !== (int) $Member->id()) {
                            continue;
                        }

                        $entryDetails = $QuestionnaireEntry->to_array();

                        $currentValue = trim((string) $QuestionnaireEntry->answer_text());
                        if ($currentValue === '' && is_array($entryDetails) && isset($entryDetails['answer'])) {
                            $currentValue = trim((string) $entryDetails['answer']);
                        }

                        $newValue = $bmiValue;

                        if ($currentValue !== '' && strpos($currentValue, ',') !== false && strpos($newValue, ',') === false) {
                            $suffix = trim((string) substr($currentValue, strpos($currentValue, ',') + 1));
                            if ($suffix !== '') {
                                $newValue .= ', '.$suffix;
                            }
                        }

                        if ($currentValue === $newValue) {
                            continue;
                        }

                        $updateData = [
                            'answer_text' => $newValue,
                        ];

                        if (is_array($entryDetails) && array_key_exists('answer', $entryDetails)) {
                            $updateData['answer'] = $newValue;
                        }

                        $QuestionnaireEntry->update($updateData);
                    }
                }

                // existing tags
                $Tags->remove_from_member($Member->id(), $existing_tagIDs);

                // new tag
                if (isset($post['new-tag']) && $post['new-tag']!='') {
                    $tagset = $Tags->parse_string($post['new-tag']);
                    if (PerchUtil::count($tagset)) {

                        if (isset($post['new-expire']) && $post['new-expire']!='') {
                            $tag_expiry = $Form->get_date('new-expires', $post);
                            if (!$tag_expiry) $tag_expiry=false;
                        }else{
                            $tag_expiry = false;
                        }

                        foreach($tagset as $tag) {
                            $Tag = $Tags->find_or_create($tag['tag'], $tag['tagDisplay']);
                            $Tag->add_to_member($Member->id(), $tag_expiry);
                        }
                    }
                }

                 // new note
               if (isset($post['new-note']) && $post['new-note']!='') {
                                $noteset = $Notes->parse_string($post['new-note']);
                                 $User = $Users->find($CurrentUser->id());

                                if (PerchUtil::count($noteset)) {
                                    foreach ($noteset as $note) {
                                        $Note = $Notes->find_or_create($note['note']);

                                        if (!$Note instanceof PerchMembers_Note) {
                                            continue;
                                        }

                                        $Note->add_to_member($Member->id(), $User->userUsername());
                                    }
                                }
                 }
                  // new notification
                             if (isset($post['new-notification-title']) && $post['new-notification-title']!='' && isset($post['new-notification-message']) && $post['new-notification-message']!='') {
                                 $data = [
                                     'memberID' => $Member->id(),
                                     'notificationTitle' => $post['new-notification-title'],
                                     'notificationMessage' => $post['new-notification-message'],
                                     'notificationDate' => date('Y-m-d H:i:s'),
                                     'notificationRead' => 0
                                 ];
                                 $Notifications->create($data);
                             }
                // echo "document";
                // print_r($_FILES);
              if (isset($_FILES['new-document']) && $_FILES['new-document']!='' &&  $_FILES['new-document']['size']!=0) {

                    $Document = $Documents->upload($_FILES['new-document'],$Member->id());

                }

                if (isset($post['send_email']) && $post['send_email']=='1') {
                    $Member->send_welcome_email();
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

        }

    }
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('The member has been successfully created. Return to %smember listing%s', '<a href="'.$API->app_path() .'">', '</a>'); 
    }

    if (is_object($Member)) {
        $tags = $Tags->get_for_member($Member->id());
          $notes = $Notes->get_for_member($Member->id());
        $documents = $Documents->get_for_member($Member->id());
          $questionnaire =  $Questionnaires->get_for_member($Member->id());
                  $notifications = $Notifications->get_for_member($Member->id());

  $questionnaire_reorder =  $Questionnaires->get_for_member($Member->id(),"re-order");
    }else{
        $tags = false;
         $notes = false;
        $documents = false;
        $questionnaire =false;
         $notifications = false;
    }
