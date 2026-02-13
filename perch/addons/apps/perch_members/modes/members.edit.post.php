<?php

    $chat_button = null;
    if (is_object($Member)) {
        $chat_button = [
            'text' => $Lang->get('Open chat'),
            'link' => $API->app_path() . '/chat/thread.php?member_id=' . (int)$Member->id(),
            'icon' => 'ext/o-chat',
        ];
    }

    echo $HTML->title_panel([
        'heading' => $Lang->get($heading1),
        'button' => $chat_button,
    ], $CurrentUser);

    if ($message) echo $message;    
        
    echo $Form->form_start(false);
        echo $HTML->heading2('Member details');
        //echo $Form->text_field('memberEmail', 'Email', isset($details['memberEmail'])?$details['memberEmail']:false, 'l');


        echo $Form->fields_from_template($Template, $details, array(), false);


        if (!is_object($Member)) {
            echo $Form->text_field('memberPassword', 'Password', isset($details['memberPassword'])?$details['memberPassword']:false, 'l');
        }

        /*
        echo $Form->select_field('memberStatus', 'Status', array(
                array('label'=>$Lang->get('Pending'), 'value'=>'pending'),
                array('label'=>$Lang->get('Active'), 'value'=>'active'),
                array('label'=>$Lang->get('Inactive'), 'value'=>'inactive'),

            ), isset($details['memberStatus'])?$details['memberStatus']:'active');
    */

        $state = '0';
        if (isset($details['memberStatus']) && $details['memberStatus']=='pending') {
            $state = '1';
        }

        echo $Form->hint('Will only be sent to active members');
        echo $Form->checkbox_field('send_email', 'Send welcome email', '1', $state);


        if (is_object($Member)) {


        echo $HTML->heading2('Tags');
?>
    <div class="form-inner">
        <table class="tags">
            <thead>
                <tr>
                    <th class="action">Enabled</th>
                    <th>Tag</th>
                    <th>Expires</th>
                </tr>
            </thead>
            <tbody>
        <?php
            if (PerchUtil::count($tags)) {
                foreach($tags as $Tag) {
                    echo '<tr>';
                        echo '<td class="action">'.$Form->checkbox('tag-'.$Tag->id(), '1', '1').'</td>';
                        echo '<td>'.PerchUtil::html($Tag->tag()).'</td>';
                        echo '<td>'.PerchUtil::html($Tag->tagExpires() ? date('d M Y', strtotime($Tag->tagExpires())) : '-').'</td>';
                    echo '</tr>';
                }
            }

            echo '<tr>';
                echo '<td class="action">'.$Form->label('new-tag', PerchLang::get('New')).'</td>';
                echo '<td>'.$Form->text('new-tag', false).'</td>';
                echo '<td>'.$Form->checkbox('new-expire', '1', '0').' '.$Form->datepicker('new-expires', false).'</td>';
            echo '</tr>';

        ?>

            </tbody>
        </table>
    </div>

<?php

        echo $HTML->heading2('Documents');
?>

  <div class="form-inner">
        <table class="tags">
            <thead>
                <tr>
                    <th class="action">ID</th>
                    <th>Document Name</th>
                      <th>Document Type</th>
                    <th>Upload Date</th>
                    <th>Archived</th>
                    <th>Status</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
        <?php
            if (PerchUtil::count($documents)) {
                $target_dir = '/perch/addons/apps/perch_members/documents/';

                foreach ($documents as $Document) {
                    $document_id = (int) $Document->documentID();
                    $document_name = (string) $Document->documentName();
                    $document_type = (string) $Document->documentType();
                    $document_status = (string) $Document->documentStatus();
                    $document_row_id = 'document-row-'.$document_id;
                    $document_href = $target_dir.rawurlencode($document_name);
                    $document_name_attr = htmlspecialchars($document_name, ENT_QUOTES, 'UTF-8');

                    echo '<tr id="'.PerchUtil::html($document_row_id).'" data-document-id="'.$document_id.'">';
                        echo '<td class="action">'.PerchUtil::html($document_id).'</td>';
                        echo '<td><a target="_blank" rel="noopener" href="'.PerchUtil::html($document_href).'">'.PerchUtil::html($document_name).'</a></td>';
                        echo '<td class="action">'.PerchUtil::html($document_type).'</td>';
                        echo '<td>'.PerchUtil::html($Document->documenUploadDate() ? date('d M Y', strtotime($Document->documenUploadDate())) : '-').'</td>';
                        echo '<td>'.PerchUtil::html($Document->documentDeleted() ? 'Archived' : 'Available').'</td>';

                        echo '<td>';
                            echo '<span class="document-status-control">';
                                echo '<select id="document-status-'.$document_id.'" name="docstatus" data-document-id="'.$document_id.'">';
                                $status_options = array(
                                    'pending'   => 'Pending',
                                    'accepted'  => 'Accepted',
                                    'declined'  => 'Declined',
                                    'rerequest' => 'Rerequest',
                                );
                                foreach ($status_options as $value => $label) {
                                    $selected = ($document_status === $value) ? ' selected="selected"' : '';
                                    echo '<option value="'.PerchUtil::html($value).'"'.$selected.'>'.PerchUtil::html($label).'</option>';
                                }
                                echo '</select>';
                                echo '<label class="document-status-note">';
                                echo '<span class="note-label">Document notification</span>';
                                echo '<textarea id="document-note-'.$document_id.'" class="document-status-note-input" data-document-id="'.$document_id.'" placeholder="Add a note for the member (optional)." rows="3"></textarea>';
                                echo '</label>';
                                echo '<span class="document-status-result" id="document-result-'.$document_id.'"></span>';
                            echo '</span>';
                        echo '</td>';

                        echo '<td class="action">';
                            echo '<button type="button" class="button button-small action-delete-document" data-document-id="'.$document_id.'" data-document-name="'.$document_name_attr.'">Delete</button>';
                            echo '<span class="document-delete-result" id="document-delete-result-'.$document_id.'"></span>';
                        echo '</td>';
                    echo '</tr>';
                }

            }

           echo '<tr>';
                echo '<td class="action">'.$Form->label('new-document', PerchLang::get('New')).'</td>';
                  //   echo '<td>'.$Form->text('new-document', false).'</td>';
                echo '<td>'.$Form->multifile_field('new-document', false).'</td>';
               // echo '<td>'.$Form->checkbox('new-expire', '1', '0').' '.$Form->datepicker('new-expires', false).'</td>';
            echo '</tr>';

        ?>

            </tbody>
        </table>
    </div>

    <?php
        echo $HTML->heading3('Automated document reminders');
    ?>
    <div class="form-inner">
        <p class="hint">Select the reminder that matches the outstanding documents. Only one option can be active at a time.</p>
        <fieldset class="document-reminder-fieldset">
            <legend>Reminder status</legend>
            <input type="hidden" name="document_reminder_status" id="document-reminder-status-input" value="<?php echo PerchUtil::html($documentReminderStatus); ?>" />
        <?php
            if (PerchUtil::count($documentReminderOptions)) {
                foreach ($documentReminderOptions as $value => $option) {
                    $label = isset($option['label']) ? $option['label'] : ucfirst(str_replace('_', ' ', (string) $value));
                    $description = isset($option['description']) ? trim((string) $option['description']) : '';
                    $checked = ($documentReminderStatus === $value) ? ' checked="checked"' : '';

                    echo '<label class="document-reminder-choice">';
                    echo '<input type="checkbox" class="document-reminder-checkbox" name="document_reminder_status_choice[]" value="'.PerchUtil::html($value).'" data-value="'.PerchUtil::html($value).'"'.$checked.' />';
                    echo '<span class="document-reminder-label">'.PerchUtil::html($label).'</span>';
                    if ($description !== '') {
                        echo '<span class="document-reminder-description">'.PerchUtil::html($description).'</span>';
                    }
                    echo '</label>';
                }
            }
        ?>
        </fieldset>
        <?php
            if ($documentReminderLastSentAt) {
                $lastSentTimestamp = strtotime($documentReminderLastSentAt);
                $lastSentFormatted = $lastSentTimestamp ? date('d M Y H:i', $lastSentTimestamp) : (string) $documentReminderLastSentAt;
                $lastStatusLabel = isset($documentReminderOptions[$documentReminderLastStatus]['label'])
                    ? $documentReminderOptions[$documentReminderLastStatus]['label']
                    : $documentReminderLastStatus;

                echo '<p class="info">Last reminder sent on '.PerchUtil::html($lastSentFormatted);
                if ($lastStatusLabel) {
                    echo ' ('.PerchUtil::html($lastStatusLabel).')';
                }
                echo '.</p>';
            }
        ?>
    </div>

     <?php
        if ($is_customer) {
            echo $HTML->heading2('Customer addresses');
            echo '<div class="form-inner">';

            echo '<h3>Shipping address</h3>';
            foreach ($address_field_keys as $field_key) {
                $label = isset($address_field_labels[$field_key]) ? $address_field_labels[$field_key] : ucfirst($field_key);
                $value = isset($shipping_address_details[$field_key]) ? $shipping_address_details[$field_key] : '';

                if ($field_key === 'instructions') {
                    echo $Form->textarea_field('shipping_'.$field_key, $label, $value, 'input-simple', false);
                } else {
                    echo $Form->text_field('shipping_'.$field_key, $label, $value, 'l');
                }
            }

            echo '</div>';
        }

     ?>

     <?php
       //Questionnaire
             $orders = [];
             echo $HTML->heading2('Orders');
             if( $Customer){
                     echo'<div class="form-inner">
                          <table class="tags">
                              <thead>
                                  <tr>

                                      <th>Order</th>
                                      <th>Date</th>
 <th>Total</th>
                                  </tr>
                              </thead>
                              <tbody>';
               $orders = $Orders->findAll_for_customer($Customer);
                if (PerchUtil::count($orders)) {
                 foreach($orders as $Order) {
                        echo '<tr><td><a target="_blank" href="/perch/addons/apps/perch_shop_orders/order/?id='.$Order->orderID().'" >'.$Order->orderInvoiceNumber().'</a></td><td>'.$Order->orderCreated().'</td><td>'.$Order->orderTotal().'</td></tr>';
                }
             }else{
               echo '<tr><td>No Orders</td></tr>';
             }
          }


     ?>
      </tbody>
             </table>
         </div>
     <?php
       //Questionnaire
             echo $HTML->heading2('Questionnaire');
     ?>

       <div class="form-inner">
             <table class="tags">
                 <thead>
                     <tr>

                         <th>Question</th>
                         <th>Answer</th>

                     </tr>
                 </thead>
                 <tbody>
             <?php

            $questions=$Questionnaires->get_questions();
            $answer_indicates_allergies = static function ($answerText) {
                $normalized = strtolower(trim((string)$answerText));

                return $normalized !== '' && strpos($normalized, 'yes') === 0;
            };
            $should_skip_question = static function($slug, $answers_by_slug) use ($answer_indicates_allergies) {
                if ($slug === 'allergy_details') {
                    if (!isset($answers_by_slug['allergies'])) {
                        return false;
                    }

                    $allergy_answer = $answers_by_slug['allergies']->answer_text();

                    if (!$answer_indicates_allergies($allergy_answer)) {
                        return true;
                    }
                }

                return false;
            };
             $bmi_edit_controls_needed = false;

                if (PerchUtil::count($questionnaire)) {
                    $answers_by_slug = [];

                    foreach ($questionnaire as $Questionnaire) {
                        $slug = $Questionnaire->question_slug();
                        if (array_key_exists($slug, $questions) && !isset($answers_by_slug[$slug])) {
                            $answers_by_slug[$slug] = $Questionnaire;
                        }
                    }

                    if (PerchUtil::count($answers_by_slug)) {
                        $historyPrinted = false;
                        foreach ($questions as $slug => $question_label) {
                            if (!isset($answers_by_slug[$slug])) {
                                continue;
                            }
                            if ($should_skip_question($slug, $answers_by_slug)) {
                                continue;
                            }
                            $Questionnaire = $answers_by_slug[$slug];
                            if (!$historyPrinted) {
                                echo '<tr><td colspan="2"><a class="button button button-simple" target="_blank" href="https://'.$_SERVER['HTTP_HOST'].'/perch/addons/apps/perch_members/questionnaire_logs?userId='.$Questionnaire->uuid().'">History</a></td></tr>';
                                $historyPrinted = true;
                            }
                            echo '<tr>';
                            echo '<td class="action">'.PerchUtil::html($question_label).'</td>';
                            echo '<td>';

                                $answerText = (string)$Questionnaire->answer_text();
                                $output = PerchUtil::html($answerText);

                                echo $output;

                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                }



             ?>

                 </tbody>
             </table>
         </div>


     <?php
       //Questionnaire
             echo $HTML->heading2('Questionnaire for Re-order');
     ?>

       <div class="form-inner">
             <table class="tags">
                 <thead>
                     <tr>

                         <th>Question</th>
                         <th>Answer</th>

                     </tr>
                 </thead>
                 <tbody>
             <?php

             $questions=$Questionnaires->get_questions("re-order");

                if (PerchUtil::count($questionnaire_reorder)) {
                    $answers_by_slug = [];

                    foreach ($questionnaire_reorder as $Questionnaire) {
                        $slug = $Questionnaire->question_slug();
                        if (array_key_exists($slug, $questions) && !isset($answers_by_slug[$slug])) {
                            $answers_by_slug[$slug] = $Questionnaire;
                        }
                    }

                    if (PerchUtil::count($answers_by_slug)) {
                        $historyPrinted = false;
                        foreach ($questions as $slug => $question_label) {
                            if (!isset($answers_by_slug[$slug])) {
                                continue;
                            }
                            if ($should_skip_question($slug, $answers_by_slug)) {
                                continue;
                            }
                            $Questionnaire = $answers_by_slug[$slug];
                            if (!$historyPrinted) {
                                echo '<tr><td colspan="2"><a class="button button button-simple" target="_blank" href="https://getweightloss.co.uk/perch/addons/apps/perch_members/questionnaire_logs?userId='.$Questionnaire->uuid().'&type=re-order">History</a></td></tr>';
                                $historyPrinted = true;
                            }
                            echo '<tr>';
                            echo '<td class="action">'.PerchUtil::html($question_label).'</td>';
                            echo '<td>';

                                echo PerchUtil::html($Questionnaire->answer_text());

                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                }



             ?>

                 </tbody>
             </table>
         </div>




    
<?php
if (!function_exists('wl_member_note_present')) {
    function wl_member_note_present($rawNote)
    {
        $rawNote = trim((string) $rawNote);
        $meta = [
            'category' => 'Admin Notes',
            'target' => 'Patient Notes',
            'thread' => '-',
            'order' => '-',
            'red_flag' => 'No',
            'body' => $rawNote,
        ];

        if (preg_match('/^\[(Admin Notes|Clinical|Complaint)\]/i', $rawNote, $matches)) {
            $meta['category'] = trim((string) $matches[1]);
            $rawNote = trim(substr($rawNote, strlen($matches[0])));
        }

        if (preg_match('/^\[(Patient Notes|Order Notes)\]/i', $rawNote, $matches)) {
            $meta['target'] = trim((string) $matches[1]);
            $rawNote = trim(substr($rawNote, strlen($matches[0])));
        }

        if (preg_match('/^\[Thread:([^\]]+)\]/i', $rawNote, $matches)) {
            $thread = trim((string) $matches[1]);
            $meta['thread'] = $thread !== '' ? $thread : '-';
            $rawNote = trim(substr($rawNote, strlen($matches[0])));
        }

        if (preg_match('/^\[Order\s*#(\d+)\]/i', $rawNote, $matches)) {
            $meta['order'] = '#' . (int) $matches[1];
            $rawNote = trim(substr($rawNote, strlen($matches[0])));
        }

        $meta['red_flag'] = in_array(strtolower($meta['category']), ['clinical', 'complaint'], true) ? 'Yes' : 'No';
        $meta['body'] = $rawNote;

        return $meta;
    }
}

if (!function_exists('wl_normalize_comms_notes')) {
    function wl_normalize_comms_notes($commsMemberNotes)
    {
        $normalized = [];

        if (!is_array($commsMemberNotes)) {
            return $normalized;
        }

        foreach ($commsMemberNotes as $item) {
            if (!is_array($item)) {
                continue;
            }

            if (isset($item['notes']) && is_array($item['notes'])) {
                foreach ($item['notes'] as $note) {
                    if (is_array($note)) {
                        $normalized[] = $note;
                    }
                }
                continue;
            }

            if (isset($item['note_id']) || isset($item['body']) || isset($item['replies'])) {
                $normalized[] = $item;
            }
        }

        return $normalized;
    }
}

if (!function_exists('wl_comms_note_author')) {
    function wl_comms_note_author($entry)
    {
        if (!is_array($entry)) {
            return '-';
        }

        $displayName = trim((string) ($entry['created_by_display_name'] ?? ''));
        if ($displayName !== '') {
            return $displayName;
        }

        $role = trim((string) ($entry['created_by_role'] ?? ''));
        if ($role !== '') {
            return ucfirst($role);
        }

        return '-';
    }
}

if (!function_exists('wl_comms_note_date')) {
    function wl_comms_note_date($value)
    {
        $rawDate = trim((string) $value);
        if ($rawDate === '') {
            return '-';
        }

        $timestamp = strtotime($rawDate);
        return $timestamp ? date('d M Y H:i', $timestamp) : $rawDate;
    }
}


if (!function_exists('wl_comms_note_identifier')) {
    function wl_comms_note_identifier($entry)
    {
        if (!is_array($entry)) {
            return '';
        }

        $candidates = [
            $entry['external_note_ref'] ?? null,
            $entry['note_id'] ?? null,
            $entry['id'] ?? null,
            $entry['noteID'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $id = trim((string) $candidate);
            if ($id !== '') {
                return $id;
            }
        }

        return '';
    }
}


if (!function_exists('wl_comms_text_key')) {
    function wl_comms_text_key($value)
    {
        $text = strtolower(trim((string) $value));
        if ($text === '') {
            return '';
        }

        return preg_replace('/\s+/', ' ', $text);
    }
}

if (!function_exists('wl_comms_indexed_texts')) {
    function wl_comms_indexed_texts($commsMemberNotes)
    {
        $indexed = [];
        $normalized = wl_normalize_comms_notes($commsMemberNotes);

        foreach ($normalized as $note) {
            if (!is_array($note)) {
                continue;
            }

            $noteKey = wl_comms_text_key($note['body'] ?? $note['note'] ?? '');
            if ($noteKey !== '') {
                $indexed[$noteKey] = true;
            }

            $replies = isset($note['replies']) && is_array($note['replies']) ? $note['replies'] : [];
            foreach ($replies as $reply) {
                if (!is_array($reply)) {
                    continue;
                }

                $replyKey = wl_comms_text_key($reply['body'] ?? $reply['reply'] ?? '');
                if ($replyKey !== '') {
                    $indexed[$replyKey] = true;
                }
            }
        }

        return $indexed;
    }
}
?>


<?php echo $HTML->heading2('Notes'); ?>

<?php
    if (!isset($NotePharmacyStatuses) || !($NotePharmacyStatuses instanceof PerchMembers_NotePharmacyStatuses)) {
        if (isset($API) && is_object($API)) {
            $NotePharmacyStatuses = new PerchMembers_NotePharmacyStatuses($API);
        } else {
            $NotePharmacyStatuses = null;
        }
    }
?>

           <div class="form-inner">
                  <table class="notes">
                      <thead>
                          <tr>

                              <th>Note</th>
                              <th>Date</th>
                              <th>Category</th>
                              <th>Type</th>
                              <th>Thread</th>
                                <th>Added by</th>
                              <th>Pharmacy status</th>
                              <th>Red flag</th>
                              <th>Escalate clinical review</th>
                                <th class="action">Action</th>
                          </tr>
                      </thead>
                      <tbody>
                  <?php
                      $commsIndexedTexts = wl_comms_indexed_texts($comms_member_notes);
                      if (PerchUtil::count($notes)) {
                          foreach($notes as $Note) {
                              $rawNoteText = (string) $Note->note_text();
                              $noteMeta = wl_member_note_present($rawNoteText);

                              $rawNoteKey = wl_comms_text_key($rawNoteText);
                              $parsedNoteKey = wl_comms_text_key($noteMeta['body']);
                              $noteExistsInComms = ($rawNoteKey !== '' && isset($commsIndexedTexts[$rawNoteKey]))
                                  || ($parsedNoteKey !== '' && isset($commsIndexedTexts[$parsedNoteKey]));

                              if ($noteExistsInComms) {
                                  continue;
                              }

                              $noteID = (int) $Note->id();
                              $pharmacyStatus = false;
                              if ($NotePharmacyStatuses instanceof PerchMembers_NotePharmacyStatuses && isset($Member) && is_object($Member)) {
                                  $pharmacyStatus = $NotePharmacyStatuses->find_one_by_member_and_note((int) $Member->id(), $noteID);
                              }
                           //  echo "statu"; print_r($pharmacyStatus);
                              $statusLabel = '';
                              $statusClassSuffix = 'sent';
                              $statusMessage = '';
                              $statusSentAt = '';

                              if ($pharmacyStatus instanceof PerchMembers_NotePharmacyStatus) {
                                  $statusLabel = trim((string) $pharmacyStatus->status());
                                  if ($statusLabel === '') {
                                      $statusLabel = 'Sent';
                                  }

                                  $statusClassSuffix = strtolower(preg_replace('/[^a-z0-9]+/', '-', $statusLabel));
                                  $statusClassSuffix = trim($statusClassSuffix, '-');
                                  if ($statusClassSuffix === '') {
                                      $statusClassSuffix = 'sent';
                                  }

                                  $statusMessage = trim((string) $pharmacyStatus->message());

                                  $sentAt = $pharmacyStatus->sentAt();
                                  if ($sentAt) {
                                      $timestamp = strtotime($sentAt);
                                      if ($timestamp) {
                                          $statusSentAt = date('d M Y H:i', $timestamp);
                                      }
                                  }
                              }


                              echo '<tr>';

                                  echo '<td>'.PerchUtil::html($noteMeta['body']).'</td>';
                                  echo '<td>'.PerchUtil::html($Note->noteDate() ? date('d M Y H:i', strtotime($Note->noteDate())) : '-').'</td>';
                                  echo '<td>'.PerchUtil::html($noteMeta['category']).'</td>';
                                  echo '<td>'.PerchUtil::html($noteMeta['target']).(($noteMeta['order'] !== '-') ? ' '.PerchUtil::html($noteMeta['order']) : '').'</td>';
                                  echo '<td>'.PerchUtil::html($noteMeta['thread']).'</td>';
                                    echo '<td>'.PerchUtil::html($Note->addedBy()).'</td>';
                                    echo '<td>';
                                    if ($pharmacyStatus instanceof PerchMembers_NotePharmacyStatus) {
                                        echo '<span class="pharmacy-status pharmacy-status-'.$statusClassSuffix.'">Status: '.PerchUtil::html($statusLabel).'</span>';
                                        if ($statusSentAt !== '') {
                                            echo '<div class="meta">Sent '.PerchUtil::html($statusSentAt).'</div>';
                                        }
                                        if ($statusMessage !== '') {
                                            echo '<div class="meta">'.PerchUtil::html($statusMessage).'</div>';
                                        }
                                    } else {
                                        echo '-';
                                    }
                                    echo '</td>';
                                    echo '<td>'.PerchUtil::html($noteMeta['red_flag']).'</td>';
                                    echo '<td class="action">';
                                    if (is_object($Member) && !($pharmacyStatus instanceof PerchMembers_NotePharmacyStatus)) {
                                        echo '<label><input type="checkbox" name="note_escalate['.(int)$Note->id().']" value="1" /> Escalate clinical review</label>';
                                    } else {
                                        echo '-';
                                    }
                                    echo '</td>';
                                    echo '<td class="action">';
                                    if (is_object($Member) && !($pharmacyStatus instanceof PerchMembers_NotePharmacyStatus)) {
                                        echo '<button type="submit" style="background-color:#199d19" class="button button-simple" name="send_note_to_pharmacy" value="'.(int)$Note->id().'">Push to pharmacy</button>';
                                    }
                                    echo '</td>';
                              echo '</tr>';
                          }
                      }

                      echo '<tr>';
                          echo '<td colspan="10" class="action">'.$Form->label('new-note', PerchLang::get('New'));
                          echo $Form->textarea('new-note', false, '', false, ' maxlength="2000"');
                          echo '<div style="margin-top:8px;display:grid;grid-template-columns:repeat(2,minmax(220px,1fr));gap:10px;align-items:start;">';
                          echo '<label>Notes category<br />';
                          echo '<select name="new-note-category">';
                          echo '<option value="admin_notes">Admin Notes</option>';
                          echo '<option value="clinical">Clinical</option>';
                          echo '<option value="complaint">Complaint</option>';
                          echo '</select></label>';
                          echo '<label>Notes type<br />';
                          echo '<select name="new-note-target">';
                          echo '<option value="patient_note">Patient Notes</option>';
                          echo '<option value="order_note">Order Notes</option>';
                          echo '</select></label>';
                          echo '<label>Origin thread/message ID<br /><input type="text" name="new-note-thread" value="" /></label>';
                          echo '<label>Order ID (when using Order Notes)<br />';
                          echo '<select name="new-note-order-id">';
                          echo '<option value="">Select an order</option>';
                          if (isset($orders) && PerchUtil::count($orders)) {
                              foreach ($orders as $OrderOption) {
                                  $orderId = (int) $OrderOption->orderID();
                                  $invoice = trim((string) $OrderOption->orderInvoiceNumber());
                                  $orderDate = trim((string) $OrderOption->orderCreated());
                                  $optionLabel = ($invoice !== '' ? $invoice : ('Order #' . $orderId));
                                  if ($orderDate !== '') {
                                      $optionLabel .= ' - ' . $orderDate;
                                  }
                                  echo '<option value="'.PerchUtil::html($orderId).'">'.PerchUtil::html($optionLabel).'</option>';
                              }
                          }
                          echo '</select></label>';
                          echo '<label style="grid-column:1/-1;"><input type="checkbox" name="new-note-red-flag" value="1" /> Red-flag for clinical escalation</label>';
                          echo '</div></td>';

                      echo '</tr>';

                  ?>

                      </tbody>
                  </table>
              </div>
<?php

	          echo $HTML->heading2('Member comms notes and replies');

          ?>

           <div class="form-inner">
	                  <table class="notes">
	                      <thead>
	                          <tr>
	                              <th>Note / Reply</th>
	                              <th>Date</th>
	                              <th>From</th>
	                          </tr>
	                      </thead>
	                      <tbody>
	                  <?php
	                      $normalizedCommsNotes = wl_normalize_comms_notes($comms_member_notes);
	                      if (PerchUtil::count($normalizedCommsNotes)) {
	                          foreach ($normalizedCommsNotes as $commsNote) {
	                              $noteBody = trim((string) ($commsNote['body'] ?? $commsNote['note'] ?? ''));
	                              $noteDate = wl_comms_note_date($commsNote['created_at'] ?? '');
	                              $noteAuthor = wl_comms_note_author($commsNote);

	                              $commsNoteID = wl_comms_note_identifier($commsNote);

	                              echo '<tr>';
	                                  echo '<td><strong>Note:</strong> '.PerchUtil::html($noteBody !== '' ? $noteBody : '-').'</td>';
	                                  echo '<td>'.PerchUtil::html($noteDate).'</td>';
	                                  echo '<td>'.PerchUtil::html($noteAuthor).'</td>';
	                              echo '</tr>';

	                              $replies = isset($commsNote['replies']) && is_array($commsNote['replies']) ? $commsNote['replies'] : [];
	                              foreach ($replies as $reply) {
	                                  if (!is_array($reply)) {
	                                      continue;
	                                  }

	                                  $replyBody = trim((string) ($reply['body'] ?? $reply['reply'] ?? ''));
	                                  $replyDate = wl_comms_note_date($reply['created_at'] ?? '');
	                                  $replyAuthor = wl_comms_note_author($reply);

	                                  echo '<tr>';
	                                      echo '<td style="padding-left:24px;">↳ '.PerchUtil::html($replyBody !== '' ? $replyBody : '-').'</td>';
	                                      echo '<td>'.PerchUtil::html($replyDate).'</td>';
	                                      echo '<td>'.PerchUtil::html($replyAuthor).'</td>';
	                                  echo '</tr>';
	                              }

	                              if ($commsNoteID !== '') {
	                                  $safeCommsNoteID = preg_replace('/[^a-zA-Z0-9_-]/', '-', $commsNoteID);
	                                  $safeCommsNoteID = trim((string) $safeCommsNoteID, '-');
	                                  if ($safeCommsNoteID === '') {
	                                      $safeCommsNoteID = 'note-reply';
	                                  }
	                                  echo '<tr>';
	                                      echo '<td colspan="3">';
	                                      echo '<label for="note-reply-'.PerchUtil::html($safeCommsNoteID).'" style="display:block;margin-bottom:6px;">Reply to this note</label>';
	                                      echo '<textarea id="note-reply-'.PerchUtil::html($safeCommsNoteID).'" name="note-reply['.PerchUtil::html($commsNoteID).']" rows="2" style="width:100%;max-width:680px;" placeholder="Type your reply"></textarea>';
	                                      echo '<div style="margin-top:8px;">';
	                                      echo '<button type="submit" class="button button-simple" name="send_member_note_reply" value="'.PerchUtil::html($commsNoteID).'">Send reply</button>';
	                                      echo '</div>';
	                                      echo '</td>';
	                                  echo '</tr>';
	                              }
	                          }
	                      } else {
	                          echo '<tr>';
	                              echo '<td colspan="3">No comms notes found.</td>';
	                          echo '</tr>';
	                      }

                  ?>

                      </tbody>
                  </table>
              </div>
<?php

          echo $HTML->heading2('Notifications');

          ?>

           <div class="form-inner">
                  <table class="notes">
                      <thead>
                          <tr>
                              <th>Title</th>
                              <th>Message</th>
                              <th>Date</th>
                          </tr>
                      </thead>
                      <tbody>
                  <?php
                      if (PerchUtil::count($notifications)) {
                          foreach($notifications as $Notification) {
                              echo '<tr>';
                                  echo '<td>'.PerchUtil::html($Notification->notificationTitle()).'</td>';
                                  echo '<td>'.PerchUtil::html($Notification->notificationMessage()).'</td>';
                                  echo '<td>'.PerchUtil::html($Notification->notificationDate() ? date('d m Y', strtotime($Notification->notificationDate())) : '-').'</td>';
                              echo '</tr>';
                          }
                      }

                      echo '<tr>';
                          echo '<td>'.$Form->text('new-notification-title', false).'</td>';
                          echo '<td>'.$Form->text('new-notification-message', false).'</td>';
                          echo '<td></td>';
                      echo '</tr>';

                  ?>

                      </tbody>
                  </table>
              </div>

<?php
        }// is object Member

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());
    
    echo $Form->form_end();

    if (is_object($Member)) {
?>
<script>
(function () {
    var form = document.querySelector('form.membersclass, form.form-simple, .main form');
    if (!form || form.classList.contains('member-tabs-enhanced')) return;

    var children = Array.prototype.slice.call(form.children || []);
    var headings = children.filter(function (node) {
        return node.tagName === 'H2';
    });

    if (headings.length < 2) return;

    form.classList.add('member-tabs-enhanced');

    var wrapper = document.createElement('div');
    wrapper.className = 'member-edit-tabs card';

    var nav = document.createElement('div');
    nav.className = 'member-edit-tabs-nav nav nav-tabs';
    nav.setAttribute('role', 'tablist');
    nav.setAttribute('aria-label', 'Member edit sections');

    var panels = document.createElement('div');
    panels.className = 'member-edit-tabs-panels';

    var colorClasses = ['color-a', 'color-b', 'color-c', 'color-d', 'color-e', 'color-f', 'color-g', 'color-h', 'color-i'];
    var tabButtons = [];

    var activateTab = function (activeIndex, shouldFocus) {
        tabButtons.forEach(function (btn, idx) {
            var isActive = idx === activeIndex;
            var targetPanel = document.getElementById(btn.getAttribute('aria-controls'));

            btn.classList.toggle('is-active', isActive);
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
            btn.setAttribute('aria-current', isActive ? 'page' : 'false');
            btn.setAttribute('tabindex', isActive ? '0' : '-1');

            if (targetPanel) {
                targetPanel.classList.toggle('is-active', isActive);
                targetPanel.classList.toggle('show', isActive);
                targetPanel.classList.toggle('active', isActive);
                targetPanel.hidden = !isActive;
            }

            if (isActive && shouldFocus) {
                btn.focus();
            }
        });
    };

    headings.forEach(function (heading, index) {
        var panel = document.createElement('section');
        var tabId = 'member-edit-tab-' + index;
        var tabButtonId = 'member-edit-tab-button-' + index;
        var headingText = (heading.textContent || '').trim() || ('Section ' + (index + 1));

        panel.className = 'member-edit-tab-panel tab-pane fade' + (index === 0 ? ' is-active active show' : '');
        panel.id = tabId;
        panel.setAttribute('role', 'tabpanel');
        panel.setAttribute('aria-labelledby', tabButtonId);
        panel.hidden = index !== 0;

        heading.classList.add('member-edit-section-heading');

        var next = heading.nextSibling;
        panel.appendChild(heading);
        while (next && next.tagName !== 'H2' && !(next.classList && next.classList.contains('submit-bar'))) {
            var move = next;
            next = next.nextSibling;
            panel.appendChild(move);
        }

        var button = document.createElement('button');
        button.type = 'button';
        button.id = tabButtonId;
        button.className = 'member-edit-tab-button nav-link ' + colorClasses[index % colorClasses.length] + (index === 0 ? ' is-active active' : '');
        button.setAttribute('role', 'tab');
        button.setAttribute('aria-controls', tabId);
        button.setAttribute('aria-selected', index === 0 ? 'true' : 'false');
        button.setAttribute('aria-current', index === 0 ? 'page' : 'false');
        button.setAttribute('tabindex', index === 0 ? '0' : '-1');
        button.textContent = headingText;

        button.addEventListener('click', function () {
            activateTab(index, false);
        });

        button.addEventListener('keydown', function (event) {
            var key = event.key;
            if (key !== 'ArrowRight' && key !== 'ArrowLeft' && key !== 'Home' && key !== 'End') return;

            event.preventDefault();
            var nextIndex = index;
            if (key === 'ArrowRight') nextIndex = (index + 1) % tabButtons.length;
            if (key === 'ArrowLeft') nextIndex = (index - 1 + tabButtons.length) % tabButtons.length;
            if (key === 'Home') nextIndex = 0;
            if (key === 'End') nextIndex = tabButtons.length - 1;
            activateTab(nextIndex, true);
        });

        tabButtons.push(button);
        nav.appendChild(button);
        panels.appendChild(panel);
    });

    wrapper.appendChild(nav);
    wrapper.appendChild(panels);
    form.insertBefore(wrapper, form.firstChild);
    activateTab(0, false);
})();
</script>
<?php
    }
