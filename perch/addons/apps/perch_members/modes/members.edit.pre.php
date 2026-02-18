<?php

require_once __DIR__ . '/../../api/routes/lib/comms_service.php';
require_once __DIR__ . '/../../api/routes/lib/comms_sync.php';

if (!function_exists('wl_member_note_extract_metadata')) {
    function wl_member_note_extract_metadata($noteText)
    {
        $noteText = trim((string) $noteText);

        $metadata = [
            'category' => 'admin_notes',
            'target_type' => 'patient_note',
            'thread_ref' => '',
            'order_id' => null,
            'red_flag' => false,
            'body' => $noteText,
        ];

        if ($noteText === '') {
            return $metadata;
        }

        if (preg_match('/^\[(Admin Notes|Clinical|Complaint)\]/i', $noteText, $matches)) {
            $label = strtolower(trim((string) $matches[1]));
            $metadata['category'] = str_replace(' ', '_', $label);
            $noteText = trim(substr($noteText, strlen($matches[0])));
        }

        if (preg_match('/^\[(Patient Notes|Order Notes)\]/i', $noteText, $matches)) {
            $label = strtolower(trim((string) $matches[1]));
            $metadata['target_type'] = $label === 'order notes' ? 'order_note' : 'patient_note';
            $noteText = trim(substr($noteText, strlen($matches[0])));
        }

        if (preg_match('/^\[Thread:([^\]]+)\]/i', $noteText, $matches)) {
            $metadata['thread_ref'] = trim((string) $matches[1]);
            $noteText = trim(substr($noteText, strlen($matches[0])));
        }

        if (preg_match('/^\[Order\s*#(\d+)\]/i', $noteText, $matches)) {
            $metadata['order_id'] = (int) $matches[1];
            $noteText = trim(substr($noteText, strlen($matches[0])));
        }

        $metadata['red_flag'] = $metadata['category'] !== 'admin_notes';
        $metadata['body'] = $noteText;

        return $metadata;
    }
}


if (!function_exists('wl_member_order_ids')) {
    function wl_member_order_ids($Orders, $Customer)
    {
        $ids = [];

        if (!is_object($Customer) || !is_object($Orders)) {
            return $ids;
        }

        $orders = $Orders->findAll_for_customer($Customer);
        if (!PerchUtil::count($orders)) {
            return $ids;
        }

        foreach ($orders as $Order) {
            $orderID = (int) $Order->orderID();
            if ($orderID > 0) {
                $ids[$orderID] = true;
            }
        }

        return $ids;
    }
}



if (!function_exists('wl_member_seen_comms_reply_keys')) {
    function wl_member_seen_comms_reply_keys($Member)
    {
        if (!is_object($Member)) {
            return [];
        }

        $properties = PerchUtil::json_safe_decode($Member->memberProperties(), true);
        if (!is_array($properties)) {
            return [];
        }

        $keys = isset($properties['seen_comms_reply_keys']) && is_array($properties['seen_comms_reply_keys'])
            ? $properties['seen_comms_reply_keys']
            : [];

        $normalized = [];
        foreach ($keys as $key) {
            $value = trim((string) $key);
            if ($value !== '') {
                $normalized[$value] = $value;
            }
        }

        return array_values($normalized);
    }
}

if (!function_exists('wl_member_mark_comms_reply_seen')) {
    function wl_member_mark_comms_reply_seen($Member, $replyKey)
    {
        if (!is_object($Member)) {
            return false;
        }

        $replyKey = trim((string) $replyKey);
        if ($replyKey === '') {
            return false;
        }

        $properties = PerchUtil::json_safe_decode($Member->memberProperties(), true);
        if (!is_array($properties)) {
            $properties = [];
        }

        $seenKeys = isset($properties['seen_comms_reply_keys']) && is_array($properties['seen_comms_reply_keys'])
            ? $properties['seen_comms_reply_keys']
            : [];

        $seenIndex = [];
        foreach ($seenKeys as $key) {
            $value = trim((string) $key);
            if ($value !== '') {
                $seenIndex[$value] = $value;
            }
        }

        $seenIndex[$replyKey] = $replyKey;
        $properties['seen_comms_reply_keys'] = array_values($seenIndex);

        return (bool) $Member->update([
            'memberProperties' => PerchUtil::json_safe_encode($properties),
        ]);
    }
}
if (!function_exists('wl_member_note_build_text')) {
    function wl_member_note_build_text($noteText, $category, $targetType, $threadRef = '', $orderId = null)
    {
        $labels = [
            'admin_notes' => 'Admin Notes',
            'clinical' => 'Clinical',
            'complaint' => 'Complaint',
        ];

        $typeLabels = [
            'patient_note' => 'Patient Notes',
            'order_note' => 'Order Notes',
        ];

        $parts = [];
        $parts[] = '[' . ($labels[$category] ?? 'Admin Notes') . ']';
        $parts[] = '[' . ($typeLabels[$targetType] ?? 'Patient Notes') . ']';

        if ($threadRef !== '') {
            $parts[] = '[Thread:' . trim($threadRef) . ']';
        }

        if ($targetType === 'order_note' && $orderId) {
            $parts[] = '[Order #' . (int) $orderId . ']';
        }

        $parts[] = trim((string) $noteText);

        return trim(implode(' ', array_filter($parts)));
    }
}
    
    $Members = new PerchMembers_Members($API);
    $message = false;
    
    $Tags = new PerchMembers_Tags($API);
    $Users = new PerchUsers();
    $Notes = new PerchMembers_Notes($API);
    $NotePharmacyStatuses = new PerchMembers_NotePharmacyStatuses($API);
    $Documents = new PerchMembers_Documents($API);
    $DocumentReminders = new PerchMembers_DocumentReminderService($API);
     $Notifications = new PerchMembers_Notifications($API);
    $Questionnaires = new PerchMembers_Questionnaires($API);
        $Orders   = new PerchShop_Orders($API);
        $Customers = new PerchShop_Customers($API);
        $Addresses = new PerchShop_Addresses($API);

    $documentReminderOptions = $DocumentReminders->get_options();
    $documentReminderStatus = 'all_approved';
    $documentReminderLastStatus = '';
    $documentReminderLastSentAt = null;

    $HTML = $API->get('HTML');

    $address_field_keys = [
        'address_1',
        'address_2',
        'city',
        'county',
        'postcode',
        'country',
        'phone',
        'instructions',
    ];

    $address_field_labels = [
        'address_1'    => 'Address line 1',
        'address_2'    => 'Address line 2',
        'city'         => 'City',
        'county'       => 'County/State',
        'postcode'     => 'Postcode',
        'country'      => 'Country',
        'phone'        => 'Phone',
        'instructions' => 'Delivery instructions',
    ];

    $shipping_address_details = array_fill_keys($address_field_keys, '');
    $is_customer = false;
    $customer_id = null;

    if (isset($_GET['id']) && $_GET['id']!='') {
        $memberID = (int) $_GET['id'];
        $Member = $Members->find($memberID);
       $Customer = $Customers->find_by_memberID($memberID);

        $details = $Member->to_array();

        $reminderData = $DocumentReminders->get_member_status_data($Member);
        $documentReminderStatus = $reminderData['status'];
        $documentReminderLastStatus = $reminderData['last_status'];
        $documentReminderLastSentAt = $reminderData['last_sent_at'];

        $heading1 = 'Editing a Member';

        if ($Customer instanceof PerchShop_Customer) {
            $is_customer = true;
            $customer_id = (int)$Customer->id();

            $ShippingAddress = $Addresses->find_for_customer($customer_id, 'shipping');
            if ($ShippingAddress instanceof PerchShop_Address) {
                foreach ($address_field_keys as $field_key) {
                    $value = $ShippingAddress->get($field_key);
                    if ($value !== false) {
                        $shipping_address_details[$field_key] = $value;
                    }
                }
            }
        }

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

        if (isset($post['document_reminder_status'])) {
            $document_reminder_selection = $DocumentReminders->sanitize_status($post['document_reminder_status']);
        } elseif (isset($post['document_reminder_status_choice']) && is_array($post['document_reminder_status_choice'])) {
            $selected_choice = reset($post['document_reminder_status_choice']);
            $document_reminder_selection = $DocumentReminders->sanitize_status($selected_choice);
        } else {
            $document_reminder_selection = 'all_approved';
        }
        $documentReminderStatus = $document_reminder_selection;

        if (isset($post['mark_comms_reply_seen']) && $post['mark_comms_reply_seen'] !== '') {
            if (!is_object($Member)) {
                $message = $HTML->failure_message('The reply could not be marked as seen because the member record could not be found.');
            } else {
                $replyKey = trim((string) $post['mark_comms_reply_seen']);
                if ($replyKey === '') {
                    $message = $HTML->failure_message('The reply could not be marked as seen because the reply key was empty.');
                } else {
                    $saved = wl_member_mark_comms_reply_seen($Member, $replyKey);
                    if ($saved) {
                        $message = $HTML->success_message('Reply marked as seen.');
                    } else {
                        $message = $HTML->failure_message('The reply could not be marked as seen.');
                    }
                }
            }

            if (is_object($Member)) {
                $details = $Member->to_array();
            }
        } elseif (isset($post['send_member_note_reply']) && $post['send_member_note_reply'] !== '') {
            if (!is_object($Member)) {
                $message = $HTML->failure_message('The reply could not be sent because the member record could not be found.');
            } else {
                $targetNoteID = trim((string) $post['send_member_note_reply']);
                $replyText = '';
                if (isset($post['note-reply']) && is_array($post['note-reply']) && isset($post['note-reply'][$targetNoteID])) {
                    $replyText = trim((string) $post['note-reply'][$targetNoteID]);
                }

                if ($targetNoteID === '') {
                    $message = $HTML->failure_message('The reply could not be sent because the note could not be identified.');
                } elseif ($replyText === '') {
                    $message = $HTML->failure_message('Please enter a reply before sending.');
                } else {
                    $replyAuthor = trim((string) $CurrentUser->username());
                    $replyPayload = [
                        'body' => $replyText,
                        'reply' => $replyText,
                        'created_by' => [
                            'name' => $replyAuthor !== '' ? $replyAuthor : 'Perch admin',
                            'role' => 'admin',
                        ],
                    ];

                    $replySent = comms_service_send_member_note_reply((int) $Member->id(), $targetNoteID, $replyPayload);

                    if ($replySent) {
                        $message = $HTML->success_message('Reply sent successfully.');
                    } else {
                        $message = $HTML->failure_message('The reply could not be sent.');
                    }
                }
            }

            if (is_object($Member)) {
                $details = $Member->to_array();
            }
        } elseif (isset($post['send_note_to_pharmacy']) && $post['send_note_to_pharmacy'] !== '') {
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
                    $existingPharmacyStatus = $NotePharmacyStatuses->find_one_by_member_and_note((int) $Member->id(), $noteID);
                    $alreadySentToPharmacy = ($existingPharmacyStatus instanceof PerchMembers_NotePharmacyStatus)
                        && strtolower((string) $existingPharmacyStatus->status()) === 'sent';

                    if ($alreadySentToPharmacy) {
                        $message = $HTML->success_message('This note has already been sent to the pharmacy.');
                    } elseif ($memberEmail === '') {
                        $message = $HTML->failure_message('The note could not be sent because the member does not have an email address.');
                    } else {
                        $noteTimestamp = $Note->noteDate();
                        $noteDateTime = $noteTimestamp ? strtotime((string) $noteTimestamp) : time();
                        $noteDateLabel = date('Y-m-d H:i:s', $noteDateTime);
                        $rawNoteText = trim((string) $Note->note_text());
                        $noteMeta = wl_member_note_extract_metadata($rawNoteText);
                        $noteText = trim((string) $noteMeta['body']);
                        $noteWithTimestamp = '['.$noteDateLabel.'] '.$noteText;

                        $addedBy = trim((string) $Note->addedBy());
                        $isClinicalCategory = in_array($noteMeta['category'], ['clinical', 'complaint'], true);
                        $should_escalate = $should_escalate || $isClinicalCategory;
                        $noteType = $should_escalate ? 'clinical_note' : 'admin_note';
                        $createdBy = [
                            'name' => $addedBy !== '' ? $addedBy : 'Perch admin',
                            'role' => $addedBy !== '' ? 'admin' : 'system',
                        ];

                        $notePayload = [
                            'note_id' => $noteID,
                            'note' => $noteWithTimestamp,
                            'note_raw' => $noteText,
                            'note_date' => $noteDateLabel,
                            'added_by' => $addedBy,
                            'member_email' => $memberEmail,
                            'escalate_clinical_review' => $should_escalate ? 1 : 0,
                            'note_type' => $noteType,
                            'note_category' => $noteMeta['category'],
                            'target_type' => $noteMeta['target_type'],
                            'thread_ref' => $noteMeta['thread_ref'],
                            'order_id' => $noteMeta['order_id'],
                            'body' => $noteText,
                            'created_by' => $createdBy,
                            'external_note_ref' => (string) $noteID,
                        ];

                        $targetType = trim((string) $noteMeta['target_type']);
                        $effectiveOrderId = isset($noteMeta['order_id']) ? (int) $noteMeta['order_id'] : 0;

                        if ($targetType === 'order_note') {
                            if ($effectiveOrderId <= 0) {
                                $message = $HTML->failure_message('Order notes require a valid order link before they can be sent.');
                                $sendSuccess = false;
                            } else {
                                comms_sync_order($effectiveOrderId, (int) $Member->id());

                                $orderNotePayload = [
                                    'note_type' => $noteType,
                                    'title' => null,
                                    'body' => $noteText,
                                    'status' => 'open',
                                    'created_by' => $createdBy,
                                    'external_note_ref' => (string) $noteID,
                                    'escalate_clinical_review' => $should_escalate ? 1 : 0,
                                    'note_category' => $noteMeta['category'],
                                    'target_type' => $targetType,
                                    'thread_ref' => $noteMeta['thread_ref'],
                                ];

                                $sendSuccess = comms_service_send_order_note($effectiveOrderId, $orderNotePayload);
                            }
                        } else {
                            $sendSuccess = comms_service_send_member_note((int) $Member->id(), $notePayload);
                        }

                        if ($sendSuccess) {
                            $message = $HTML->success_message('The note has been sent to the pharmacy.');

                            $statusValue = 'Sent';
                            $statusMessage = null;

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
                            $message = $HTML->failure_message('The note could not be sent to the pharmacy.');
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
                $DocumentReminders->update_member_status($Member, $document_reminder_selection);
                $statusData = $DocumentReminders->get_member_status_data($Member);
                $documentReminderStatus = $statusData['status'];
                $documentReminderLastStatus = $statusData['last_status'];
                $documentReminderLastSentAt = $statusData['last_sent_at'];
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

                        $DocumentReminders->update_member_status($Member, $document_reminder_selection);
                        $statusData = $DocumentReminders->get_member_status_data($Member);
                        $documentReminderStatus = $statusData['status'];
                        $documentReminderLastStatus = $statusData['last_status'];
                        $documentReminderLastSentAt = $statusData['last_sent_at'];

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
            if ($result && $is_customer && $Customer instanceof PerchShop_Customer) {
                $customer_id = (int)$Customer->id();

                $ShippingAddress = $Addresses->find_for_customer($customer_id, 'shipping');

                $shipping_post_data = [];

                foreach ($address_field_keys as $field_key) {
                    $shipping_key = 'shipping_' . $field_key;
                    if (array_key_exists($shipping_key, $post)) {
                        $shipping_post_data[$field_key] = trim((string)$post[$shipping_key]);
                    }
                }

                if (PerchUtil::count($shipping_post_data)) {
                    $shipping_post_data['customer'] = $customer_id;

                    $has_shipping_values = false;
                    foreach ($shipping_post_data as $key => $value) {
                        if ($key === 'customer') {
                            continue;
                        }

                        if ($value !== '') {
                            $has_shipping_values = true;
                            break;
                        }
                    }

                    if ($ShippingAddress instanceof PerchShop_Address) {
                        $ShippingAddress->update([
                            'addressDynamicFields' => PerchUtil::json_safe_encode($shipping_post_data),
                        ]);
                    } elseif ($has_shipping_values) {
                        $create_data = [
                            'customerID' => $customer_id,
                            'addressSlug' => 'shipping',
                            'addressTitle' => 'shipping',
                            'addressDynamicFields' => PerchUtil::json_safe_encode($shipping_post_data),
                        ];

                        if (isset($shipping_post_data['country']) && $shipping_post_data['country'] !== '' && is_numeric($shipping_post_data['country'])) {
                            $create_data['countryID'] = $shipping_post_data['country'];
                        }

                        $Addresses->create($create_data);
                    }
                }
            }

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
                                $noteCategory = isset($post['new-note-category']) ? trim((string) $post['new-note-category']) : 'admin_notes';
                                if (!in_array($noteCategory, ['admin_notes', 'clinical', 'complaint'], true)) {
                                    $noteCategory = 'admin_notes';
                                }

                                $noteTargetType = isset($post['new-note-target']) ? trim((string) $post['new-note-target']) : 'patient_note';
                                if (!in_array($noteTargetType, ['patient_note', 'order_note'], true)) {
                                    $noteTargetType = 'patient_note';
                                }

                                $noteThreadRef = isset($post['new-note-thread']) ? trim((string) $post['new-note-thread']) : '';
                                $noteOrderId = isset($post['new-note-order-id']) ? (int) $post['new-note-order-id'] : 0;
                                $validMemberOrderIds = wl_member_order_ids($Orders, $Customer);
                                if ($noteOrderId > 0 && !isset($validMemberOrderIds[$noteOrderId])) {
                                    $noteOrderId = 0;
                                }
                                if ($noteTargetType === 'order_note' && $noteOrderId <= 0) {
                                    $noteTargetType = 'patient_note';
                                }
                                $isRedFlag = isset($post['new-note-red-flag']) && $post['new-note-red-flag'] === '1';
                                if ($noteCategory === 'clinical' || $noteCategory === 'complaint') {
                                    $isRedFlag = true;
                                }

                                $structuredNote = wl_member_note_build_text($post['new-note'], $noteCategory, $noteTargetType, $noteThreadRef, $noteOrderId);
                                $noteset = $Notes->parse_string($structuredNote);
                                 $User = $Users->find($CurrentUser->id());

                                if (PerchUtil::count($noteset)) {
                                    foreach ($noteset as $note) {
                                        $Note = $Notes->find_or_create($note['note']);

                                        if (!$Note instanceof PerchMembers_Note) {
                                            continue;
                                        }

                                        $Note->add_to_member($Member->id(), $User->userUsername());

                                        $noteTimestamp = $Note->noteDate();
                                        $noteDateTime = $noteTimestamp ? strtotime((string) $noteTimestamp) : time();
                                        $noteDateLabel = date('Y-m-d H:i:s', $noteDateTime);
                                        $rawNoteText = trim((string) $Note->note_text());
                                        $noteMeta = wl_member_note_extract_metadata($rawNoteText);
                                        $noteBody = trim((string) $noteMeta['body']);
                                        $effectiveCategory = trim((string) $noteMeta['category']);
                                        $effectiveTargetType = trim((string) $noteMeta['target_type']);
                                        $effectiveThreadRef = trim((string) $noteMeta['thread_ref']);
                                        $effectiveOrderId = isset($noteMeta['order_id']) ? (int) $noteMeta['order_id'] : 0;
                                        $addedBy = trim((string) $User->userUsername());
                                        $noteType = ($effectiveCategory === 'clinical' || $effectiveCategory === 'complaint') ? 'clinical_note' : 'admin_note';
                                        $createdBy = [
                                            'name' => $addedBy !== '' ? $addedBy : 'Perch admin',
                                            'role' => $addedBy !== '' ? 'admin' : 'system',
                                        ];

                                        if ($effectiveTargetType === 'order_note' && $effectiveOrderId > 0 && $noteBody !== '' && is_object($Member)) {
                                            comms_sync_order($effectiveOrderId, (int) $Member->id());

                                            $orderNotePayload = [
                                                'note_type' => $noteType,
                                                'title' => null,
                                                'body' => $noteBody,
                                                'status' => 'open',
                                                'created_by' => $createdBy,
                                                'external_note_ref' => (string) $Note->id(),
                                            ];

                                            comms_service_send_order_note($effectiveOrderId, $orderNotePayload);
                                        }

                                        if ($isRedFlag && $effectiveTargetType === 'patient_note' && is_object($Member)) {
                                            $existingPharmacyStatus = $NotePharmacyStatuses->find_one_by_member_and_note((int) $Member->id(), (int) $Note->id());
                                            $alreadySentToPharmacy = ($existingPharmacyStatus instanceof PerchMembers_NotePharmacyStatus)
                                                && strtolower((string) $existingPharmacyStatus->status()) === 'sent';

                                            if ($alreadySentToPharmacy) {
                                                continue;
                                            }

                                            $memberEmail = trim((string)$Member->memberEmail());
                                            $notePayload = [
                                                'note_id' => (int) $Note->id(),
                                                'note' => '['.$noteDateLabel.'] '.$noteBody,
                                                'note_raw' => $noteBody,
                                                'note_date' => $noteDateLabel,
                                                'added_by' => $addedBy,
                                                'member_email' => $memberEmail,
                                                'escalate_clinical_review' => 1,
                                                'note_type' => 'clinical_note',
                                                'note_category' => $effectiveCategory,
                                                'target_type' => $effectiveTargetType,
                                                'thread_ref' => $effectiveThreadRef,
                                                'order_id' => $effectiveOrderId > 0 ? $effectiveOrderId : null,
                                                'body' => $noteBody,
                                                'created_by' => $createdBy,
                                                'external_note_ref' => (string) $Note->id(),
                                            ];

                                            $sentToComms = comms_service_send_member_note((int) $Member->id(), $notePayload);

                                            if ($sentToComms) {
                                                $NotePharmacyStatuses->record_sent_status((int) $Member->id(), (int) $Note->id(), 'Sent', 'Escalated for clinical review');
                                            }
                                        }
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

                $reminderData = $DocumentReminders->get_member_status_data($Member);
                $documentReminderStatus = $reminderData['status'];
                $documentReminderLastStatus = $reminderData['last_status'];
                $documentReminderLastSentAt = $reminderData['last_sent_at'];
            }else{
                $details = array();
            }

        }

    }
    
    if (isset($_GET['created']) && !$message) {
        $message = $HTML->success_message('The member has been successfully created. Return to %smember listing%s', '<a href="'.$API->app_path() .'">', '</a>');
    }

    if ($is_customer && $Customer instanceof PerchShop_Customer) {
        $customer_id = (int)$Customer->id();

        $shipping_address_details = array_fill_keys($address_field_keys, '');

        $ShippingAddress = $Addresses->find_for_customer($customer_id, 'shipping');
        if ($ShippingAddress instanceof PerchShop_Address) {
            foreach ($address_field_keys as $field_key) {
                $value = $ShippingAddress->get($field_key);
                if ($value !== false) {
                    $shipping_address_details[$field_key] = $value;
                }
            }
        }
    }

    if (is_object($Member)) {
        $tags = $Tags->get_for_member($Member->id());
          $notes = $Notes->get_for_member($Member->id());
        $comms_member_notes = comms_service_get_member_notes((int) $Member->id());
        $seen_comms_reply_keys = wl_member_seen_comms_reply_keys($Member);
        $documents = $Documents->get_for_member($Member->id());
          $questionnaire =  $Questionnaires->get_for_member($Member->id());
                  $notifications = $Notifications->get_for_member($Member->id());

  $questionnaire_reorder =  $Questionnaires->get_for_member($Member->id(),"re-order");
    }else{
        $tags = false;
         $notes = false;
        $comms_member_notes = [];
        $seen_comms_reply_keys = [];
        $documents = false;
        $questionnaire =false;
         $notifications = false;
    }
