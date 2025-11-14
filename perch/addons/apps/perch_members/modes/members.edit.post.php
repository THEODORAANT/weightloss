<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get($heading1),
    ], $CurrentUser);

    if ($message) echo $message;    
        
    echo $HTML->heading2('Member details');
    
    echo $Form->form_start(false);
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
                                <th>Added by</th>
                              <th>Pharmacy status</th>
                              <th>Escalate clinical review</th>
                                <th class="action">Action</th>
                          </tr>
                      </thead>
                      <tbody>
                  <?php
                      if (PerchUtil::count($notes)) {
                          foreach($notes as $Note) {
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

                                  echo '<td>'.PerchUtil::html($Note->note_text()).'</td>';
                                  echo '<td>'.PerchUtil::html($Note->noteDate() ? date('d M Y', strtotime($Note->noteDate())) : '-').'</td>';
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
                                    echo '<td class="action">';
                                    if (is_object($Member) && !($pharmacyStatus instanceof PerchMembers_NotePharmacyStatus)) {
                                        echo '<label><input type="checkbox" name="note_escalate['.(int)$Note->id().']" value="1" /> Escalate clinical review</label>';
                                    } else {
                                        echo '-';
                                    }
                                    echo '</td>';
                                    echo '<td class="action">';
                                    if (is_object($Member) && !($pharmacyStatus instanceof PerchMembers_NotePharmacyStatus)) {
                                        echo '<button type="submit" style="background-color:#199d19" class="button button-simple" name="send_note_to_pharmacy" value="'.(int)$Note->id().'">Send to pharmacy</button>';
                                    }
                                    echo '</td>';
                              echo '</tr>';
                          }
                      }

                      echo '<tr>';
                          echo '<td colspan="6" class="action">'.$Form->label('new-note', PerchLang::get('New'));
                          echo $Form->text('new-note', false).'</td>';

                      echo '</tr>';

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
